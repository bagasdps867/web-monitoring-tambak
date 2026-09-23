@extends('layouts.app')

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
:root {
    --bg-mint: #d7edef;
    --bg-pastel-blue: #e8f3f9;
    --bg-card: #ffffff;
    --text-title: #0f172a;
    --text-body: #475569;
    --border-color: #cde4e7;
    --primary-blue: #0284c7;
    --badge-blue: #0284c7;
    --footer-grad: linear-gradient(90deg, #177ba5 0%, #1a839e 40%, #159599 100%);
    --stat-num: #0284c7;
    --card-num: #c2dfe4;
    --slide2-grad-1: rgba(16, 85, 122, 0.92);
    --slide2-grad-2: rgba(30, 161, 145, 0.88)
}

body.dark-mode {
    --bg-mint: #071929;
    --bg-pastel-blue: #071929;
    --bg-card: #0C2338;
    --text-title: #E8F4FA;
    --text-body: #7BAFC6;
    --border-color: rgba(255, 255, 255, 0.12);
    --primary-blue: #1A6DC4;
    --badge-blue: #06B6D4;
    --footer-grad: linear-gradient(90deg, #1A6DC4 0%, #2CC4B5 100%);
    --stat-num: #3AA8E8;
    --card-num: rgba(255, 255, 255, 0.05);
    --slide2-grad-1: rgba(7, 25, 41, 0.95);
    --slide2-grad-2: rgba(14, 52, 75, 0.90);
}

body {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.section-wrap-mint {
    background-color: var(--bg-mint);
    width: 100%;
    transition: background-color 0.3s ease;
}

.section-wrap-pastel-blue {
    background-color: var(--bg-pastel-blue);
    width: 100%;
    transition: background-color 0.3s ease;
}

.section-container {
    max-width: 1140px;
    margin: 0 auto;
    padding: 85px 24px;
    position: relative;
    z-index: 2;
}

/* --- CSS SLIDER HEADER BARU --- */
.hero-slider {
    width: 100%;
    height: 85vh;
    /* Menjaga tinggi header tetap pas di layar */
    position: relative;
    background-color: #081422;
}

.swiper-wrapper {
    height: 100%;
}

.hero-slide {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 70px 24px 80px;
    color: #ffffff;
    box-sizing: border-box;
    height: 100%;
    will-change: opacity;
    backface-visibility: hidden;
}

/* Slide 1 - Pengaturan arah konten */
.slide-1 {
    flex-direction: column;
    text-align: center;
}

/* Slide 2 - Grid untuk foto dan card */
.hero-grid {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 60px;
    max-width: 1140px;
    margin: 0 auto;
    width: 100%;
    align-items: center;
}

.hero-left {
    text-align: left;
}

.hero-left .hero-title {
    text-align: left;
    margin-bottom: 20px;
}

.glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 20px 25px;
    border-radius: 16px;
    margin-bottom: 16px;
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
    transform: translateZ(0);
}

.glass-card h3 {
    font-size: 32px;
    font-weight: 800;
    margin: 0 0 4px 0;
    color: #ffffff;
}

.glass-card h3 span {
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 1px;
    color: #a5f3fc;
    text-transform: uppercase;
}

.glass-card p {
    margin: 0;
    font-size: 14px;
    color: #e2e8f0;
}

/* Kustomisasi Navigasi Swiper */
.swiper-button-next,
.swiper-button-prev {
    color: rgba(255, 255, 255, 0.5) !important;
    transition: 0.3s;
}

.swiper-button-next:hover,
.swiper-button-prev:hover {
    color: #ffffff !important;
}

.swiper-pagination-bullet {
    background: #ffffff !important;
}

@media (max-width: 900px) {
    .hero-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .hero-left,
    .hero-left .hero-title {
        text-align: center;
    }
}

/* --- Akhir CSS Slider --- */

.hero-tag {
    color: var(--badge-blue);
    font-size: 11px;
    letter-spacing: 2px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 18px;
}

.hero-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(36px, 4.5vw, 54px);
    font-weight: 700;
    margin-bottom: 18px;
    line-height: 1.2;
}

.hero-subtitle {
    font-size: 17px;
    font-weight: 400;
    color: #f1f5f9;
    line-height: 1.55;
    margin-bottom: 14px;
}

.hero-desc {
    max-width: 580px;
    color: #cbd5e1;
    font-size: 13.5px;
    line-height: 1.6;
    margin-bottom: 34px;
}

.btn-wrap {
    display: flex;
    gap: 16px;
    align-items: center;
    justify-content: center;
}

.btn-fill {
    background: var(--primary-blue);
    color: #ffffff;
    padding: 11px 26px;
    border-radius: 7px;
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 600;
    transition: background 0.2s ease, transform 0.2s ease;
}

.btn-fill:hover {
    background: #0369a1;
    color: #ffffff;
    transform: translateY(-2px);
}

body.dark-mode .btn-fill:hover {
    background: #3AA8E8;
}

.btn-line {
    background: rgba(15, 23, 42, 0.35);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.4);
    padding: 11px 26px;
    border-radius: 7px;
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 600;
    backdrop-filter: blur(4px);
    transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}

