@extends('layouts.app')

@push('styles')
<style>
/* =======================================================
   ANIMATION TOOLKIT & BEHAVIOR
======================================================= */
:root {
    --ai-easing: cubic-bezier(0.2, 0.8, 0.2, 1);
}

body {
    background-color: #e6f0ed !important;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Custom Scrollbar */
html,
body,
.hourly-scroll-container,
.custom-chart-tabs {
    scrollbar-width: thin;
    scrollbar-color: transparent transparent;
    transition: scrollbar-color 0.3s var(--ai-easing);
}

html:hover,
body:hover,
.hourly-scroll-container:hover,
.custom-chart-tabs:hover {
    scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
}

::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: transparent;
    border-radius: 10px;
    transition: background 0.3s var(--ai-easing);
}

:hover::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.4);
}

/* Kartu Utama & Micro-interactions */
.card-custom {
    background: #ffffff;
    border-radius: 20px;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    transition: transform 0.3s var(--ai-easing), box-shadow 0.3s var(--ai-easing);
}

.card-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05) !important;
}

/* =======================================================
   STATUS BADGES & PURE CSS DOTS
======================================================= */
.badge-normal,
.badge-warning-custom,
.badge-danger-custom {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 5px 14px;
    border-radius: 50rem;
    font-size: 0.8rem;
    font-weight: 600;
    line-height: 1;
    letter-spacing: 0.2px;
    white-space: nowrap;
    transition: all 0.3s var(--ai-easing);
}

.badge-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}

.badge-normal {
    background-color: #dcfce7;
    color: #15803d;
    border: 1px solid rgba(22, 163, 74, 0.15);
}

.badge-normal .badge-dot {
    background-color: #16a34a;
}

.badge-warning-custom {
    background-color: #fef08a;
    color: #854d0e;
    border: 1px solid rgba(202, 138, 4, 0.2);
}

.badge-warning-custom .badge-dot {
    background-color: #ca8a04;
}

.badge-danger-custom {
    background-color: #fecaca;
    color: #b91c1c;
    border: 1px solid rgba(220, 38, 38, 0.2);
}

.badge-danger-custom .badge-dot {
    background-color: #dc2626;
    animation: subtleCriticalPulse 1.6s ease-in-out infinite;
}

@keyframes subtleCriticalPulse {

    0%,
    100% {
        opacity: 1;
        box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4);
    }

    50% {
        opacity: 0.7;
        box-shadow: 0 0 0 6px rgba(220, 38, 38, 0);
    }
}

@keyframes statusPulse {

    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }

    50% {
        opacity: 0.5;
        transform: scale(0.85);
    }
}

#system-status-dot {
    animation: statusPulse 2s infinite var(--ai-easing);
}

/* Weather Card */
.card-weather-target {
    background: #85caf5;
    border-radius: 28px;
    border: none;
    padding: 1.25rem !important;
    color: #0c2d2a;
    transition: transform 0.3s var(--ai-easing);
}

.card-weather-target:hover {
    transform: translateY(-2px);
}

.weather-divider {
    border-top: 1px solid rgba(255, 255, 255, 0.5) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.5) !important;
}

.weather-border-x {
    border-left: 1px solid rgba(255, 255, 255, 0.5) !important;
    border-right: 1px solid rgba(255, 255, 255, 0.5) !important;
}

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
}

.hourly-pill {
    background: rgba(255, 255, 255, 0.45);
    border-radius: 16px;
    padding: 8px 10px;
    text-align: center;
    flex: 0 0 62px !important;
    width: 62px;
    transition: transform 0.25s var(--ai-easing), background 0.25s var(--ai-easing);
}

.hourly-pill:hover {
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 0.7);
}

/* AI Recommendation Styles */
.ai-icon-wrapper {
    width: 54px;
    height: 54px;
    background-color: #eff6ff;
    border: 2px solid #bfdbfe;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2563eb;
    font-size: 1.5rem;
    transition: all 0.3s var(--ai-easing);
    position: relative;
}

@keyframes aiRobotPulse {
    0% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.6), 0 0 0 0 rgba(59, 130, 246, 0.3);
    }

    50% {
        box-shadow: 0 0 0 8px rgba(59, 130, 246, 0.25), 0 0 0 16px rgba(59, 130, 246, 0);
    }

    100% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0), 0 0 0 0 rgba(59, 130, 246, 0);
    }
}

.ai-icon-wrapper.analyzing {
    animation: aiRobotPulse 1.2s var(--ai-easing) infinite;
    border-color: #3b82f6;
    background-color: #e0f2fe;
}

.badge-ml-engine {
    background-color: #e0e7ff;
    color: #4f46e5;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
    letter-spacing: 0.5px;
    vertical-align: middle;
}

.btn-ai-analyze {
    background: linear-gradient(135deg, #2563eb 0%, #0284c7 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    padding: 10px 22px;
    border-radius: 30px;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
    transition: all 0.35s var(--ai-easing);
}

.btn-ai-analyze:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
    color: #ffffff;
}

.btn-ai-loading {
    background: #475569 !important;
    color: #ffffff !important;
    cursor: not-allowed;
    box-shadow: none !important;
    transform: none !important;
}

.ai-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: #ffffff;
    border-radius: 50%;
    display: inline-block;
    animation: spin360 0.8s linear infinite;
}

@keyframes spin360 {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.ai-box-notice {
    position: relative;
    overflow: hidden;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    transition: all 0.4s var(--ai-easing);
    min-height: 200px;
}

.ai-scan-line {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 60%;
    background: linear-gradient(180deg, rgba(59, 130, 246, 0.18) 0%, rgba(59, 130, 246, 0.02) 100%);
    pointer-events: none;
    opacity: 0;
}

.ai-box-notice.analyzing .ai-scan-line {
    opacity: 1;
    animation: scanGradient 1.8s var(--ai-easing) infinite;
}

@keyframes scanGradient {
    0% {
        transform: translateY(-100%);
        opacity: 0;
    }

    20% {
        opacity: 0.8;
    }

    80% {
        opacity: 0.8;
    }

    100% {
        transform: translateY(220%);
        opacity: 0;
    }
}

.loading-steps-container {
    max-width: 420px;
    margin: 0 auto;
    text-align: left;
}

.loading-step {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 18px;
    color: #64748b;
    font-weight: 500;
    border-radius: 12px;
    margin-bottom: 8px;
    opacity: 0;
    transform: translateY(8px);
    transition: all 0.4s var(--ai-easing);
}

.loading-step.visible {
    opacity: 1;
    transform: translateY(0);
}

.loading-step.active {
    background: rgba(224, 242, 254, 0.7);
    color: #0f172a;
    font-weight: 600;
}

.loading-dot {
    width: 10px;
    height: 10px;
    background-color: #94a3b8;
    border-radius: 50%;
    transition: all 0.3s var(--ai-easing);
}

.loading-step.active .loading-dot {
    background-color: #2563eb;
    animation: dotPulse 1.2s var(--ai-easing) infinite;
}

@keyframes dotPulse {

    0%,
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.5);
    }

    50% {
        transform: scale(1.35);
        box-shadow: 0 0 0 8px rgba(37, 99, 235, 0);
    }
}

.ai-result-container {
    animation: resultSlideUp 0.6s var(--ai-easing) forwards;
}

@keyframes resultSlideUp {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }

    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* LAYOUT HYBRID AI DIAGNOSIS STATUS CARD */
.status-summary-card-custom {
    border-radius: 18px;
    padding: 20px 24px;
    margin-bottom: 22px;
    transition: all 0.3s ease;
}

.status-summary-card-custom.theme-green {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-left: 6px solid #22c55e;
}

