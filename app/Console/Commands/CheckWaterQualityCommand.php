<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sensor;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\DashboardController;

class CheckWaterQualityCommand extends Command
{
    protected $signature = 'water:check';
    protected $description = 'Cek kualitas air secara otomatis';

    public function handle()
    {
        $controller = new DashboardController();
        $latest = Sensor::latest()->first();

        if (!$latest) {
            Log::info('Tidak ada data sensor untuk dicek.');
            return;
        }

        $quality = $controller->calculateFuzzyQuality($latest->ph, $latest->suhu, $latest->kekeruhan);

        if ($quality < 50) {
            Log::warning('⚠️ Kualitas air buruk! Nilai: ' . $quality);
            // Bisa tambahkan broadcast/email/whatsapp di sini
        } else {
            Log::info('Kualitas air baik. Nilai: ' . $quality);
        }
    }
}
