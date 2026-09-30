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
        // 1. Komen atau hapus truncate agar data lama TIDAK terhapus
        // DB::table('sensors')->truncate();

        // 2. Set ke 20 karena kita hanya ingin menambah 20 data baru setiap kali dijalankan
        $totalData = 20;
        $dataChunk = [];
        $now = Carbon::now();

        // 3. Looping untuk membuat data historis (perbaiki syntax kondisi menjadi $i < $totalData)
        for ($i = 0; $i < $totalData; $i++) {
            
            // Probabilitas: 60% Normal, 40% Kritis
            $isCritical = mt_rand(1, 100) <= 40;

            if ($isCritical) {
                $ph        = mt_rand(1, 2) == 1 ? mt_rand(30, 55) / 10 : mt_rand(90, 110) / 10;
                $suhu      = mt_rand(1, 2) == 1 ? mt_rand(150, 200) / 10 : mt_rand(330, 380) / 10;
                $tds       = mt_rand(600, 1500);
                $kekeruhan = mt_rand(55, 200);
            } else {
                $ph        = mt_rand(65, 80) / 10;
                $suhu      = mt_rand(250, 290) / 10;
                $tds       = mt_rand(150, 400);
                $kekeruhan = mt_rand(0, 25);
            }

            $timestamp = (clone $now)->subHours($i);

            $dataChunk[] = [
                'ph'         => $ph,
                'suhu'       => $suhu,
                'tds'        => $tds,
                'kekeruhan'  => $kekeruhan,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            // 4. Insert per 250 data agar efisien di memori database
            if (count($dataChunk) === 250) {
                DB::table('sensors')->insert($dataChunk);
                $dataChunk = []; 
            }
        }

        // 5. Insert sisa data
        if (!empty($dataChunk)) {
            DB::table('sensors')->insert($dataChunk);
        }
    }
}