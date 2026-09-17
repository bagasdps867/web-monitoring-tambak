<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    // Pastikan semua kolom ini terdaftar
    protected $fillable = [
        'ph',
        'suhu',
        'tds',
        'kekeruhan',
        'kualitas',
    ];
}