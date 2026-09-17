@extends('layouts.app')

@push('head')
<!-- Google Fonts Plus Jakarta Sans & Bootstrap Icons -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('content')
<style>
* {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
}

/* ================= CSS VARIABLES ================= */
:root {
    --bg-main: #D6F0EE;
    --bg-card: #FFFFFF;
    --bg-input: #FFFFFF;
    --text-title: #0F172A;
    --text-desc: #64748B;
    --text-main: #0D2626;
    --border-card: rgba(157, 212, 209, 0.45);
    --border-input: #E2E8F0;
    --table-header-bg: #9DD4D1;
    --table-header-text: #0D2626;
    --dropdown-bg: #FFFFFF;
    --dropdown-hover: #F1F5F9;
}

/* DEKLARASI DARK MODE */
html.dark, body.dark, 
html.dark-mode, body.dark-mode, 
[data-bs-theme="dark"], [data-theme="dark"],
[data-bs-theme="dark"] body, [data-theme="dark"] body {
    --bg-main: #0F172A !important;
    --bg-card: #1E293B !important;
    --bg-input: #0F172A !important;
    --text-title: #F8FAFC !important;
    --text-desc: #94A3B8 !important;
    --text-main: #F1F5F9 !important;
    --border-card: #334155 !important;
    --border-input: #334155 !important;
    --table-header-bg: #1E3A8A !important;
    --table-header-text: #FFFFFF !important;
    --dropdown-bg: #1E293B !important;
    --dropdown-hover: #334155 !important;
}

body {
    background-color: var(--bg-main) !important;
    color: var(--text-main) !important;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.text-main-dark { color: var(--text-title) !important; }
.text-slate { color: var(--text-desc) !important; }

/* ================= TIPOGRAFI HEADER ================= */
.page-title {
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--text-title) !important;
    letter-spacing: -0.5px;
    line-height: 1.2;
    margin-bottom: 4px;
}
.page-desc {
    font-size: 0.85rem;
    color: var(--text-desc) !important;
    margin-bottom: 2px;
    font-weight: 400;
}
.page-subdesc {
    font-size: 0.74rem;
    color: var(--text-desc) !important;
    font-weight: 400;
    opacity: 0.8;
}

/* ================= TOMBOL DROPDOWN EXPORT ================= */
.dropdown-export { position: relative; display: inline-block; }
.btn-export-main {
    background-color: #10B981;
    color: #ffffff !important;
    border: none;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 9px 18px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background-color 0.2s ease;
    cursor: pointer;
}
.btn-export-main:hover { background-color: #059669; }

.export-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 110%;
    background-color: var(--dropdown-bg) !important;
    min-width: 200px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    border-radius: 10px;
    border: 1px solid var(--border-card) !important;
    padding: 6px 0;
    z-index: 100;
}
.export-menu.show { display: block; }
.export-item {
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-title) !important;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 600;
    transition: background-color 0.15s ease;
    cursor: pointer;
}
.export-item:hover { background-color: var(--dropdown-hover) !important; }