.status-summary-card-custom.theme-orange {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-left: 6px solid #f59e0b;
}

.status-summary-card-custom.theme-red {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-left: 6px solid #ef4444;
}

.badge-diag-main {
    padding: 6px 16px;
    border-radius: 50rem;
    font-weight: 700;
    font-size: 0.88rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.badge-diag-main.theme-green {
    background-color: #dcfce7;
    color: #15803d;
    border: 1px solid #86efac;
}

.badge-diag-main.theme-orange {
    background-color: #fef3c7;
    color: #b45309;
    border: 1px solid #fcd34d;
}

.badge-diag-main.theme-red {
    background-color: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fca5a5;
}

.badge-sub-pill {
    padding: 6px 14px;
    border-radius: 50rem;
    font-weight: 600;
    font-size: 0.82rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.badge-dot-small {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.notice-warning-box {
    border-radius: 14px;
    padding: 14px 18px;
    margin-top: 14px;
    font-size: 0.92rem;
    line-height: 1.5;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.notice-warning-box.theme-green {
    background-color: #ffffff;
    border: 1px solid #dcfce7;
    color: #166534;
}

.notice-warning-box.theme-orange {
    background-color: #fff7ed;
    border: 1px solid #ffedd5;
    color: #9a3412;
}

.notice-warning-box.theme-red {
    background-color: #fff1f2;
    border: 1px solid #ffe4e6;
    color: #9f1239;
}

/* RECOMMENDATION CARDS */
.rec-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    opacity: 0;
    transform: translateY(16px);
    animation: cardStaggerIn 0.5s var(--ai-easing) forwards;
    transition: transform 0.3s var(--ai-easing), box-shadow 0.3s var(--ai-easing), border-left-width 0.25s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.rec-card-danger {
    border-left: 6px solid #ef4444;
}

.rec-card-warning {
    border-left: 6px solid #f59e0b;
}

.rec-card-info {
    border-left: 6px solid #3b82f6;
}

.rec-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.08) !important;
}

@keyframes cardStaggerIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

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

.btn-chart-filter {
    background-color: #ffffff;
    color: #64748b;
    border: 1px solid #e2e8f0;
    padding: 6px 18px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.25s var(--ai-easing);
}

.btn-chart-filter.active {
    background-color: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
}

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

/* DARK MODE OVERRIDES */
body.dark-mode {
    background-color: #0B1120 !important;
    color: #F8FAFC !important;
}

body.dark-mode .card-custom,
body.dark-mode .custom-tab-wrapper,
body.dark-mode .ai-method-card,
body.dark-mode .ai-reason-card {
    background-color: #111827 !important;
    border: 1px solid #1F2937 !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important;
}

body.dark-mode .status-summary-card-custom.theme-green {
    background: #064e3b !important;
    border-color: #047857 !important;
}

body.dark-mode .status-summary-card-custom.theme-orange {
    background: #78350f !important;
    border-color: #b45309 !important;
}

body.dark-mode .status-summary-card-custom.theme-red {
    background: #7f1d1d !important;
    border-color: #b91c1c !important;
}

body.dark-mode .ai-box-notice,
body.dark-mode .ai-intro-box {
    background: #0B1120 !important;
    border-color: #1F2937 !important;
    color: #F8FAFC !important;
}

body.dark-mode .rec-card {
    background: #111827 !important;
    border-color: #1F2937 !important;
}

body.dark-mode .rec-card h6 {
    color: #F8FAFC !important;
}

.custom-tab-wrapper {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 4px;
    display: flex;
    width: 100%;
    margin-bottom: 20px;
}

.custom-tab-btn {
    flex: 1;
    border: none;
    background: transparent;
    padding: 12px 20px;
    font-weight: 700;
    font-size: 0.95rem;
    color: #475569;
    border-radius: 10px;
    transition: all 0.2s ease;
    text-align: center;
}

.custom-tab-btn.active {
    background: #0d89ec;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(13, 137, 236, 0.25);
}

.ai-intro-box {
    background: #f0f7ff;
    border: 1px solid #dbeafe;
    border-radius: 16px;
    padding: 22px 26px;
    color: #1e293b;
    font-size: 0.95rem;
    line-height: 1.6;
}

.ai-method-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    height: 100%;
}

.ai-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.ai-section-label {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.6px;
    text-transform: uppercase;
    color: #0284c7;
    margin-bottom: 4px;
}

.ai-reason-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px 24px;
}

