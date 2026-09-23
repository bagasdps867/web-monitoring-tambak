<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SensorDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan tabel sensors sebelum diisi data baru
        DB::table('sensors')->truncate();

        // 2. Skenario Tren: pH turun tajam dari 8.4 ke 7.4 dalam 1 jam terakhir
        // - pH 7.4 saat ini -> Dibaca "Normal" oleh Logic Fuzzy
        // - Tren turun tajam -> Diprediksi "Warning" oleh Model ML untuk 1 jam ke depan

$phValues = [8.5, 8.2, 7.8, 7.5, 7.1];
        $totalData = count($phValues);

        foreach ($phValues as $index => $ph) {
            // Hitung mundur waktu per 15 menit dari sekarang
            $minutesAgo = ($totalData - 1 - $index) * 15;
            $timestamp = Carbon::now()->subMinutes($minutesAgo);

            DB::table('sensors')->insert([
                'ph'         => $ph,
                'suhu'       => 28.5,   // Nilai suhu normal
                'tds'        => 350,    // Nilai TDS normal
                'kekeruhan'  => 12,     // Nilai kekeruhan normal
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }
}