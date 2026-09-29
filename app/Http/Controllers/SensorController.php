<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Diperlukan untuk query database langsung

class SensorController extends Controller
{
    /**
     * Display the history view.
     */
    public function history()
    {
        return view('history');
    }

    /**
     * Get all sensor history data.
     */
    public function getHistoryData()
    {
        $sensorData = Sensor::orderByDesc('created_at')->get();

        $formattedData = $sensorData->map(fn($data) => $this->formatSensorData($data));

        return response()->json($formattedData);
    }

    /**
     * Filter sensor history data based on request.
     */
    public function filterHistory(Request $request)
    {
        $query = Sensor::query();

        $this->applyDateFilter($query, $request);
        $this->applyPhFilter($query, $request);
        $this->applySuhuFilter($query, $request);
        $this->applyKekeruhanFilter($query, $request);
        $this->applyTdsFilter($query, $request);

        $sensorData = $query->orderByDesc('created_at')->get();

        $formattedData = $sensorData->map(fn($data) => $this->formatSensorData($data));

        return response()->json($formattedData);
    }

    /**
     * Format a single sensor data record.
     */
    private function formatSensorData($data)
    {
        return [
            'waktu'     => Carbon::parse($data->created_at)->format('Y-m-d H:i:s'), 
            'ph'        => $data->ph,
            'suhu'      => $data->suhu,
            'tds'       => $data->tds ?? 0,
            'kekeruhan' => $data->kekeruhan,
            'kualitas'  => $data->kualitas ?? 'Baik',
            'status'    => $this->determineWaterQualityStatus($data->kualitas)
        ];
    }

    /**
     * Mengambil data ambang batas untuk dikirim ke aplikasi Flutter/ESP32.
     */
    public function getAmbangBatas()
    {
        $threshold = DB::table('thresholds')->first();
        
        if (!$threshold) {
            return response()->json([
                'ph_min'        => 6.5, 
                'ph_max'        => 8.5,
                'suhu_min'      => 26.0, 
                'suhu_max'      => 32.0,
                'tds_min'       => 0.0, 
                'tds_max'       => 3000.0,
                'kekeruhan_min' => 0.0, 
                'kekeruhan_max' => 50.0,
            ]);
        }

        return response()->json($threshold);
    }

    /**
     * Menyimpan data ambang batas yang dikirim dari HP/slider.
     */
    public function setAmbangBatas(Request $request)
    {
        DB::table('thresholds')->updateOrInsert(
            ['id' => 1],
            [
                'ph_min'        => $request->ph_min,
                'ph_max'        => $request->ph_max,
                'suhu_min'      => $request->suhu_min,
                'suhu_max'      => $request->suhu_max,
                'tds_min'       => $request->tds_min,
                'tds_max'       => $request->tds_max,
                'kekeruhan_min' => $request->kekeruhan_min,
                'kekeruhan_max' => $request->kekeruhan_max,
                'updated_at'    => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Ambang batas berhasil diperbarui di web!'
        ]);
    }