.num-badge-circle {
    width: 26px;
    height: 26px;
    background: #2563eb;
    color: #ffffff;
    border-radius: 50%;
    font-weight: 700;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

@keyframes fadeSlideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-tab-pane {
    animation: fadeSlideIn 0.4s var(--ai-easing) forwards;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pt-1" style="padding-bottom: 100px !important;">
    <div id="alertContainer" class="mb-3"></div>

    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <span class="text-uppercase fw-bold text-primary small"
                style="letter-spacing: 0.5px; font-size: 0.75rem;">WATER QUALITY MONITORING</span>
            <h2 class="fw-bold m-0 text-dark" style="font-size: 1.8rem;">Dashboard Monitoring</h2>
            <p class="text-secondary small m-0">Pantau kondisi kualitas air secara real-time.</p>
        </div>
        <div id="system-status-container"
            class="d-inline-flex flex-wrap align-items-center bg-white px-3 py-2 rounded-pill shadow-sm"
            style="width: fit-content;">
            <span id="system-status-dot" class="badge bg-success rounded-circle p-1 me-2"
                style="width: 8px; height: 8px; flex-shrink: 0;"></span>
            <span id="system-status-text" class="fw-bold small me-2 text-dark text-nowrap">System Online</span>
            <span class="text-muted small text-nowrap" style="font-size: 0.8rem;">Last updated: <span
                    id="last-updated-time">--:--:--</span></span>
        </div>
    </div>

    <!-- ROW 1: Top Cards -->
    <div class="row g-3 mb-4 align-items-stretch">
        <!-- KUALITAS AIR CARD -->
        <div class="col-lg-3">
            <div id="card-quality"
                class="card card-custom h-100 p-3 position-relative overflow-hidden shadow-sm d-flex flex-column justify-content-between"
                style="border-radius: 20px; background-color: #eef7f9; border: 1px solid #ccece6;">
                <div class="position-absolute"
                    style="width: 200px; height: 200px; background: rgba(14, 165, 233, 0.06); border-radius: 50%; top: -60px; right: -60px; z-index: 0;">
                </div>

                <div class="position-relative z-1 d-flex justify-content-between align-items-start">
                    <div class="d-flex flex-column gap-1">
                        <span class="fw-bold"
                            style="font-size: 0.75rem; letter-spacing: 0.5px; color: #52708f;">KUALITAS AIR</span>
                        <div id="quality-pill-wrapper" class="badge-normal shadow-sm">
                            <span id="quality-dot" class="badge-dot"></span>
                            <span id="quality-label">Normal</span>
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

        <!-- WEATHER CARD -->
        <div class="col-lg-3">
            <div class="card card-weather-target h-100 d-flex flex-column justify-content-between shadow-sm">
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

                <div class="d-flex align-items-center gap-3 my-2">
                    <i class="{{ $cuaca['icon'] ?? 'fas fa-cloud-sun text-warning' }}"
                        style="font-size: 3.2rem; filter: drop-shadow(0 3px 5px rgba(0,0,0,0.08));"></i>
                    <div>
                        <div class="fw-bold"
                            style="font-size: 2.8rem; line-height: 1; color: #0c2d2a; letter-spacing: -1px;">
                            {{ $cuaca['suhu'] ?? '--' }}°C
                        </div>
                        <div style="font-size: 0.85rem; color: #417280; font-weight: 500;">
                            {{ $cuaca['kondisi'] ?? '--' }}
                        </div>
                    </div>
                </div>

                <div class="row text-center py-2 weather-divider my-1">
                    <div class="col-4 px-1">
                        <div class="fw-bold" style="font-size: 1.05rem; color: #0c2d2a;">
                            {{ $cuaca['kelembaban'] ?? '--' }}%</div>
                        <div style="font-size: 0.65rem; color: #417280;">Kelembaban</div>
                    </div>
                    <div class="col-4 px-1 weather-border-x d-flex align-items-center justify-content-center">
                        <div id="weather-time" class="fw-bold" style="font-size: 0.9rem; color: #ef4444;">
                            {{ $cuaca['waktu'] ?? '--:--' }}</div>
                    </div>
                    <div class="col-4 px-1">
                        <div class="fw-bold" style="font-size: 1.05rem; color: #0c2d2a;">{{ $cuaca['angin'] ?? '--' }}
                        </div>
                        <div style="font-size: 0.65rem; color: #417280;">Angin · {{ $cuaca['arah_angin'] ?? '--' }}
                        </div>
                    </div>
                </div>

                <div class="hourly-scroll-container pt-1">
                    @if(isset($cuaca['hourly']) && is_iterable($cuaca['hourly']))
                    @foreach($cuaca['hourly'] as $item)
                    <div class="hourly-pill">
                        <div style="font-size: 0.68rem; color: #417280; font-weight: 600;">{{ $item['jam'] ?? '' }}
                        </div>
                        <i class="{{ $item['icon'] ?? 'fas fa-cloud' }} my-1 d-block" style="font-size: 0.85rem;"></i>
                        <div class="fw-bold" style="font-size: 0.85rem; color: #0c2d2a;">{{ $item['suhu'] ?? '' }}</div>
                        <div style="font-size: 0.6rem; color: #417280;">{{ $item['angin'] ?? '' }}</div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- SENSOR SUMMARY CARDS -->
        <div class="col-lg-6">
            <div class="d-flex flex-column justify-content-between h-100 gap-2">
                <div id="card-ph"
                    class="card card-custom px-4 py-3 d-flex flex-row justify-content-between align-items-center h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 icon-bg-blue d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-vial fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.85rem; margin-bottom: 2px;">pH</div>
                            <div class="d-flex align-items-baseline gap-1" style="line-height: 1;">
                                <h4 id="ph-value" class="fw-bold m-0 text-dark" style="font-size: 1.5rem;">--</h4>
                                <span class="text-secondary" style="font-size: 0.9rem;">pH</span>
                            </div>
                            <div class="text-secondary mt-1" style="font-size: 0.8rem;">Tingkat keasaman air</div>
                        </div>
                    </div>
                    <span id="ph-badge" class="badge-normal"><span class="badge-dot"></span> Normal</span>
                </div>

                <div id="card-suhu"
                    class="card card-custom px-4 py-3 d-flex flex-row justify-content-between align-items-center h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 icon-bg-green d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-temperature-half fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.85rem; margin-bottom: 2px;">Suhu</div>
                            <div class="d-flex align-items-baseline gap-1" style="line-height: 1;">
                                <h4 id="suhu-value" class="fw-bold m-0 text-dark" style="font-size: 1.5rem;">--</h4>
                                <span class="text-secondary" style="font-size: 0.9rem;">°C</span>
                            </div>
                            <div class="text-secondary mt-1" style="font-size: 0.8rem;">Temperatur air tambak</div>
                        </div>
                    </div>
                    <span id="suhu-badge" class="badge-normal"><span class="badge-dot"></span> Normal</span>
                </div>

                <div id="card-tds"
                    class="card card-custom px-4 py-3 d-flex flex-row justify-content-between align-items-center h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 icon-bg-cyan d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-filter fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.85rem; margin-bottom: 2px;">TDS</div>
                            <div class="d-flex align-items-baseline gap-1" style="line-height: 1;">
                                <h4 id="tds-value" class="fw-bold m-0 text-dark" style="font-size: 1.5rem;">--</h4>
                                <span class="text-secondary" style="font-size: 0.9rem;">ppm</span>
                            </div>
                            <div class="text-secondary mt-1" style="font-size: 0.8rem;">Total padatan terlarut</div>
                        </div>
                    </div>
                    <span id="tds-badge" class="badge-normal"><span class="badge-dot"></span> Normal</span>
                </div>

                <div id="card-kekeruhan"
                    class="card card-custom px-4 py-3 d-flex flex-row justify-content-between align-items-center h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 icon-bg-purple d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-water fs-5"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.85rem; margin-bottom: 2px;">Kekeruhan</div>
                            <div class="d-flex align-items-baseline gap-1" style="line-height: 1;">
                                <h4 id="kekeruhan-value" class="fw-bold m-0 text-dark" style="font-size: 1.5rem;">--
                                </h4>
                                <span class="text-secondary" style="font-size: 0.9rem;">NTU</span>
                            </div>
                            <div class="text-secondary mt-1" style="font-size: 0.8rem;">Tingkat kekeruhan air</div>
                        </div>
                    </div>
                    <span id="kekeruhan-badge" class="badge-normal"><span class="badge-dot"></span> Normal</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: HYBRID AI SMART RECOMMENDATION -->
    <div class="row mb-4">
        <div class="col-12">
            <div id="tips-card" class="card card-custom p-3 p-md-4" style="border-radius: 20px;">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 pb-4 mb-4"
                    style="border-bottom: 1px solid #f1f5f9;">
                    <div class="d-flex align-items-center gap-3 pe-md-4">
                        <div id="aiIconWrapper" class="ai-icon-wrapper flex-shrink-0">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1 text-dark d-flex align-items-center flex-wrap gap-2">
                                Hybrid AI Recommendation
                                <span class="badge-ml-engine">ML &amp; FUZZY SOP</span>
                            </h4>
                            <div class="text-muted" style="font-size: 0.9rem;">
                                Evaluasi kondisi kolam secara cerdas menggunakan perpaduan Machine Learning, Logic
                                Fuzzy, dan SOP Budidaya.
                            </div>
                        </div>
                    </div>

                    <div class="flex-shrink-0 mt-2 mt-md-0" style="min-width: fit-content;">
                        <button id="btnAiAction" onclick="triggerAiRecommendation()"
                            class="btn btn-ai-analyze d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-wand-magic-sparkles"></i>
                            <span>Analisis Hybrid AI</span>
                        </button>
                    </div>
                </div>

                <div id="ai-container-box" class="ai-box-notice p-4 mx-md-2 mb-2">
                    <div class="ai-scan-line"></div>

                    <div id="ai-standby" class="text-center py-4">
                        <div class="ai-icon-wrapper mx-auto mb-3"
                            style="width: 48px; height: 48px; font-size: 1.25rem;">
                            <i class="fas fa-wand-magic-sparkles"></i>
                        </div>
                        <p class="text-muted mb-0 mx-auto" style="max-width: 480px; line-height: 1.6;">
                            Klik tombol <strong>"Analisis Hybrid AI"</strong> untuk membaca parameter real-time,
                            mendapatkan status diagnosis, serta tindakan SOP terpilih.
                        </p>
                    </div>

                    <div id="ai-loading" style="display: none;" class="w-100 py-3">
                        <div class="loading-steps-container">
                            <div class="loading-step" id="step-1">
                                <div class="loading-dot"></div>
                                <span>Membaca sensor air real-time...</span>
                            </div>
                            <div class="loading-step" id="step-2">
                                <div class="loading-dot"></div>
                                <span>Menjalankan model Machine Learning...</span>
                            </div>
                            <div class="loading-step" id="step-3">
                                <div class="loading-dot"></div>
                                <span>Mengevaluasi Logic Fuzzy &amp; SOP Mitigasi...</span>
                            </div>
                        </div>
                    </div>

                    <div id="ai-content-text" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 3: MONITORING DATA HEADER & FILTER -->
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 mt-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Monitoring Data</h4>
            <small class="text-muted">Grafik historis parameter kualitas air</small>
        </div>
        <div
            class="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto justify-content-between justify-content-md-end">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" id="btn-today" class="btn btn-outline-primary btn-chart-filter active"
                    onclick="filterChartTime('today')">Today</button>
                <button type="button" id="btn-7days" class="btn btn-outline-primary btn-chart-filter"
                    onclick="filterChartTime('7days')">7 Days</button>
                <button type="button" id="btn-30days" class="btn btn-outline-primary btn-chart-filter"
                    onclick="filterChartTime('30days')">30 Days</button>
            </div>
        </div>
    </div>

    <!-- Mobile Chart Tabs -->
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
                        <small class="text-secondary" style="font-size: 0.75rem;">Perubahan tingkat pH dari waktu ke
                            waktu</small>
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
                        <small class="text-secondary" style="font-size: 0.75rem;">Perubahan total padatan terlarut dalam
                            air</small>
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
                        <small class="text-secondary" style="font-size: 0.75rem;">Perubahan tingkat kekeruhan
                            air</small>
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

let charts = {
    ph: null,
    suhu: null,
    tds: null,
    kekeruhan: null
};
let scoreChart = null;

function parseDate(dateStr) {
    if (!dateStr) return new Date();
    if (typeof dateStr === 'string') {
        const isoStr = dateStr.replace(' ', 'T');
        const d = new Date(isoStr);
        if (!isNaN(d.getTime())) return d;
    }
    const d = new Date(dateStr);
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

    const colorMap = {
        'ph': '#2563eb',
        'suhu': '#10b981',
        'tds': '#06b6d4',
        'kekeruhan': '#8b5cf6'
    };
    const themeColor = colorMap[key] || '#64748b';
    const unitHtml = unit ?
        ` <span style="color: ${themeColor}; font-weight: 600; font-size: 0.75rem;">${unit}</span>` : '';

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
        el.innerHTML = `<span class="badge-dot"></span> Normal`;
    } else if (status === 'Warning') {
        el.className = 'badge-warning-custom';
        el.innerHTML = `<span class="badge-dot"></span> Warning`;
    } else {
        el.className = 'badge-danger-custom';
        el.innerHTML = `<span class="badge-dot"></span> Critical`;
    }
}

function animateGaugeScore(targetScore, color) {
    if (!scoreChart) return;

    scoreChart.data.datasets[0].data = [targetScore, 100 - targetScore];
    scoreChart.data.datasets[0].backgroundColor[0] = color;
    scoreChart.options.animation = {
        duration: 900,
        easing: 'easeOutCubic'
    };
    scoreChart.update();

    const valueEl = document.getElementById('quality-value');
    if (valueEl) {
        let startVal = parseInt(valueEl.textContent) || 0;
        const duration = 900;
        const startTime = performance.now();

        function step(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easeProgress = 1 - Math.pow(1 - progress, 3);
            const currentVal = Math.round(startVal + (targetScore - startVal) * easeProgress);
            valueEl.textContent = currentVal;
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }
        requestAnimationFrame(step);
    }
}

function updateSensorBadgesAndValues(latest) {
    if (!latest) return;

    const phVal = parseFloat(latest.ph) || 0;
    const phEl = document.getElementById('ph-value');
    const miniPhEl = document.getElementById('mini-ph-val');
    if (phEl) phEl.textContent = phVal.toFixed(1);
    if (miniPhEl) miniPhEl.textContent = phVal.toFixed(1);
    updateBadgeUI('ph-badge', evalPh(phVal));

    const suhuVal = parseFloat(latest.suhu) || 0;
    const suhuEl = document.getElementById('suhu-value');
    const miniSuhuEl = document.getElementById('mini-suhu-val');
    if (suhuEl) suhuEl.textContent = suhuVal.toFixed(1);
    if (miniSuhuEl) miniSuhuEl.textContent = `${suhuVal.toFixed(1)}°`;
    updateBadgeUI('suhu-badge', evalSuhu(suhuVal));

    const tdsVal = parseFloat(latest.tds) || 0;
    const tdsEl = document.getElementById('tds-value');
    const miniTdsEl = document.getElementById('mini-tds-val');
    if (tdsEl) tdsEl.textContent = Math.round(tdsVal);
    if (miniTdsEl) miniTdsEl.textContent = Math.round(tdsVal);
    updateBadgeUI('tds-badge', evalTds(tdsVal));

    const ntuVal = parseFloat(latest.kekeruhan ?? latest.ntu) || 0;
    const kekeruhanEl = document.getElementById('kekeruhan-value');
    const miniKekeruhanEl = document.getElementById('mini-kekeruhan-val');
    if (kekeruhanEl) kekeruhanEl.textContent = ntuVal.toFixed(1);
    if (miniKekeruhanEl) miniKekeruhanEl.textContent = ntuVal.toFixed(1);
    updateBadgeUI('kekeruhan-badge', evalKekeruhan(ntuVal));

    const qualityScore = (latest.kualitas !== null && latest.kualitas !== undefined) ?
        Math.round(parseFloat(latest.kualitas)) :
        calculateGaugeScore(phVal, suhuVal, tdsVal, ntuVal);

    let qualityText = 'Normal';
    let qualityColor = '#10b981';
    let summary = 'Semua parameter dalam kondisi aman dan optimal.';
    let pillClass = 'badge-normal shadow-sm';

    if (qualityScore < 50) {
        qualityText = 'Critical';
        qualityColor = '#ef4444';
        summary = 'Kualitas air kritis, perlu tindakan pengondisian segera.';
        pillClass = 'badge-danger-custom shadow-sm';
    } else if (qualityScore < 75) {
        qualityText = 'Warning';
        qualityColor = '#f59e0b';
        summary = 'Kualitas air kurang stabil, pantau perubahan pH & suhu.';
        pillClass = 'badge-warning-custom shadow-sm';
    }

    const qualityPill = document.getElementById('quality-pill-wrapper');
    if (qualityPill) qualityPill.className = pillClass;

    const labelEl = document.getElementById('quality-label');
    if (labelEl) labelEl.textContent = qualityText;

    const badgeTextEl = document.getElementById('quality-badge-text');
    if (badgeTextEl) {
        badgeTextEl.textContent = qualityText.toUpperCase();
        badgeTextEl.style.color = qualityColor;
    }

    const summaryEl = document.getElementById('quality-summary-text');
    if (summaryEl) summaryEl.textContent = summary;

    animateGaugeScore(qualityScore, qualityColor);
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
            plugins: {
                tooltip: {
                    enabled: false
                }
            }
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

function createLineChart(id, label, initialLabels, dataPoints, borderColor, bgColor, threshold = null, thresholdColor =
    '#cbd5e1') {
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
        pointHoverBorderColor: '#ffffff'
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

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: initialLabels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            layout: {
                padding: {
                    top: 15,
                    bottom: 5
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#64748b',
                    bodyColor: borderColor,
                    borderColor: '#e2e8f0',
                    borderWidth: 1.5
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#94a3b8'
                    },
                    border: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        color: '#f8fafc',
                        borderDash: [4, 4]
                    },
                    ticks: {
                        color: '#94a3b8'
                    },
                    border: {
                        display: false
                    }
                }
            }
        },
        plugins: [crosshairPlugin]
    });
}

