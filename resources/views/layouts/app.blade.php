<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    {{-- Bootstrap & Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- Google Fonts Global (Plus Jakarta Sans) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Theme & Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/app-DqME6eCz.css') }}">

    {{-- Font Override for Landing Page --}}
    @stack('head')

    <title>Aqua Monitor</title>

    {{-- JS Libraries --}}
    <script src="{{ asset('build/assets/app-D4nMHFhB.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <style>
    /* Font khusus teks navbar (tanpa merusak ikon Font Awesome) */
    .navbar,
    .navbar .nav-link,
    .navbar-brand {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
    }

    /* Kembalikan font khusus untuk ikon Font Awesome */
    .navbar i,
    .navbar .fas,
    .navbar .far,
    .navbar .fa-solid {
        font-family: "Font Awesome 6 Free" !important;
        font-weight: 900 !important;
    }

    /* Style untuk animasi indikator navbar */
    .nav-indicator {
        position: absolute;
        background-color: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 0.25rem;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        pointer-events: none;
        z-index: 1;
    }

    .nav-item-link {
        position: relative;
        z-index: 2;
        transition: color 0.2s ease;
    }

    /* Custom Theme Toggle Switch */
    .custom-theme-toggle {
        position: relative;
        display: inline-block;
        width: 58px;
        height: 30px;
        cursor: pointer;
    }

    .custom-theme-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.2);
        border: 1.5px solid rgba(255, 255, 255, 0.4);
        border-radius: 30px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        padding: 3px;
    }

    .toggle-thumb {
        width: 22px;
        height: 22px;
        background-color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        position: relative; /* Ditambahkan agar ikon absolute tetap di dalam */
    }

    /* Posisikan ikon bertumpuk */
    .toggle-thumb i {
        position: absolute;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    /* Light Mode: Matahari Tampak, Bulan Sembunyi */
    .toggle-thumb .fa-sun {
        font-size: 12px;
        color: #ffb703;
        opacity: 1;
        transform: rotate(0deg);
    }

    .toggle-thumb .fa-moon {
        font-size: 11.5px;
        color: #1f2937;
        opacity: 0;
        transform: rotate(-30deg);
    }

    /* Dark Mode (Checked): Bergeser Ke Kanan + Ganti Ikon Bulan */
    .custom-theme-toggle input:checked+.toggle-slider .toggle-thumb {
        transform: translateX(27px);
    }

    .custom-theme-toggle input:checked+.toggle-slider .toggle-thumb .fa-sun {
        opacity: 0;
        transform: rotate(30deg);
    }

    .custom-theme-toggle input:checked+.toggle-slider .toggle-thumb .fa-moon {
        opacity: 1;
        transform: rotate(0deg);
    }

    /* ================= DYNAMIC DARK MODE OVERRIDES ================= */
    /* Warna Background disesuaikan menggunakan palet: Background Utama #071929, Navbar #0C2338 */
    body.dark-mode {
        background-color: #071929 !important;
        color: #E8F4FA !important;
    }

    body.dark-mode .navbar.d-none.d-md-flex {
        background-color: #0C2338 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    body.dark-mode .navbar.fixed-bottom {
        background-color: #0C2338 !important;
        border-top: 1px solid rgba(255, 255, 255, 0.12) !important;
    }
    
    /* Perbaikan link mobile agar rapih */
    .mobile-nav-link {
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .mobile-nav-link:hover {
        color: white !important;
    }
    </style>
</head>

<body class="@yield('body-class')">

    {{-- Navbar atas (desktop) --}}
    <nav class="navbar navbar-expand-lg d-none d-md-flex py-3" style="background-color: #1558A8;">
        <div class="container px-4">
            <!-- Bagian Kiri: Logo & Brand -->
            <a class="navbar-brand text-white fw-bold d-flex align-items-center" href="{{ url('/dashboard') }}"
                style="font-size: 1.25rem;">
                <div class="me-2 d-flex justify-content-center align-items-center"
                    style="width: 44px; height: 44px; background-color: #ffffff; border-radius: 12px; padding: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                    <img src="{{ asset('assets/icon.png') }}" alt="Aquator Logo"
                        style="width: 100%; height: 100%; object-fit: contain; transform: scale(1.9);">
                </div>
                AQUATOR
            </a>

            <!-- Bagian Kanan: Menu Navigasi & Theme Switch -->
            <div class="ms-auto d-flex align-items-center">
                <!-- Wrapper Menu Links (Navigasi dengan animasi) -->
                <div class="position-relative d-flex align-items-center me-3" id="navMenuWrapper">
                    <div id="navIndicator" class="nav-indicator"></div>

                    <a class="nav-link text-white mx-1 px-3 py-1 nav-item-link {{ request()->is('tentang-tambak') ? 'active' : '' }}"
                        href="{{ url('/tentang-tambak') }}">
                        Home
                    </a>

                    <a class="nav-link text-white mx-1 px-3 py-1 nav-item-link {{ request()->is('dashboard') ? 'active' : '' }}"
                        href="{{ url('/dashboard') }}">
                        Dashboard
                    </a>

                    <a class="nav-link text-white mx-1 px-3 py-1 nav-item-link {{ request()->is('history') ? 'active' : '' }}"
                        href="{{ url('/history') }}">
                        Laporan
                    </a>
                </div>

                <!-- Theme Switcher Desktop -->
                <label class="custom-theme-toggle mb-0" for="themeSwitch" title="Ubah Tema">
                    <input type="checkbox" id="themeSwitch">
                    <span class="toggle-slider">
                        <span class="toggle-thumb">
                            <i class="fas fa-sun"></i>
                            <i class="fas fa-moon"></i>
                        </span>
                    </span>
                </label>
            </div>
        </div>
    </nav>

    {{-- Navbar bawah (mobile) Disesuaikan dengan Desktop --}}
    <div class="navbar fixed-bottom border-top d-md-none py-2 px-3 d-flex justify-content-around align-items-center" style="background-color: #1558A8; z-index: 1030;">
        <a href="{{ url('/tentang-tambak') }}" class="mobile-nav-link d-flex flex-column align-items-center text-white {{ request()->is('tentang-tambak') ? 'fw-bold' : 'opacity-75' }}">
            <i class="fas fa-home mb-1"></i>
            <span class="small" style="font-size: 0.75rem;">Home</span>
        </a>
        <a href="{{ url('/dashboard') }}" class="mobile-nav-link d-flex flex-column align-items-center text-white {{ request()->is('dashboard') ? 'fw-bold' : 'opacity-75' }}">
            <i class="fas fa-chart-line mb-1"></i>
            <span class="small" style="font-size: 0.75rem;">Dashboard</span>
        </a>
        <a href="{{ url('/history') }}" class="mobile-nav-link d-flex flex-column align-items-center text-white {{ request()->is('history') ? 'fw-bold' : 'opacity-75' }}">
            <i class="fas fa-clock-rotate-left mb-1"></i>
            <span class="small" style="font-size: 0.75rem;">Laporan</span>
        </a>
        
        <!-- Theme Switcher Mobile (Menggunakan layout custom toggle yang sama) -->
        <div class="d-flex flex-column align-items-center text-white">
            <label class="custom-theme-toggle mb-1" for="mobileThemeSwitch" title="Ubah Tema" style="transform: scale(0.85); transform-origin: bottom;">
                <input type="checkbox" id="mobileThemeSwitch">
                <span class="toggle-slider">
                    <span class="toggle-thumb">
                        <i class="fas fa-sun"></i>
                        <i class="fas fa-moon"></i>
                    </span>
                </span>
            </label>
            <span class="small opacity-75" style="font-size: 0.75rem;">Tema</span>
        </div>
    </div>

    <main>
        @yield('content')
    </main>

    <script src="{{ asset('js/app.js') }}"></script>

    {{-- Script Animasi Navbar --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const wrapper = document.getElementById('navMenuWrapper');
        const indicator = document.getElementById('navIndicator');
        const links = wrapper ? wrapper.querySelectorAll('.nav-item-link') : [];
        const activeLink = wrapper ? wrapper.querySelector('.nav-item-link.active') : null;

        function moveIndicator(element) {
            if (!element || !indicator) return;
            indicator.style.left = element.offsetLeft + 'px';
            indicator.style.width = element.offsetWidth + 'px';
            indicator.style.top = element.offsetTop + 'px';
            indicator.style.height = element.offsetHeight + 'px';
            indicator.style.opacity = '1';
        }

        if (activeLink) {
            moveIndicator(activeLink);
        } else if (indicator) {
            indicator.style.opacity = '0';
        }

        links.forEach(link => {
            link.addEventListener('mouseenter', function() {
                moveIndicator(this);
            });
        });

        if (wrapper) {
            wrapper.addEventListener('mouseleave', function() {
                if (activeLink) {
                    moveIndicator(activeLink);
                } else if (indicator) {
                    indicator.style.opacity = '0';
                }
            });
        }
    });
    </script>

    {{-- Script Function Light / Dark Mode (Disinkronkan antara Desktop & Mobile) --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const desktopSwitch = document.getElementById('themeSwitch');
        const mobileSwitch = document.getElementById('mobileThemeSwitch');
        const body = document.body;

        function applyTheme(isDark) {
            if (isDark) {
                body.classList.add('dark-mode');
                if (desktopSwitch) desktopSwitch.checked = true;
                if (mobileSwitch) mobileSwitch.checked = true;
                localStorage.setItem('theme', 'dark');
            } else {
                body.classList.remove('dark-mode');
                if (desktopSwitch) desktopSwitch.checked = false;
                if (mobileSwitch) mobileSwitch.checked = false;
                localStorage.setItem('theme', 'light');
            }
        }

        // Cek status tema di LocalStorage saat muat halaman
        if (localStorage.getItem('theme') === 'dark') {
            applyTheme(true);
        }

        // Event listener toggle desktop
        if (desktopSwitch) {
            desktopSwitch.addEventListener('change', (e) => applyTheme(e.target.checked));
        }

        // Event listener toggle mobile
        if (mobileSwitch) {
            mobileSwitch.addEventListener('change', (e) => applyTheme(e.target.checked));
        }
    });
    </script>

    @stack('styles')
    @stack('scripts')
</body>

</html>