@extends('layouts.app')

@push('styles')
<style>
/* Background & Font Utama */
body {
    background-color: #e6f0ed !important;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Kartu Utama */
.card-custom {
    background: #ffffff;
    border-radius: 20px;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
}

/* Status Badge */
.badge-normal {
    background-color: #dcfce7;
    color: #15803d;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
}

.badge-warning-custom {
    background-color: #fef3c7;
    color: #b45309;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
}

.badge-danger-custom {
    background-color: #fee2e2;
    color: #b91c1c;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
}

/* --- KARTU CUACA TERBARU --- */
.card-weather-target {
    background: #85caf5;
    border-radius: 28px;
    border: none;
    padding: 1.25rem !important;
    color: #0c2d2a;
}

.weather-divider {
    border-top: 1px solid rgba(255, 255, 255, 0.5) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.5) !important;
}

.weather-border-x {
    border-left: 1px solid rgba(255, 255, 255, 0.5) !important;
    border-right: 1px solid rgba(255, 255, 255, 0.5) !important;
}

/* Container scroll horizontal */
.hourly-scroll-container {
    display: flex !important;
    gap: 8px;
    overflow-x: auto !important;
    overflow-y: hidden;
    width: 100%;
    min-width: 0;
    padding-bottom: 6px;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.hourly-scroll-container::-webkit-scrollbar {
    display: none;
}

/* Item per jam */
.hourly-pill {
    background: rgba(255, 255, 255, 0.45);
    border-radius: 16px;
    padding: 8px 10px;
    text-align: center;
    flex: 0 0 62px !important;
    width: 62px;
}

/* Box AI Smart Recommendation */
.ai-box-notice {
    background-color: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 14px;
    transition: all 0.3s ease;
}

.ai-box-notice.ai-state-optimal {
    background-color: #f0fdf4 !important;
    border: 1px solid #bbf7d0 !important;
    color: #14532d !important;
}

.ai-box-notice.ai-state-warning {
    background-color: #fef2f2 !important;
    border: 1px solid #fecaca !important;
    color: #991b1b !important;
}

/* Background Icon Sensor */
.icon-bg-blue {
    background-color: #e0f2fe;
    color: #0284c7;
}

.icon-bg-green {
    background-color: #dcfce7;
    color: #16a34a;
}

.icon-bg-cyan {
    background-color: #e0f7fa;
    color: #00acc1;
}

.icon-bg-purple {
    background-color: #f3e8ff;
    color: #9333ea;
}

/* Tombol Filter & Export Monitoring Data */
.btn-chart-filter {
    background-color: #ffffff;
    color: #64748b;
    border: 1px solid #e2e8f0;
    padding: 6px 18px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.btn-chart-filter.active {
    background-color: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
}

/* Header Statistik AVG/MIN/MAX di dalam Grafik */
.stat-header-label {
    font-size: 0.65rem;
    color: #94a3b8;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.stat-header-val {
    font-size: 1rem;
    font-weight: 700;
}

.stat-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 50px;
}

/* ================== DARK MODE OVERRIDE ================== */
body.dark-mode {
    background-color: #0b1329 !important;
    color: #f1f5f9 !important;
}

body.dark-mode .card-custom {
    background-color: #1e293b !important;
    border: 1px solid #334155 !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35) !important;
}

body.dark-mode .text-dark,
body.dark-mode h2, body.dark-mode h4, body.dark-mode h5 {
    color: #f8fafc !important;
}

body.dark-mode .text-muted,
body.dark-mode .text-secondary {
    color: #94a3b8 !important;
}

/* Kartu Kualitas Air */
body.dark-mode #card-quality {
    background-color: #162238 !important;
    border: 1px solid #1e3a5f !important;
}
body.dark-mode #card-quality .bg-white {
    background-color: #1e293b !important;
    border-color: #334155 !important;
    color: #f8fafc !important;
}
body.dark-mode #card-quality span[style*="color: #52708f"],
body.dark-mode #card-quality p {
    color: #94a3b8 !important;
}

/* Kartu Cuaca BMKG */
body.dark-mode .card-weather-target {
    background: #1e293b !important;
    border: 1px solid #334155 !important;
    color: #f1f5f9 !important;
}
body.dark-mode .card-weather-target div[style*="color: #0c2d2a"] {
    color: #f8fafc !important;
}
body.dark-mode .card-weather-target span[style*="color: #417280"],
body.dark-mode .card-weather-target div[style*="color: #417280"],
body.dark-mode .card-weather-target div[style*="color: #3d6875"] {
    color: #94a3b8 !important;
}
body.dark-mode .weather-divider,
body.dark-mode .weather-border-x {
    border-color: #334155 !important;
}
body.dark-mode .hourly-pill {
    background: rgba(255, 255, 255, 0.08) !important;
}
body.dark-mode .hourly-pill div[style*="color: #0c2d2a"] {
    color: #f8fafc !important;
}
body.dark-mode .hourly-pill div[style*="color: #417280"] {
    color: #94a3b8 !important;
}

/* Status Online di Kanan Atas */
body.dark-mode #system-status-container {
    background-color: #1e293b !important;
    border: 1px solid #334155 !important;
}

body.dark-mode #system-status-text {
    color: #f8fafc !important;
}

body.dark-mode #system-status-container .text-muted,
body.dark-mode #system-status-container #last-updated-time {
    color: #94a3b8 !important;
}

/* Box AI Recommendation Dark Mode */
body.dark-mode .border-bottom {
    border-color: #334155 !important;
}
body.dark-mode .ai-box-notice {
    background-color: #0f172a !important;
    border-color: #334155 !important;
    color: #f8fafc !important;
}
body.dark-mode .ai-box-notice.ai-state-optimal {
    background-color: #064e3b !important;
    border: 1px solid #059669 !important;
    color: #a7f3d0 !important;
}
body.dark-mode .ai-box-notice.ai-state-warning {
    background-color: #450a0a !important;
    border: 1px solid #dc2626 !important;
    color: #fca5a5 !important;
}