.btn-line:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: #ffffff;
    color: #ffffff;
    transform: translateY(-2px);
}

.badge-sub {
    color: var(--badge-blue);
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    display: block;
    margin-bottom: 12px;
}

.section-title {
    font-family: 'Playfair Display', serif;
    font-size: 38px;
    font-weight: 700;
    color: var(--text-title);
    line-height: 1.25;
    margin-bottom: 20px;
}

.grid-split {
    display: grid;
    grid-template-columns: 1fr 1.15fr;
    gap: 50px;
    align-items: center;
}

.split-text p {
    color: var(--text-body);
    font-size: 14.5px;
    line-height: 1.7;
    margin-bottom: 16px;
}

.split-img-card {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.split-img-card img {
    width: 100%;
    height: 390px;
    object-fit: cover;
    display: block;
}

.floating-pill {
    position: absolute;
    bottom: 18px;
    left: 18px;
    background: var(--primary-blue);
    color: #fff;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 1px;
}

.feature-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 22px;
    margin-top: 36px;
}

.feat-card {
    background: var(--bg-card);
    border-radius: 18px;
    padding: 30px 28px;
    display: flex;
    align-items: flex-start;
    gap: 20px;
    position: relative;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
}

.feat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

.feat-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}

/* Icon Default Light Mode */
.icon-green {
    background: rgba(34, 197, 94, 0.15);
    color: #16a34a;
}

.icon-blue {
    background: rgba(14, 165, 233, 0.15);
    color: #0284c7;
}

.icon-amber {
    background: rgba(245, 158, 11, 0.15);
    color: #d97706;
}

.icon-teal {
    background: rgba(20, 184, 166, 0.15);
    color: #0d9488;
}

/* Icon Khusus Dark Mode berdasarkan Palet */
body.dark-mode .icon-green {
    background: rgba(16, 185, 129, 0.12);
    color: #10B981;
}

body.dark-mode .icon-blue {
    background: rgba(6, 182, 212, 0.12);
    color: #06B6D4;
}

body.dark-mode .icon-amber {
    background: rgba(245, 158, 11, 0.12);
    color: #F59E0B;
}

body.dark-mode .icon-teal {
    background: rgba(44, 196, 181, 0.12);
    color: #2CC4B5;
}

.feat-bar {
    width: 28px;
    height: 3px;
    border-radius: 2px;
    background: var(--badge-blue);
    margin-bottom: 12px;
}

.feat-info h4 {
    font-size: 16.5px;
    font-weight: 700;
    margin-bottom: 6px;
    color: var(--text-title);
}

.feat-info p {
    font-size: 13.5px;
    color: var(--text-body);
    line-height: 1.55;
}

.feat-num {
    position: absolute;
    right: 26px;
    top: 18px;
    font-size: 32px;
    font-weight: 800;
    color: var(--card-num);
}

.gallery-grid {
    display: grid;
    grid-template-columns: 1.25fr 1fr;
    gap: 20px;
    margin-top: 26px;
}

.gal-big {
    height: 360px;
    position: relative;
    border-radius: 16px;
    overflow: hidden;
}

.gal-right {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.gal-small {
    height: 170px;
    position: relative;
    border-radius: 16px;
    overflow: hidden;
}

.gal-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.5s ease;
}

.gal-big:hover .gal-img,
.gal-small:hover .gal-img {
    transform: scale(1.05);
}

.gal-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 16px 20px;
    background: linear-gradient(transparent, rgba(15, 23, 42, 0.85));
    color: #fff;
}

.gal-tag {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #cbd5e1;
    display: block;
    margin-bottom: 2px;
}