function initCharts() {
    initScoreGauge();
    charts.ph = createLineChart('phChart', 'pH', [], [], '#2563eb', 'rgba(37, 99, 235, 0.1)', 7.5, '#bfdbfe');
    charts.suhu = createLineChart('suhuChart', 'Suhu (°C)', [], [], '#10b981', 'rgba(16, 185, 129, 0.1)', 30,
        '#bbf7d0');
    charts.tds = createLineChart('tdsChart', 'TDS (ppm)', [], [], '#06b6d4', 'rgba(6, 182, 212, 0.1)', 500, '#fca5a5');
    charts.kekeruhan = createLineChart('kekeruhanChart', 'Kekeruhan (NTU)', [], [], '#8b5cf6',
        'rgba(139, 92, 246, 0.1)', 20, '#fca5a5');

    filterChartTime(activeTimeRange);

    if (sensorData.length > 0) {
        updateSensorBadgesAndValues(sensorData[0]);
    }
}

function processSensorDataByRange(rawData, range) {
    if (!rawData || rawData.length === 0) return {
        labels: [],
        ph: [],
        suhu: [],
        tds: [],
        kekeruhan: []
    };

    let sortedData = [...rawData].sort((a, b) => parseDate(a.created_at) - parseDate(b.created_at));
    const now = new Date();

    if (range === 'today') {
        const todayStr = now.toDateString();
        let todayData = sortedData.filter(item => parseDate(item.created_at).toDateString() === todayStr);
        let grouped = {};
        todayData.forEach(item => {
            const timeKey = formatTimeLabel(item.created_at);
            if (!grouped[timeKey]) grouped[timeKey] = {
                ph: [],
                suhu: [],
                tds: [],
                kekeruhan: []
            };
            grouped[timeKey].ph.push(parseFloat(item.ph) || 0);
            grouped[timeKey].suhu.push(parseFloat(item.suhu) || 0);
            grouped[timeKey].tds.push(parseFloat(item.tds) || 0);
            grouped[timeKey].kekeruhan.push(parseFloat(item.kekeruhan ?? item.ntu) || 0);
        });

        let labels = Object.keys(grouped);
        let ph = labels.map(k => grouped[k].ph.reduce((a, b) => a + b, 0) / grouped[k].ph.length);
        let suhu = labels.map(k => grouped[k].suhu.reduce((a, b) => a + b, 0) / grouped[k].suhu.length);
        let tds = labels.map(k => grouped[k].tds.reduce((a, b) => a + b, 0) / grouped[k].tds.length);
        let kekeruhan = labels.map(k => grouped[k].kekeruhan.reduce((a, b) => a + b, 0) / grouped[k].kekeruhan.length);

        return {
            labels,
            ph,
            suhu,
            tds,
            kekeruhan
        };
    } else {
        const daysCount = range === '7days' ? 7 : 30;
        const limitDate = new Date();
        limitDate.setDate(limitDate.getDate() - daysCount);
        let filteredData = sortedData.filter(item => parseDate(item.created_at) >= limitDate);
        let grouped = {};
        filteredData.forEach(item => {
            const dateKey = parseDate(item.created_at).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short'
            });
            if (!grouped[dateKey]) grouped[dateKey] = {
                ph: [],
                suhu: [],
                tds: [],
                kekeruhan: []
            };
            grouped[dateKey].ph.push(parseFloat(item.ph) || 0);
            grouped[dateKey].suhu.push(parseFloat(item.suhu) || 0);
            grouped[dateKey].tds.push(parseFloat(item.tds) || 0);
            grouped[dateKey].kekeruhan.push(parseFloat(item.kekeruhan ?? item.ntu) || 0);
        });

        let labels = Object.keys(grouped);
        let ph = labels.map(k => grouped[k].ph.reduce((a, b) => a + b, 0) / grouped[k].ph.length);
        let suhu = labels.map(k => grouped[k].suhu.reduce((a, b) => a + b, 0) / grouped[k].suhu.length);
        let tds = labels.map(k => grouped[k].tds.reduce((a, b) => a + b, 0) / grouped[k].tds.length);
        let kekeruhan = labels.map(k => grouped[k].kekeruhan.reduce((a, b) => a + b, 0) / grouped[k].kekeruhan.length);

        return {
            labels,
            ph,
            suhu,
            tds,
            kekeruhan
        };
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
        if (charts.ph.data.datasets[1]) charts.ph.data.datasets[1].data = Array(processed.labels.length).fill(7.5);
        charts.ph.update();
    }
    if (charts.suhu) {
        charts.suhu.data.labels = processed.labels;
        charts.suhu.data.datasets[0].data = processed.suhu;
        if (charts.suhu.data.datasets[1]) charts.suhu.data.datasets[1].data = Array(processed.labels.length).fill(30);
        charts.suhu.update();
    }
    if (charts.tds) {
        charts.tds.data.labels = processed.labels;
        charts.tds.data.datasets[0].data = processed.tds;
        if (charts.tds.data.datasets[1]) charts.tds.data.datasets[1].data = Array(processed.labels.length).fill(500);
        charts.tds.update();
    }
    if (charts.kekeruhan) {
        charts.kekeruhan.data.labels = processed.labels;
        charts.kekeruhan.data.datasets[0].data = processed.kekeruhan;
        if (charts.kekeruhan.data.datasets[1]) charts.kekeruhan.data.datasets[1].data = Array(processed.labels.length)
            .fill(20);
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
        const weatherTimeEl = document.getElementById('weather-time');
        if (weatherTimeEl) {
            weatherTimeEl.textContent =
                `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')} WIB`;
        }
    }
    updateClock();
    setInterval(updateClock, 10000);
}

