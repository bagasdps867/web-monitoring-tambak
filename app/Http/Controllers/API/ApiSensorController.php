<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use Illuminate\Http\Request;

class ApiSensorController extends Controller
{
    // Fungsi untuk menerima input dari ESP32
    public function store(Request $request)
    {
        $request->validate([
            'ph' => 'required|numeric',
            'suhu' => 'required|numeric',
            'kekeruhan' => 'required|numeric',
        ]);

        Sensor::create([
            'ph' => $request->ph,
            'suhu' => $request->suhu,
            'kekeruhan' => $request->kekeruhan,
        ]);

        return response()->json(['message' => 'Data berhasil disimpan']);
    }

    // Fungsi untuk mengirim data laporan ke Flutter
    public function index()
    {
        $sensors = Sensor::orderBy('created_at', 'desc')->get();

        $formattedData = $sensors->map(function ($item) {
            return [
                'waktu'     => $item->created_at ? $item->created_at->toDateTimeString() : now()->toDateTimeString(),
                'ph'        => $item->ph ?? 0,
                'suhu'      => $item->suhu ?? 0,
                'tds'       => $item->tds ?? 0,
                'kekeruhan' => $item->kekeruhan ?? 0,
                'kualitas'  => $item->kualitas ?? 'Baik',
                'status'    => $item->status ?? 'Normal',
            ];
        });

        return response()->json($formattedData, 200);
    }
}