/* Tombol Filter Grafik */
body.dark-mode .btn-chart-filter,
body.dark-mode .btn-chart-export {
    background-color: #1e293b !important;
    color: #cbd5e1 !important;
    border-color: #334155 !important;
}
body.dark-mode .btn-chart-filter.active {
    background-color: #2563eb !important;
    color: #ffffff !important;
    border-color: #2563eb !important;
}

/* Styling Tombol Filter Chart Mobile */
.custom-chart-tabs {
    white-space: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none; 
    padding-right: 15px;
}
.custom-chart-tabs::-webkit-scrollbar {
    display: none; 
}
.custom-chart-tabs::after {
    content: "";
    flex: 0 0 15px;
}
.btn-chart-tab {
    background-color: #f1f5f9;
    color: #64748b;
    border-radius: 50rem;
    padding: 0.5rem 1.25rem;
    font-weight: 600;
    border: 1px solid transparent;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    transition: all 0.2s ease;
}
.btn-chart-tab.active {
    background-color: #e0f2fe;
    color: #0284c7;
    border-color: #7dd3fc;
}

@media (max-width: 767px) {
    .chart-card-wrapper {
        display: none;
    }
    .chart-card-wrapper.active-mobile {
        display: block;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pt-1" style="padding-bottom: 100px !important;">
    <!-- Notifikasi Alert Container -->
    <div id="alertContainer" class="mb-3"></div>

    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <span class="text-uppercase fw-bold text-primary small"
                style="letter-spacing: 0.5px; font-size: 0.75rem;">WATER QUALITY MONITORING</span>
            <h2 class="fw-bold m-0 text-dark" style="font-size: 1.8rem;">Dashboard Monitoring</h2>
            <p class="text-secondary small m-0">Pantau kondisi kualitas air secara real-time.</p>
        </div>
        <div id="system-status-container" class="d-inline-flex flex-wrap align-items-center bg-white px-3 py-2 rounded-pill shadow-sm" style="width: fit-content;">
            <span id="system-status-dot" class="badge bg-success rounded-circle p-1 me-2" style="width: 8px; height: 8px; flex-shrink: 0;"></span>
            <span id="system-status-text" class="fw-bold small me-2 text-dark text-nowrap">System Online</span>
            <span class="text-muted small text-nowrap" style="font-size: 0.8rem;">Last updated: <span id="last-updated-time">--:--:--</span></span>
        </div>
    </div>

    <!-- ROW 1: Top Cards -->
    <div class="row g-3 mb-4 align-items-stretch">
        <!-- 1. Skor Kualitas Air & Mini Grid -->
        <div class="col-lg-3">
            <div id="card-quality"
                class="card h-100 p-3 position-relative overflow-hidden shadow-sm d-flex flex-column justify-content-between"
                style="border-radius: 20px; background-color: #eef7f9; border: 1px solid #ccece6;">
                <div class="position-absolute"
                    style="width: 200px; height: 200px; background: rgba(14, 165, 233, 0.06); border-radius: 50%; top: -60px; right: -60px; z-index: 0;">
                </div>

                <div class="position-relative z-1 d-flex justify-content-between align-items-start">
                    <div class="d-flex flex-column gap-1">
                        <span class="fw-bold"
                            style="font-size: 0.75rem; letter-spacing: 0.5px; color: #52708f;">KUALITAS AIR</span>
                        <div class="d-inline-flex align-items-center bg-white rounded-pill px-2 py-1 shadow-sm"
                            style="border: 1px solid #a7f3d0; width: fit-content;">
                            <i id="quality-dot" class="fas fa-circle me-1"
                                style="font-size: 7px; color: #10b981;"></i>
                            <span id="quality-label" class="fw-bold"
                                style="color: #10b981; font-size: 0.75rem;">Normal</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #1e40af 0%, #06b6d4 100%);">
                        <i class="fas fa-droplet text-white"></i>
                    </div>
                </div>

                <div class="text-center my-2 position-relative z-1">
                    <div class="d-inline-flex align-items-center justify-content-center position-relative"
                        style="width: 145px; height: 145px;">
                        <canvas id="scoreGauge"></canvas>
                        <div class="position-absolute text-center mt-1">
                            <h2 id="quality-value" class="fw-bolder mb-0 text-dark"
                                style="font-size: 2.3rem; line-height: 1;">--</h2>
                            <small class="text-secondary fw-medium" style="font-size: 0.75rem;">/ 100</small>
                            <span id="quality-badge-text" class="fw-bold d-block mt-1"
                                style="color: #1d4ed8; font-size: 0.85rem; letter-spacing: 0.5px;">--</span>
                        </div>
                    </div>
                </div>

                <p id="quality-summary-text" class="text-center mb-2 px-1 position-relative z-1"
                    style="font-size: 0.8rem; color: #64748b; line-height: 1.3;">
                    Memuat status kualitas air...
                </p>

                <div class="row g-2 text-center position-relative z-1">
                    <div class="col-6">
                        <div class="bg-white py-2 px-1 d-flex flex-column justify-content-center"
                            style="border: 1px solid #ccece6; border-radius: 10px; min-height: 52px;">
                            <small style="font-size: 0.7rem; color: #64748b;">pH</small>
                            <strong id="mini-ph-val" class="fw-bold"
                                style="color: #1e3a8a; font-size: 0.95rem;">--</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white py-2 px-1 d-flex flex-column justify-content-center"
                            style="border: 1px solid #ccece6; border-radius: 10px; min-height: 52px;">
                            <small style="font-size: 0.7rem; color: #64748b;">Suhu</small>
                            <strong id="mini-suhu-val" class="fw-bold"
                                style="color: #10b981; font-size: 0.95rem;">--</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white py-2 px-1 d-flex flex-column justify-content-center"
                            style="border: 1px solid #ccece6; border-radius: 10px; min-height: 52px;">
                            <small style="font-size: 0.7rem; color: #64748b;">TDS</small>
                            <strong id="mini-tds-val" class="fw-bold"
                                style="color: #06b6d4; font-size: 0.95rem;">--</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white py-2 px-1 d-flex flex-column justify-content-center"
                            style="border: 1px solid #ccece6; border-radius: 10px; min-height: 52px;">
                            <small style="font-size: 0.7rem; color: #64748b;">NTU</small>
                            <strong id="mini-kekeruhan-val" class="fw-bold"
                                style="color: #8b5cf6; font-size: 0.95rem;">--</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weather BMKG Card Dinamis -->
        <div class="col-lg-3">
            <div class="card card-weather-target h-100 d-flex flex-column justify-content-between shadow-sm">
                <!-- Header: Lokasi & Hari -->
                <div class="d-flex justify-content-between align-items-start">
                    <div class="fw-bold text-uppercase d-flex align-items-center gap-1"
                        style="font-size: 0.72rem; color: #3d6875;">
                        <i class="fas fa-location-dot text-danger"></i> {{ $cuaca['lokasi'] ?? 'Sidoarjo' }}
                    </div>
                    <div class="text-end" style="line-height: 1.1;">
                        <div class="fw-bold" style="font-size: 0.95rem; color: #0c2d2a;">Weather</div>
                        <span style="font-size: 0.72rem; color: #417280;">{{ $cuaca['hari'] ?? 'Hari ini' }}</span>
                    </div>
                </div>

                <!-- Suhu Utama & Kondisi -->
                <div class="d-flex align-items-center gap-3 my-2">
                    <i class="{{ $cuaca['icon'] ?? 'fas fa-cloud-sun text-warning' }}"
                        style="font-size: 3.2rem; filter: drop-shadow(0 3px 5px rgba(0,0,0,0.08));"></i>
                    <div>
                        <div class="fw-bold"
                            style="font-size: 2.8rem; line-height: 1; color: #0c2d2a; letter-spacing: -1px;">
                            {{ $cuaca['suhu'] ?? '--' }}°C</div>
                        <div style="font-size: 0.85rem; color: #417280; font-weight: 500;">{{ $cuaca['kondisi'] ?? '--' }}</div>
                    </div>
                </div>

                <!-- Middle Stats: Kelembaban, Waktu, Angin -->
                <div class="row text-center py-2 weather-divider my-1">
                    <div class="col-4 px-1">
                        <div class="fw-bold" style="font-size: 1.05rem; color: #0c2d2a;">{{ $cuaca['kelembaban'] ?? '--' }}%</div>
                        <div style="font-size: 0.65rem; color: #417280;">Kelembaban</div>
                    </div>
                    <div class="col-4 px-1 weather-border-x d-flex align-items-center justify-content-center">
                        <div id="weather-time" class="fw-bold" style="font-size: 0.9rem; color: #ef4444;">
                            {{ $cuaca['waktu'] ?? '--:--' }}</div>
                    </div>
                    <div class="col-4 px-1">
                        <div class="fw-bold" style="font-size: 1.05rem; color: #0c2d2a;">{{ $cuaca['angin'] ?? '--' }}</div>
                        <div style="font-size: 0.65rem; color: #417280;">Angin · {{ $cuaca['arah_angin'] ?? '--' }}</div>
                    </div>
                </div>

                <!-- Hourly Forecast (Horizontal Scroll) -->
                <div class="hourly-scroll-container pt-1">
                    @if(isset($cuaca['hourly']))
                        @foreach($cuaca['hourly'] as $item)
                        <div class="hourly-pill">
                            <div style="font-size: 0.68rem; color: #417280; font-weight: 600;">{{ $item['jam'] }}</div>
                            <i class="{{ $item['icon'] }} my-1 d-block" style="font-size: 0.85rem;"></i>
                            <div class="fw-bold" style="font-size: 0.85rem; color: #0c2d2a;">{{ $item['suhu'] }}</div>
                            <div style="font-size: 0.6rem; color: #417280;">{{ $item['angin'] }}</div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Daftar Kartu Sensor Kanan -->
        <div class="col-lg-6">
            <div class="d-flex flex-column justify-content-between h-100 gap-2">
                <!-- pH -->
                <div id="card-ph"
                    class="card card-custom px-3 py-2.5 d-flex flex-row justify-content-between align-items-center h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 icon-bg-blue d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px;">
                            <i class="fas fa-droplet"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">pH</small>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 id="ph-value" class="fw-bold m-0 text-dark">--</h5>
                                <span class="text-muted small" style="font-size: 0.75rem;">pH</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;">Tingkat keasaman air</small>
                        </div>
                    </div>
                    <span id="ph-badge" class="badge-normal"><i class="fas fa-circle me-1" style="font-size: 5px;"></i> Normal</span>
                </div>

                <!-- Suhu -->
                <div id="card-suhu"
                    class="card card-custom px-3 py-2.5 d-flex flex-row justify-content-between align-items-center h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 icon-bg-green d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px;">
                            <i class="fas fa-temperature-half"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Suhu</small>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 id="suhu-value" class="fw-bold m-0 text-dark">--</h5>
                                <span class="text-muted small" style="font-size: 0.75rem;">°C</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;">Temperatur air tambak</small>
                        </div>
                    </div>
                    <span id="suhu-badge" class="badge-normal"><i class="fas fa-circle me-1" style="font-size: 5px;"></i> Normal</span>
                </div>

                <!-- TDS -->
                <div id="card-tds"
                    class="card card-custom px-3 py-2.5 d-flex flex-row justify-content-between align-items-center h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 icon-bg-cyan d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px;">
                            <i class="fas fa-circle-dot"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">TDS</small>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 id="tds-value" class="fw-bold m-0 text-dark">--</h5>
                                <span class="text-muted small" style="font-size: 0.75rem;">ppm</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;">Total padatan terlarut</small>
                        </div>
                    </div>
                    <span id="tds-badge" class="badge-normal"><i class="fas fa-circle me-1" style="font-size: 5px;"></i> Normal</span>
                </div>

                <!-- Kekeruhan -->
                <div id="card-kekeruhan"
                    class="card card-custom px-3 py-2.5 d-flex flex-row justify-content-between align-items-center h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 icon-bg-purple d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px;">
                            <i class="fas fa-water"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Kekeruhan</small>
                            <div class="d-flex align-items-baseline gap-1">
                                <h5 id="kekeruhan-value" class="fw-bold m-0 text-dark">--</h5>
                                <span class="text-muted small" style="font-size: 0.75rem;">NTU</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;">Tingkat kekeruhan air</small>
                        </div>
                    </div>
                    <span id="kekeruhan-badge" class="badge-normal"><i class="fas fa-circle me-1" style="font-size: 5px;"></i> Normal</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: AI SMART RECOMMENDATION -->
    <div class="row mb-4">
        <div class="col-12">
            <div id="tips-card" class="card card-custom p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 pb-3 mb-3 border-bottom">
                    <!-- Sisi Kiri: Ikon & Teks -->
                    <div class="d-flex align-items-center gap-3 pe-md-4">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
                            style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                            <i class="fas fa-robot fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">AI Smart Recommendation</h5>
                            <div class="text-muted" style="font-size: 0.85rem; line-height: 1.4;">
                                Saran tindakan cerdas berbasis Machine Learning saat kondisi air kolam bermasalah
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sisi Kanan: Tombol -->
                    <div class="flex-shrink-0 mt-2 mt-md-0" style="min-width: fit-content;">
                        <button id="btnAiAction" onclick="triggerAiRecommendation()" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm d-flex align-items-center justify-content-center gap-2 w-100">
                            <i class="fas fa-wand-magic-sparkles"></i>
                            <span id="btnAiText">Cek Tindakan AI</span>
                        </button>
                    </div>
                </div>

                <!-- Box Konten Pesan -->
                <div id="ai-container-box" class="ai-box-notice p-4 border rounded">
                    <div id="ai-standby" class="text-center py-2 text-muted" style="font-size: 0.9rem;">
                        <i class="fas fa-circle-info text-primary me-2 fs-5 align-middle"></i>
                        <span class="align-middle">Klik tombol <strong>"Cek Tindakan AI"</strong> di atas jika Anda ingin mengevaluasi tindakan penanganan parameter air.</span>
                    </div>
                    <div id="ai-loading" style="display: none;" class="text-center py-2 text-muted small">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Mengevaluasi parameter air tambak via Model Machine Learning...
                    </div>
                    <div id="ai-content-text" style="display: none; font-size: 0.9rem; line-height: 1.65;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 3: MONITORING DATA HEADER & FILTER -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Monitoring Data</h4>
            <small class="text-muted">Grafik historis parameter kualitas air</small>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto justify-content-between justify-content-md-end">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" id="btn-today" class="btn btn-outline-primary btn-chart-filter active" onclick="filterChartTime('today')">Today</button>
                <button type="button" id="btn-7days" class="btn btn-outline-primary btn-chart-filter" onclick="filterChartTime('7days')">7 Days</button>
                <button type="button" id="btn-30days" class="btn btn-outline-primary btn-chart-filter" onclick="filterChartTime('30days')">30 Days</button>
            </div>
        </div>
    </div>

    <!-- Tombol Tab Chart (Hanya Tampil di Mobile) -->
    <div class="d-flex d-md-none gap-2 mb-3 pb-2 custom-chart-tabs">
        <button class="btn btn-chart-tab active" onclick="switchMobileChart('ph')" id="tab-ph">
            <i class="fas fa-check me-2 check-icon"></i> pH
        </button>
        <button class="btn btn-chart-tab" onclick="switchMobileChart('suhu')" id="tab-suhu">
            <i class="fas fa-check me-2 check-icon d-none"></i> Suhu
        </button>
        <button class="btn btn-chart-tab" onclick="switchMobileChart('tds')" id="tab-tds">
            <i class="fas fa-check me-2 check-icon d-none"></i> TDS
        </button>
        <button class="btn btn-chart-tab" onclick="switchMobileChart('kekeruhan')" id="tab-kekeruhan">
            <i class="fas fa-check me-2 check-icon d-none"></i> Kekeruhan
        </button>
    </div>

    <div class="row g-3">
        <!-- 1. Grafik pH -->
        <div class="col-12 col-md-6 chart-card-wrapper active-mobile" id="chart-wrapper-ph">
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Grafik pH</h5>
                        <small class="text-secondary" style="font-size: 0.75rem;">Perubahan tingkat pH dari waktu ke waktu</small>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="stat-box">
                            <div class="stat-header-label">AVG</div>
                            <div id="ph-avg" class="stat-header-val" style="color: #2563eb;">--</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-header-label">MIN</div>
                            <div id="ph-min" class="stat-header-val" style="color: #2563eb;">--</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-header-label">MAX</div>
                            <div id="ph-max" class="stat-header-val" style="color: #2563eb;">--</div>
                        </div>
                    </div>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="phChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 2. Grafik Suhu -->
        <div class="col-12 col-md-6 chart-card-wrapper" id="chart-wrapper-suhu">
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Grafik Suhu</h5>
                        <small class="text-secondary" style="font-size: 0.75rem;">Perubahan suhu air</small>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="stat-box">
                            <div class="stat-header-label">AVG</div>
                            <div id="suhu-avg" class="stat-header-val" style="color: #10b981;">--</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-header-label">MIN</div>
                            <div id="suhu-min" class="stat-header-val" style="color: #10b981;">--</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-header-label">MAX</div>
                            <div id="suhu-max" class="stat-header-val" style="color: #10b981;">--</div>
                        </div>
                    </div>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="suhuChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 3. Grafik TDS -->
        <div class="col-12 col-md-6 chart-card-wrapper" id="chart-wrapper-tds">
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Grafik TDS</h5>
                        <small class="text-secondary" style="font-size: 0.75rem;">Perubahan total padatan terlarut dalam air</small>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="stat-box">
                            <div class="stat-header-label">AVG</div>
                            <div id="tds-avg" class="stat-header-val" style="color: #06b6d4;">--</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-header-label">MIN</div>
                            <div id="tds-min" class="stat-header-val" style="color: #06b6d4;">--</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-header-label">MAX</div>
                            <div id="tds-max" class="stat-header-val" style="color: #06b6d4;">--</div>
                        </div>
                    </div>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="tdsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 4. Grafik Kekeruhan -->
        <div class="col-12 col-md-6 chart-card-wrapper" id="chart-wrapper-kekeruhan">
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Grafik Kekeruhan</h5>
                        <small class="text-secondary" style="font-size: 0.75rem;">Perubahan tingkat kekeruhan air</small>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="stat-box">
                            <div class="stat-header-label">AVG</div>
                            <div id="kekeruhan-avg" class="stat-header-val" style="color: #8b5cf6;">--</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-header-label">MIN</div>
                            <div id="kekeruhan-min" class="stat-header-val" style="color: #8b5cf6;">--</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-header-label">MAX</div>
                            <div id="kekeruhan-max" class="stat-header-val" style="color: #8b5cf6;">--</div>
                        </div>
                    </div>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="kekeruhanChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let sensorData = @json($sensorData ?? []);
let activeTimeRange = 'today';

let charts = { ph: null, suhu: null, tds: null, kekeruhan: null };
let scoreChart = null;

// Cross-browser helper untuk parsing date string dari DB/Laravel
function parseDate(dateStr) {
    if (!dateStr) return new Date();
    // Mengubah format SQL YYYY-MM-DD HH:mm:ss menjadi format aman Safari YYYY/MM/DD HH:mm:ss
    const safeStr = typeof dateStr === 'string' ? dateStr.replace(/-/g, '/') : dateStr;
    const d = new Date(safeStr);
    return isNaN(d.getTime()) ? new Date() : d;
}

function formatTimeLabel(dateStr) {
    if (!dateStr) return '';
    const d = parseDate(dateStr);

    const minutes = d.getMinutes();
    const roundedMinutes = Math.round(minutes / 30) * 30;
    d.setMinutes(roundedMinutes);
    d.setSeconds(0);

    const hours = String(d.getHours()).padStart(2, '0');
    const mins = String(d.getMinutes()).padStart(2, '0');
    return `${hours}:${mins}`;
}

function calculateGaugeScore(ph, suhu, tds, ntu) {
    let score = 100;
    if (ph < 6.5 || ph > 8.5) score -= 25;
    if (suhu < 26.0 || suhu > 31.0) score -= 25;
    if (tds > 500) score -= 25;
    if (ntu > 20.0) score -= 25;
    return Math.max(10, Math.min(100, score));
}

function updateChartStats(key, arrData, unit = '') {
    const minEl = document.getElementById(`${key}-min`);
    const maxEl = document.getElementById(`${key}-max`);
    const avgEl = document.getElementById(`${key}-avg`);

    if (!arrData || !arrData.length) {
        if (minEl) minEl.textContent = '--';
        if (maxEl) maxEl.textContent = '--';
        if (avgEl) avgEl.textContent = '--';
        return;
    }

    const validNums = arrData.filter(v => v !== null && !isNaN(v));
    if (!validNums.length) {
        if (minEl) minEl.textContent = '--';
        if (maxEl) maxEl.textContent = '--';
        if (avgEl) avgEl.textContent = '--';
        return;
    }

    const min = Math.min(...validNums).toFixed(1);
    const max = Math.max(...validNums).toFixed(1);
    const avg = (validNums.reduce((a, b) => a + b, 0) / validNums.length).toFixed(1);

    const colorMap = { 'ph': '#2563eb', 'suhu': '#10b981', 'tds': '#06b6d4', 'kekeruhan': '#8b5cf6' };
    const themeColor = colorMap[key] || '#64748b';
    const unitHtml = unit ? ` <span style="color: ${themeColor}; font-weight: 600; font-size: 0.75rem;">${unit}</span>` : '';

    if (minEl) minEl.innerHTML = `${min}${unitHtml}`;
    if (maxEl) maxEl.innerHTML = `${max}${unitHtml}`;
    if (avgEl) avgEl.innerHTML = `${avg}${unitHtml}`;
}

function evalPh(val) {
    if (val >= 6.5 && val <= 8.5) return 'Normal';
    if ((val >= 6.0 && val < 6.5) || (val > 8.5 && val <= 9.0)) return 'Warning';
    return 'Critical';
}

function evalSuhu(val) {
    if (val >= 26.0 && val <= 31.0) return 'Normal';
    if ((val >= 24.0 && val < 26.0) || (val > 31.0 && val <= 33.0)) return 'Warning';
    return 'Critical';
}

function evalTds(val) {
    if (val <= 500) return 'Normal';
    if (val <= 1000) return 'Warning';
    return 'Critical';
}

function evalKekeruhan(val) {
    if (val <= 20.0) return 'Normal';
    if (val <= 50.0) return 'Warning';
    return 'Critical';
}

function updateBadgeUI(elementId, status) {
    const el = document.getElementById(elementId);
    if (!el) return;

    if (status === 'Normal') {
        el.className = 'badge-normal';
        el.innerHTML = `<i class="fas fa-circle me-1" style="font-size: 5px;"></i> Normal`;
    } else if (status === 'Warning') {
        el.className = 'badge-warning-custom';
        el.innerHTML = `<i class="fas fa-circle me-1" style="font-size: 5px;"></i> Warning`;
    } else {
        el.className = 'badge-danger-custom';
        el.innerHTML = `<i class="fas fa-circle me-1" style="font-size: 5px;"></i> Critical`;
    }
}

function updateSensorBadgesAndValues(latest) {
    if (!latest) return;

    const phVal = parseFloat(latest.ph) || 0;
    document.getElementById('ph-value').textContent = phVal.toFixed(1);
    document.getElementById('mini-ph-val').textContent = phVal.toFixed(1);
    updateBadgeUI('ph-badge', evalPh(phVal));

    const suhuVal = parseFloat(latest.suhu) || 0;
    document.getElementById('suhu-value').textContent = suhuVal.toFixed(1);
    document.getElementById('mini-suhu-val').textContent = `${suhuVal.toFixed(1)}°`;
    updateBadgeUI('suhu-badge', evalSuhu(suhuVal));

    const tdsVal = parseFloat(latest.tds) || 0;
    document.getElementById('tds-value').textContent = Math.round(tdsVal);
    document.getElementById('mini-tds-val').textContent = Math.round(tdsVal);
    updateBadgeUI('tds-badge', evalTds(tdsVal));

    const ntuVal = parseFloat(latest.kekeruhan) || 0;
    document.getElementById('kekeruhan-value').textContent = ntuVal.toFixed(1);
    document.getElementById('mini-kekeruhan-val').textContent = ntuVal.toFixed(1);
    updateBadgeUI('kekeruhan-badge', evalKekeruhan(ntuVal));

    const qualityScore = latest.kualitas !== null && latest.kualitas !== undefined ?
        Math.round(parseFloat(latest.kualitas)) :
        calculateGaugeScore(phVal, suhuVal, tdsVal, ntuVal);

    document.getElementById('quality-value').textContent = qualityScore;

    let qualityText = 'Normal'; 
    let qualityColor = '#10b981';
    let summary = 'Semua parameter dalam kondisi aman dan optimal.';

    if (qualityScore < 50) {
        qualityText = 'Critical';
        qualityColor = '#ef4444';
        summary = 'Kualitas air kritis, perlu tindakan pengondisian segera.';
    } else if (qualityScore < 75) {
        qualityText = 'Warning'; 
        qualityColor = '#f59e0b';
        summary = 'Kualitas air kurang stabil, pantau perubahan pH & suhu.';
    }

    document.getElementById('quality-label').textContent = qualityText;
    document.getElementById('quality-label').style.color = qualityColor;
    document.getElementById('quality-dot').style.color = qualityColor;
    document.getElementById('quality-badge-text').textContent = qualityText.toUpperCase();
    document.getElementById('quality-badge-text').style.color = qualityColor;
    document.getElementById('quality-summary-text').textContent = summary;

    if (scoreChart) {
        scoreChart.data.datasets[0].data = [qualityScore, 100 - qualityScore];
        scoreChart.data.datasets[0].backgroundColor[0] = qualityColor;
        scoreChart.update();
    }
}

function initScoreGauge() {
    const canvas = document.getElementById('scoreGauge');
    if (!canvas) return;
    const ctxGauge = canvas.getContext('2d');
    scoreChart = new Chart(ctxGauge, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [0, 100],
                backgroundColor: ['#10b981', '#e2e8f0'],
                borderWidth: 0,
                borderRadius: 20
            }]
        },
        options: {
            cutout: '80%',
            rotation: 210,
            circumference: 360,
            responsive: true,
            maintainAspectRatio: true,
            plugins: { tooltip: { enabled: false }, legend: { display: false } }
        }
    });
}