function fetchRealtimeData() {
    fetch('/api/sensor/latest')
        .then(response => response.ok ? response.json() : Promise.reject('Gagal mengambil data'))
        .then(res => {
            const latest = res.data;
            if (latest && latest.ph !== undefined) {
                const isNew = sensorData.length === 0 ||
                    (latest.id && sensorData[0] && latest.id !== sensorData[0].id) ||
                    (latest.created_at && sensorData[0] && latest.created_at !== sensorData[0].created_at);

                if (isNew) {
                    sensorData.unshift(latest);
                    if (sensorData.length > 1440) sensorData.pop();
                    updateSensorBadgesAndValues(latest);
                    filterChartTime(activeTimeRange);
                } else {
                    updateSensorBadgesAndValues(latest);
                }
            }
            const updatedEl = document.getElementById('last-updated-time');
            if (updatedEl) updatedEl.textContent = new Date().toLocaleTimeString('id-ID', {
                hour12: false
            });

            const dotEl = document.getElementById('system-status-dot');
            if (dotEl) dotEl.className = 'badge bg-success rounded-circle p-1 me-2';

            const statusTextEl = document.getElementById('system-status-text');
            if (statusTextEl) statusTextEl.textContent = 'System Online';
        })
        .catch(() => {
            const updatedEl = document.getElementById('last-updated-time');
            if (updatedEl) updatedEl.textContent = new Date().toLocaleTimeString('id-ID', {
                hour12: false
            });

            const dotEl = document.getElementById('system-status-dot');
            if (dotEl) dotEl.className = 'badge bg-warning rounded-circle p-1 me-2';

            const statusTextEl = document.getElementById('system-status-text');
            if (statusTextEl) statusTextEl.textContent = 'Live Cache';
        });
}