/* ================= KARTU SUMMARY & WARNA TEMA ================= */
.card-summary {
    background-color: var(--bg-card) !important;
    border-radius: 16px;
    padding: 16px 18px;
    border: 1px solid var(--border-card) !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.summary-label {
    font-size: 0.67rem;
    font-weight: 700;
    color: var(--text-desc) !important;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 8px;
    line-height: 1.3;
}
.summary-value-row {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 4px;
}
.summary-icon {
    width: 26px;
    height: 26px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.summary-num {
    font-size: 1.45rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    line-height: 1.1;
    margin: 0;
}
.summary-sub {
    font-size: 0.72rem;
    color: var(--text-desc) !important;
    font-weight: 400;
    margin-top: 4px;
}

/* WARNA KARTU SUMMARY (LIGHT MODE) */
.color-total { color: #1A6DC4; }
.color-ph { color: #1A6DC4; }
.color-suhu { color: #10B981; }
.color-tds { color: #0D9488; }
.color-kekeruhan { color: #8B5CF6; }
.color-kualitas { color: #10B981; }

/* WARNA KARTU SUMMARY (DARK MODE) */
html.dark .color-total, [data-bs-theme="dark"] .color-total, body.dark .color-total { color: #38BDF8 !important; }
html.dark .color-ph, [data-bs-theme="dark"] .color-ph, body.dark .color-ph { color: #38BDF8 !important; }
html.dark .color-suhu, [data-bs-theme="dark"] .color-suhu, body.dark .color-suhu { color: #34D399 !important; }
html.dark .color-tds, [data-bs-theme="dark"] .color-tds, body.dark .color-tds { color: #2DD4BF !important; }
html.dark .color-kekeruhan, [data-bs-theme="dark"] .color-kekeruhan, body.dark .color-kekeruhan { color: #C084FC !important; }
html.dark .color-kualitas, [data-bs-theme="dark"] .color-kualitas, body.dark .color-kualitas { color: #34D399 !important; }

/* ADJUSTMENT IKON SVG DI DARK MODE */
html.dark .summary-icon svg path[stroke="#1E293B"],
html.dark .summary-icon svg rect[stroke="#1E293B"],
html.dark .summary-icon svg circle[stroke="#1E293B"],
[data-bs-theme="dark"] .summary-icon svg path[stroke="#1E293B"],
[data-bs-theme="dark"] .summary-icon svg rect[stroke="#1E293B"],
[data-bs-theme="dark"] .summary-icon svg circle[stroke="#1E293B"] {
    stroke: #94A3B8 !important;
}
html.dark .summary-icon svg rect[fill="#F8FAFC"],
[data-bs-theme="dark"] .summary-icon svg rect[fill="#F8FAFC"] {
    fill: #334155 !important;
}

/* ================= FILTER SECTION ================= */
.card-filter {
    background-color: var(--bg-card) !important;
    border-radius: 16px;
    border: 1px solid var(--border-card) !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
    padding: 22px 24px;
    margin-bottom: 24px;
}
.filter-title {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--text-desc) !important;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    display: block;
    margin-bottom: 14px;
}
.form-label-custom {
    font-size: 0.76rem;
    font-weight: 500;
    color: var(--text-desc) !important;
    margin-bottom: 6px;
}
.form-control-custom, .form-select-custom {
    font-size: 0.85rem;
    background-color: var(--bg-input) !important;
    border: 1px solid var(--border-input) !important;
    border-radius: 8px;
    padding: 8px 12px;
    color: var(--text-title) !important;
    font-weight: 500;
}
.btn-filter-apply {
    background-color: #1A6DC4;
    color: #ffffff !important;
    font-size: 0.85rem;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    padding: 9px 16px;
    width: 100%;
    transition: background-color 0.2s ease;
}
.btn-filter-apply:hover { background-color: #125296; }

/* ================= TABEL DATA MONITORING ================= */
.card-table {
    background-color: var(--bg-card) !important;
    border-radius: 16px;
    border: 1px solid var(--border-card) !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
    overflow: hidden;
    margin-bottom: 28px;
}
.card-header-table { background-color: var(--bg-card) !important; }
.table-custom-header {
    background-color: var(--table-header-bg) !important;
    color: var(--table-header-text) !important;
    border: none;
}
.table-custom-header th {
    font-size: 0.82rem;
    padding: 13px 18px;
    font-weight: 700;
    letter-spacing: 0.2px;
    border: none;
    color: var(--table-header-text) !important;
}
.table-custom-body tr {
    background-color: var(--bg-card) !important;
    color: var(--text-title) !important;
    border-bottom: 1px solid var(--border-card) !important;
}
.select-outline {
    background-color: var(--bg-input) !important;
    border: 1.5px solid #30A1CE !important;
    font-size: 0.82rem;
    font-weight: 600;
    border-radius: 8px;
    color: var(--text-title) !important;
    width: auto !important;
    min-width: 150px;
}

/* BADGE KUALITAS AIR */
.badge-kualitas-baik { background-color: #DCFCE7; color: #16A34A; font-weight: 700; padding: 4px 16px; border-radius: 50px; font-size: 0.78rem; display: inline-block; }
.badge-kualitas-cukup { background-color: #FEF3C7; color: #D97706; font-weight: 700; padding: 4px 16px; border-radius: 50px; font-size: 0.78rem; display: inline-block; }
.badge-kualitas-buruk { background-color: #FEE2E2; color: #DC2626; font-weight: 700; padding: 4px 16px; border-radius: 50px; font-size: 0.78rem; display: inline-block; }
.status-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-right: 6px; }

/* PAGINATION */
.pagination-custom .page-btn {
    border: 1.5px solid var(--border-card);
    background-color: var(--bg-input);
    color: var(--text-title);
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}
.pagination-custom .page-btn.active {
    background-color: #1A6DC4;
    color: #ffffff !important;
    border-color: #1A6DC4;
}
.pagination-custom .page-btn.disabled { opacity: 0.4; cursor: not-allowed; }

/* ================= MEDIA QUERIES (OPTIMASI MOBILE) ================= */
@media (max-width: 767.98px) {
    .page-title { font-size: 1.5rem; }
    
    .dropdown-export, .btn-export-main, .export-menu {
        width: 100%;
    }
    
    .card-summary { padding: 12px 14px; }
    .summary-label { font-size: 0.6rem; letter-spacing: 0.2px; }
    .summary-num { font-size: 1.15rem; }
    .summary-icon { width: 22px; height: 22px; }
    .summary-icon svg { width: 20px; height: 20px; }
    .summary-sub { font-size: 0.65rem; }
    
    .card-header-table { padding-left: 1rem !important; padding-right: 1rem !important; }
}
</style>

<div class="container-fluid py-4" style="min-height: 100vh;">
    <div class="container">

        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 screen-only">
            <div>
                <h2 class="page-title">Laporan Kualitas Air</h2>
                <p class="page-desc">Pantau dan analisis data kualitas air yang telah direkam oleh sistem.</p>
                <div class="page-subdesc">Menampilkan seluruh data monitoring yang tersimpan</div>
            </div>
            
            <div class="dropdown-export">
                <button type="button" class="btn-export-main shadow-sm" onclick="toggleExportMenu(event)">
                    <i class="bi bi-download"></i> Export Data <i class="bi bi-chevron-down ms-1" style="font-size: 0.75rem;"></i>
                </button>
                <div class="export-menu" id="exportMenu">
                    <div class="export-item" onclick="exportTableToExcel()">
                        <i class="bi bi-file-earmark-excel text-success fs-5"></i>
                        <div>
                            <div>Export ke Excel</div>
                            <small class="text-muted fw-normal">Format spreadsheet (.xlsx)</small>
                        </div>
                    </div>
                    <div class="export-item" onclick="exportToPDF()">
                        <i class="bi bi-file-earmark-pdf text-danger fs-5"></i>
                        <div>
                            <div>Export ke PDF</div>
                            <small class="text-muted fw-normal">Format dokumen cetak (.pdf)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6 Kartu Summary -->
        <div class="row g-2 g-md-3 mb-4 screen-only">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card-summary">
                    <div class="summary-label">TOTAL DATA</div>
                    <div class="summary-value-row">
                        <div class="summary-icon">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="20" height="20" rx="4" stroke="#1E293B" stroke-width="1.8" fill="#F8FAFC"/>
                                <path d="M6.5 17V13" stroke="#F59E0B" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M12 17V8" stroke="#10B981" stroke-width="2.5" stroke-linecap="round"/>
                                <path d="M17.5 17V10" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h4 class="summary-num color-total" id="summary_total">0</h4>
                    </div>
                    <div class="summary-sub">Data tersimpan</div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="card-summary">
                    <div class="summary-label">RATA-RATA PH</div>
                    <div class="summary-value-row">
                        <div class="summary-icon">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 3H15M10 3V8L6 18C5.2 19.6 6.4 21 8 21H16C17.6 21 18.8 19.6 18 18L14 8V3" stroke="#1E293B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7.5 16.5C9.5 15.5 14.5 15.5 16.5 16.5L17.5 18.5C17.8 19.2 17.2 20 16.5 20H7.5C6.8 20 6.2 19.2 6.5 18.5L7.5 16.5Z" fill="#2DD4BF" fill-opacity="0.65"/>
                                <circle cx="11" cy="18" r="0.8" fill="#0F766E"/>
                                <circle cx="13.5" cy="17" r="1" fill="#0F766E"/>
                            </svg>
                        </div>
                        <h4 class="summary-num color-ph" id="summary_ph">-</h4>
                    </div>
                    <div class="summary-sub">Rata-rata keseluruhan</div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="card-summary">
                    <div class="summary-label">RATA-RATA SUHU</div>
                    <div class="summary-value-row">
                        <div class="summary-icon">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 14.5V5C10 3.9 10.9 3 12 3C13.1 3 14 3.9 14 5V14.5C15.5 15.6 16 17.5 15.2 19.2C14.5 20.8 12.8 21.8 11 21.6C9.2 21.4 7.8 19.9 7.8 18.1C7.8 16.7 8.6 15.4 10 14.5Z" stroke="#1E293B" stroke-width="1.8" stroke-linejoin="round"/>
                                <circle cx="12" cy="18" r="2.5" fill="#EF4444"/>
                                <path d="M11.5 9V16H12.5V9C12.5 8.7 12.3 8.5 12 8.5C11.7 8.5 11.5 8.7 11.5 9Z" fill="#EF4444"/>
                            </svg>
                        </div>
                        <h4 class="summary-num color-suhu" id="summary_suhu">-</h4>
                    </div>
                    <div class="summary-sub">Rata-rata keseluruhan</div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="card-summary">
                    <div class="summary-label">RATA-RATA TDS</div>
                    <div class="summary-value-row">
                        <div class="summary-icon">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 3L21 8L11 18C9.5 19.5 7 20 5.5 18.5C4 17 4.5 14.5 6 13L16 3Z" stroke="#1E293B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 15L14 9L17 12L11 18C9.5 19.5 7 20 5.5 18.5C5.1 18.1 5 17.7 5.2 17.2L8 15Z" fill="#14B8A6" fill-opacity="0.6"/>
                                <path d="M15 2L22 9" stroke="#1E293B" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h4 class="summary-num color-tds" id="summary_tds">-</h4>
                    </div>
                    <div class="summary-sub">Rata-rata keseluruhan</div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="card-summary">
                    <div class="summary-label">RATA-RATA KEKERUHAN</div>
                    <div class="summary-value-row">
                        <div class="summary-icon">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="9" stroke="#1E293B" stroke-width="1.8"/>
                                <path d="M6 13C8 11 9.5 11 11.5 13C13.5 15 15 15 17 13C17.6 12.4 18 12 18.5 12V17C18.5 19 16 20.5 12 20.5C8 20.5 5.5 19 5.5 17V13.5L6 13Z" fill="#38BDF8" fill-opacity="0.5"/>
                                <path d="M6 13C8 11 9.5 11 11.5 13C13.5 15 15 15 17 13" stroke="#0284C7" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h4 class="summary-num color-kekeruhan" id="summary_kekeruhan">-</h4>
                    </div>
                    <div class="summary-sub">Rata-rata keseluruhan</div>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <div class="card-summary">
                    <div class="summary-label">KUALITAS AIR</div>
                    <div class="summary-value-row">
                        <div class="summary-icon">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="20" height="20" rx="4" fill="#16A34A"/>
                                <path d="M7 12.5L10.5 16L17 8.5" stroke="#FFFFFF" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h4 class="summary-num color-kualitas" id="summary_kualitas">-</h4>
                    </div>
                    <div class="summary-sub" id="summary_kualitas_sub">-</div>
                </div>
            </div>
        </div>

        <!-- Filter Data Card -->
        <div class="card-filter screen-only">
            <span class="filter-title">FILTER DATA</span>
            <div class="row align-items-end g-3">
                <div class="col-md-3">
                    <label class="form-label-custom">Dari Tanggal</label>
                    <input type="date" id="start_date" class="form-control form-control-custom shadow-none">
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Sampai Tanggal</label>
                    <input type="date" id="end_date" class="form-control form-control-custom shadow-none">
                </div>
                <div class="col-md-3">
                    <label class="form-label-custom">Status</label>
                    <select id="status_filter" class="form-select form-select-custom shadow-none">
                        <option value="all">Semua</option>
                        <option value="normal">Normal</option>
                        <option value="warning">Warning</option>
                        <option value="critical">Critical / Buruk</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn-filter-apply shadow-sm" onclick="loadHistoryData()">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabel Data Monitoring -->
        <div class="card-table">
            <div class="card-header card-header-table border-0 py-3 px-4 d-flex flex-row justify-content-between align-items-center gap-3 screen-only">
                <div class="text-nowrap">
                    <h5 class="fw-bold text-main-dark mb-0 text-nowrap" style="font-size: 1.1rem; letter-spacing: -0.3px;">Data Monitoring</h5>
                    <small class="text-slate d-block" style="font-size: 0.78rem;" id="tabel_hasil_text">0 hasil</small>
                </div>
                <select id="sort_order" class="form-select select-outline shadow-none" onchange="loadHistoryData()">
                    <option value="latest">Latest First</option>
                    <option value="oldest">Oldest First</option>
                </select>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap" id="historyTable">
                    <thead class="table-custom-header">
                        <tr>
                            <th class="ps-4">Tanggal &amp; Waktu</th>
                            <th>pH</th>
                            <th>Suhu (°C)</th>
                            <th>TDS (ppm)</th>
                            <th>Kekeruhan (NTU)</th>
                            <th>Kualitas Air</th>
                            <th class="pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody" class="table-custom-body">
                        <!-- Baris tabel dirender via JS -->
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            <div class="card-footer card-header-table border-0 py-3 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 screen-only">
                <div class="text-slate small text-center text-sm-start" style="font-size: 0.8rem;">
                    Menampilkan <span class="fw-bold text-main-dark" id="page-range">0–0</span> dari <span class="fw-bold text-main-dark" id="page-total">0</span> data
                </div>
                <div class="pagination-custom d-flex align-items-center justify-content-center gap-1 ms-sm-auto" id="pagination-wrapper">
                    <!-- Pagination tombol diisi dinamis -->
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
let currentFilteredData = [];
let currentPage = 1;
const itemsPerPage = 10;

/* TOGGLE DROPDOWN MENU */
function toggleExportMenu(e) {
    e.stopPropagation();
    let menu = document.getElementById('exportMenu');
    menu.classList.toggle('show');
}

document.addEventListener('click', function() {
    let menu = document.getElementById('exportMenu');
    if (menu) menu.classList.remove('show');
});

function evaluateWaterQuality(item) {
    let ph = parseFloat(item?.ph) || 0;
    let suhu = parseFloat(item?.suhu) || 0;
    let tds = parseFloat(item?.tds) || 0;
    let kekeruhan = parseFloat(item?.kekeruhan) || 0;

    let score = 100;
    if (ph < 6.5 || ph > 8.5) score -= 25;
    if (suhu < 26.0 || suhu > 31.5) score -= 25;
    if (tds > 500) score -= 25;
    if (kekeruhan > 25.0) score -= 25;

    let kualitas = item?.kualitas !== undefined && item?.kualitas !== null ? parseFloat(item.kualitas) : score;

    if (kualitas >= 75) {
        return { label: 'Normal', textLabel: 'Baik', dotColor: '#10B981', textColor: '#10B981', badgeHtml: '<span class="badge-kualitas-baik">Baik</span>', category: 'normal' };
    } else if (kualitas >= 45) {
        return { label: 'Warning', textLabel: 'Cukup', dotColor: '#F59E0B', textColor: '#D97706', badgeHtml: '<span class="badge-kualitas-cukup">Cukup</span>', category: 'warning' };
    } else {
        return { label: 'Critical', textLabel: 'Buruk', dotColor: '#EF4444', textColor: '#EF4444', badgeHtml: '<span class="badge-kualitas-buruk">Buruk</span>', category: 'critical' };
    }
}

function filter30MinIntervals(dataList) {
    let grouped = {};

    dataList.forEach(item => {
        let rawDate = item.created_at || item.updated_at;
        if (!rawDate) return;

        let date = new Date(rawDate);
        if (isNaN(date.getTime())) return;

        let slotMinute = date.getMinutes() < 30 ? '00' : '30';
        let slotKey = `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()} ${date.getHours()}:${slotMinute}`;

        if (!grouped[slotKey]) {
            grouped[slotKey] = item;
        }
    });

    return Object.values(grouped);
}

function loadHistoryData() {
    let startDate = $("#start_date").val();
    let endDate = $("#end_date").val();
    let status = $("#status_filter").val();
    let filterRoute = "{{ Route::has('history.filter') ? route('history.filter') : '/history/filter' }}";

    $.ajax({
        url: filterRoute,
        type: "GET",
        data: { start_date: startDate, end_date: endDate, status: status },
        dataType: "json",
        success: function(response) {
            let rawList = Array.isArray(response) ? response : (response.data || response.sensors || []);
            let sortOrder = $("#sort_order").val();
            
            // 1. Filter Data Client-Side
            rawList = rawList.filter(item => {
                let itemDate = (item.created_at || item.updated_at || "").substring(0, 10);
                let evalRes = evaluateWaterQuality(item);
                
                let passStartDate = startDate ? (itemDate >= startDate) : true;
                let passEndDate = endDate ? (itemDate <= endDate) : true;
                let passStatus = (status === 'all' || !status) ? true : (evalRes.category === status);
                
                return passStartDate && passEndDate && passStatus;
            });

            // 2. Sorting
            rawList.sort((a, b) => {
                let dateA = new Date(a.created_at || a.updated_at || 0);
                let dateB = new Date(b.created_at || b.updated_at || 0);
                return sortOrder === 'oldest' ? dateA - dateB : dateB - dateA;
            });

            // 3. Interval 30 Menit
            currentFilteredData = filter30MinIntervals(rawList);

            let totalData = currentFilteredData.length;
            let sumPh = 0, sumSuhu = 0, sumTds = 0, sumKekeruhan = 0, normalCount = 0;

            currentFilteredData.forEach(item => {
                sumPh += parseFloat(item.ph) || 0;
                sumSuhu += parseFloat(item.suhu) || 0;
                sumTds += parseFloat(item.tds) || 0;
                sumKekeruhan += parseFloat(item.kekeruhan) || 0;
                if (evaluateWaterQuality(item).category === 'normal') normalCount++;
            });

            if (totalData === 0) {
                $("#summary_total").text("0");
                $("#summary_ph, #summary_suhu, #summary_tds, #summary_kekeruhan, #summary_kualitas").text("-");
                $("#summary_kualitas_sub").text("0 data normal");
                $("#tabel_hasil_text").text("0 hasil");
            } else {
                $("#summary_total").text(totalData.toLocaleString('id-ID'));
                $("#summary_ph").text((sumPh / totalData).toFixed(1));
                $("#summary_suhu").text((sumSuhu / totalData).toFixed(1) + "°C");
                $("#summary_tds").text((sumTds / totalData).toFixed(0) + " ppm");
                $("#summary_kekeruhan").text((sumKekeruhan / totalData).toFixed(1) + " NTU");
                $("#tabel_hasil_text").text(totalData + " hasil");

                let isDark = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark') || document.documentElement.getAttribute('data-bs-theme') === 'dark';
                let normalRatio = normalCount / totalData;
                let qualityText = normalRatio >= 0.7 ? "Baik" : (normalRatio >= 0.4 ? "Cukup" : "Buruk");
                let qualityColor = normalRatio >= 0.7 ? (isDark ? "#34D399" : "#10B981") : (normalRatio >= 0.4 ? (isDark ? "#FBBF24" : "#D97706") : (isDark ? "#F87171" : "#EF4444"));

                $("#summary_kualitas").text(qualityText).css('color', qualityColor);
                $("#summary_kualitas_sub").text(`${normalCount} dari ${totalData} data normal`);
            }

            renderTablePage(1);
        },
        error: function(xhr, status, error) {
            $("#historyBody").html('<tr><td colspan="7" class="text-center py-4 text-danger fw-bold">Gagal memuat data. Periksa route backend atau koneksi internet.</td></tr>');
        }
    });
}

function renderTablePage(page) {
    currentPage = page;
    let tableBody = $("#historyBody");
    tableBody.empty();
    let totalData = currentFilteredData.length;

    if (totalData === 0) {
        tableBody.append('<tr><td colspan="7" class="text-center py-4 text-slate">🔍 Data tidak ditemukan</td></tr>');
        $("#page-range").text("0–0");
        $("#page-total").text("0");
        $("#pagination-wrapper").empty();
        return;
    }

    let startIndex = (page - 1) * itemsPerPage;
    let endIndex = Math.min(startIndex + itemsPerPage, totalData);
    let pageData = currentFilteredData.slice(startIndex, endIndex);

    pageData.forEach(item => {
        let rawDate = item.created_at || item.updated_at;
        let dateObj = rawDate ? new Date(rawDate) : new Date();
        let datePart = isNaN(dateObj) ? "-" : dateObj.toLocaleDateString("id-ID", { day: '2-digit', month: 'short', year: 'numeric' });
        let timePart = isNaN(dateObj) ? "-" : dateObj.toTimeString().split(' ')[0];
        let evalRes = evaluateWaterQuality(item);

        let row = `
            <tr>
                <td class="ps-4 py-3">
                    <div class="fw-bold text-main-dark" style="font-size: 0.88rem;">${datePart}</div>
                    <div class="text-muted" style="font-size: 0.74rem;">${timePart}</div>
                </td>
                <td class="fw-bold" style="color: #2563EB; font-size: 0.88rem;">
                    ${(parseFloat(item.ph) || 0).toFixed(1)}
                </td>
                <td class="fw-bold" style="color: #059669; font-size: 0.88rem;">
                    ${(parseFloat(item.suhu) || 0).toFixed(1)}<span class="text-muted fw-normal ms-1" style="font-size: 0.75rem;">°C</span>
                </td>
                <td class="fw-bold" style="color: #0D9488; font-size: 0.88rem;">
                    ${(parseFloat(item.tds) || 0).toFixed(2)}<span class="text-muted fw-normal ms-1" style="font-size: 0.75rem;">ppm</span>
                </td>
                <td class="fw-bold" style="color: #7C3AED; font-size: 0.88rem;">
                    ${(parseFloat(item.kekeruhan) || 0).toFixed(2)}<span class="text-muted fw-normal ms-1" style="font-size: 0.75rem;">NTU</span>
                </td>
                <td>${evalRes.badgeHtml}</td>
                <td class="pe-4 fw-bold" style="color: ${evalRes.textColor}; font-size: 0.84rem;">
                    <span class="status-dot" style="background-color: ${evalRes.dotColor};"></span> ${evalRes.label}
                </td>
            </tr>
        `;
        tableBody.append(row);
    });

    $("#page-range").text(`${startIndex + 1}–${endIndex}`);
    $("#page-total").text(totalData);
    renderPaginationControls(totalData, page);
}

function renderPaginationControls(totalData, page) {
    let totalPages = Math.ceil(totalData / itemsPerPage);
    let wrapper = $("#pagination-wrapper");
    wrapper.empty();
    if (totalPages <= 1) return;

    let prevDisabled = page === 1 ? 'disabled' : '';
    let prevOnClick = page > 1 ? `onclick="renderTablePage(${page - 1})"` : '';
    wrapper.append(`<button class="page-btn ${prevDisabled}" ${prevOnClick}>&lsaquo;</button>`);

    let startPage = Math.max(1, page - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        let active = i === page ? 'active' : '';
        wrapper.append(`<button class="page-btn ${active}" onclick="renderTablePage(${i})">${i}</button>`);
    }

    let nextDisabled = page === totalPages ? 'disabled' : '';
    let nextOnClick = page < totalPages ? `onclick="renderTablePage(${page + 1})"` : '';
    wrapper.append(`<button class="page-btn ${nextDisabled}" ${nextOnClick}>&rsaquo;</button>`);
}

function getFormattedDate() {
    const today = new Date();
    return `${String(today.getDate()).padStart(2, '0')}-${String(today.getMonth() + 1).padStart(2, '0')}-${today.getFullYear()}`;
}

function exportToPDF() {
    if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') {
        alert("Pustaka PDF sedang dimuat, silakan coba 2 detik lagi.");
        return;
    }
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape', 'mm', 'a4');
    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.setTextColor(26, 109, 196);
    doc.text("AQUATOR — Laporan Kualitas Air", 14, 18);
    doc.setFont("helvetica", "normal");
    doc.setFontSize(9);
    doc.setTextColor(100, 116, 139);
    doc.text(`Dicetak: ${new Date().toLocaleString('id-ID')}  |  Menampilkan total ${currentFilteredData.length} data monitoring`, 14, 25);

    doc.autoTable({
        columns: [
            { header: 'Tanggal & Waktu', dataKey: 'waktu' }, { header: 'pH', dataKey: 'ph' },
            { header: 'Suhu (°C)', dataKey: 'suhu' }, { header: 'TDS (ppm)', dataKey: 'tds' },
            { header: 'Kekeruhan (NTU)', dataKey: 'kekeruhan' }, { header: 'Kualitas Air', dataKey: 'kualitas' },
            { header: 'Status', dataKey: 'status' }
        ],
        body: currentFilteredData.map(item => {
            let rawDate = item.created_at || item.updated_at;
            let dateObj = rawDate ? new Date(rawDate) : new Date();
            let evalRes = evaluateWaterQuality(item);
            return {
                waktu: isNaN(dateObj) ? "-" : dateObj.toLocaleString('id-ID'),
                ph: (parseFloat(item.ph) || 0).toFixed(1),
                suhu: (parseFloat(item.suhu) || 0).toFixed(1) + ' °C',
                tds: (parseFloat(item.tds) || 0).toFixed(2) + ' ppm',
                kekeruhan: (parseFloat(item.kekeruhan) || 0).toFixed(2) + ' NTU',
                kualitas: evalRes.textLabel,
                status: evalRes.label
            };
        }),
        startY: 30, theme: 'grid', styles: { fontSize: 8.5, cellPadding: 3, font: "helvetica", halign: 'center' },
        headStyles: { fillColor: [157, 212, 209], textColor: [13, 38, 38], fontStyle: 'bold', halign: 'center' },
        columnStyles: { waktu: { halign: 'left', cellWidth: 50 }, ph: { cellWidth: 25 }, suhu: { cellWidth: 30 }, tds: { cellWidth: 35 }, kekeruhan: { cellWidth: 35 }, kualitas: { cellWidth: 30 }, status: { cellWidth: 30 } }
    });
    doc.save(`laporan aquator ${getFormattedDate()}.pdf`);
}

async function exportTableToExcel() {
    if (typeof ExcelJS === 'undefined') {
        alert("Pustaka ExcelJS belum siap. Silakan coba beberapa detik lagi.");
        return;
    }
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Laporan Kualitas Air");
    worksheet.addRow(['Tanggal & Waktu', 'pH', 'Suhu (°C)', 'TDS (ppm)', 'Kekeruhan (NTU)', 'Kualitas Air', 'Status']);

    currentFilteredData.forEach(item => {
        let rawDate = item.created_at || item.updated_at;
        let dateObj = rawDate ? new Date(rawDate) : new Date();
        let evalRes = evaluateWaterQuality(item);
        worksheet.addRow([
            isNaN(dateObj) ? "-" : dateObj.toLocaleString('id-ID'),
            parseFloat(item.ph) || 0, parseFloat(item.suhu) || 0,
            parseFloat(item.tds) || 0, parseFloat(item.kekeruhan) || 0,
            evalRes.textLabel, evalRes.label
        ]);
    });
    const buffer = await workbook.xlsx.writeBuffer();
    saveAs(new Blob([buffer]), "Laporan_AQUATOR.xlsx");
}

$(document).ready(function() {
    loadHistoryData();
    setInterval(() => {
        if (!$("#start_date").val() && !$("#end_date").val() && $("#status_filter").val() === 'all') {
            loadHistoryData();
        }
    }, 20000);
});
</script>
@endsection