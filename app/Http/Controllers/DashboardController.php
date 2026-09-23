<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Halaman Utama Dashboard
     */
    public function index()
    {
        $sensorData = Sensor::orderBy('created_at', 'desc')->take(1440)->get()->reverse()->values();
        $cuaca = $this->getCuacaData();

        return view('dashboard', compact('sensorData', 'cuaca'));
    }

    /**
     * Endpoint Polling Real-time (per 5 detik)
     */
    public function getLatestSensor()
    {
        $latest = Sensor::latest()->first();

        if (!$latest) {
            return response()->json([
                'status' => 'empty',
                'data'   => null
            ]);
        }

        // Pastikan nilai kualitas terhitung dan tersimpan di DB
        $this->ensureSensorQuality($latest);

        return response()->json([
            'status' => 'success',
            'data'   => $latest
        ]);
    }

    /**
     * Endpoint Data Sensor Lengkap + Cuaca (untuk Chart/Tabel)
     */
    public function getSensorData()
    {
        $sensorData = Sensor::orderBy('created_at', 'desc')->take(1440)->get();
        $latest = $sensorData->first();

        if ($latest) {
            $this->ensureSensorQuality($latest);
        }

        return response()->json([
            'sensors' => $sensorData->toArray(),
            'cuaca'   => $this->getCuacaData()
        ]);
    }

    /**
     * Endpoint Analisis Hybrid AI (ML + Fuzzy) & SOP
     */
    public function getAiRecommendation()
    {
        // Ambil 2 data sensor terakhir untuk menghitung Tren Delta
        $latestTwo = Sensor::orderBy('created_at', 'desc')->take(2)->get();

        if ($latestTwo->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data sensor belum tersedia untuk dianalisis.'
            ], 200);
        }

        $latest   = $latestTwo->first();
        $previous = $latestTwo->count() > 1 ? $latestTwo->last() : $latest;

        $this->ensureSensorQuality($latest);

        // Parameter Real-time (t)
        $ph        = (float) $latest->ph;
        $suhu      = (float) $latest->suhu;
        $tds       = (float) ($latest->tds ?? 0);
        $kekeruhan = (float) $latest->kekeruhan;

        // Hitung Selisih Tren Delta (t - (t-1))
        $phDelta        = $ph - (float) $previous->ph;
        $suhuDelta      = $suhu - (float) $previous->suhu;
        $tdsDelta       = $tds - (float) ($previous->tds ?? 0);
        $kekeruhanDelta = $kekeruhan - (float) $previous->kekeruhan;

        $statusSaatIni           = null;
        $statusPrediksi          = null;
        $statusPrediksiMasaDepan = null;
        $finalStatus             = null;
        $mlPredData              = null;
        $isOffline               = false;

        // Panggil Service Flask Python Hybrid AI
        try {
            $response = Http::asJson()->timeout(3)->post('http://127.0.0.1:5000/predict', [
                'ph'              => $ph,
                'suhu'            => $suhu,
                'tds'             => $tds,
                'kekeruhan'       => $kekeruhan,
                'ph_delta'        => $phDelta,
                'suhu_delta'      => $suhuDelta,
                'tds_delta'       => $tdsDelta,
                'kekeruhan_delta' => $kekeruhanDelta,
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                
                $sensorRt = $resData['sensor_realtime_t'] ?? [];
                $hybridAi = $resData['hybrid_ai'] ?? [];
                
                $statusSaatIni           = $sensorRt['status_saat_ini'] ?? 'Baik';
                $statusPrediksi          = $hybridAi['status_prediksi'] ?? 'Baik';
                $statusPrediksiMasaDepan = $hybridAi['status_prediksi_masa_depan'] ?? $statusPrediksi;
                $finalStatus             = $hybridAi['final_status'] ?? $statusSaatIni;
                $mlPredData              = $resData['ml_prediction_t_plus_1'] ?? null;
            } else {
                $isOffline = true;
            }
        } catch (\Exception $e) {
            \Log::error("Python Hybrid AI Service Offline: " . $e->getMessage());
            $isOffline = true;
        }

        // Fallback Lokal jika Service Flask Offline
        if ($isOffline) {
            $score = $latest->kualitas;
            
            if ($score >= 75) {
                $statusSaatIni = 'Baik';
            } elseif ($score >= 45) {
                $statusSaatIni = 'Sedang';
            } else {
                $statusSaatIni = 'Buruk';
            }

            $statusPrediksi          = 'Offline';
            $statusPrediksiMasaDepan = 'Offline';
            $finalStatus             = $statusSaatIni;
            $mlPredData              = null;
        }

        return response()->json([
            'status'       => 'success',
            'sensor_data'  => [
                'ph'        => $ph,
                'suhu'      => $suhu,
                'tds'       => $tds,
                'kekeruhan' => $kekeruhan,
            ],
            'hybrid_ai'    => [
                'status_saat_ini'            => $statusSaatIni,
                'status_prediksi'            => $statusPrediksi,
                'status_prediksi_masa_depan' => $statusPrediksiMasaDepan,
                'final_status'               => $finalStatus,
                'is_offline'                 => $isOffline,
            ],
            'ml_prediction' => $mlPredData,
            'tindakan_sop'  => $this->getSopActions($finalStatus),
            'advice'        => $this->getLocalEmergencyAdvice($latest)
        ]);
    }

    /**
     * Store Data dari Perangkat IoT
     */
    public function storeFromDevice(Request $request)
    {
        // Bersihkan data lama di atas 2 bulan
        Sensor::where('created_at', '<', now()->subMonths(2))->delete();

        $validated = $request->validate([
            'ph'        => 'required|numeric',
            'suhu'      => 'required|numeric',
            'tds'       => 'required|numeric',
            'kekeruhan' => 'required|numeric',
        ]);

        $fuzzyScore = $this->calculateFuzzyQuality(
            $validated['ph'],
            $validated['suhu'],
            $validated['tds'],
            $validated['kekeruhan']
        );

        $sensor = Sensor::create([
            'ph'        => $validated['ph'],
            'suhu'      => $validated['suhu'],
            'tds'       => $validated['tds'],
            'kekeruhan' => $validated['kekeruhan'],
            'kualitas'  => $fuzzyScore,
        ]);

        return response()->json(['status' => 'success', 'data' => $sensor], 201);
    }

    /**
     * Pengecekan Peringatan Parameter
     */
    public function checkWaterQuality()
    {
        $latestData = Sensor::latest()->first();

        if (!$latestData) {
            return response()->json(['status' => 'ok', 'message' => 'Belum ada data sensor.']);
        }

        $warnings = [];
        if ($latestData->ph < 7.0 || $latestData->ph > 8.5) $warnings[] = "⚠️ pH tidak normal! Saat ini: {$latestData->ph}";
        if ($latestData->suhu < 26.0 || $latestData->suhu > 34.0) $warnings[] = "🔥 Suhu ekstrem! Saat ini: {$latestData->suhu}°C";
        if ($latestData->tds > 500) $warnings[] = "⚡ TDS tinggi! Saat ini: {$latestData->tds} ppm";
        if ($latestData->kekeruhan > 43) $warnings[] = "💧 Kekeruhan tinggi! Saat ini: {$latestData->kekeruhan} NTU";

        return !empty($warnings)
            ? response()->json(['status' => 'warning', 'messages' => $warnings])
            : response()->json(['status' => 'ok', 'message' => 'Semua parameter dalam kondisi normal.']);
    }

    /**
     * Check Kualitas Air Sederhana
     */
    public function checkWater()
    {
        $latest = Sensor::latest()->first();

        if (!$latest) {
            return response()->json(['status' => 'no_data', 'message' => 'Tidak ada data sensor.']);
        }

        $this->ensureSensorQuality($latest);

        return response()->json([
            'status'   => $latest->kualitas < 50 ? 'warning' : 'normal',
            'quality'  => round($latest->kualitas, 2),
            'messages' => $latest->kualitas < 50
                ? ['Kualitas air buruk. Segera cek kolam.']
                : ['Kualitas air dalam kondisi baik.']
        ]);
    }

    /**
     * Batch Update Data Kualitas yang Kosong/Null
     */
    public function updateMissingQuality()
    {
        $sensors = Sensor::whereNull('kualitas')->get();
        $count = 0;

        foreach ($sensors as $sensor) {
            if ($sensor->ph && $sensor->suhu && $sensor->kekeruhan) {
                $this->ensureSensorQuality($sensor);
                $count++;
            }
        }

        return response()->json([
            'message' => "Berhasil update kualitas untuk $count data yang sebelumnya null."
        ]);
    }

    /**
     * Hitung Kualitas Air Menggunakan Logika Fuzzy
     */
    public function calculateFuzzyQuality($ph, $suhu, $tds, $ntu)
    {
        $ph_membership = [
            'asam'   => $this->trapezoid($ph, 0, 0, 6.0, 6.8),
            'netral' => $this->trapezoid($ph, 6.5, 7.0, 8.5, 8.8),
            'basa'   => $this->trapezoid($ph, 8.6, 9.0, 14, 14),
        ];

        $suhu_membership = [
            'dingin'  => $this->trapezoid($suhu, 0, 0, 24, 26),
            'optimal' => $this->trapezoid($suhu, 25, 27, 33, 35),
            'panas'   => $this->trapezoid($suhu, 34, 36, 45, 45),
        ];

        $tds_normal = $this->trapezoid($tds, 0, 0, 400, 500);
        $tds_sedang = $this->trapezoid($tds, 450, 550, 950, 1050);
        $tds_tinggi = $this->trapezoid($tds, 950, 1050, 5000, 5000);

        $keruh_membership = [
            'jernih'  => $this->trapezoid($ntu, 0, 0, 3, 5),
            'optimal' => $this->trapezoid($ntu, 3, 5, 40, 43),
            'keruh'   => $this->trapezoid($ntu, 40, 45, 100, 100),
        ];

        $base_rules = [
            ['asam', 'dingin', 'jernih', 'buruk'],
            ['asam', 'dingin', 'optimal', 'buruk'],
            ['asam', 'dingin', 'keruh', 'buruk'],
            ['asam', 'optimal', 'jernih', 'cukup'],
            ['asam', 'optimal', 'optimal', 'cukup'],
            ['asam', 'optimal', 'keruh', 'buruk'],
            ['asam', 'panas', 'jernih', 'buruk'],
            ['asam', 'panas', 'optimal', 'buruk'],
            ['asam', 'panas', 'keruh', 'buruk'],

            ['netral', 'dingin', 'jernih', 'cukup'],
            ['netral', 'dingin', 'optimal', 'cukup'],
            ['netral', 'dingin', 'keruh', 'buruk'],
            ['netral', 'optimal', 'jernih', 'baik'],
            ['netral', 'optimal', 'optimal', 'baik'],
            ['netral', 'optimal', 'keruh', 'cukup'],
            ['netral', 'panas', 'jernih', 'cukup'],
            ['netral', 'panas', 'optimal', 'cukup'],
            ['netral', 'panas', 'keruh', 'buruk'],

            ['basa', 'dingin', 'jernih', 'buruk'],
            ['basa', 'dingin', 'optimal', 'buruk'],
            ['basa', 'dingin', 'keruh', 'buruk'],
            ['basa', 'optimal', 'jernih', 'cukup'],
            ['basa', 'optimal', 'optimal', 'cukup'],
            ['basa', 'optimal', 'keruh', 'buruk'],
            ['basa', 'panas', 'jernih', 'buruk'],
            ['basa', 'panas', 'optimal', 'buruk'],
            ['basa', 'panas', 'keruh', 'buruk'],
        ];

        $rule_outputs = ['buruk' => 0, 'cukup' => 0, 'baik' => 0];

        foreach ($base_rules as $rule) {
            [$ph_key, $suhu_key, $keruh_key, $output_label] = $rule;
            $mu_ph    = $ph_membership[$ph_key] ?? 0;
            $mu_suhu  = $suhu_membership[$suhu_key] ?? 0;
            $mu_keruh = $keruh_membership[$keruh_key] ?? 0;

            $mu_normal = min($mu_ph, $mu_suhu, $mu_keruh, $tds_normal);
            $rule_outputs[$output_label] = max($rule_outputs[$output_label], $mu_normal);

            $mu_sedang = min($mu_ph, $mu_suhu, $mu_keruh, $tds_sedang);
            $sedang_label = ($output_label === 'baik') ? 'cukup' : 'buruk';
            $rule_outputs[$sedang_label] = max($rule_outputs[$sedang_label], $mu_sedang);

            $mu_tinggi = min($mu_ph, $mu_suhu, $mu_keruh, $tds_tinggi);
            $rule_outputs['buruk'] = max($rule_outputs['buruk'], $mu_tinggi);
        }

        $numerator = 0;
        $denominator = 0;

        for ($x = 0; $x <= 100; $x += 1) { 
            $mu_buruk = min($rule_outputs['buruk'], $this->output_membership($x, 'buruk'));
            $mu_cukup = min($rule_outputs['cukup'], $this->output_membership($x, 'cukup'));
            $mu_baik  = min($rule_outputs['baik'], $this->output_membership($x, 'baik'));

            $mu_total = max($mu_buruk, $mu_cukup, $mu_baik);

            $numerator   += $x * $mu_total;
            $denominator += $mu_total;
        }

        if ($denominator == 0) return 0;

        return round($numerator / $denominator, 2);
    }

    /**
     * Helper: Memastikan Data Sensor Memiliki Kualitas Air yang Tersimpan
     */
    private function ensureSensorQuality(Sensor $sensor)
    {
        if ($sensor->kualitas === null) {
            $sensor->kualitas = round($this->calculateFuzzyQuality(
                $sensor->ph ?? 7.0,
                $sensor->suhu ?? 28.0,
                $sensor->tds ?? 0.0,
                $sensor->kekeruhan ?? 0.0
            ), 2);
            $sensor->save();
        }
    }

    private function getSopActions($status)
    {
        $status = ucfirst(strtolower(trim($status)));

        if ($status === 'Baik') {
            return [
                ['text' => 'Budidaya dilanjutkan secara normal.', 'icon' => 'fas fa-check-circle'],
                ['text' => 'Pemberian pakan dilakukan sesuai jadwal.', 'icon' => 'fas fa-calendar-alt'],
                ['text' => 'Monitoring IoT tetap berjalan.', 'icon' => 'fas fa-chart-line'],
                ['text' => 'Kondisi organisme diamati secara visual.', 'icon' => 'fas fa-eye'],
                ['text' => 'Data kualitas air dicatat secara otomatis.', 'icon' => 'fas fa-file-alt'],
                ['text' => 'Tidak diperlukan tindakan korektif khusus.', 'icon' => 'fas fa-shield-alt'],
            ];
        } elseif ($status === 'Sedang') {
            return [
                ['text' => 'Lakukan pemeriksaan kondisi tambak.', 'icon' => 'fas fa-search'],
                ['text' => 'Periksa kembali pembacaan sensor.', 'icon' => 'fas fa-sliders-h'],
                ['text' => 'Amati aktivitas udang dan ikan.', 'icon' => 'fas fa-eye'],
                ['text' => 'Periksa respons terhadap pakan.', 'icon' => 'fas fa-utensils'],
                ['text' => 'Tingkatkan frekuensi pengamatan.', 'icon' => 'fas fa-clock'],
                ['text' => 'Evaluasi kondisi air.', 'icon' => 'fas fa-tint'],
                ['text' => 'Lakukan tindakan korektif apabila kondisi terus memburuk.', 'icon' => 'fas fa-exclamation-triangle'],
                ['text' => 'Catat kejadian pada log budidaya.', 'icon' => 'fas fa-clipboard-list'],
            ];
        } else {
            return [
                ['text' => 'Lakukan verifikasi pembacaan sensor.', 'icon' => 'fas fa-exclamation-circle'],
                ['text' => 'Lakukan pemeriksaan langsung terhadap kondisi tambak.', 'icon' => 'fas fa-shoe-prints'],
                ['text' => 'Amati perilaku udang dan ikan.', 'icon' => 'fas fa-eye'],
                ['text' => 'Evaluasi pemberian pakan.', 'icon' => 'fas fa-utensils'],
                ['text' => 'Evaluasi kondisi sirkulasi air.', 'icon' => 'fas fa-sync-alt'],
                ['text' => 'Lakukan tindakan korektif sesuai parameter yang bermasalah.', 'icon' => 'fas fa-wrench'],
                ['text' => 'Lakukan monitoring ulang setelah tindakan.', 'icon' => 'fas fa-redo-alt'],
                ['text' => 'Catat kejadian dan tindakan yang dilakukan.', 'icon' => 'fas fa-clipboard-check'],
            ];
        }
    }

    private function getLocalEmergencyAdvice($latest)
    {
        $recommendations = [];

        // Parameter pH
        if ($latest->ph < 6.0) {
            $recommendations[] = [
                'type'  => 'PH KRITIS',
                'level' => 'danger',
                'time'  => 'Segera',
                'title' => 'Tebar Kapur Dolomit / Kalsit',
                'desc'  => "pH sangat asam ({$latest->ph}). Segera tebar kapur pertanian dosis 10–15 ppm secara merata.",
                'icon'  => 'fa-vial'
            ];
        } elseif ($latest->ph < 6.5) {
            $recommendations[] = [
                'type'  => 'PH WARNING',
                'level' => 'warning',
                'time'  => 'Harian',
                'title' => 'Pantau Pergerakan Alkalinitas',
                'desc'  => "pH cenderung asam ({$latest->ph}). Berikan kapur ringan jika tren turun terus berlanjut.",
                'icon'  => 'fa-vial'
            ];
        } elseif ($latest->ph > 8.5 && $latest->ph <= 9.0) {
            $recommendations[] = [
                'type'  => 'PH WARNING',
                'level' => 'warning',
                'time'  => 'Harian',
                'title' => 'Aplikasi Probiotik & Molase',
                'desc'  => "pH cenderung basa ({$latest->ph}). Kurangi pakan sedikit dan aplikasikan probiotik/molase.",
                'icon'  => 'fa-vial'
            ];
        } elseif ($latest->ph > 9.0) {
            $recommendations[] = [
                'type'  => 'PH KRITIS',
                'level' => 'danger',
                'time'  => 'Segera',
                'title' => 'Ganti Air Permukaan & Fermentasi Probiotik',
                'desc'  => "pH sangat basa ({$latest->ph}). Lakukan pergantian air permukaan 10–20% dan berikan fermentasi probiotik.",
                'icon'  => 'fa-vial'
            ];
        }

        // Parameter Suhu
        if ($latest->suhu < 24.0) {
            $recommendations[] = [
                'type'  => 'SUHU KRITIS',
                'level' => 'danger',
                'time'  => 'Segera',
                'title' => 'Pangkas Pemberian Pakan',
                'desc'  => "Suhu sangat dingin ({$latest->suhu}°C). Kurangi pakan hingga 50% untuk mencegah sisa pakan membusuk.",
                'icon'  => 'fa-temperature-low'
            ];
        } elseif ($latest->suhu < 26.0) {
            $recommendations[] = [
                'type'  => 'SUHU WARNING',
                'level' => 'warning',
                'time'  => 'Harian',
                'title' => 'Kurangi Porsi Pakan Harian',
                'desc'  => "Suhu cenderung dingin ({$latest->suhu}°C). Kurangi porsi pakan harian 10–20%.",
                'icon'  => 'fa-temperature-half'
            ];
        } elseif ($latest->suhu > 32.0 && $latest->suhu < 34.0) {
            $recommendations[] = [
                'type'  => 'SUHU WARNING',
                'level' => 'warning',
                'time'  => 'Siang Hari',
                'title' => 'Optimalkan Kincir Aerasi',
                'desc'  => "Suhu cenderung panas ({$latest->suhu}°C). Nyalakan kincir air untuk memecah stratifikasi suhu.",
                'icon'  => 'fa-temperature-half'
            ];
        } elseif ($latest->suhu >= 34.0) {
            $recommendations[] = [
                'type'  => 'SUHU KRITIS',
                'level' => 'danger',
                'time'  => 'Segera',
                'title' => 'Tambah Kedalaman & Kincir Penuh',
                'desc'  => "Suhu sangat panas ({$latest->suhu}°C). Segera tambah kedalaman air kolam dan nyalakan kincir penuh.",
                'icon'  => 'fa-temperature-high'
            ];
        }

        // Parameter TDS
        if ($latest->tds > 500 && $latest->tds <= 1000) {
            $recommendations[] = [
                'type'  => 'TDS WARNING',
                'level' => 'warning',
                'time'  => '< 24 Jam',
                'title' => 'Pembuangan Air Dasar (Sipon)',
                'desc'  => "TDS tinggi ({$latest->tds} ppm). Lakukan pembuangan air dasar (sipon) perlahan dan sirkulasi air baru.",
                'icon'  => 'fa-filter'
            ];
        } elseif ($latest->tds > 1000) {
            $recommendations[] = [
                'type'  => 'TDS KRITIS',
                'level' => 'danger',
                'time'  => 'Segera',
                'title' => 'Ganti Air 20–30% Bertahap',
                'desc'  => "TDS sangat tinggi ({$latest->tds} ppm). Segera ganti air 20–30% bertahap dan tingkatkan aerasi.",
                'icon'  => 'fa-filter'
            ];
        }

        // Parameter Kekeruhan
        if ($latest->kekeruhan > 30 && $latest->kekeruhan <= 45) {
            $recommendations[] = [
                'type'  => 'KEKERUHAN WARNING',
                'level' => 'warning',
                'time'  => '1–2 Hari',
                'title' => 'Puasakan Biota & Aplikasi Probiotik',
                'desc'  => "Kekeruhan tinggi ({$latest->kekeruhan} NTU). Puasakan biota sementara waktu dan berikan probiotik.",
                'icon'  => 'fa-water'
            ];
        } elseif ($latest->kekeruhan > 45) {
            $recommendations[] = [
                'type'  => 'KEKERUHAN KRITIS',
                'level' => 'danger',
                'time'  => 'Segera',
                'title' => 'Sipon Dasar & Stop Pakan Sementara',
                'desc'  => "Kekeruhan sangat tinggi ({$latest->kekeruhan} NTU). Segera sipon dasar kolam dan stop pakan 1–2 hari.",
                'icon'  => 'fa-water'
            ];
        }

        usort($recommendations, function ($a, $b) {
            if ($a['level'] === $b['level']) return 0;
            return ($a['level'] === 'danger') ? -1 : 1;
        });

        foreach ($recommendations as $index => &$item) {
            $item['num'] = '#' . ($index + 1);
        }

        return $recommendations;
    }

    private function trapezoid($x, $a, $b, $c, $d)
    {
        if ($x <= $a || $x >= $d) return 0;
        elseif ($x >= $b && $x <= $c) return 1;
        elseif ($x > $a && $x < $b) return ($x - $a) / ($b - $a);
        elseif ($x > $c && $x < $d) return ($d - $x) / ($d - $c);
        return 0;
    }

    private function output_membership($x, $label)
    {
        switch ($label) {
            case 'buruk': return $this->trapezoid($x, 0, 0, 40, 60);
            case 'cukup': return $this->trapezoid($x, 50, 65, 75, 85);
            case 'baik':  return $this->trapezoid($x, 75, 85, 100, 100);
            default: return 0;
        }
    }

    private function getWeatherIcon($code) {
        $code = (string)$code;
        if (in_array($code, ['0'])) return 'fas fa-sun text-warning';
        if (in_array($code, ['1', '2', '3'])) return 'fas fa-cloud-sun text-warning';
        if (in_array($code, ['4'])) return 'fas fa-cloud text-secondary';
        if (in_array($code, ['60', '61', '63'])) return 'fas fa-cloud-showers-heavy text-primary';
        if (in_array($code, ['95'])) return 'fas fa-cloud-bolt text-warning';
        return 'fas fa-cloud-sun text-warning';
    }

    private function convertWindDir($code) {
        $code = strtoupper(trim((string)$code));
        $map = [
            'N' => '↑ U', 'NE' => '↗ TL', 'E' => '→ T', 'SE' => '↘ TG', 
            'S' => '↓ S', 'SW' => '↙ BD', 'W' => '← B', 'NW' => '↖ BL'
        ];
        return $map[$code] ?? '↗ TL';
    }

    private function getCuacaData()
    {
        if (Cache::has('bmkg_cuaca_jabon')) {
            return Cache::get('bmkg_cuaca_jabon');
        }

        try {
            $url = "https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=35.15.05.2001";

            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
                'Accept'     => 'application/json'
            ])->withoutVerifying()->timeout(5)->get($url);

            if ($response->successful()) {
                $data = $response->json();

                $cuacaList = [];
                if (isset($data['data'][0]['cuaca'])) {
                    foreach ($data['data'][0]['cuaca'] as $group) {
                        if (is_array($group)) {
                            foreach ($group as $item) {
                                $cuacaList[] = $item;
                            }
                        } else {
                            $cuacaList[] = $group;
                        }
                    }
                } elseif (isset($data['data'])) {
                    $cuacaList = $data['data'];
                }

                if (!empty($cuacaList)) {
                    $now = Carbon::now('Asia/Jakarta');
                    $currentForecast = null;
                    $hourly = [];

                    foreach ($cuacaList as $item) {
                        $localTimeStr = $item['local_datetime'] ?? $item['datetime'] ?? null;
                        if (!$localTimeStr) continue;

                        $dt = Carbon::parse($localTimeStr, 'Asia/Jakarta');

                        $dataPoint = [
                            'carbon'     => $dt,
                            'temp'       => (string)($item['t'] ?? '30'),
                            'weather'    => $item['weather_desc'] ?? 'Cerah Berawan',
                            'humidity'   => (string)($item['hu'] ?? '75'),
                            'wind_speed' => (string)round((float)($item['ws'] ?? 10)),
                            'wind_dir'   => $this->convertWindDir($item['wd'] ?? 'TL'),
                        ];

                        if ($dt->lte($now)) {
                            $currentForecast = $dataPoint;
                        }

                        if ($dt->gte($now->copy()->subHours(2)) && count($hourly) < 5) {
                            $hourly[] = [
                                'jam'   => $dt->format('H:i'),
                                'suhu'  => $dataPoint['temp'] . '°',
                                'icon'  => $this->getWeatherIconText($dataPoint['weather'], $dt->format('H:i')),
                                'angin' => $dataPoint['wind_dir'],
                            ];
                        }
                    }

                    if (!$currentForecast && !empty($cuacaList)) {
                        $first = $cuacaList[0];
                        $currentForecast = [
                            'temp'       => (string)($first['t'] ?? '31'),
                            'weather'    => $first['weather_desc'] ?? 'Cerah Berawan',
                            'humidity'   => (string)($first['hu'] ?? '78'),
                            'wind_speed' => (string)round((float)($first['ws'] ?? 12)),
                            'wind_dir'   => $this->convertWindDir($first['wd'] ?? 'TL'),
                        ];
                    }

                    if ($currentForecast) {
                        $cuacaReal = [
                            'lokasi'     => 'JABON, SIDOARJO',
                            'hari'       => $now->translatedFormat('l'),
                            'waktu'      => $now->format('H:i') . ' WIB',
                            'suhu'       => $currentForecast['temp'],
                            'kondisi'    => $currentForecast['weather'],
                            'icon'       => $this->getWeatherIconText($currentForecast['weather'], $now->format('H:i')),
                            'kelembaban' => $currentForecast['humidity'],
                            'angin'      => $currentForecast['wind_speed'] . ' km/j',
                            'arah_angin' => $currentForecast['wind_dir'],
                            'hourly'     => $hourly
                        ];

                        Cache::put('bmkg_cuaca_jabon', $cuacaReal, 1800);
                        return $cuacaReal;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("BMKG API Error: " . $e->getMessage());
        }

        return $this->getOpenMeteoCuaca();
    }

    private function getWeatherIconText($desc, $jam = null)
    {
        $desc = strtolower($desc);

        $isNight = false;
        if ($jam) {
            $hour = (int) explode(':', $jam)[0];
            $isNight = ($hour >= 18 || $hour < 6);
        }

        if (str_contains($desc, 'petir')) return 'fas fa-cloud-bolt text-warning';
        if (str_contains($desc, 'hujan')) return 'fas fa-cloud-showers-heavy text-primary';
        if (str_contains($desc, 'kabut') || str_contains($desc, 'kabur')) return 'fas fa-smog text-secondary';
        if (str_contains($desc, 'berawan') && str_contains($desc, 'cerah')) return $isNight ? 'fas fa-cloud-moon text-info' : 'fas fa-cloud-sun text-warning';
        if (str_contains($desc, 'berawan')) return 'fas fa-cloud text-secondary';
        if (str_contains($desc, 'cerah')) return $isNight ? 'fas fa-moon text-warning' : 'fas fa-sun text-warning';

        return $isNight ? 'fas fa-moon text-warning' : 'fas fa-sun text-warning';
    }

    private function getOpenMeteoCuaca()
    {
        try {
            $url = "https://api.open-meteo.com/v1/forecast?latitude=-7.5622&longitude=112.7675&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m,wind_direction_10m&hourly=temperature_2m,weather_code,wind_direction_10m&timezone=Asia%2FJakarta";
            $res = Http::timeout(5)->get($url);

            if ($res->successful()) {
                $data = $res->json();
                $now = Carbon::now('Asia/Jakarta');
                
                $hourly = [];
                if (isset($data['hourly']['time'])) {
                    foreach ($data['hourly']['time'] as $idx => $timeStr) {
                        $dt = Carbon::parse($timeStr, 'Asia/Jakarta');
                        if ($dt->gte($now->copy()->subHour()) && count($hourly) < 5) {
                            $hourly[] = [
                                'jam'   => $dt->format('H:i'),
                                'suhu'  => round($data['hourly']['temperature_2m'][$idx]) . '°',
                                'icon'  => $this->getWeatherIcon($data['hourly']['weather_code'][$idx] ?? 1),
                                'angin' => '↗ TL'
                            ];
                        }
                    }
                }

                $current = $data['current'] ?? [];
                return [
                    'lokasi'     => 'JABON, SIDOARJO',
                    'hari'       => $now->translatedFormat('l'),
                    'waktu'      => $now->format('H:i') . ' WIB',
                    'suhu'       => (string)round($current['temperature_2m'] ?? 31),
                    'kondisi'    => 'Cerah Berawan',
                    'icon'       => 'fas fa-cloud-sun text-warning',
                    'kelembaban' => (string)($current['relative_humidity_2m'] ?? 75),
                    'angin'      => round($current['wind_speed_10m'] ?? 10) . ' km/j',
                    'arah_angin' => '↗ TL',
                    'hourly'     => $hourly
                ];
            }
        } catch (\Exception $e) {
            \Log::error("OpenMeteo Error: " . $e->getMessage());
        }

        return $this->getDefaultCuaca();
    }

    private function getDefaultCuaca() {
        $now = Carbon::now('Asia/Jakarta');
        return [
            'lokasi'     => 'JABON, SIDOARJO',
            'hari'       => $now->translatedFormat('l'),
            'waktu'      => $now->format('H:i') . ' WIB',
            'suhu'       => '31',
            'kondisi'    => 'Cerah Berawan',
            'icon'       => 'fas fa-cloud-sun text-warning',
            'kelembaban' => '78',
            'angin'      => '12 km/j',
            'arah_angin' => '↗ TL',
            'hourly'     => [
                ['jam' => '07:00', 'suhu' => '29°', 'icon' => 'fas fa-sun text-warning', 'angin' => '↗ TL'],
                ['jam' => '13:00', 'suhu' => '32°', 'icon' => 'fas fa-cloud-sun text-warning', 'angin' => '→ T'],
                ['jam' => '19:00', 'suhu' => '28°', 'icon' => 'fas fa-cloud-moon text-info', 'angin' => '↘ TG'],
                ['jam' => '01:00', 'suhu' => '26°', 'icon' => 'fas fa-moon text-warning', 'angin' => '↙ BD'],
            ]
        ];
    }
}