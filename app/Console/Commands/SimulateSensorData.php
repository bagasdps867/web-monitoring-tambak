<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sensor;
use App\Http\Controllers\DashboardController;

class SimulateSensorData extends Command
{
    // 1. Tambahkan opsi --count untuk menentukan batas data sebelum berhenti
    protected $signature = 'sensor:simulate {--count=0 : Jumlah data yang dikirim sebelum berhenti otomatis (0 = tanpa batas)}';
    protected $description = 'Simulasi pengiriman data sensor IoT secara acak dan teratur';

    public function handle()
    {
        $maxCount = (int) $this->option('count');
        $sentCount = 0;

        if ($maxCount > 0) {
            $this->info("Simulasi Sensor IoT Dimulai... (Berhenti otomatis setelah {$maxCount} data)");
        } else {
            $this->info('Simulasi Sensor IoT Dimulai... (Tekan Ctrl+C untuk berhenti)');
        }

        $controller = new DashboardController();

        while (true) {
            // 2. Kondisi penghenti perulangan
            if ($maxCount > 0 && $sentCount >= $maxCount) {
                $this->info("Simulasi selesai. Total data terkirim: {$sentCount}");
                break;
            }

            // Mengambil data terakhir sebagai titik awal
            $last = Sensor::latest()->first();
            $ph   = $last ? $last->ph : 7.2;
            $suhu = $last ? $last->suhu : 30.0;
            $tds  = $last ? $last->tds : 400;
            $ntu  = $last ? $last->kekeruhan : 15.0;

            // Pergerakan angka acak
            $ph   = round(max(5.5, min(9.5, $ph + (rand(-10, 10) / 100))), 1);
            $suhu = round(max(22.0, min(36.0, $suhu + (rand(-20, 20) / 100))), 1);
            $tds  = round(max(100, min(2000, $tds + rand(-15, 15))), 0);
            $ntu  = round(max(1.0, min(75.0, $ntu + (rand(-30, 30) / 100))), 1);

            // 3. Panggil langsung method tanpa ReflectionClass
            $fuzzyScore = $controller->calculateFuzzyQuality($ph, $suhu, $tds, $ntu);

            // Simpan data baru ke database
            Sensor::create([
                'ph'        => $ph,
                'suhu'      => $suhu,
                'tds'       => $tds,
                'kekeruhan' => $ntu,
                'kualitas'  => $fuzzyScore,
            ]);

            $sentCount++;
            $this->info("Data Ke-{$sentCount} Terkirim -> pH: {$ph} | Suhu: {$suhu}°C | TDS: {$tds}ppm | NTU: {$ntu} | Kualitas: {$fuzzyScore}");

            // Jeda 3 detik per pengiriman
            sleep(3);
        }
    }
}