const crosshairPlugin = {
    id: 'crosshair',
    afterDraw: chart => {
        const activeElements = chart.tooltip?.getActiveElements();
        if (activeElements && activeElements.length) {
            const x = activeElements[0].element.x;
            const yAxis = chart.scales.y;
            const ctx = chart.ctx;

            ctx.save();
            ctx.beginPath();
            ctx.moveTo(x, yAxis.top);
            ctx.lineTo(x, yAxis.bottom);
            ctx.lineWidth = 1.5;
            ctx.strokeStyle = 'rgba(203, 213, 225, 0.8)';
            ctx.setLineDash([4, 4]);
            ctx.stroke();
            ctx.restore();
        }
    }
};

function createLineChart(id, label, initialLabels, dataPoints, borderColor, bgColor, threshold = null, thresholdColor = '#cbd5e1') {
    const canvas = document.getElementById(id);
    if (!canvas) return null;
    const ctx = canvas.getContext('2d');
    let gradient = ctx.createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0, bgColor);
    gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

    const datasets = [{
        label: label,
        data: dataPoints,
        borderColor: borderColor,
        backgroundColor: gradient,
        borderWidth: 2,
        fill: true,
        tension: 0.4,
        pointRadius: 0,
        pointHoverRadius: 6,
        pointHoverBackgroundColor: borderColor,
        pointHoverBorderColor: '#ffffff',
        pointHoverBorderWidth: 2
    }];

    if (threshold !== null) {
        datasets.push({
            label: 'Ambang Batas',
            data: Array(dataPoints.length).fill(threshold),
            borderColor: thresholdColor,
            borderWidth: 1.5,
            borderDash: [4, 4],
            fill: false,
            pointRadius: 0,
            pointHoverRadius: 0
        });
    }

    let unit = '';
    if (label.includes('pH')) unit = 'pH';
    else if (label.includes('Suhu')) unit = '°C';
    else if (label.includes('TDS')) unit = 'ppm';
    else if (label.includes('Kekeruhan')) unit = 'NTU';

    return new Chart(ctx, {
        type: 'line',
        data: { labels: initialLabels, datasets: datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { top: 15, bottom: 5 } },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#64748b',
                    titleFont: { size: 12, weight: 'normal', family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" },
                    bodyColor: borderColor,
                    bodyFont: { size: 16, weight: 'bold', family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" },
                    borderColor: '#e2e8f0',
                    borderWidth: 1.5,
                    padding: { top: 10, bottom: 10, left: 14, right: 14 },
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(context) { return context[0].label; },
                        label: function(context) {
                            if (context.dataset.label === 'Ambang Batas') {
                                return `Batas: ${context.parsed.y} ${unit}`;
                            }
                            return `${context.parsed.y} ${unit}`;
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false, drawBorder: false }, ticks: { font: { size: 11 }, color: '#94a3b8', padding: 8 }, border: { display: false } },
                y: { grid: { display: true, color: '#f8fafc', drawBorder: false, borderDash: [4, 4] }, ticks: { font: { size: 11 }, color: '#94a3b8', padding: 10, maxTicksLimit: 5 }, border: { display: false } }
            }
        },
        plugins: [crosshairPlugin]
    });
}

