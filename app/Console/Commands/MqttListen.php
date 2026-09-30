<?php

namespace App\Console\Commands;

use App\Models\Sensor;
use Illuminate\Console\Command;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

//kalau pakai ssh (nohup php artisan mqtt:listen > ~/mqtt.log 2>&1 &)
/*
class MqttListen extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Subscribe ke topic sensor ESP32 di MQTT dan simpan ke tabel sensors';

    const KEYS = ['suhu', 'kekeruhan', 'tds', 'ph'];

    // ESP32 publish 4 topic terpisah per siklus; baris baru jadi setelah keempatnya masuk.
    // ponytail: buffer sederhana, kalau 1 pesan hilang siklus bisa tercampur. Ganti ESP32 ke 1 topic JSON kalau sering terjadi.
    public static function ingest(array &$buf, string $key, string $msg): ?array
    {
        if (!is_numeric($msg)) return null;
        $buf[$key] = (float) $msg;
        if (count($buf) < count(self::KEYS)) return null;

        $row = $buf;
        $buf = [];
        // -127 = DS18B20 tidak terbaca, jangan disimpan sebagai data
        return $row['suhu'] < -100 ? null : $row;
    }

    public function handle()
    {
        $n = 0;
        while (true) {
            try {
                $cfg  = config('mqtt');
                $mqtt = new MqttClient($cfg['host'], $cfg['port'], 'laravel-' . uniqid());
                $mqtt->connect((new ConnectionSettings)
                    ->setUsername($cfg['username'])
                    ->setPassword($cfg['password'])
                    ->setUseTls(true), true);
                $this->info('MQTT terhubung, menunggu data...');

                $buf = [];
                foreach (self::KEYS as $key) {
                    $mqtt->subscribe("{$cfg['topic']}/$key", function ($topic, $msg) use (&$buf, $key, &$n) {
                        $row = self::ingest($buf, $key, $msg);
                        if (!$row) return;

                        Sensor::create($row); // kualitas dihitung SensorObserver
                        $this->line('Tersimpan: ' . json_encode($row));

                        if ($n++ % 720 === 0) { // ~1 jam sekali, pertahankan aturan hapus data > 2 bulan
                            Sensor::where('created_at', '<', now()->subMonths(2))->delete();
                        }
                    }, 0);
                }
                $mqtt->loop(true);
            } catch (\Throwable $e) {
                $this->error('MQTT putus: ' . $e->getMessage() . ' — coba lagi 5 detik');
                sleep(5);
            }
        }
    }
}
*/





//kalau pakai cron job
class MqttListen extends Command
{
    protected $signature = 'mqtt:listen {--seconds=0 : Berhenti sendiri setelah N detik (untuk cron di shared hosting), 0 = jalan terus}';
    protected $description = 'Subscribe ke topic sensor ESP32 di MQTT dan simpan ke tabel sensors';

    const KEYS = ['suhu', 'kekeruhan', 'tds', 'ph'];

    // ESP32 publish 4 topic per siklus (suhu dulu). Baris jadi setelah keempatnya masuk;
    // 'suhu' selalu memulai siklus baru, jadi start di tengah siklus / pesan hilang tidak mencampur nilai.
    public static function ingest(array &$buf, string $key, string $msg): ?array
    {
        if ($key === 'suhu') $buf = [];
        if (!$buf && $key !== 'suhu') return null;
        if (!is_numeric($msg)) { $buf = []; return null; }
        $buf[$key] = (float) $msg;
        if (count($buf) < count(self::KEYS)) return null;

        $row = $buf;
        $buf = [];
        return $row['suhu'] < -100 ? null : $row;
    }

    public function handle()
    {
        $limit = (int) $this->option('seconds');
        while (true) {
            try {
                $cfg  = config('mqtt');
                $mqtt = new MqttClient($cfg['host'], $cfg['port'], 'laravel-' . uniqid());
                $mqtt->connect((new ConnectionSettings)
                    ->setUsername($cfg['username'])
                    ->setPassword($cfg['password'])
                    ->setUseTls(true), true);
                $this->info('MQTT terhubung, menunggu data...');

                $buf = [];
                foreach (self::KEYS as $key) {
                    $mqtt->subscribe("{$cfg['topic']}/$key", function ($topic, $msg) use (&$buf, $key) {
                        $row = self::ingest($buf, $key, $msg);
                        if (!$row) return;

                        Sensor::create($row); // kualitas dihitung SensorObserver
                        $this->line('Tersimpan: ' . json_encode($row));

                        if (mt_rand(1, 720) === 1) { // ~1 jam sekali, pertahankan aturan hapus data > 3 bulan
                            Sensor::where('created_at', '<', now()->subMonths(3))->delete();
                        }
                    }, 0);
                }
                if ($limit) {
                    $mqtt->registerLoopEventHandler(function ($client, $elapsed) use ($limit) {
                        if ($elapsed >= $limit) $client->interrupt();
                    });
                }
                $mqtt->loop(true);
                if ($limit) { $mqtt->disconnect(); return; } // cron menyalakan lagi
            } catch (\Throwable $e) {
                $this->error('MQTT gagal: ' . $e->getMessage());
                if ($limit) return 1; // cron akan mencoba lagi menit depan
                sleep(5);
            }
        }
    }
}