    /**
     * Apply date range filters to the query.
     */
    private function applyDateFilter($query, Request $request)
    {
        try {
            if ($request->filled(['start_date', 'end_date'])) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay(),
                ]);
            } elseif ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', Carbon::parse($request->start_date));
            } elseif ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', Carbon::parse($request->end_date));
            }
        } catch (\Exception $e) {
            // Log atau abaikan agar tidak fatal error
        }
    }

    /**
     * Apply pH filters to the query.
     */
    private function applyPhFilter($query, Request $request)
    {
        if ($request->filled('ph') && in_array($request->ph, ['acid', 'neutral', 'base'])) {
            match ($request->ph) {
                'acid'    => $query->where('ph', '<', 7.0),
                'neutral' => $query->whereBetween('ph', [7.0, 8.5]),
                'base'    => $query->where('ph', '>', 8.5),
            };
        }
    }

    /**
     * Apply suhu (temperature) filters to the query.
     */
    private function applySuhuFilter($query, Request $request)
    {
        if ($request->filled('suhu') && $request->suhu !== 'all') {
            match ($request->suhu) {
                'cold'         => $query->where('suhu', '<', 26),
                'optimal_suhu' => $query->whereBetween('suhu', [26, 32]),
                'hot'          => $query->where('suhu', '>', 32),
                default        => null
            };
        }
    }

    /**
     * Apply kekeruhan (turbidity) filters to the query.
     */
    private function applyKekeruhanFilter($query, Request $request)
    {
        if ($request->filled('kekeruhan') && $request->kekeruhan !== 'all') {
            match ($request->kekeruhan) {
                'clear'             => $query->where('kekeruhan', '<', 5),
                'optimal_kekeruhan' => $query->whereBetween('kekeruhan', [5, 40]),
                'turbid'            => $query->where('kekeruhan', '>', 40),
                default             => null
            };
        }
    }

    /**
     * Apply TDS (Total Dissolved Solids) filters to the query.
     */
    private function applyTdsFilter($query, Request $request)
    {
        if ($request->filled('tds') && $request->tds !== 'all') {
            match ($request->tds) {
                'normal'  => $query->where('tds', '<=', 1000),
                'medium'  => $query->whereBetween('tds', [1000, 2000]),
                'high'    => $query->where('tds', '>', 2000),
                default   => null
            };
        }
    }

    /**
     * Determine label/status string based on Fuzzy score.
     */
    private function determineWaterQualityStatus($value)
    {
        if ($value === null) {
            return 'Belum Dihitung';
        }

        if ($value <= 40) {
            return 'Buruk';
        } elseif ($value >= 60) {
            return 'Baik';
        } else {
            return 'Sedang';
        }
    }

    /**
     * Fungsi untuk memproses data dari Mobile Flutter dan mengembalikan teks AI / SOP
     */
    public function getAiRekomendasi(Request $request)
    {
        try {
            // 1. Ambil data yang dikirim dari HP
            $ph = $request->ph ?? 7.0;
            $suhu = $request->suhu ?? 28.0;
            $tds = $request->tds ?? 250;
            $kekeruhan = $request->kekeruhan ?? 15;

            // 2. Logika Sederhana SOP Tambak
            $status = "Normal";
            $langkah = [];
            $kritis = 0;

            if ($ph < 6.5 || $ph > 8.5) {
                $status = "Warning";
                $langkah[] = "1. Lakukan sirkulasi atau pergantian air kolam sebesar 20-30%.";
                $kritis++;
            }
            if ($suhu < 26 || $suhu > 32) {
                $status = "Warning";
                $langkah[] = "2. Nyalakan kincir air/aerator untuk menstabilkan suhu dan suplai oksigen.";
                $kritis++;
            }
            if ($tds > 1000) {
                $status = "Warning";
                $langkah[] = "3. Endapkan air atau periksa sistem filter bio untuk mengurangi padatan TDS.";
                $kritis++;
            }
            if ($kekeruhan > 50) {
                $status = "Warning";
                $langkah[] = "4. Hentikan pemberian pakan sementara waktu dan berikan probiotik air.";
                $kritis++;
            }

            if ($kritis >= 2) {
                $status = "Critical";
            }

            // 3. Format Balasan Teks agar rapi saat dibaca Flutter
            if ($status === "Normal") {
                $rekomendasi = "EVALUASI: Kualitas air dalam keadaan sangat optimal.\n1. Lanjutkan budidaya secara normal.\n2. Pemberian pakan dilakukan sesuai jadwal.\n3. Lanjutkan monitoring visual.";
            } else {
                $rekomendasi = "EVALUASI: Parameter air terdeteksi di luar batas normal ($status). Segera mitigasi:\n" . implode("\n", $langkah);
            }

            // 4. Kembalikan ke HP dalam bentuk JSON
            return response()->json([
                'status' => 'success',
                'rekomendasi' => $rekomendasi
            ], 200);

        } catch (\Exception $e) {
            // Jika ada error di Laravel, kirim pesan error ke HP
            return response()->json([
                'status' => 'error',
                'rekomendasi' => 'Peringatan: Gagal memproses AI di server. ' . $e->getMessage()
            ], 500);
        }
    }
}