function initCharts() {
    initScoreGauge();

    charts.ph = createLineChart('phChart', 'pH', [], [], '#2563eb', 'rgba(37, 99, 235, 0.1)', 7.5, '#bfdbfe');
    charts.suhu = createLineChart('suhuChart', 'Suhu (°C)', [], [], '#10b981', 'rgba(16, 185, 129, 0.1)', 30, '#bbf7d0');
    charts.tds = createLineChart('tdsChart', 'TDS (ppm)', [], [], '#06b6d4', 'rgba(6, 182, 212, 0.1)', 500, '#fca5a5');
    charts.kekeruhan = createLineChart('kekeruhanChart', 'Kekeruhan (NTU)', [], [], '#8b5cf6', 'rgba(139, 92, 246, 0.1)', 20, '#fca5a5');

    filterChartTime(activeTimeRange);

    if (sensorData.length > 0) {
        updateSensorBadgesAndValues(sensorData[0]);
    }
}

function processSensorDataByRange(rawData, range) {
    if (!rawData || rawData.length === 0) {
        return { labels: [], ph: [], suhu: [], tds: [], kekeruhan: [] };
    }

    let sortedData = [...rawData].sort((a, b) => parseDate(a.created_at) - parseDate(b.created_at));
    const now = new Date();

    if (range === 'today') {
        const todayStr = now.toDateString();
        let todayData = sortedData.filter(item => {
            if (!item.created_at) return false;
            return parseDate(item.created_at).toDateString() === todayStr;
        });

        let grouped = {};
        todayData.forEach(item => {
            const timeKey = formatTimeLabel(item.created_at);
            if (!grouped[timeKey]) {
                grouped[timeKey] = { ph: [], suhu: [], tds: [], kekeruhan: [] };
            }
            grouped[timeKey].ph.push(parseFloat(item.ph) || 0);
            grouped[timeKey].suhu.push(parseFloat(item.suhu) || 0);
            grouped[timeKey].tds.push(parseFloat(item.tds) || 0);
            grouped[timeKey].kekeruhan.push(parseFloat(item.kekeruhan) || 0);
        });

        let labels = Object.keys(grouped);
        let ph = labels.map(k => (grouped[k].ph.reduce((a, b) => a + b, 0) / grouped[k].ph.length));
        let suhu = labels.map(k => (grouped[k].suhu.reduce((a, b) => a + b, 0) / grouped[k].suhu.length));
        let tds = labels.map(k => (grouped[k].tds.reduce((a, b) => a + b, 0) / grouped[k].tds.length));
        let kekeruhan = labels.map(k => (grouped[k].kekeruhan.reduce((a, b) => a + b, 0) / grouped[k].kekeruhan.length));

        return { labels, ph, suhu, tds, kekeruhan };
    } else {
        const daysCount = range === '7days' ? 7 : 30;
        const limitDate = new Date();
        limitDate.setDate(limitDate.getDate() - daysCount);

        let filteredData = sortedData.filter(item => {
            if (!item.created_at) return false;
            return parseDate(item.created_at) >= limitDate;
        });

        let grouped = {};
        filteredData.forEach(item => {
            const d = parseDate(item.created_at);
            const dateKey = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });

            if (!grouped[dateKey]) {
                grouped[dateKey] = { ph: [], suhu: [], tds: [], kekeruhan: [] };
            }
            grouped[dateKey].ph.push(parseFloat(item.ph) || 0);
            grouped[dateKey].suhu.push(parseFloat(item.suhu) || 0);
            grouped[dateKey].tds.push(parseFloat(item.tds) || 0);
            grouped[dateKey].kekeruhan.push(parseFloat(item.kekeruhan) || 0);
        });

        let labels = Object.keys(grouped);
        let ph = labels.map(k => (grouped[k].ph.reduce((a, b) => a + b, 0) / grouped[k].ph.length));
        let suhu = labels.map(k => (grouped[k].suhu.reduce((a, b) => a + b, 0) / grouped[k].suhu.length));
        let tds = labels.map(k => (grouped[k].tds.reduce((a, b) => a + b, 0) / grouped[k].tds.length));
        let kekeruhan = labels.map(k => (grouped[k].kekeruhan.reduce((a, b) => a + b, 0) / grouped[k].kekeruhan.length));

        return { labels, ph, suhu, tds, kekeruhan };
    }
}

