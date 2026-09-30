<?php

namespace Tests\Unit;

use App\Console\Commands\MqttListen;
use PHPUnit\Framework\TestCase;

class MqttIngestTest extends TestCase
{
    private function siklus(array &$buf, string $suhu = '28.5'): ?array
    {
        $row = null;
        foreach (['suhu' => $suhu, 'kekeruhan' => '12', 'tds' => '300', 'ph' => '7.1'] as $k => $v) {
            $row = MqttListen::ingest($buf, $k, $v);
        }
        return $row;
    }

    public function test_row_jadi_setelah_siklus_lengkap()
    {
        $buf = [];
        $this->assertSame(
            ['suhu' => 28.5, 'kekeruhan' => 12.0, 'tds' => 300.0, 'ph' => 7.1],
            $this->siklus($buf)
        );
        $this->assertSame([], $buf);
    }

    public function test_start_di_tengah_siklus_tidak_mencampur_nilai()
    {
        $buf = [];
        $this->assertNull(MqttListen::ingest($buf, 'tds', '999')); // sisa siklus lama
        $this->assertNull(MqttListen::ingest($buf, 'ph', '9'));
        $this->assertSame(28.5, $this->siklus($buf)['suhu']);
        $this->assertSame(300.0, $this->siklus($buf)['tds']); // bukan 999
    }

    public function test_pesan_hilang_dan_nilai_buruk_ditolak()
    {
        $buf = [];
        MqttListen::ingest($buf, 'suhu', '28');
        MqttListen::ingest($buf, 'kekeruhan', '12');   // tds & ph hilang
        $this->assertNull(MqttListen::ingest($buf, 'suhu', '29')); // siklus baru, buffer lama dibuang
        $this->assertNull($this->siklus($buf, '-127.00'));          // DS18B20 putus
        $this->assertNull(MqttListen::ingest($buf, 'suhu', 'abc')); // bukan angka
    }
}