/* =======================================================
   EXACT UI HYBRID AI RECOMMENDATION GENERATOR
======================================================= */
function generateRecommendationCardsHtml(data) {
    if (!data || data.status === 'error') {
        return `
            <div class="alert alert-warning rounded-4 mb-0" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> ${data?.message || 'Gagal memuat analisis Hybrid AI.'}
            </div>
        `;
    }

    const hybrid = data.hybrid_ai || {};
    const sopList = data.tindakan_sop || [];
    const adviceList = data.advice || data.recommendations || [];

    // 1. Dapatkan string status asli & Deteksi Status Offline ML
    const isMlOffline = hybrid.is_offline ||
        (hybrid.status_prediksi && hybrid.status_prediksi.toString().toLowerCase() === 'offline') ||
        (hybrid.status_prediksi_masa_depan && hybrid.status_prediksi_masa_depan.toString().toLowerCase() === 'offline');

    let rawFuzzy = (data.sensor_realtime_t?.status_saat_ini || hybrid.status_saat_ini || 'Baik').toString()
    .toLowerCase();
    let rawMl = isMlOffline ? 'offline' : (hybrid.status_prediksi_masa_depan || hybrid.status_prediksi || 'Baik')
        .toString().toLowerCase();

    // 2. Klasifikasi kondisi Fuzzy & ML
    let isFuzzyCritical = rawFuzzy.includes('buruk') || rawFuzzy.includes('critical') || rawFuzzy.includes('kritis') ||
        rawFuzzy.includes('danger');
    let isFuzzyWarning = rawFuzzy.includes('sedang') || rawFuzzy.includes('waspada') || rawFuzzy.includes('warning');
    let isFuzzyBaik = !isFuzzyCritical && !isFuzzyWarning;

    let isMlCritical = rawMl.includes('buruk') || rawMl.includes('critical') || rawMl.includes('kritis') || rawMl
        .includes('danger');
    let isMlWarning = rawMl.includes('sedang') || rawMl.includes('waspada') || rawMl.includes('warning');
    let isMlBaik = !isMlOffline && !isMlCritical && !isMlWarning;

    // 3. Evaluasi Matriks Kondisi Hybrid AI
    let theme = 'green';
    let diagText = 'Aman / Baik';
    let fuzzyBadgeText = isFuzzyBaik ? 'Baik' : (isFuzzyWarning ? 'Sedang / Waspada' : 'Buruk / Critical');
    let mlBadgeText = isMlOffline ? 'Offline' : (isMlBaik ? 'Baik' : (isMlWarning ? 'Sedang / Waspada' :
        'Buruk / Critical'));
    let narasi = '';

    if (isMlOffline) {
        // ML OFFLINE: Status Diagnosis, Tema, dan Peringatan Dini MURNI MENGIKUTI LOGIC FUZZY
        if (isFuzzyBaik) {
            theme = 'green';
            diagText = 'Aman / Baik';
            narasi =
                'Kondisi air saat ini (real-time) dalam kondisi BAIK. Model Machine Learning saat ini sedang Offline.';
        } else if (isFuzzyWarning) {
            theme = 'orange';
            diagText = 'Sedang / Waspada';
            narasi =
                'Kondisi air saat ini (real-time) dalam kondisi WASPADA / SEDANG. Model Machine Learning saat ini sedang Offline.';
        } else {
            theme = 'red';
            diagText = 'Kritis';
            narasi =
                'Kondisi air saat ini (real-time) dalam kondisi KRITIS. Model Machine Learning saat ini sedang Offline, segera lakukan tindakan mitigasi berdasarkan SOP!';
        }
    } else {
        // ML ONLINE: Evaluasi Kombinasi Hybrid AI
        if (isFuzzyBaik && isMlBaik) {
            theme = 'green';
            diagText = 'Aman / Baik';
            narasi = 'Kondisi air saat ini aman dan optimal, serta diprediksi tetap stabil hingga 1 jam ke depan.';
        } else if (isFuzzyBaik && !isMlBaik) {
            theme = 'orange';
            diagText = 'Sedang / Waspada';
            narasi =
                'Kondisi air saat ini (real-time) dalam kondisi BAIK, namun Machine Learning memprediksi adanya penurunan kualitas air dalam 1 jam ke depan.';
        } else if (isFuzzyWarning && isMlBaik) {
            theme = 'orange';
            diagText = 'Sedang / Waspada';
            narasi =
                'Kondisi air saat ini WASPADA, namun Machine Learning memprediksi tren pemulihan kualitas air dalam 1 jam ke depan.';
        } else if (isFuzzyCritical && isMlBaik) {
            theme = 'red';
            diagText = 'Kritis';
            narasi =
                'Kondisi air saat ini KRITIS, namun Machine Learning memprediksi tren pemulihan kualitas air dalam 1 jam ke depan.';
        } else if (isFuzzyWarning) {
            theme = 'orange';
            diagText = 'Sedang / Waspada';
            narasi = 'Kondisi air saat ini WASPADA dan diprediksi berisiko memburuk dalam 1 jam ke depan.';
        } else {
            theme = 'red';
            diagText = 'Kritis';
            narasi =
                'Kondisi air saat ini KRITIS dan diprediksi akan terus memburuk dalam 1 jam ke depan. Segera lakukan tindakan!';
        }
    }

    // Dynamic Badges & Theme Colors
    let diagIcon = theme === 'green' ? 'fa-shield-check' : 'fa-triangle-exclamation';
    let fuzzyStyle = isFuzzyBaik ? 'background-color: #dcfce7; color: #15803d;' : (isFuzzyWarning ?
        'background-color: #fef3c7; color: #b45309;' : 'background-color: #fee2e2; color: #b91c1c;');
    let fuzzyDotStyle = isFuzzyBaik ? 'background-color: #16a34a;' : (isFuzzyWarning ? 'background-color: #ca8a04;' :
        'background-color: #dc2626;');

    let mlStyle = isMlOffline ? 'background-color: #f1f5f9; color: #64748b;' : (isMlBaik ?
        'background-color: #dcfce7; color: #15803d;' : (isMlWarning ? 'background-color: #ffedd5; color: #c2410c;' :
            'background-color: #fee2e2; color: #b91c1c;'));
    let mlDotStyle = isMlOffline ? 'background-color: #94a3b8;' : (isMlBaik ? 'background-color: #16a34a;' : (
        isMlWarning ? 'background-color: #ea580c;' : 'background-color: #dc2626;'));

    // Format list rekomendasi / SOP
    let itemLevel = (theme === 'red') ? 'danger' : ((theme === 'orange') ? 'warning' : 'info');
    let rawItems = adviceList.length > 0 ? adviceList : sopList;

    const iconMap = {
        'activity': 'fa-chart-line',
        'calendar': 'fa-calendar-alt',
        'file-text': 'fa-file-alt',
        'shield-check': 'fa-shield-alt',
        'sliders': 'fa-sliders-h',
        'droplets': 'fa-tint',
        'alert-triangle': 'fa-exclamation-triangle',
        'alert-octagon': 'fa-exclamation-circle',
        'footprints': 'fa-shoe-prints',
        'refresh-cw': 'fa-sync-alt',
        'rotate-cw': 'fa-redo',
        'search': 'fa-search',
        'eye': 'fa-eye',
        'utensils': 'fa-utensils',
        'clock': 'fa-clock',
        'wrench': 'fa-wrench',
        'clipboard-list': 'fa-clipboard-list',
        'clipboard-check': 'fa-clipboard-check',
        'check-circle': 'fa-check-circle'
    };

    let combinedItems = rawItems.map((item, idx) => {
        let textVal = '';
        let iconVal = 'fa-clipboard-check';

        if (typeof item === 'string') {
            textVal = item;
        } else if (typeof item === 'object' && item !== null) {
            textVal = item.text || item.desc || '';
            if (item.icon) {
                const cleanIcon = item.icon.replace(/^fa-/, '').trim();
                iconVal = iconMap[cleanIcon] || (item.icon.startsWith('fa-') ? item.icon : `fa-${item.icon}`);
            }
        }

        return {
            num: item.num || `#${idx + 1}`,
            type: item.type || 'SOP ACTION',
            time: item.time || 'Segera',
            title: item.title || `Langkah Tindakan ${idx + 1}`,
            desc: textVal,
            level: item.level || itemLevel,
            icon: iconVal
        };
    });

    let sopListHtml = '';
    if (combinedItems.length === 0) {
        sopListHtml = `
            <div class="rec-card rec-card-info d-flex p-3 mb-3">
                <div class="d-flex flex-column align-items-center me-3" style="width: 50px;">
                    <div class="rounded-3 d-flex align-items-center justify-content-center mb-1 icon-bg-blue" style="width: 44px; height: 44px;">
                        <i class="fas fa-circle-check fs-5"></i>
                    </div>
                    <span class="text-muted fw-bold" style="font-size: 0.75rem;">#1</span>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge rounded-pill px-3 py-1" style="background-color: #e0f2fe; color: #0369a1; font-size: 0.65rem; font-weight: 700;">SISTEM NORMAL</span>
                        <span class="badge bg-light text-secondary rounded-pill px-3 py-1 border" style="font-size: 0.65rem; font-weight: 600;"><i class="far fa-clock me-1"></i> Rutin</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 1rem;">Kondisi Air Tambak Optimal</h6>
                    <p class="text-secondary mb-0" style="font-size: 0.85rem; line-height: 1.5;">Budidaya dilanjutkan secara normal. Lakukan pemantauan rutin berkala.</p>
                </div>
            </div>
        `;
    } else {
        combinedItems.forEach((item) => {
            const isDanger = item.level === 'danger';
            const isWarning = item.level === 'warning';
            const stripClass = isDanger ? 'rec-card-danger' : (isWarning ? 'rec-card-warning' :
                'rec-card-info');
            const badgeBg = isDanger ? 'background-color: #fee2e2; color: #b91c1c;' : (isWarning ?
                'background-color: #ffedd5; color: #c2410c;' : 'background-color: #e0f2fe; color: #0369a1;');
            const iconBg = isDanger ? 'background: #fee2e2; color: #ef4444;' : (isWarning ?
                'background: #ffedd5; color: #f97316;' : 'background: #e0f2fe; color: #0284c7;');

            sopListHtml += `
                <div class="rec-card ${stripClass} d-flex p-3 mb-3">
                    <div class="d-flex flex-column align-items-center me-3" style="width: 50px;">
                        <div class="rounded-3 d-flex align-items-center justify-content-center mb-1" style="width: 44px; height: 44px; ${iconBg}">
                            <i class="fas ${item.icon} fs-5"></i>
                        </div>
                        <span class="text-muted fw-bold" style="font-size: 0.75rem;">${item.num}</span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge rounded-pill px-3 py-1" style="${badgeBg} font-size: 0.65rem; letter-spacing: 0.5px; font-weight: 700;">${item.type}</span>
                            <span class="badge bg-light text-secondary rounded-pill px-3 py-1 border" style="font-size: 0.65rem; font-weight: 600;"><i class="far fa-clock me-1"></i> ${item.time}</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 1rem;">${item.title}</h6>
                        <p class="text-secondary mb-0" style="font-size: 0.85rem; line-height: 1.5;">${item.desc}</p>
                    </div>
                </div>
            `;
        });
    }

    return `
        <!-- 1. STATUS DIAGNOSIS & EARLY WARNING HEADER CARD -->
        <div class="status-summary-card-custom theme-${theme}">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="badge-diag-main theme-${theme}">
                    <i class="fas ${diagIcon}"></i> Status Diagnosis: ${diagText}
                </span>

                <span class="badge-sub-pill" style="${fuzzyStyle}">
                    <span class="badge-dot-small" style="${fuzzyDotStyle}"></span>
                    Logic Fuzzy (Saat Ini): ${fuzzyBadgeText}
                </span>

                <span class="badge-sub-pill" style="${mlStyle}">
                    <span class="badge-dot-small" style="${mlDotStyle}"></span>
                    Model ML (Prediksi +1 Jam): ${mlBadgeText}
                </span>
            </div>

            <div class="notice-warning-box theme-${theme}">
                <i class="fas fa-triangle-exclamation mt-1" style="font-size: 1.1rem; flex-shrink: 0;"></i>
                <div>
                    <strong>Peringatan Dini:</strong> ${narasi}
                </div>
            </div>
        </div>

        <!-- 2. CUSTOM FULL-WIDTH TAB SWITCHER -->
        <div class="custom-tab-wrapper">
            <button class="custom-tab-btn active" id="btn-tab-rekomendasi" onclick="switchAiTab('rekomendasi')">
                Rekomendasi
            </button>
            <button class="custom-tab-btn" id="btn-tab-carakerja" onclick="switchAiTab('carakerja')">
                Cara Kerja AI
            </button>
        </div>

        <!-- 3. TAB CONTENT PANES -->
        <!-- PANE REKOMENDASI -->
        <div id="pane-rekomendasi" class="animate-tab-pane">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold m-0 text-dark" style="font-size: 1.05rem;">Rekomendasi &amp; Langkah Mitigasi</h5>
                    <span class="badge bg-primary-subtle text-primary fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">${combinedItems.length}</span>
                </div>
            </div>
            ${sopListHtml}
        </div>

        <!-- PANE CARA KERJA AI -->
        <div id="pane-carakerja" class="animate-tab-pane" style="display: none;">
            <div class="ai-intro-box mb-4">
                Bayangkan kolammu dijaga oleh <strong>dua asisten</strong> sekaligus. Yang pertama mengecek keadaan air <strong>saat ini juga</strong>, yang kedua <strong>meramal</strong> akan seperti apa airnya nanti. Gabungan keduanya disebut <strong class="text-primary">Hybrid AI</strong> — supaya kamu tahu masalah hari ini sekaligus siap menghadapi masalah besok.
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6">
                    <div class="ai-method-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="ai-icon-box bg-info-subtle text-info">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div>
                                <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-1 fw-bold mb-1" style="font-size: 0.65rem;">REAL-TIME · T</span>
                                <h5 class="fw-bold text-dark m-0" style="font-size: 1.1rem;">Fuzzy / Rule-Based</h5>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="ai-section-label">FUNGSI</div>
                            <p class="text-secondary m-0" style="font-size: 0.85rem; line-height: 1.5;">
                                Memeriksa nilai sensor saat ini (pH, suhu, TDS, kekeruhan) terhadap ambang batas baku budidaya.
                            </p>
                        </div>

                        <div class="mb-3">
                            <div class="ai-section-label">CARA KERJA</div>
                            <p class="text-secondary m-0" style="font-size: 0.85rem; line-height: 1.5;">
                                Mengevaluasi kondisi detik ini ke dalam kategori Normal, Warning, atau Critical.
                            </p>
                        </div>

                        <div>
                            <div class="ai-section-label">KEUNGGULAN</div>
                            <p class="text-secondary m-0" style="font-size: 0.85rem; line-height: 1.5;">
                                Sangat cepat, transparan, dan pasti — kondisi kritis langsung terdeteksi tanpa ragu.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="ai-method-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="ai-icon-box bg-primary-subtle text-primary">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold mb-1" style="font-size: 0.65rem;">PREDIKSI · T+1</span>
                                <h5 class="fw-bold text-dark m-0" style="font-size: 1.1rem;">Machine Learning</h5>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="ai-section-label">FUNGSI</div>
                            <p class="text-secondary m-0" style="font-size: 0.85rem; line-height: 1.5;">
                                Memprediksi nilai parameter sensor satu langkah ke depan (t+1) berdasarkan model regresi.
                            </p>
                        </div>

                        <div class="mb-3">
                            <div class="ai-section-label">CARA KERJA</div>
                            <p class="text-secondary m-0" style="font-size: 0.85rem; line-height: 1.5;">
                                Menganalisis tren data untuk memperkirakan pergerakan kualitas air di masa mendatang.
                            </p>
                        </div>

                        <div>
                            <div class="ai-section-label">KEUNGGULAN</div>
                            <p class="text-secondary m-0" style="font-size: 0.85rem; line-height: 1.5;">
                                Memberikan peringatan dini (proaktif) sebelum masalah benar-benar terjadi di tambak.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ai-reason-card">
                <h6 class="fw-bold text-dark mb-3" style="font-size: 0.95rem;">Alasan keduanya digabungkan</h6>
                
                <div class="d-flex align-items-start gap-3 mb-3">
                    <span class="num-badge-circle">1</span>
                    <div>
                        <strong class="text-dark d-block mb-1" style="font-size: 0.9rem;">Responsif sekaligus proaktif</strong>
                        <p class="text-secondary mb-0" style="font-size: 0.83rem; line-height: 1.5;">
                            Fuzzy menangani kondisi saat ini agar penanganan darurat tidak terlambat, sedangkan ML mengantisipasi kondisi masa depan agar pencegahan bisa lebih awal.
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3">
                    <span class="num-badge-circle">2</span>
                    <div>
                        <strong class="text-dark d-block mb-1" style="font-size: 0.9rem;">Pengambilan keputusan lebih aman (Safety First)</strong>
                        <p class="text-secondary mb-0" style="font-size: 0.83rem; line-height: 1.5;">
                            Penggabungan (Fusion Rule) memastikan mitigasi darurat tetap diprioritaskan jika kondisi real-time sudah memburuk, terlepas dari hasil prediksi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function switchAiTab(tabName) {
    const btnRekomendasi = document.getElementById('btn-tab-rekomendasi');
    const btnCaraKerja = document.getElementById('btn-tab-carakerja');
    const paneRekomendasi = document.getElementById('pane-rekomendasi');
    const paneCaraKerja = document.getElementById('pane-carakerja');

    if (!paneRekomendasi || !paneCaraKerja) return;

    paneRekomendasi.classList.remove('animate-tab-pane');
    paneCaraKerja.classList.remove('animate-tab-pane');

    void paneRekomendasi.offsetWidth;
    void paneCaraKerja.offsetWidth;

    if (tabName === 'rekomendasi') {
        if (btnRekomendasi) btnRekomendasi.classList.add('active');
        if (btnCaraKerja) btnCaraKerja.classList.remove('active');
        paneRekomendasi.style.display = 'block';
        paneCaraKerja.style.display = 'none';
        paneRekomendasi.classList.add('animate-tab-pane');
    } else {
        if (btnCaraKerja) btnCaraKerja.classList.add('active');
        if (btnRekomendasi) btnRekomendasi.classList.remove('active');
        paneCaraKerja.style.display = 'block';
        paneRekomendasi.style.display = 'none';
        paneCaraKerja.classList.add('animate-tab-pane');
    }
}

function triggerAiRecommendation() {
    const btn = document.getElementById('btnAiAction');
    const iconWrapper = document.getElementById('aiIconWrapper');
    const standby = document.getElementById('ai-standby');
    const loading = document.getElementById('ai-loading');
    const contentText = document.getElementById('ai-content-text');
    const containerBox = document.getElementById('ai-container-box');

    if (!btn || !loading || !contentText || !containerBox) return;

    btn.disabled = true;
    btn.classList.remove('btn-ai-analyze');
    btn.classList.add('btn-ai-loading');
    btn.innerHTML = `<span class="ai-spinner me-2"></span><span>Menganalisis…</span>`;

    if (iconWrapper) iconWrapper.classList.add('analyzing');
    containerBox.classList.add('analyzing');
    if (standby) standby.style.display = 'none';
    contentText.style.display = 'none';
    contentText.innerHTML = '';
    loading.style.display = 'block';

    const steps = [
        document.getElementById('step-1'),
        document.getElementById('step-2'),
        document.getElementById('step-3')
    ];
    steps.forEach(s => {
        if (s) s.classList.remove('visible', 'active');
    });

    setTimeout(() => {
        if (steps[0]) steps[0].classList.add('visible', 'active');
    }, 100);
    setTimeout(() => {
        if (steps[0]) steps[0].classList.remove('active');
        if (steps[1]) steps[1].classList.add('visible', 'active');
    }, 700);
    setTimeout(() => {
        if (steps[1]) steps[1].classList.remove('active');
        if (steps[2]) steps[2].classList.add('visible', 'active');
    }, 1300);
    setTimeout(() => {
        executeAiFetch();
    }, 1900);

    function executeAiFetch() {
        fetch("{{ route('dashboard.ai') }}", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.ok ? res.json() : Promise.reject('Network response was not ok'))
            .then(data => {
                finishAiAnalysis(data);
            })
            .catch(() => {
                finishAiAnalysis({
                    status: 'error',
                    message: 'Gagal terhubung ke modul AI. Pastikan server terhubung dengan baik.'
                });
            });
    }

    function finishAiAnalysis(data) {
        btn.disabled = false;
        btn.classList.remove('btn-ai-loading');
        btn.classList.add('btn-ai-analyze');
        btn.innerHTML = `<i class="fas fa-wand-magic-sparkles"></i><span>Analisis Hybrid AI</span>`;

        if (iconWrapper) iconWrapper.classList.remove('analyzing');
        containerBox.classList.remove('analyzing');
        loading.style.display = 'none';

        contentText.style.display = 'block';
        contentText.className = 'ai-result-container';
        contentText.innerHTML = generateRecommendationCardsHtml(data);
    }
}

function switchMobileChart(chartId) {
    document.querySelectorAll('.chart-card-wrapper').forEach(card => card.classList.remove('active-mobile'));
    const targetChartWrapper = document.getElementById('chart-wrapper-' + chartId);
    if (targetChartWrapper) targetChartWrapper.classList.add('active-mobile');

    document.querySelectorAll('.btn-chart-tab').forEach(btn => {
        btn.classList.remove('active');
        const icon = btn.querySelector('.check-icon');
        if (icon) icon.classList.add('d-none');
    });

    const activeTab = document.getElementById('tab-' + chartId);
    if (activeTab) {
        activeTab.classList.add('active');
        const activeIcon = activeTab.querySelector('.check-icon');
        if (activeIcon) activeIcon.classList.remove('d-none');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initCharts();
    startLiveWeatherClock();

    const timeEl = document.getElementById('last-updated-time');
    if (timeEl) timeEl.textContent = new Date().toLocaleTimeString('id-ID', {
        hour12: false
    });

    setInterval(fetchRealtimeData, 5000);

    const scrollContainer = document.querySelector('.hourly-scroll-container');
    if (scrollContainer) {
        let autoScrollTimer;

        function startAutoScroll() {
            autoScrollTimer = setInterval(() => {
                const maxScroll = scrollContainer.scrollWidth - scrollContainer.clientWidth;
                if (scrollContainer.scrollLeft >= maxScroll - 5) {
                    scrollContainer.scrollTo({
                        left: 0,
                        behavior: 'smooth'
                    });
                } else {
                    scrollContainer.scrollBy({
                        left: 70,
                        behavior: 'smooth'
                    });
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