function filterChartTime(range) {
    activeTimeRange = range;

    ['btn-today', 'btn-7days', 'btn-30days'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) btn.classList.remove('active');
    });

    const activeBtn = document.getElementById(`btn-${range}`);
    if (activeBtn) activeBtn.classList.add('active');

    const processed = processSensorDataByRange(sensorData, range);

    if (charts.ph) {
        charts.ph.data.labels = processed.labels;
        charts.ph.data.datasets[0].data = processed.ph;
        charts.ph.update();
    }
    if (charts.suhu) {
        charts.suhu.data.labels = processed.labels;
        charts.suhu.data.datasets[0].data = processed.suhu;
        charts.suhu.update();
    }
    if (charts.tds) {
        charts.tds.data.labels = processed.labels;
        charts.tds.data.datasets[0].data = processed.tds;
        charts.tds.update();
    }
    if (charts.kekeruhan) {
        charts.kekeruhan.data.labels = processed.labels;
        charts.kekeruhan.data.datasets[0].data = processed.kekeruhan;
        charts.kekeruhan.update();
    }

    updateChartStats('ph', processed.ph, 'pH');
    updateChartStats('suhu', processed.suhu, '°C');
    updateChartStats('tds', processed.tds, 'ppm');
    updateChartStats('kekeruhan', processed.kekeruhan, 'NTU');
}