.gal-title {
    font-size: 15.5px;
    font-weight: 600;
    color: #ffffff;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    text-align: center;
    gap: 30px;
    margin: 48px 0 25px;
}

.stat-val {
    font-size: 50px;
    font-weight: 800;
    color: var(--stat-num);
    letter-spacing: -0.5px;
}

.stat-label {
    font-size: 14px;
    color: var(--text-body);
    margin-top: 6px;
}

.process-list {
    display: flex;
    flex-direction: column;
    gap: 22px;
    margin-top: 24px;
}

.process-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
}

.num-circle {
    width: 36px;
    height: 36px;
    background: #0ea5e9;
    color: #fff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    flex-shrink: 0;
}

.num-circle.orange {
    background: #f97316;
}

body.dark-mode .num-circle {
    background: #06B6D4;
    color: #fff;
}

body.dark-mode .num-circle.orange {
    background: #F59E0B;
    color: #fff;
}

.process-text h5 {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 4px;
    color: var(--text-title);
}

.process-text p {
    font-size: 13px;
    color: var(--text-body);
    line-height: 1.5;
}

.location-grid {
    display: grid;
    grid-template-columns: 1.8fr 1fr;
    gap: 20px;
    margin: 32px auto 0;
    max-width: 1000px;
    align-items: stretch;
}

.map-wrapper {
    border-radius: 16px;
    overflow: hidden;
    height: 100%;
    min-height: 320px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
}

.map-wrapper iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.loc-cards {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.loc-card {
    background: var(--bg-card);
    padding: 16px 18px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.loc-head {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    font-weight: 700;
    margin-bottom: 4px;
    color: var(--text-title);
}

.btn-wa {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: #22c55e;
    color: #fff;
    padding: 7px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    margin-top: 8px;
    width: fit-content;
}

body.dark-mode .btn-wa {
    background: #25D366;
}

@media (max-width: 850px) {
    .location-grid {
        grid-template-columns: 1fr;
    }

    .map-wrapper {
        height: 280px;
    }
}

/* CSS FOOTER BARU (3 KOLOM SEIMBANG) */
footer {
    background: var(--footer-grad);
    color: #ffffff;
    padding: 60px 5% 28px;
}

.footer-container {
    max-width: 1140px;
    margin: 0 auto;
}

.footer-grid {
    display: grid;
    grid-template-columns: 1.8fr 1fr 1fr;
    gap: 40px;
    margin-bottom: 45px;
    align-items: start;
}

.footer-brand-col .brand {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    margin-bottom: 20px;
}

.footer-brand-col p {
    font-size: 13.5px;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.85);
    margin-bottom: 18px;
    max-width: 320px;
}

.footer-brand-col .quote-text {
    font-size: 12.5px;
    color: rgba(255, 255, 255, 0.75);
    font-style: italic;
}

.footer-col h5 {
    color: rgba(255, 255, 255, 0.7);
    font-size: 11px;
    letter-spacing: 1.5px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 20px;
}

.footer-col ul {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 0;
    margin: 0;
}

.footer-col a,
.footer-col span {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-size: 13.5px;
    transition: color 0.2s, transform 0.2s;
    display: inline-block;
}

.footer-col a:hover {
    color: #ffffff;
    transform: translateX(3px);
}

.footer-divider {
    border: 0;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    margin: 0 0 24px 0;
}

.copyright {
    text-align: center;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
}

@media (max-width: 900px) {

    .grid-split,
    .gallery-grid,
    .location-grid {
        grid-template-columns: 1fr;
    }

    .feature-grid,
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .footer-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
}

/* Optimisasi Animasi Khusus HP */
@media (max-width: 768px) {
    [data-aos] {
        /* Menghilangkan penundaan (delay) agar elemen langsung muncul */
        transition-delay: 0s !important;
        animation-delay: 0s !important;
        /* Mempercepat durasi animasi jadi 250ms agar sangat instan di HP */
        transition-duration: 250ms !important;
    }

    .btn-wrap {
        flex-direction: column;
        width: 100%;
    }
}

/* --- TAMBAHAN CSS UNTUK SOP --- */
.sop-section-wrap {
    background-color: #eefafb;
    position: relative;
    overflow: hidden;
    padding: 85px 0;
    transition: background-color 0.3s ease;
}

body.dark-mode .sop-section-wrap {
    background-color: #091D2F;
}

.sop-bg-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: clamp(150px, 25vw, 400px);
    font-weight: 900;
    color: rgba(42, 157, 143, 0.12);
    /* Warna dipertegas */
    z-index: 1;
    font-family: 'Playfair Display', serif;
    pointer-events: none;
    letter-spacing: -5px;
    line-height: 1;
    text-shadow: 0 0 40px rgba(42, 157, 143, 0.25);
    /* Efek Menyala */
}

