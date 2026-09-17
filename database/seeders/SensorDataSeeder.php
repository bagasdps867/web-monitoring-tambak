<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sensor;
use Carbon\Carbon;

class SensorDataSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan tabel sebelum seeding
        Sensor::truncate();

        // Bulatkan waktu saat ini ke kelipatan 30 menit terdekat
        $now = Carbon::now();
        $minute = $now->minute < 30 ? 0 : 30;
        $now->minute($minute)->second(0);

        // Total data: 30 Hari x 24 Jam x 2 (setiap 30 menit) = 1.440 Data
        $totalData = 30 * 24 * 2;
        $insertData = [];

        for ($i = $totalData - 1; $i >= 0; $i--) {
            $timestamp = $now->copy()->subMinutes(30 * $i);
            $hour = $timestamp->hour;

            // Fluktuasi suhu dasar realistis (siang hari lebih panas dibanding malam hari)
            $isDaytime = ($hour >= 10 && $hour <= 15);

            // Distribusi acak untuk mencakup SEMUA kondisi kualitas air
            $chance = rand(1, 100);

            if ($chance <= 70) {
                // 1. KONDISI BAIK / NORMAL (70% data)
                $ph = rand(70, 82) / 10;                     // 7.0 - 8.2 (Ideal)
                $suhu = $isDaytime ? rand(290, 310) / 10     // 29.0 - 31.0°C
                                   : rand(265, 285) / 10;    // 26.5 - 28.5°C
                $tds = rand(200, 480);                       // 200 - 480 ppm
                $kekeruhan = rand(10, 150) / 10;             // 1.0 - 15.0 NTU (Jernih)

            } elseif ($chance <= 90) {
                // 2. KONDISI SEDANG / WARNING (20% data)
                $ph = rand(0, 1) ? (rand(65, 69) / 10) : (rand(83, 87) / 10); // 6.5-6.9 atau 8.3-8.7
                $suhu = $isDaytime ? rand(315, 330) / 10    // 31.5 - 33.0°C (Agak Panas)
                                   : rand(245, 260) / 10;   // 24.5 - 26.0°C (Agak Dingin)
                $tds = rand(550, 1100);                      // 550 - 1100 ppm
                $kekeruhan = rand(200, 380) / 10;            // 20.0 - 38.0 NTU (Agak Keruh)

            } else {
                // 3. KONDISI BURUK / CRITICAL (10% data)
                $ph = rand(0, 1) ? (rand(48, 62) / 10) : (rand(90, 105) / 10); // 4.8-6.2 atau 9.0-10.5
                $suhu = $isDaytime ? rand(335, 360) / 10    // 33.5 - 36.0°C (Panas Ekstrem)
                                   : rand(220, 240) / 10;   // 22.0 - 24.0°C (Dingin Ekstrem)
                $tds = rand(1200, 2500);                     // 1200 - 2500 ppm (Tinggi)
                $kekeruhan = rand(400, 750) / 10;            // 40.0 - 75.0 NTU (Sangat Keruh)
            }

            $phVal = round($ph, 1);
            $suhuVal = round($suhu, 1);
            $tdsVal = $tds;
            $keruhVal = round($kekeruhan, 1);

            // Hitung nilai Fuzzy Quality untuk disimpan ke database
            $kualitas = $this->calculateFuzzyQuality($phVal, $suhuVal, $tdsVal, $keruhVal);

            $insertData[] = [
                'ph'         => $phVal,
                'suhu'       => $suhuVal,
                'tds'        => $tdsVal,
                'kekeruhan'  => $keruhVal,
                'kualitas'   => $kualitas, // Menyimpan hasil kalkulasi ke database
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // Bulk insert per 500 baris
        foreach (array_chunk($insertData, 500) as $chunk) {
            Sensor::insert($chunk);
        }
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
            case 'buruk':
                return $this->trapezoid($x, 0, 0, 40, 60);
            case 'cukup': // Label sudah sinkron dengan Controller
                return $this->trapezoid($x, 50, 65, 75, 85);
            case 'baik':
                return $this->trapezoid($x, 75, 85, 100, 100);
            default:
                return 0;
        }
    }

    private function calculateFuzzyQuality($ph, $suhu, $tds, $ntu)
    {
        // 1. Fuzzifikasi pH 
        $ph_asam   = $this->trapezoid($ph, 0, 0, 6.0, 6.8);
        $ph_netral = $this->trapezoid($ph, 6.5, 7.0, 8.2, 8.7);
        $ph_basa   = $this->trapezoid($ph, 8.4, 8.8, 14, 14);

        // 2. Fuzzifikasi Suhu
        $suhu_dingin  = $this->trapezoid($suhu, 0, 0, 24, 26);
        $suhu_optimal = $this->trapezoid($suhu, 25, 27, 31, 33);
        $suhu_panas   = $this->trapezoid($suhu, 32, 34, 45, 45);

        // 3. Fuzzifikasi TDS
        $tds_normal = $this->trapezoid($tds, 0, 0, 600, 1000);
        $tds_sedang = $this->trapezoid($tds, 800, 1000, 1500, 2000);
        $tds_tinggi = $this->trapezoid($tds, 1800, 2000, 5000, 5000);

        // 4. Fuzzifikasi Kekeruhan
        $keruh_jernih  = $this->trapezoid($ntu, 0, 0, 5, 10);
        $keruh_optimal = $this->trapezoid($ntu, 5, 10, 30, 40);
        $keruh_keruh   = $this->trapezoid($ntu, 35, 45, 100, 100);

        // Basis Aturan Fuzzy (Ubah 'sedang' menjadi 'cukup')
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
            $mu_ph = ${"ph_" . $ph_key};
            $mu_suhu = ${"suhu_" . $suhu_key};
            $mu_keruh = ${"keruh_" . $keruh_key};

            $mu_normal = min($mu_ph, $mu_suhu, $mu_keruh, $tds_normal);
            $rule_outputs[$output_label] = max($rule_outputs[$output_label], $mu_normal);

            $mu_sedang = min($mu_ph, $mu_suhu, $mu_keruh, $tds_sedang);
            $sedang_label = ($output_label === 'baik') ? 'cukup' : 'buruk';
            $rule_outputs[$sedang_label] = max($rule_outputs[$sedang_label], $mu_sedang);

            $mu_tinggi = min($mu_ph, $mu_suhu, $mu_keruh, $tds_tinggi);
            $rule_outputs['buruk'] = max($rule_outputs['buruk'], $mu_tinggi);
        }

        // Defuzzifikasi Centroid
        $numerator = 0;
        $denominator = 0;

        for ($x = 0; $x <= 100; $x += 1) { 
            $mu_buruk  = min($rule_outputs['buruk'], $this->output_membership($x, 'buruk'));
            $mu_cukup  = min($rule_outputs['cukup'], $this->output_membership($x, 'cukup'));
            $mu_baik   = min($rule_outputs['baik'], $this->output_membership($x, 'baik'));

            $mu_total = max($mu_buruk, $mu_cukup, $mu_baik);

            $numerator += $x * $mu_total;
            $denominator += $mu_total;
        }

        if ($denominator == 0) return 0;

        return round($numerator / $denominator, 2);
    }
}