function startLiveWeatherClock() {
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const weatherTimeEl = document.getElementById('weather-time');
        if (weatherTimeEl) {
            weatherTimeEl.textContent = `${hours}:${minutes} WIB`;
        }
    }
    updateClock();
    setInterval(updateClock, 10000);
}

function fetchRealtimeData() {
    fetch('/api/sensor/latest')
        .then(response => response.ok ? response.json() : Promise.reject('Gagal mengambil data'))
        .then(data => {
            const latest = data.sensors ? data.sensors[0] : (Array.isArray(data) ? data[0] : data);

            if (latest && latest.ph !== undefined) {
                if (sensorData.length === 0 || sensorData[0].id !== latest.id) {
                    sensorData.unshift(latest);
                }

                updateSensorBadgesAndValues(latest);
                filterChartTime(activeTimeRange);
            }

            const now = new Date();
            document.getElementById('last-updated-time').textContent = now.toLocaleTimeString();
            document.getElementById('system-status-dot').className = 'badge bg-success rounded-circle p-1 me-2';
            document.getElementById('system-status-text').textContent = 'System Online';
        })
        .catch(() => {
            const now = new Date();
            document.getElementById('last-updated-time').textContent = now.toLocaleTimeString();
            document.getElementById('system-status-dot').className = 'badge bg-warning rounded-circle p-1 me-2';
            document.getElementById('system-status-text').textContent = 'Live Cache';
        });
}