body.dark-mode .sop-bg-text {
    color: rgba(44, 196, 181, 0.18);
    text-shadow: 0 0 50px rgba(44, 196, 181, 0.35);
}

.sop-grid {
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    gap: 60px;
    align-items: center;
    position: relative;
    z-index: 2;
}

.sop-subtitle {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.sop-subtitle-line {
    width: 30px;
    height: 2px;
    background: #23a094;
}

.sop-subtitle-text {
    color: #23a094;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
}

body.dark-mode .sop-subtitle-line {
    background: #2CC4B5;
}

body.dark-mode .sop-subtitle-text {
    color: #2CC4B5;
}

.sop-list {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    /* Dibuat 2 Kolom */
    gap: 12px;
}

.sop-item {
    background: var(--bg-card);
    border-radius: 8px;
    padding: 11px 16px;
    /* Padding lebih tipis agar ramping */
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    border: 1px solid var(--border-color);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.sop-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
}

/* Agar rapi di HP (kembali jadi 1 kolom saat layar kecil) */
@media (max-width: 576px) {
    .sop-list {
        grid-template-columns: 1fr;
    }
}

.sop-num {
    color: #23a094;
    font-weight: 800;
    font-size: 13.5px;
    min-width: 18px;
}

body.dark-mode .sop-num {
    color: #2CC4B5;
}

.sop-divider {
    width: 1.5px;
    height: 16px;
    background: var(--border-color);
}

.sop-title {
    color: var(--text-title);
    font-weight: 600;
    font-size: 14px;
}

.btn-sop-fill {
    background: #2a9d8f;
    color: #ffffff;
    padding: 12px 26px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.2s;
}

.btn-sop-fill:hover {
    background: #21867a;
    color: #ffffff;
    transform: translateY(-2px);
}

.btn-sop-line {
    background: transparent;
    color: #2a9d8f;
    border: 1px solid #2a9d8f;
    padding: 12px 26px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.2s;
}

.btn-sop-line:hover {
    background: rgba(42, 157, 143, 0.08);
    color: #2a9d8f;
    transform: translateY(-2px);
}

body.dark-mode .btn-sop-line:hover {
    background: rgba(44, 196, 181, 0.15);
}

html {
    scroll-behavior: smooth;
}
</style>
@endpush

@section('content')

<!-- ================= HEADER SLIDER ================= -->
<header class="swiper hero-slider">
    <div class="swiper-wrapper">

        <!-- SLIDE 1 -->
        <div class="swiper-slide hero-slide slide-1"
            style="background: linear-gradient(rgba(8, 20, 34, 0.72), rgba(8, 20, 34, 0.78)), url('{{ asset('assets/image-hero.png') }}') center/cover no-repeat;">
            <div class="hero-content">
                <span class="hero-tag">TENTANG TAMBAK KAMI</span>
                <h1 class="hero-title">Tambak Tradisional Kami</h1>
                <p class="hero-subtitle">Budidaya Udang Vaname & Ikan Mujaer<br>dengan Perawatan Alami</p>
                <p class="hero-desc" style="margin: 0 auto 34px;">Memadukan pengalaman budidaya tradisional dengan
                    pemantauan kualitas air berbasis digital.</p>
                <div class="btn-wrap">
                    <a href="#budidaya" class="btn-fill">Lihat Budidaya Kami</a>
                    <a href="#kontak" class="btn-line">Hubungi Kami</a>
                </div>
            </div>
        </div>

        <!-- SLIDE 2 -->
        <div class="swiper-slide hero-slide slide-2"
            style="background: linear-gradient(to right, var(--slide2-grad-1), var(--slide2-grad-2)), url('{{ asset('assets/image-hero2.png') }}') center/cover no-repeat;">
            <div class="hero-grid">
                <div class="hero-left">
                    <span class="hero-tag" style="color: #cbd5e1;">— PANDUAN RESMI | TAMBAK BANGUNSARI</span>
                    <h1 class="hero-title">Budidaya yang Terukur,<br>Panen yang Pasti.</h1>
                    <p class="hero-desc" style="max-width: 100%; margin-bottom: 24px;">Kami tidak sekadar membudidayakan
                        — kami menjalankan setiap langkah berdasarkan standar yang telah teruji. Dari persiapan tambak,
                        monitoring IoT, hingga keputusan panen berbasis data.</p>
                    <div class="btn-wrap" style="justify-content: flex-start;">
                        <a href="#sop" class="btn-fill">Lihat SOP Lengkap <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>

                <div class="hero-right">
                    <div class="glass-card">
                        <h3>28 <span>HARI</span></h3>
                        <p>Prosedur lengkap</p>
                    </div>
                    <div class="glass-card">
                        <h3>4 <span>SENSOR</span></h3>
                        <p>Parameter terpantau</p>
                    </div>
                    <div class="glass-card">
                        <h3>10 <span>TAHAP</span></h3>
                        <p>Alur budidaya</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</header>

<div class="section-wrap-mint">
    <section class="section-container">
        <div class="grid-split">
            <div class="split-text" data-aos="fade-right" data-aos-duration="600">
                <span class="badge-sub">TENTANG KAMI</span>
                <h2 class="section-title">Tradisional dalam Cara,<br>Modern dalam Pemantauan.</h2>
                <p>Kami membudidayakan udang Vaname dan ikan mujaer secara tradisional di tambak air tawar Sidoarjo
                    dengan sistem pengelolaan manual namun terpantau digital.</p>
                <p>Kami tetap mengandalkan pengalaman lokal dan metode alami dalam perawatan tambak, sementara teknologi
                    digunakan untuk membantu memastikan kondisi air tetap terpantau secara berkala.</p>
            </div>
            <div class="split-img-card" data-aos="fade-left" data-aos-duration="600">
                <img src="{{ asset('assets/bersama.jpg') }}" alt="Tambak Sidoarjo">
                <span class="floating-pill">TRADISIONAL × DIGITAL</span>
            </div>
        </div>
    </section>

    <section class="section-container" style="padding-top: 0;">
        <div style="text-align: center; max-width: 600px; margin: 0 auto 30px;" data-aos="fade-up"
            data-aos-duration="500">
            <span class="badge-sub">TAMBAK BANGUNSARI</span>
            <h2 class="section-title">Keunggulan Kami</h2>
        </div>

        <div class="feature-grid">
            <div class="feat-card" data-aos="fade-up" data-aos-duration="500" data-aos-delay="50">
                <div class="feat-icon-box icon-green"><i class="bi bi-flower1"></i></div>
                <div class="feat-info">
                    <div class="feat-bar"></div>
                    <h4>Sistem Tradisional</h4>
                    <p>Budidaya udang dan ikan dengan metode alami yang menjaga keseimbangan ekosistem tambak.</p>
                </div>
                <span class="feat-num">01</span>
            </div>

            <div class="feat-card" data-aos="fade-up" data-aos-duration="500" data-aos-delay="100">
                <div class="feat-icon-box icon-blue"><i class="bi bi-display"></i></div>
                <div class="feat-info">
                    <div class="feat-bar"></div>
                    <h4>Monitoring Kualitas Air</h4>
                    <p>Memantau nilai pH, suhu, TDS dan kekeruhan air secara real-time melalui website.</p>
                </div>
                <span class="feat-num">02</span>
            </div>

            <div class="feat-card" data-aos="fade-up" data-aos-duration="500" data-aos-delay="150">
                <div class="feat-icon-box icon-amber"><i class="bi bi-bell-fill"></i></div>
                <div class="feat-info">
                    <div class="feat-bar"></div>
                    <h4>Pemantauan Lebih Teratur</h4>
                    <p>Analisis AI membantu mendeteksi pola perubahan air dan memberikan rekomendasi tindakan yang
                        tepat.</p>
                </div>
                <span class="feat-num">03</span>
            </div>

            <div class="feat-card" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
                <div class="feat-icon-box icon-teal"><i class="bi bi-water"></i></div>
                <div class="feat-info">
                    <div class="feat-bar"></div>
                    <h4>Siap Panen Berkala</h4>
                    <p>Kualitas air yang terjaga membantu mendukung pertumbuhan udang dan ikan hingga masa panen.</p>
                </div>
                <span class="feat-num">04</span>
            </div>
        </div>
    </section>
</div>

<!-- ================= BAGIAN SOP BARU ================= -->
<div id="sop" class="sop-section-wrap">
    <div class="sop-bg-text">SOP</div>

    <section class="section-container" style="padding-top: 0; padding-bottom: 0;">
        <div class="sop-grid">

            <!-- Kolom Teks Kiri -->
            <div data-aos="fade-right" data-aos-duration="600">
                <div class="sop-subtitle">
                    <div class="sop-subtitle-line"></div>
                    <span class="sop-subtitle-text">Panduan Resmi Tambak Bangunsari</span>
                </div>
                <h2 class="section-title" style="font-size: clamp(32px, 4vw, 42px); margin-bottom: 18px;">
                    SOP Budidaya <br> <span style="color: #2a9d8f;">Presisi</span>
                </h2>
                <p
                    style="color: var(--text-body); font-size: 15px; line-height: 1.7; margin-bottom: 34px; max-width: 90%;">
                    Standar operasional lengkap pengelolaan tambak kami — dari persiapan kolam, monitoring IoT,
                    manajemen pakan, hingga evaluasi siklus panen.
                </p>
                <div class="btn-wrap" style="justify-content: flex-start;">
                    <!-- Tombol Baca Dokumen -->
                    <a href="{{ asset('assets/sop-budidaya.pdf') }}" target="_blank" class="btn-sop-fill">
                        <i class="bi bi-eye"></i> Baca Dokumen
                    </a>

                    <!-- Tombol Unduh PDF -->
                    <a href="{{ asset('assets/sop-budidaya.pdf') }}" download="SOP_Budidaya_Presisi.pdf"
                        class="btn-sop-line">
                        <i class="bi bi-download"></i> Unduh PDF
                    </a>
                </div>
            </div>

            <!-- Kolom List Kanan -->
            <div class="sop-list" data-aos="fade-left" data-aos-duration="600">
                <div class="sop-item">
                    <span class="sop-num">01</span>
                    <div class="sop-divider"></div><span class="sop-title">Persiapan Tambak</span>
                </div>
                <div class="sop-item">
                    <span class="sop-num">02</span>
                    <div class="sop-divider"></div><span class="sop-title">Persiapan Sistem IoT</span>
                </div>
                <div class="sop-item">
                    <span class="sop-num">03</span>
                    <div class="sop-divider"></div><span class="sop-title">Penebaran bibit</span>
                </div>
                <div class="sop-item">
                    <span class="sop-num">04</span>
                    <div class="sop-divider"></div><span class="sop-title">Monitoring Kualitas Air</span>
                </div>
                <div class="sop-item">
                    <span class="sop-num">05</span>
                    <div class="sop-divider"></div><span class="sop-title">Klasifikasi Kondisi Air</span>
                </div>
                <div class="sop-item">
                    <span class="sop-num">06</span>
                    <div class="sop-divider"></div><span class="sop-title">Pemberian Pakan</span>
                </div>
                <div class="sop-item">
                    <span class="sop-num">07</span>
                    <div class="sop-divider"></div><span class="sop-title">Tindakan Korektif</span>
                </div>
                <div class="sop-item">
                    <span class="sop-num">08</span>
                    <div class="sop-divider"></div><span class="sop-title">Monitoring Pertumbuhan</span>
                </div>
                <div class="sop-item">
                    <span class="sop-num">09</span>
                    <div class="sop-divider"></div><span class="sop-title">Panen & Pencatatan</span>
                </div>
                <div class="sop-item">
                    <span class="sop-num">10</span>
                    <div class="sop-divider"></div><span class="sop-title">Evaluasi Siklus</span>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Buka lagi section-wrap-mint untuk Dari Tambak Kami -->
<div class="section-wrap-mint">
    <section class="section-container" style="padding-top: 60px;">
        <div data-aos="fade-right" data-aos-duration="500">
            <h2 class="section-title" style="margin-bottom: 4px;">Dari Tambak Kami</h2>
            <p style="color: var(--text-body); font-size: 14px;">Bagian dari proses budidaya yang kami jalankan setiap
                hari.</p>
        </div>

        <div class="gallery-grid">
            <div class="gal-big" data-aos="zoom-in" data-aos-duration="600">
                <img src="{{ asset('assets/pirik.png') }}" alt="Budidaya Ikan Mujaer" class="gal-img">
                <div class="gal-overlay">
                    <span class="gal-tag">MUJAER</span>
                    <div class="gal-title">Ikan Mujaer</div>
                </div>
            </div>
            <div class="gal-right">
                <div class="gal-small" data-aos="fade-left" data-aos-duration="500" data-aos-delay="100">
                    <img src="{{ asset('assets/udang.jpg') }}" alt="Udang Vaname" class="gal-img">
                    <div class="gal-overlay">
                        <span class="gal-tag">UDANG</span>
                        <div class="gal-title">Udang Vaname</div>
                    </div>
                </div>
                <div class="gal-small" data-aos="fade-left" data-aos-duration="500" data-aos-delay="150">
                    <img src="{{ asset('assets/jala.jpg') }}" alt="Peralatan Tambak" class="gal-img">
                    <div class="gal-overlay">
                        <span class="gal-tag">JALA</span>
                        <div class="gal-title">Peralatan Tambak</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="section-wrap-pastel-blue">
    <section id="budidaya" class="section-container" style="text-align: center;">
        <div data-aos="fade-up" data-aos-duration="500">
            <h2 class="section-title" style="margin-bottom: 10px;">Budidaya Kami</h2>
            <p style="color: var(--text-body); font-size: 15px; max-width: 580px; margin: auto;">Kami membudidayakan
                udang Vaname dan ikan mujaer secara tradisional di tambak air tawar Sidoarjo.</p>
        </div>

        <div class="stats-grid">
            <div data-aos="zoom-in-up" data-aos-duration="500" data-aos-delay="50">
                <div class="stat-val" style="color: #10B981;">350 KG</div>
                <div class="stat-label" style="color: #7BAFC6; font-weight: 500;">Rata-rata panen udang per siklus</div>
            </div>
            <div data-aos="zoom-in-up" data-aos-duration="500" data-aos-delay="100">
                <div class="stat-val" style="color: #06B6D4;">±250 KG</div>
                <div class="stat-label" style="color: #7BAFC6; font-weight: 500;">Mujaer siap konsumsi lokal per panen
                </div>
            </div>
            <div data-aos="zoom-in-up" data-aos-duration="500" data-aos-delay="150">
                <div class="stat-val" style="color: #1A6DC4;">24/7</div>
                <div class="stat-label" style="color: #7BAFC6; font-weight: 500;">Pemantauan kualitas air otomatis</div>
            </div>
        </div>
        <p style="color: var(--text-body); font-size: 13.5px; opacity: 0.85;" data-aos="fade-up" data-aos-duration="500"
            data-aos-delay="200">
            Kualitas air dipantau otomatis melalui dashboard untuk membantu menjaga kondisi budidaya.
        </p>
    </section>
</div>

<div class="section-wrap-mint">
    <section class="section-container">
        <div class="grid-split">
            <div class="split-img-card" data-aos="fade-right" data-aos-duration="600">
                <img src="{{ asset('assets/mujaer.png') }}" alt="Kualitas Panen" style="height: 480px;">
            </div>
            <div data-aos="fade-left" data-aos-duration="600">
                <span class="badge-sub">PROSES</span>
                <h2 class="section-title">Kualitas Panen Unggul,<br>Terjamin oleh Data.</h2>

                <div class="process-list">
                    <div class="process-item" data-aos="fade-up" data-aos-duration="500" data-aos-delay="50">
                        <div class="num-circle">01</div>
                        <div class="process-text">
                            <h5>Air Tambak</h5>
                            <p>Kondisi air menjadi bagian penting dalam proses budidaya.</p>
                        </div>
                    </div>
                    <div class="process-item" data-aos="fade-up" data-aos-duration="500" data-aos-delay="100">
                        <div class="num-circle">02</div>
                        <div class="process-text">
                            <h5>Sensor Monitoring</h5>
                            <p>Parameter pH, suhu, dan kekeruhan dipantau secara digital.</p>
                        </div>
                    </div>
                    <div class="process-item" data-aos="fade-up" data-aos-duration="500" data-aos-delay="150">
                        <div class="num-circle">03</div>
                        <div class="process-text">
                            <h5>Dashboard AQUATOR</h5>
                            <p>Data monitoring dapat dilihat melalui dashboard terpadu.</p>
                        </div>
                    </div>
                    <div class="process-item" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
                        <div class="num-circle orange">04</div>
                        <div class="process-text">
                            <h5>Keputusan Budidaya</h5>
                            <p>Data membantu proses pemantauan kondisi tambak secara lebih teratur.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="section-wrap-pastel-blue">
    <section id="kontak" class="section-container">
        <div style="text-align: center; margin-bottom: 24px;" data-aos="fade-up" data-aos-duration="500">
            <h2 class="section-title" style="margin-bottom: 6px;">Kunjungi Kami</h2>
            <p style="color: var(--text-body); font-size: 14px;">Temukan lokasi tambak kami di Sidoarjo.</p>
        </div>

        <div class="location-grid" data-aos="fade-up" data-aos-duration="600">
            <!-- Peta Utama (Besar) -->
            <div class="map-wrapper">
                <iframe src="https://maps.google.com/maps?q=-7.539009,112.790448&t=k&z=17&ie=UTF8&iwloc=&output=embed"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

            <!-- Kartu Info (Kecil & Ringkas) -->
            <div class="loc-cards">
                <div class="loc-card">
                    <div class="loc-head">
                        <i class="bi bi-geo-alt-fill" style="color: #ef4444; font-size: 14px;"></i> Lokasi Tambak
                    </div>
                    <p style="color: var(--text-body); font-size: 12px; line-height: 1.4; margin: 0;">
                        Dusun Bangunsari, Kecamatan Jabon,<br>Kabupaten Sidoarjo, Jawa Timur.
                    </p>
                    <a href="https://maps.app.goo.gl/sUfxWwUgjt6U7Le58" target="_blank"
                        style="color: var(--primary-blue); text-decoration: none; font-size: 11.5px; font-weight: 600; display: inline-block; margin-top: 6px;">
                        Lihat di Google Maps &rarr;
                    </a>
                </div>

                <div class="loc-card">
                    <div class="loc-head">
                        <i class="bi bi-telephone-fill" style="color: #ef4444; font-size: 13px;"></i> Kontak Kami
                    </div>
                    <a href="https://wa.me/6285707178918" target="_blank"
                        style="font-size: 16px; font-weight: 800; color: var(--text-title); margin-top: 1px; text-decoration: none; display: block;">
                        0857-0717-8918
                    </a>
                    <p style="color: var(--text-body); font-size: 11px; margin: 1px 0 0;">WhatsApp Available</p>
                    <a href="https://wa.me/6285707178918" target="_blank" class="btn-wa">
                        <i class="bi bi-whatsapp"></i> Chat WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- FOOTER (3 KOLOM REFRESHED) -->
<footer>
    <div class="footer-container">
        <div class="footer-grid">
            <!-- Brand Column -->
            <div class="footer-brand-col">
                <a href="{{ url('/') }}" class="brand">
                    <div class="d-flex justify-content-center align-items-center"
                        style="width: 40px; height: 40px; background-color: #ffffff; border-radius: 10px; padding: 5px; box-shadow: 0 2px 6px rgba(0,0,0,0.15); overflow: hidden;">
                        <img src="{{ asset('assets/icon.png') }}" alt="Aquator Logo"
                            style="width: 100%; height: 100%; object-fit: contain; transform: scale(1.8);">
                    </div>
                    <span class="fw-bold" style="font-size: 19px; color: #ffffff; letter-spacing: 0.5px;">AQUATOR</span>
                </a>
                <p>Budidaya Udang & Ikan Mujaer di Sidoarjo secara tradisional & digital.</p>
                <div class="quote-text">"Kualitas air terpantau, budidaya lebih terarah."</div>
            </div>

            <!-- Jelajahi -->
            <div class="footer-col">
                <h5>JELAJAHI</h5>
                <ul>
                    <li><a href="{{ url('/home') }}">Home</a></li>
                    <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ url('/history') }}">Laporan</a></li>
                </ul>
            </div>

            <!-- Kontak -->
            <div class="footer-col">
                <h5>KONTAK</h5>
                <ul>
                    <li><a href="https://wa.me/6285707178918" target="_blank">0857-0717-8918</a></li>
                </ul>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="copyright">
            &copy; 2026 AQUATOR / TambakKita. All rights reserved.
        </div>
    </div>
</footer>

<!-- Script Swiper -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var swiper = new Swiper(".hero-slider", {
        loop: true,
        speed: 1200,
        grabCursor: true,
        allowTouchMove: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        effect: "fade",
        fadeEffect: {
            crossFade: true
        }
    });
});
</script>

<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isMobile = window.innerWidth <= 768;

    AOS.init({
        duration: isMobile ? 250 : 500,
        once: true,
        offset: isMobile ? 10 : 60,
        easing: 'ease-out-quad',
        disableMutationObserver: true
    });
});
</script>
@endsection