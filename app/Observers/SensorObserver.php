<?php

namespace App\Observers;

use App\Models\Sensor;

class SensorObserver
{
    public function creating(Sensor $sensor)
    {
        $sensor->kualitas = app('App\Http\Controllers\DashboardController')
            ->calculateFuzzyQuality($sensor->ph, $sensor->suhu, $sensor->tds, $sensor->kekeruhan);
    }

    public function updating(Sensor $sensor)
    {
        $sensor->kualitas = app('App\Http\Controllers\DashboardController')
            ->calculateFuzzyQuality($sensor->ph, $sensor->suhu, $sensor->tds, $sensor->kekeruhan);
    }
}