function triggerAiRecommendation() {
    const btn = document.getElementById('btnAiAction');
    const btnText = document.getElementById('btnAiText');
    const standby = document.getElementById('ai-standby');
    const loading = document.getElementById('ai-loading');
    const contentText = document.getElementById('ai-content-text');
    const containerBox = document.getElementById('ai-container-box');

    if (!btn || !btnText || !standby || !loading || !contentText || !containerBox) return;

    btn.disabled = true;
    btnText.textContent = 'Mengevaluasi...';
    standby.style.display = 'none';
    contentText.style.display = 'none';
    loading.style.display = 'block';

    containerBox.classList.remove('ai-state-optimal', 'ai-state-warning');
    containerBox.removeAttribute('style');

    fetch("{{ route('dashboard.ai') }}", {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(res => {
            if (!res.ok) throw new Error('Network error status: ' + res.status);
            return res.json();
        })
        .then(data => {
            btn.disabled = false;
            btnText.textContent = 'Cek Tindakan AI';
            loading.style.display = 'none';
            contentText.style.display = 'block';

            const status = (data.status || data.state || '').toLowerCase();
            const rawRecommendation = data.recommendation || data.message || data.result || data.action || '';

            if (status === 'optimal' || status === 'normal' || status === 'safe') {
                containerBox.classList.add('ai-state-optimal');
                contentText.innerHTML = `<div class="d-flex align-items-center gap-2">
                    <i class="fas fa-circle-check fs-5 text-success"></i>
                    <div>${rawRecommendation || 'Kualitas air tambak dalam kondisi optimal dan aman.'}</div>
                </div>`;

            } else if (status === 'success' || status === 'warning' || status === 'critical' || status === 'danger') {
                containerBox.classList.add('ai-state-warning');

                let formatted = rawRecommendation
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                    .replace(/\n/g, '<br>');

                contentText.innerHTML = `<div class="mb-2 fw-bold d-flex align-items-center gap-2">
                    <i class="fas fa-triangle-exclamation"></i> Rekomendasi Penanganan AI:
                </div>` + formatted;

            } else if (data.error) {
                contentText.innerHTML = `<span class="text-danger small">⚠️ ${data.error}</span>`;
            } else {
                let formatted = rawRecommendation
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                    .replace(/\n/g, '<br>');
                contentText.innerHTML = formatted || 'Evaluasi AI telah selesai dilakukan.';
            }
        })
        .catch(err => {
            btn.disabled = false;
            btnText.textContent = 'Cek Tindakan AI';
            loading.style.display = 'none';
            contentText.style.display = 'block';
            contentText.innerHTML = '<span class="text-danger small">Gagal menghubungkan ke server AI ML (Port 5000). Pastikan Flask <code>app.py</code> aktif.</span>';
        });
}

function switchMobileChart(chartId) {
    document.querySelectorAll('.chart-card-wrapper').forEach(card => {
        card.classList.remove('active-mobile');
    });
    
    document.getElementById('chart-wrapper-' + chartId).classList.add('active-mobile');

    document.querySelectorAll('.btn-chart-tab').forEach(btn => {
        btn.classList.remove('active');
        btn.querySelector('.check-icon').classList.add('d-none');
    });

    const activeTab = document.getElementById('tab-' + chartId);
    activeTab.classList.add('active');
    activeTab.querySelector('.check-icon').classList.remove('d-none');
}

/* Single Inisialisasi DOMContentLoaded */
document.addEventListener('DOMContentLoaded', () => {
    initCharts();
    startLiveWeatherClock();

    const now = new Date();
    document.getElementById('last-updated-time').textContent = now.toLocaleTimeString();
    setInterval(fetchRealtimeData, 5000);

    // Auto scroll untuk widget hourly forecast
    const scrollContainer = document.querySelector('.hourly-scroll-container');
    if (scrollContainer) {
        let autoScrollTimer;
        function startAutoScroll() {
            autoScrollTimer = setInterval(() => {
                const maxScroll = scrollContainer.scrollWidth - scrollContainer.clientWidth;
                if (scrollContainer.scrollLeft >= maxScroll - 5) {
                    scrollContainer.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    scrollContainer.scrollBy({ left: 70, behavior: 'smooth' });
                }
            }, 2500);
        }
        startAutoScroll();
        scrollContainer.addEventListener('mouseenter', () => clearInterval(autoScrollTimer));
        scrollContainer.addEventListener('mouseleave', () => startAutoScroll());
    }
});
</script>
@endpush