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
        $sensorData = Sensor::orderBy('id', 'desc')->take(1440)->get()->reverse()->values();
        $cuaca = $this->getCuacaData();

        return view('dashboard', compact('sensorData', 'cuaca'));
    }

    /**
     * Endpoint Polling Real-time (per 5 detik)
     */
    public function getLatestSensor()
    {
        $latest = Sensor::orderBy('id', 'desc')->first();

        if (!$latest) {
            return response()->json([
                'status' => 'empty',
                'data'   => null
            ]);
        }

        // Hitung ulang dan simpan otomatis skor fuzzy terbaru ke database
        $this->ensureSensorQuality($latest);

        // Eksekusi fungsi Fuzzy & SOP secara real-time
        $rekomendasiSaatIni = $this->getLocalEmergencyAdvice($latest);

        return response()->json([
            'status'               => 'success',
            'data'                 => $latest,
            'rekomendasi_saat_ini' => $rekomendasiSaatIni 
        ]);
    }

    /**
     * Endpoint Data Sensor Lengkap + Cuaca (untuk Chart/Tabel)
     */
    public function getSensorData()
    {
        $sensorData = Sensor::orderBy('id', 'desc')->take(1440)->get();
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
        $latestTwo = Sensor::orderBy('id', 'desc')->take(2)->get();

        if ($latestTwo->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data sensor belum tersedia untuk dianalisis.'
            ], 200);
        }

        $latest   = $latestTwo->first();
        $previous = $latestTwo->count() > 1 ? $latestTwo->last() : $latest;

        $this->ensureSensorQuality($latest);
        $fuzzyScore = $latest->kualitas;

        // Status Fuzzy Saat Ini (Sesuai Klasifikasi SOP PDF Bab 5)
        if ($fuzzyScore >= 75) {
            $statusFuzzySaatIni = 'Baik';
        } elseif ($fuzzyScore >= 45) {
            $statusFuzzySaatIni = 'Sedang';
        } else {
            $statusFuzzySaatIni = 'Buruk';
        }

        $ph        = (float) $latest->ph;
        $suhu      = (float) $latest->suhu;
        $tds       = (float) ($latest->tds ?? 0);
        $kekeruhan = (float) $latest->kekeruhan;

        $phDelta        = $ph - (float) $previous->ph;
        $suhuDelta      = $suhu - (float) $previous->suhu;
        $tdsDelta       = $tds - (float) ($previous->tds ?? 0);
        $kekeruhanDelta = $kekeruhan - (float) $previous->kekeruhan;

        $statusPrediksiMl = 'Offline';
        $mlPredData       = null;
        $isOffline        = false;

        // Machine Learning Prediksi +1 Jam
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
                $hybridAi = $resData['hybrid_ai'] ?? [];
                $statusPrediksiMl = $hybridAi['status_prediksi'] ?? 'Offline';
                $mlPredData       = $resData['ml_prediction_t_plus_1'] ?? null;
            } else {
                $isOffline = true;
            }
        } catch (\Exception $e) {
            \Log::error("Python ML Service Offline: " . $e->getMessage());
            $isOffline = true;
        }

        $teksStatusML = ($isOffline || $statusPrediksiMl === 'Offline') ? 'Tidak Diketahui (Offline)' : $statusPrediksiMl;
        $keteranganStatus = "Kualitas air saat ini berada di level <b>{$statusFuzzySaatIni}</b> dan 1 jam ke depan diprediksi menjadi <b>{$teksStatusML}</b>.";

        $rekomendasiSaatIni = $this->getLocalEmergencyAdvice($latest);
        $pesanAntisipasi = $this->generateAnticipationMessage($statusFuzzySaatIni, $teksStatusML);
        $tindakanPrediksi = [];

        if (!$isOffline && $mlPredData) {
            $tindakanPrediksi = $this->getPredictiveAdvice($mlPredData);
        }

        if (empty($tindakanPrediksi)) {
            if (in_array($teksStatusML, ['Sedang', 'Buruk'])) {
                $tindakanPrediksi[] = [
                    'num'   => '#1',
                    'badge' => 'PREVENTIF',
                    'type'  => 'PEMANTAUAN UMUM',
                    'title' => 'Tingkatkan Frekuensi Pemantauan',
                    'desc'  => 'Sistem mendeteksi potensi penurunan kualitas air secara umum. Siapkan langkah mitigasi standar.',
                    'icon'  => 'fas fa-eye',
                    'level' => 'warning'
                ];
            } else {
                $tindakanPrediksi[] = [
                    'num'   => '#1',
                    'badge' => 'STABIL',
                    'type'  => 'PREDIKSI AMAN',
                    'title' => 'Kondisi Air Stabil',
                    'desc'  => 'Parameter air diprediksi tetap stabil dan aman dalam 1 jam ke depan. Tidak perlu tindakan khusus.',
                    'icon'  => 'fas fa-shield-alt',
                    'level' => 'success'
                ];
            }
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
                'status_saat_ini'   => $statusFuzzySaatIni,
                'status_prediksi'   => $statusPrediksiMl,
                'keterangan_status' => $keteranganStatus,
                'is_offline'        => $isOffline,
            ],
            'rekomendasi_saat_ini' => $rekomendasiSaatIni,
            'prediksi_ke_depan' => [
                'pesan_antisipasi' => $pesanAntisipasi,
                'tindakan'         => $tindakanPrediksi
            ]
        ]);
    }

    private function generateAnticipationMessage($current, $predicted)
    {
        if ($predicted === 'Tidak Diketahui (Offline)' || $predicted === 'Offline') {
            return "Sistem Prediksi AI sedang offline. Lakukan pemantauan manual berdasarkan SOP saat ini.";
        }
        if ($current === 'Baik' && $predicted === 'Sedang') {
            return "Kondisi diperkirakan menurun menjadi Waspada. Siapkan tindakan korektif ringan dan perketat pemantauan.";
        }
        if ($predicted === 'Buruk') {
            return "PERINGATAN KRITIS! Kondisi diprediksi memburuk signifikan. Segera siapkan mitigasi darurat seperti Sipon, penambahan kincir, atau pergantian air.";
        }
        if (in_array($current, ['Sedang', 'Buruk']) && $predicted === 'Baik') {
            return "Kondisi diprediksi akan membaik. Tetap lanjutkan tindakan korektif yang sedang berjalan hingga parameter benar-benar stabil.";
        }
        
        return "Kondisi diperkirakan relatif stabil. Lanjutkan pemantauan rutin dan SOP budidaya normal.";
    }

    private function getPredictiveAdvice($mlData)
    {
        $advice = [];
        $predPh = (float) ($mlData['ph'] ?? 7.0);
        $predSuhu = (float) ($mlData['suhu'] ?? 28.0);
        $predKekeruhan = (float) ($mlData['kekeruhan'] ?? 0.0);
        $predTds = (float) ($mlData['tds'] ?? 0.0);

        if ($predPh < 6.8 || $predPh > 8.5) {
            $advice[] = [
                'type'  => 'PH AIR', 'badge' => 'PREVENTIF', 'level' => 'warning',
                'title' => 'Antisipasi Perubahan pH',
                'desc'  => 'pH diprediksi bergerak di luar batas optimal (6.8 - 8.5). Siapkan larutan kapur atau probiotik sebagai tindakan korektif cepat.', 'icon'  => 'fas fa-vial'
            ];
        }

        if ($predSuhu < 26.0 || $predSuhu > 34.0) {
            $advice[] = [
                'type'  => 'SUHU', 'badge' => 'PREVENTIF', 'level' => 'warning',
                'title' => 'Persiapan Fluktuasi Suhu',
                'desc'  => 'Suhu diprediksi di luar batas optimal (26 - 34°C). Bersiap untuk menyesuaikan manajemen pakan pada jam berikutnya.', 'icon'  => 'fas fa-temperature-half'
            ];
        }

        if ($predKekeruhan > 43.0) {
            $advice[] = [
                'type'  => 'KEKERUHAN', 'badge' => 'PREVENTIF', 'level' => 'warning',
                'title' => 'Pantau Kekeruhan Tambak',
                'desc'  => 'Tren kekeruhan diprediksi melebihi batas optimal (> 43 NTU). Pertimbangkan sirkulasi tambahan atau persiapan sipon.', 'icon'  => 'fas fa-water'
            ];
        }

        if ($predTds > 500.0) {
            $advice[] = [
                'type'  => 'TDS AIR', 'badge' => 'PREVENTIF', 'level' => 'warning',
                'title' => 'Antisipasi Kenaikan TDS',
                'desc'  => 'TDS diprediksi melebihi batas optimal (> 500 ppm). Siapkan sirkulasi air atau penambahan air jernih.', 'icon'  => 'fas fa-filter'
            ];
        }

        foreach ($advice as $index => &$item) {
            $item['num'] = '#' . ($index + 1);
        }

        return $advice;
    }

    public function storeFromDevice(Request $request)
    {
        Sensor::where('created_at', '<', now()->subMonths(2))->delete();

        $validated = $request->validate([
            'ph'        => 'required|numeric',
            'suhu'      => 'required|numeric',
            'tds'       => 'required|numeric',
            'kekeruhan' => 'required|numeric',
        ]);

        $fuzzyScore = $this->calculateFuzzyQuality(
            $validated['ph'], $validated['suhu'], $validated['tds'], $validated['kekeruhan']
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
     * KALKULASI FUZZY MAMDANI PRESI SAMA DENGAN DOKUMEN SOP PDF BAB 4.1
     */
    public function calculateFuzzyQuality($ph, $suhu, $tds, $ntu)
    {
        // 1. Membership pH (Murni Netral pada 6.8 - 8.5, Asam 0% di atas pH 6.8)
        $ph_membership = [
            'asam'   => $this->trapezoid($ph, 0, 0, 6.0, 6.8),
            'netral' => $this->trapezoid($ph, 6.5, 7.0, 8.5, 9.0),
            'basa'   => $this->trapezoid($ph, 8.5, 9.0, 14.0, 14.0),
        ];

        // 2. Membership Suhu (SOP PDF 4.1: Dingin 0-28°C, Optimal 26-34°C, Panas 32-40°C)
        $suhu_membership = [
            'dingin'  => $this->trapezoid($suhu, 0, 0, 24.0, 26.0),
            'optimal' => $this->trapezoid($suhu, 25.0, 27.0, 33.0, 35.0),
            'panas'   => $this->trapezoid($suhu, 34.0, 36.0, 40.0, 40.0), 
        ];

        // 3. Membership TDS (Normal <= 500 ppm)
        $tds_normal = $this->trapezoid($tds, 0, 0, 450, 500);
        $tds_sedang = $this->trapezoid($tds, 450, 500, 900, 1000);
        $tds_tinggi = $this->trapezoid($tds, 900, 1000, 9999, 9999); 

        // 4. Membership Kekeruhan (SOP PDF 4.1: Optimal 3 - 43 NTU)
        $keruh_membership = [
            'jernih'  => $this->trapezoid($ntu, 0, 0, 3.0, 5.0),
            'optimal' => $this->trapezoid($ntu, 3.0, 5.0, 40.0, 43.0), 
            'keruh'   => $this->trapezoid($ntu, 40.0, 43.0, 9999, 9999), 
        ];

        // Rule Base Fuzzy Mamdani
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
            $sedang_label = ($output_label === 'buruk') ? 'buruk' : 'cukup';
            $rule_outputs[$sedang_label] = max($rule_outputs[$sedang_label], $mu_sedang);

            $mu_tinggi = min($mu_ph, $mu_suhu, $mu_keruh, $tds_tinggi);
            $rule_outputs['buruk'] = max($rule_outputs['buruk'], $mu_tinggi);
        }

        // Defuzzifikasi Centroid
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

    private function ensureSensorQuality(Sensor $sensor)
    {
        $sensor->kualitas = round($this->calculateFuzzyQuality(
            $sensor->ph ?? 7.0,
            $sensor->suhu ?? 28.0,
            $sensor->tds ?? 0.0,
            $sensor->kekeruhan ?? 0.0
        ), 2);
        $sensor->save();
    }

    private function getLocalEmergencyAdvice($latest)
    {
        $recommendations = [];
        $hasCritical = false;
        $hasWarning = false;

        $fuzzyScore = (float) ($latest->kualitas ?? 100);
        if ($fuzzyScore < 45) {
            $hasCritical = true;
        } elseif ($fuzzyScore < 75) {
            $hasWarning = true;
        }

        if ($hasCritical) {
            $recommendations[] = [
                'type'  => 'PROSEDUR SOP', 'badge' => 'SOP BURUK', 'level' => 'danger', 'time' => 'Segera',
                'title' => 'Verifikasi & Pemeriksaan Tambak',
                'desc'  => '1. Lakukan verifikasi pembacaan sensor.<br>2. Lakukan pemeriksaan langsung terhadap kondisi tambak.', 
                'icon'  => 'fas fa-search-location'
            ];
            $recommendations[] = [
                'type'  => 'PROSEDUR SOP', 'badge' => 'SOP BURUK', 'level' => 'danger', 'time' => 'Segera',
                'title' => 'Evaluasi Organisme & Sirkulasi',
                'desc'  => '3. Amati perilaku udang dan ikan.<br>4. Evaluasi pemberian pakan.<br>5. Evaluasi kondisi sirkulasi air.', 
                'icon'  => 'fas fa-water'
            ];
            $recommendations[] = [
                'type'  => 'PROSEDUR SOP', 'badge' => 'SOP BURUK', 'level' => 'danger', 'time' => 'Pasca Tindakan',
                'title' => 'Tindakan Korektif & Pencatatan',
                'desc'  => '6. Lakukan tindakan korektif sesuai parameter yang bermasalah.<br>7. Lakukan monitoring ulang setelah tindakan.<br>8. Catat kejadian dan tindakan yang dilakukan.', 
                'icon'  => 'fas fa-clipboard-check'
            ];
        } elseif ($hasWarning) {
            $recommendations[] = [
                'type'  => 'PROSEDUR SOP', 'badge' => 'SOP SEDANG', 'level' => 'warning', 'time' => 'Rutin',
                'title' => 'Pemeriksaan Tambak & Sensor',
                'desc'  => '1. Lakukan pemeriksaan kondisi tambak.<br>2. Periksa kembali pembacaan sensor.', 
                'icon'  => 'fas fa-microchip'
            ];
            $recommendations[] = [
                'type'  => 'PROSEDUR SOP', 'badge' => 'SOP SEDANG', 'level' => 'warning', 'time' => 'Rutin',
                'title' => 'Amati Organisme & Pakan',
                'desc'  => '3. Amati aktivitas udang dan ikan.<br>4. Periksa respons terhadap pakan.', 
                'icon'  => 'fas fa-fish'
            ];
            $recommendations[] = [
                'type'  => 'PROSEDUR SOP', 'badge' => 'SOP SEDANG', 'level' => 'warning', 'time' => 'Berkala',
                'title' => 'Evaluasi Air, Tindakan & Catat',
                'desc'  => '5. Tingkatkan frekuensi pengamatan.<br>6. Evaluasi kondisi air.<br>7. Lakukan tindakan korektif apabila kondisi terus memburuk.<br>8. Catat kejadian pada log budidaya.', 
                'icon'  => 'fas fa-clipboard-list'
            ];
        } else {
            $recommendations[] = [
                'type'  => 'PROSEDUR SOP', 'badge' => 'SOP BAIK', 'level' => 'success', 'time' => 'Sesuai Jadwal',
                'title' => 'Operasional & Pakan Normal',
                'desc'  => '1. Budidaya dilanjutkan secara normal.<br>2. Pemberian pakan dilakukan sesuai jadwal.', 
                'icon'  => 'fas fa-check-circle'
            ];
            $recommendations[] = [
                'type'  => 'PROSEDUR SOP', 'badge' => 'SOP BAIK', 'level' => 'success', 'time' => 'Rutin',
                'title' => 'Monitoring IoT & Visual',
                'desc'  => '3. Monitoring IoT tetap berjalan.<br>4. Kondisi organisme diamati secara visual.', 
                'icon'  => 'fas fa-eye'
            ];
            $recommendations[] = [
                'type'  => 'PROSEDUR SOP', 'badge' => 'SOP BAIK', 'level' => 'success', 'time' => 'Otomatis',
                'title' => 'Pencatatan & Sistem',
                'desc'  => '5. Data kualitas air dicatat secara otomatis.<br>6. Tidak diperlukan tindakan korektif khusus.', 
                'icon'  => 'fas fa-database'
            ];
        }

        foreach ($recommendations as $index => &$item) {
            $item['num'] = '#' . ($index + 1);
        }

        return $recommendations;
    }

    private function trapezoid($x, $a, $b, $c, $d)
    {
        if ($x < $a || $x > $d) return 0;
        elseif ($x >= $b && $x <= $c) return 1;
        elseif ($x > $a && $x < $b) return ($x - $a) / ($b - $a);
        elseif ($x > $c && $x < $d) return ($d - $x) / ($d - $c);
        return 0;
    }

    private function output_membership($x, $label)
    {
        switch ($label) {
            case 'buruk': return $this->trapezoid($x, 0, 0, 30, 45);
            case 'cukup': return $this->trapezoid($x, 40, 50, 65, 75);
            case 'baik':  return $this->trapezoid($x, 70, 80, 100, 100);
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