<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Analytics — FlexiTag</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite([
        'resources/css/components/user-sidebar.css',
        'resources/css/pages/user-analytics.css',
        'resources/js/user-sidebar.js',
        'resources/js/user-analytics.js'
    ])
</head>
<body class="user-analytics-body">

<div class="analytics-viewport-wrap">

    {{-- ========================================================================= --}}
    {{-- 1. TOP APP BAR (KONSISTEN DENGAN HOME & MERCH)                             --}}
    {{-- ========================================================================= --}}
    <header class="user-topbar">
        <div class="topbar-left-group">
            <button type="button" class="hamburger-btn" id="openDrawerBtn" aria-label="Buka Menu Navigasi" onclick="toggleUserSidebar(true)">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="topbar-branding">
                <span class="topbar-subtitle">FLEXITAG</span>
                <h1 class="topbar-title">Analytics</h1>
            </div>
        </div>
    </header>

    {{-- ========================================================================= --}}
    {{-- 2. MAIN ANALYTICS CONTAINER (MOBILE PORTRAIT CENTERED)                   --}}
    {{-- ========================================================================= --}}
    <main class="analytics-content">

        {{-- Panel Hero Maroon + Pattern + Toraja Sunburst --}}
        <section class="analytics-hero-card" aria-label="Statistik Interaksi">
            <div class="analytics-hero-pattern" aria-hidden="true"></div>

            {{-- Pa'barre Allo Sunburst SVG --}}
            <svg class="analytics-hero-sunburst" viewBox="0 0 100 100" fill="none" stroke="#E5B32A" stroke-width="2" aria-hidden="true">
                <circle cx="50" cy="50" r="45" stroke-dasharray="4 2"/>
                <circle cx="50" cy="50" r="28"/>
                <circle cx="50" cy="50" r="14"/>
                <line x1="50" y1="5" x2="50" y2="95"/>
                <line x1="5" y1="50" x2="95" y2="50"/>
                <line x1="18" y1="18" x2="82" y2="82"/>
                <line x1="18" y1="82" x2="82" y2="18"/>
            </svg>

            <span class="hero-tagline-badge">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span>Live Telemetri</span>
            </span>

            <h2 class="analytics-hero-title">Statistik Interaksi</h2>
            <p class="analytics-hero-subtitle">Lihat performa interaksi sentuhan NFC dan aktivitas pengunjung tag FlexiTag Anda.</p>
        </section>

        {{-- Segmented Period Selector --}}
        <nav class="period-selector-wrap" aria-label="Pilihan Rentang Waktu">
            <a href="{{ route('user.analytics', ['period' => '7d']) }}" 
               class="period-pill-btn {{ $period === '7d' ? 'is-active' : '' }}">
                7 Hari Terakhir
            </a>
            <a href="{{ route('user.analytics', ['period' => '30d']) }}" 
               class="period-pill-btn {{ $period === '30d' ? 'is-active' : '' }}">
                30 Hari
            </a>
            <a href="{{ route('user.analytics', ['period' => 'month']) }}" 
               class="period-pill-btn {{ $period === 'month' ? 'is-active' : '' }}">
                Bulan Ini
            </a>
        </nav>

        {{-- Summary Cards (TOTAL TAP & MINGGU INI) --}}
        <section class="summary-cards-grid" aria-label="Ringkasan Tap">
            {{-- Total Tap Card --}}
            <div class="summary-card">
                <div class="summary-card-head">
                    <span class="summary-card-label">TOTAL TAP</span>
                    <div class="summary-card-icon" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                            <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                            <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                            <line x1="12" y1="20" x2="12.01" y2="20"></line>
                        </svg>
                    </div>
                </div>

                <div class="summary-card-body">
                    <span class="summary-card-num">{{ number_format($totalTaps, 0, ',', '.') }}</span>
                    <span class="summary-card-suffix">kali</span>
                </div>

                <div class="summary-badge-sub {{ $periodComparisonStatus === 'positive' ? 'is-up' : ($periodComparisonStatus === 'negative' ? 'is-down' : '') }}">
                    {{ $periodComparisonText }}
                </div>
            </div>

            {{-- Minggu Ini Card --}}
            <div class="summary-card">
                <div class="summary-card-head">
                    <span class="summary-card-label">MINGGU INI</span>
                    <div class="summary-card-icon" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                </div>

                <div class="summary-card-body">
                    <span class="summary-card-num">{{ number_format($weeklyTaps, 0, ',', '.') }}</span>
                    <span class="summary-card-suffix">tap</span>
                </div>

                <div class="summary-badge-sub">
                    {{ $weeklyComparisonText }}
                </div>
            </div>
        </section>

        {{-- Aktivitas Tap Harian Card (7-Day Bar Chart) --}}
        <section class="chart-card" aria-label="Aktivitas Tap Harian">
            <div class="chart-card-header">
                <h3 class="chart-card-title">Aktivitas Tap Harian</h3>
                @if ($maxDailyTap > 0)
                    <span class="chart-trend-badge">
                        <span>✦</span>
                        <span>Tren Positif</span>
                    </span>
                @else
                    <span class="chart-trend-badge is-flat">
                        <span>Belum Ada Data</span>
                    </span>
                @endif
            </div>

            {{-- 7-Day Bar Chart Representation --}}
            <div class="bar-chart-container" role="img" aria-label="Grafik tap 7 hari terakhir">
                @foreach ($dailyActivity as $day)
                    <div class="bar-chart-col">
                        @if ($day['is_highest'] && $day['count'] > 0)
                            <div class="bar-peak-bubble">
                                {{ $day['count'] }}
                            </div>
                        @endif

                        <div class="bar-track" title="{{ $day['day_label'] }} ({{ $day['full_date'] }}): {{ $day['count'] }} tap">
                            <div class="bar-fill {{ $day['is_highest'] ? 'is-highest' : '' }}" 
                                 style="height: {{ $day['height_percent'] }}%;"></div>
                        </div>

                        <span class="bar-day-label {{ $day['is_highest'] ? 'is-highest' : '' }}">
                            {{ $day['day_label'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Insight Callout --}}
            <div class="chart-insight-box">
                <span class="chart-insight-icon">💡</span>
                <span>{{ $highestInsightText }}</span>
            </div>
        </section>

        {{-- Distribusi Mode NFC Card (Truthful State) --}}
        <section class="mode-card" aria-label="Distribusi Mode NFC">
            <div class="mode-card-header">
                <h3 class="mode-card-title">Distribusi Mode NFC</h3>
                <span class="mode-active-chip">
                    <span class="chip-dot"></span>
                    <span>Aktif: {{ $currentActiveModeLabel }}</span>
                </span>
            </div>

            <div class="mode-distribution-wrap">
                {{-- Donut Chart Visual SVG --}}
                <div class="mode-donut-graphic" aria-hidden="true">
                    <svg viewBox="0 0 100 100" width="100" height="100">
                        <circle cx="50" cy="50" r="38" stroke="#EAE6DF" stroke-width="16" fill="transparent" />
                        {{-- Segmen utama mencerminkan mode profil aktif --}}
                        <circle cx="50" cy="50" r="38" stroke="#8B0000" stroke-width="16" fill="transparent"
                                stroke-dasharray="190 238.76" stroke-dashoffset="0" stroke-linecap="round" />
                        <circle cx="50" cy="50" r="38" stroke="#D4AF37" stroke-width="16" fill="transparent"
                                stroke-dasharray="48 238.76" stroke-dashoffset="-190" stroke-linecap="round" />
                    </svg>
                </div>

                {{-- Legend & Jujur Terhadap Skema Database --}}
                <div class="mode-details-list">
                    <div class="mode-detail-item">
                        <div class="mode-detail-left">
                            <span class="mode-bullet" style="background: #8B0000;"></span>
                            <span class="mode-detail-name">Mode Aktif Saat Ini</span>
                        </div>
                        <span class="mode-detail-val">{{ $currentActiveModeLabel }}</span>
                    </div>

                    <div class="mode-detail-item">
                        <div class="mode-detail-left">
                            <span class="mode-bullet" style="background: #D4AF37;"></span>
                            <span class="mode-detail-name">Histori Tap Per Mode</span>
                        </div>
                        <span class="mode-detail-val" style="font-size: 0.68rem; color: #78716C;">Belum tersedia</span>
                    </div>

                    <div class="mode-honest-empty-box">
                        Distribusi mode belum tersedia secara historis per tap pada sistem.
                    </div>
                </div>
            </div>
        </section>

        {{-- Hardware Teraktif Card (Maroon Theme) --}}
        <section class="hardware-card-maroon" aria-label="Hardware Teraktif">
            {{-- Ornamen Lingkaran Watermark --}}
            <svg class="hardware-circles-pattern" viewBox="0 0 100 100" fill="none" stroke="#FFFFFF" stroke-width="2" aria-hidden="true">
                <circle cx="50" cy="50" r="45" stroke-dasharray="3 3"/>
                <circle cx="50" cy="50" r="28"/>
                <circle cx="50" cy="50" r="14"/>
            </svg>

            <div class="hardware-card-top">
                <span class="hardware-label-gold">HARDWARE TERAKTIF</span>
                @if ($activeHardware && $activeHardware['dominance_percent'] > 0)
                    <span class="hardware-dominance-badge">
                        {{ $activeHardware['dominance_percent'] }}% Dominasi
                    </span>
                @else
                    <span class="hardware-dominance-badge" style="background: rgba(255,255,255,0.1); color: #FAF9F6; border-color: rgba(255,255,255,0.2);">
                        Tag Terdaftar
                    </span>
                @endif
            </div>

            @if ($activeHardware)
                <div class="hardware-main-info">
                    {{-- Thumbnail Visual --}}
                    <div class="hardware-visual-thumb">
                        @if (file_exists(public_path('images/keychain.jpg')))
                            <img src="{{ asset('images/keychain.jpg') }}" alt="Gantungan Kunci FlexiTag">
                        @else
                            <span class="hardware-visual-icon">🏷️</span>
                        @endif
                    </div>

                    <div class="hardware-details">
                        <h4 class="hardware-name">
                            {{ $activeHardware['tag_code'] }}
                        </h4>
                        <div class="hardware-meta-row">
                            <span class="hardware-meta-item">
                                <strong>{{ $activeHardware['taps_count'] }}</strong> kali interaksi
                            </span>
                            @if ($activeHardware['location'])
                                <span>•</span>
                                <span class="hardware-meta-item">
                                    📍 {{ $activeHardware['location'] }}
                                </span>
                            @endif
                            @if ($activeHardware['batch_id'])
                                <span>•</span>
                                <span class="hardware-meta-item">
                                    Batch: {{ $activeHardware['batch_id'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div style="font-size: 0.78rem; color: #F8D8DC; padding: 0.5rem 0;">
                    Belum ada hardware atau tag NFC yang tertaut dengan akun Anda.
                </div>
            @endif
        </section>

    </main>

    {{-- ========================================================================= --}}
    {{-- UNIFIED USER SIDEBAR / NAVIGATION DRAWER                                  --}}
    {{-- ========================================================================= --}}
    @include('partials.user-sidebar')

</div>

</body>
</html>
