<?php

namespace App\Helpers;

class FuzzyHelper
{
    // Fungsi membership PH
    private static function phMembership($ph)
    {
        return [
            'rendah' => $ph <= 7 ? 1 : ($ph < 7.5 ? (7.5 - $ph) / 0.5 : 0),
            'normal' => ($ph >= 7.5 && $ph <= 8.5) ? 1 : (($ph > 7 && $ph < 7.5) ? ($ph - 7) / 0.5 : (($ph > 8.5 && $ph < 9) ? (9 - $ph) / 0.5 : 0)),
            'tinggi' => $ph >= 9 ? 1 : ($ph > 8.5 ? ($ph - 8.5) / 0.5 : 0),
        ];
    }

    // Fungsi membership suhu
    private static function suhuMembership($suhu)
    {
        return [
            'rendah' => $suhu <= 26 ? 1 : ($suhu < 28 ? (28 - $suhu) / 2 : 0),
            'normal' => ($suhu >= 28 && $suhu <= 32) ? 1 : (($suhu > 26 && $suhu < 28) ? ($suhu - 26) / 2 : (($suhu > 32 && $suhu < 34) ? (34 - $suhu) / 2 : 0)),
            'tinggi' => $suhu >= 34 ? 1 : ($suhu > 32 ? ($suhu - 32) / 2 : 0),
        ];
    }

    // Fungsi membership kekeruhan
    private static function kekeruhanMembership($kekeruhan)
    {
        return [
            'jernih' => $kekeruhan <= 10 ? 1 : ($kekeruhan < 15 ? (15 - $kekeruhan) / 5 : 0),
            'normal' => ($kekeruhan >= 15 && $kekeruhan <= 30) ? 1 : (($kekeruhan > 10 && $kekeruhan < 15) ? ($kekeruhan - 10) / 5 : (($kekeruhan > 30 && $kekeruhan < 35) ? (35 - $kekeruhan) / 5 : 0)),
            'keruh' => $kekeruhan >= 35 ? 1 : ($kekeruhan > 30 ? ($kekeruhan - 30) / 5 : 0),
        ];
    }

    // Fungsi menghitung output fuzzy
    public static function hitungKualitasAir($ph, $suhu, $kekeruhan)
    {
        $phSet = self::phMembership($ph);
        $suhuSet = self::suhuMembership($suhu);
        $kekeruhanSet = self::kekeruhanMembership($kekeruhan);

        // Aturan fuzzy (semua kombinasi, hanya 3 aturan utama digunakan di contoh ini)
        $rules = [
            ['ph' => 'normal', 'suhu' => 'normal', 'kekeruhan' => 'normal', 'output' => 100], // baik
            ['ph' => 'normal', 'suhu' => 'normal', 'kekeruhan' => 'keruh', 'output' => 70],
            ['ph' => 'rendah', 'suhu' => 'tinggi', 'kekeruhan' => 'keruh', 'output' => 40],
            ['ph' => 'tinggi', 'suhu' => 'tinggi', 'kekeruhan' => 'keruh', 'output' => 30],
            ['ph' => 'rendah', 'suhu' => 'rendah', 'kekeruhan' => 'jernih', 'output' => 50],
            ['ph' => 'normal', 'suhu' => 'rendah', 'kekeruhan' => 'normal', 'output' => 75],
            ['ph' => 'tinggi', 'suhu' => 'normal', 'kekeruhan' => 'normal', 'output' => 85],
        ];

        $numerator = 0;
        $denominator = 0;

        foreach ($rules as $rule) {
            $alpha = min(
                $phSet[$rule['ph']],
                $suhuSet[$rule['suhu']],
                $kekeruhanSet[$rule['kekeruhan']]
            );

            $numerator += $alpha * $rule['output'];
            $denominator += $alpha;
        }

        return $denominator === 0 ? 0 : round($numerator / $denominator, 2);
    }
}
