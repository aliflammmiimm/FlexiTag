@extends('layouts.admin')

@section('title', 'Analitik Interaksi NFC')

@section('content')
    <!-- Page Title & Top Toolbar -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5 mb-7">
        <div>
            <h2 class="text-2xl sm:text-[26px] font-bold text-[#1A1A1A] tracking-tight">
                Analitik Interaksi NFC
            </h2>
            <p class="text-xs text-[#737373] mt-1 font-normal max-w-2xl leading-relaxed">
                Pantau intensitas pemindaian chip NTAG215, tren interaksi publik, dan efektivitas relay darurat di seluruh Sulawesi Selatan.
            </p>
        </div>

        <!-- Filter & Export Toolbar -->
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            
            <!-- Date Range Dropdown Button -->
            <div class="relative">
                <button 
                    type="button" 
                    onclick="toggleDateRangeDropdown()" 
                    class="bg-white hover:bg-[#F9F7F4] border border-[#ECE8E1] rounded-2xl px-4 py-2.5 text-xs font-semibold text-[#1A1A1A] flex items-center gap-2 shadow-xs transition cursor-pointer"
                >
                    <svg class="w-4 h-4 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span id="selected-range-label">
                        @if ($range === '7_days') 7 Hari Terakhir
                        @elseif ($range === '90_days') 90 Hari Terakhir
                        @elseif ($range === 'year') Tahun Ini
                        @else 30 Hari Terakhir
                        @endif
                    </span>
                    <svg class="w-3.5 h-3.5 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="date-range-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-[#ECE8E1] py-1.5 z-40 text-xs">
                    <a href="{{ route('admin.analytics', ['range' => '7_days']) }}" class="block px-4 py-2 hover:bg-[#F6F5F2] {{ $range === '7_days' ? 'font-bold text-maroon' : 'text-[#4A4A4A]' }}">7 Hari Terakhir</a>
                    <a href="{{ route('admin.analytics', ['range' => '30_days']) }}" class="block px-4 py-2 hover:bg-[#F6F5F2] {{ $range === '30_days' || empty($range) ? 'font-bold text-maroon' : 'text-[#4A4A4A]' }}">30 Hari Terakhir</a>
                    <a href="{{ route('admin.analytics', ['range' => '90_days']) }}" class="block px-4 py-2 hover:bg-[#F6F5F2] {{ $range === '90_days' ? 'font-bold text-maroon' : 'text-[#4A4A4A]' }}">90 Hari Terakhir</a>
                    <a href="{{ route('admin.analytics', ['range' => 'year']) }}" class="block px-4 py-2 hover:bg-[#F6F5F2] {{ $range === 'year' ? 'font-bold text-maroon' : 'text-[#4A4A4A]' }}">Tahun Ini (2025/2026)</a>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================= -->
    <!-- TOP 3 METRIC CARDS                                        -->
    <!-- ========================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-7">

        <!-- Card 1: TOTAL TAP KESELURUHAN -->
        <div class="bg-white rounded-3xl p-6 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase">
                        Total Tap Keseluruhan
                    </span>
                    <div class="w-10 h-10 rounded-full bg-[#FCF5E3] text-[#B8860B] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.53 16.11a6 6 0 0 1 6.95 0m-9.5-3.56a11 11 0 0 1 12.08 0M12 20h.01"/>
                        </svg>
                    </div>
                </div>
                <div class="text-3xl sm:text-4xl font-extrabold text-[#1A1A1A] tracking-tight">
                    {{ $metrics['total_tap'] }}
                </div>
            </div>

            <!-- Footer Badge -->
            <div class="mt-4 pt-3 flex items-center">
                <span class="inline-flex items-center gap-1.5 bg-[#EAF5EC] text-[#2E7D32] text-[11px] font-bold px-3 py-1 rounded-full">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/>
                    </svg>
                    <span>{{ $metrics['growth_rate'] }}</span>
                    <span class="font-normal text-[#4A7C52] ml-0.5">{{ $metrics['growth_label'] }}</span>
                </span>
            </div>
        </div>

        <!-- Card 2: RATA-RATA TAP HARIAN -->
        <div class="bg-white rounded-3xl p-6 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase">
                        Rata-Rata Tap Harian
                    </span>
                    <div class="w-10 h-10 rounded-full bg-[#FBE8E8] text-maroon flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zm-7.518-.267A8.25 8.25 0 1120.25 10.5M8.288 14.212A5.25 5.25 0 1117.25 10.5"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl sm:text-4xl font-extrabold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['avg_daily_tap'] }}
                    </span>
                    <span class="text-xs font-medium text-[#737373]">Tap / hari</span>
                </div>
            </div>

            <!-- Footer Badge -->
            <div class="mt-4 pt-3 flex items-center">
                <span class="inline-flex items-center gap-1.5 bg-[#F6F5F2] text-[#666666] text-[11px] font-medium px-3 py-1 rounded-full border border-[#ECE8E1]">
                    <svg class="w-3.5 h-3.5 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                    </svg>
                    <span>{{ $metrics['peak_hours'] }}</span>
                </span>
            </div>
        </div>

        <!-- Card 3: PANGGILAN RELAY DARURAT -->
        <div class="bg-white rounded-3xl p-6 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase">
                        Panggilan Relay Darurat
                    </span>
                    <div class="w-10 h-10 rounded-full bg-[#F5F2E8] text-[#8C6D23] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl sm:text-4xl font-extrabold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['emergency_cases'] }}
                    </span>
                    <span class="text-sm font-bold text-[#2E7D32]">
                        {{ $metrics['emergency_label'] }}
                    </span>
                </div>
            </div>

            <!-- Footer Badge -->
            <div class="mt-4 pt-3 flex items-center">
                <span class="inline-flex items-center gap-1.5 bg-[#EAF5EC] text-[#2E7D32] text-[11px] font-bold px-3 py-1 rounded-full">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
                    </svg>
                    <span>{{ $metrics['emergency_response'] }}</span>
                </span>
            </div>
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- MIDDLE SECTION: FREKUENSI SCAN NFC HARIAN (LINE CHART)    -->
    <!-- ========================================================= -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] mb-7">
        
        <!-- Header: Title & Chart Legend -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-base font-bold text-[#1A1A1A]">
                    Frekuensi Scan NFC Harian (30 Hari Terakhir)
                </h3>
                <p class="text-xs text-[#737373] mt-0.5 font-normal">
                    Statistik pemindaian langsung dari smartphone pengguna (Android &amp; iOS)
                </p>
            </div>

            <!-- Legend Pills -->
            <div class="flex items-center gap-4 text-xs font-medium shrink-0">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#540404]"></span>
                    <span class="text-[#4A4A4A]">Pemindaian Berhasil (NFC)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#D4AF37]"></span>
                    <span class="text-[#4A4A4A]">Puncak Aktivasi</span>
                </div>
            </div>
        </div>

        <!-- Responsive Interactive SVG Area Chart -->
        <div class="w-full overflow-x-auto relative">
            <div class="min-w-[640px]">
                <svg viewBox="0 0 680 250" class="w-full h-56 sm:h-64 overflow-visible">
                    <defs>
                        <!-- Area Gradient Fill -->
                        <linearGradient id="areaGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#540404" stop-opacity="0.28" />
                            <stop offset="60%" stop-color="#540404" stop-opacity="0.08" />
                            <stop offset="100%" stop-color="#540404" stop-opacity="0.0" />
                        </linearGradient>

                        <!-- Glow Filter for Peak -->
                        <filter id="glow" x="-20%" y="-20%" width="140%" height="140%">
                            <feGaussianBlur stdDeviation="3" result="glow"/>
                            <feComposite in="SourceGraphic" in2="glow" operator="over"/>
                        </filter>
                    </defs>

                    <!-- Horizontal Grid Lines and Y-Axis Labels -->
                    <!-- 200 -->
                    <line x1="45" y1="45" x2="650" y2="45" stroke="#F0ECE7" stroke-width="1" />
                    <text x="30" y="49" fill="#999999" font-size="11" font-weight="500" text-anchor="end">200</text>

                    <!-- 150 -->
                    <line x1="45" y1="85" x2="650" y2="85" stroke="#F0ECE7" stroke-width="1" />
                    <text x="30" y="89" fill="#999999" font-size="11" font-weight="500" text-anchor="end">150</text>

                    <!-- 100 -->
                    <line x1="45" y1="125" x2="650" y2="125" stroke="#F0ECE7" stroke-width="1" />
                    <text x="30" y="129" fill="#999999" font-size="11" font-weight="500" text-anchor="end">100</text>

                    <!-- 50 -->
                    <line x1="45" y1="165" x2="650" y2="165" stroke="#F0ECE7" stroke-width="1" />
                    <text x="30" y="169" fill="#999999" font-size="11" font-weight="500" text-anchor="end">50</text>

                    <!-- 0 (Baseline) -->
                    <line x1="45" y1="205" x2="650" y2="205" stroke="#ECE8E1" stroke-width="1.2" />
                    <text x="30" y="209" fill="#999999" font-size="11" font-weight="500" text-anchor="end">0</text>

                    <!-- Vertical Dotted Line for Peak (14 Mei at x=520) -->
                    <line x1="520" y1="58" x2="520" y2="205" stroke="#D4AF37" stroke-width="1.5" stroke-dasharray="3,3" />

                    <!-- Area Fill Polygon Under Curve -->
                    <path 
                        d="M 45 195 
                           C 90 185, 100 178, 140 170 
                           C 180 160, 200 155, 235 148 
                           C 275 140, 300 125, 335 112 
                           C 375 95, 400 80, 435 70 
                           C 470 60, 495 56, 520 58 
                           C 555 60, 580 82, 615 72 
                           L 615 205 L 45 205 Z" 
                        fill="url(#areaGradient)" 
                    />

                    <!-- Main Spline Stroke Curve -->
                    <path 
                        d="M 45 195 
                           C 90 185, 100 178, 140 170 
                           C 180 160, 200 155, 235 148 
                           C 275 140, 300 125, 335 112 
                           C 375 95, 400 80, 435 70 
                           C 470 60, 495 56, 520 58 
                           C 555 60, 580 82, 615 72" 
                        fill="none" 
                        stroke="#540404" 
                        stroke-width="2.8" 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                    />

                    <!-- Data Point Circles -->
                    <!-- 20 Apr (x=45, y=195) -->
                    <circle cx="45" cy="195" r="4.5" fill="#FFFFFF" stroke="#540404" stroke-width="2.5" class="hover:r-6 cursor-pointer transition-all" />

                    <!-- 25 Apr (x=140, y=170) -->
                    <circle cx="140" cy="170" r="4.5" fill="#FFFFFF" stroke="#540404" stroke-width="2.5" class="hover:r-6 cursor-pointer transition-all" />

                    <!-- 30 Apr (x=235, y=148) -->
                    <circle cx="235" cy="148" r="4.5" fill="#FFFFFF" stroke="#540404" stroke-width="2.5" class="hover:r-6 cursor-pointer transition-all" />

                    <!-- 05 Mei (x=335, y=112) -->
                    <circle cx="335" cy="112" r="4.5" fill="#FFFFFF" stroke="#540404" stroke-width="2.5" class="hover:r-6 cursor-pointer transition-all" />

                    <!-- 10 Mei (x=435, y=70) -->
                    <circle cx="435" cy="70" r="4.5" fill="#FFFFFF" stroke="#540404" stroke-width="2.5" class="hover:r-6 cursor-pointer transition-all" />

                    <!-- 14 Mei PEAK POINT (x=520, y=58) -->
                    <!-- Outer Halo -->
                    <circle cx="520" cy="58" r="9" fill="#D4AF37" fill-opacity="0.3" />
                    <!-- Gold Peak Node -->
                    <circle cx="520" cy="58" r="5.5" fill="#D4AF37" stroke="#FFFFFF" stroke-width="2.2" class="cursor-pointer" />

                    <!-- 19 Mei (x=615, y=72) -->
                    <circle cx="615" cy="72" r="4.5" fill="#FFFFFF" stroke="#540404" stroke-width="2.5" class="hover:r-6 cursor-pointer transition-all" />

                    <!-- FLOATING TOOLTIP BOX (above 14 Mei Peak) -->
                    <g transform="translate(520, 36)">
                        <!-- Tooltip Container Box -->
                        <rect x="-48" y="-24" width="96" height="26" rx="6" fill="#380208" filter="url(#glow)" />
                        <!-- Tooltip Pointer Arrow -->
                        <polygon points="-4,2 4,2 0,6" fill="#380208" />
                        <!-- Date text -->
                        <text x="0" y="-12" fill="#F0C75E" font-size="8.5" font-weight="700" text-anchor="middle" letter-spacing="0.5">14 MEI 2025</text>
                        <!-- Value text -->
                        <text x="0" y="-2" fill="#FFFFFF" font-size="9.5" font-weight="800" text-anchor="middle">184 Tap (Peak)</text>
                    </g>

                    <!-- X-Axis Date Labels -->
                    <text x="45" y="228" fill="#737373" font-size="11" font-weight="500" text-anchor="middle">20 Apr</text>
                    <text x="140" y="228" fill="#737373" font-size="11" font-weight="500" text-anchor="middle">25 Apr</text>
                    <text x="235" y="228" fill="#737373" font-size="11" font-weight="500" text-anchor="middle">30 Apr</text>
                    <text x="335" y="228" fill="#737373" font-size="11" font-weight="500" text-anchor="middle">05 Mei</text>
                    <text x="435" y="228" fill="#737373" font-size="11" font-weight="500" text-anchor="middle">10 Mei</text>
                    
                    <!-- 14 Mei (Bold Highlighted Red) -->
                    <text x="520" y="228" fill="#540404" font-size="11.5" font-weight="800" text-anchor="middle">14 Mei</text>

                    <text x="615" y="228" fill="#737373" font-size="11" font-weight="500" text-anchor="middle">19 Mei</text>
                </svg>
            </div>
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- BOTTOM SECTION: MODE DISTRIBUTION & GEOGRAPHIC REGIONS    -->
    <!-- ========================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-7">

        <!-- ===================================================== -->
        <!-- CARD 1: MODE PALING SERING DIAKSES (DONUT + BREAKDOWN)-->
        <!-- ===================================================== -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <div>
                <!-- Header -->
                <div class="mb-6">
                    <h3 class="text-base font-bold text-[#1A1A1A]">
                        Mode Paling Sering Diakses
                    </h3>
                    <p class="text-xs text-[#737373] mt-0.5 font-normal">
                        Distribusi profil yang ditampilkan saat merchandise disentuh
                    </p>
                </div>

                <!-- Donut Chart & Breakdown List Row -->
                <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-8">
                    
                    <!-- SVG Donut Chart -->
                    <div class="relative w-44 h-44 shrink-0 flex items-center justify-center">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 160 160">
                            <!-- Background Track Circle -->
                            <circle cx="80" cy="80" r="58" fill="none" stroke="#F6F5F2" stroke-width="20" />

                            <!-- Segment 1: Mode Kampus (45%) -->
                            <!-- Perimeter = 2 * PI * 58 ≈ 364.42 -->
                            <!-- 45% = 163.99 -->
                            <circle 
                                cx="80" 
                                cy="80" 
                                r="58" 
                                fill="none" 
                                stroke="#540404" 
                                stroke-width="20" 
                                stroke-dasharray="163.99 364.42" 
                                stroke-dashoffset="0"
                            />

                            <!-- Segment 2: Mode UMKM & Bisnis (30%) -->
                            <!-- 30% = 109.33, Offset = -163.99 -->
                            <circle 
                                cx="80" 
                                cy="80" 
                                r="58" 
                                fill="none" 
                                stroke="#D4AF37" 
                                stroke-width="20" 
                                stroke-dasharray="109.33 364.42" 
                                stroke-dashoffset="-163.99"
                            />

                            <!-- Segment 3: Mode Kerja & Portofolio (15%) -->
                            <!-- 15% = 54.66, Offset = -(163.99 + 109.33) = -273.32 -->
                            <circle 
                                cx="80" 
                                cy="80" 
                                r="58" 
                                fill="none" 
                                stroke="#1A1A1A" 
                                stroke-width="20" 
                                stroke-dasharray="54.66 364.42" 
                                stroke-dashoffset="-273.32"
                            />

                            <!-- Segment 4: Mode Barang Hilang (10%) -->
                            <!-- 10% = 36.44, Offset = -(273.32 + 54.66) = -327.98 -->
                            <circle 
                                cx="80" 
                                cy="80" 
                                r="58" 
                                fill="none" 
                                stroke="#A39E93" 
                                stroke-width="20" 
                                stroke-dasharray="36.44 364.42" 
                                stroke-dashoffset="-327.98"
                            />
                        </svg>

                        <!-- Center Label inside Donut -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <span class="text-xl font-extrabold text-[#1A1A1A] leading-tight">
                                3.420
                            </span>
                            <span class="text-[8.5px] uppercase font-bold tracking-wider text-[#8A8A85]">
                                Total Tap
                            </span>
                            <span class="text-[9px] font-bold text-[#2E7D32] mt-0.5">
                                100% Valid
                            </span>
                        </div>
                    </div>

                    <!-- Breakdown List (Right Column) -->
                    <div class="flex-1 w-full space-y-3.5 text-xs">
                        @foreach ($modeBreakdown as $mode)
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $mode['dot_color'] }} shrink-0"></span>
                                    <span class="font-medium text-[#1A1A1A]">{{ $mode['name'] }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-[#737373] text-[11.5px]">{{ $mode['taps'] }} tap</span>
                                    <span class="{{ $mode['badge_bg'] }} {{ $mode['badge_text'] }} font-bold text-[10.5px] px-2 py-0.5 rounded-md min-w-[38px] text-center">
                                        {{ $mode['percentage'] }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

            <!-- Card Footer: Kategori Terbanyak -->
            <div class="border-t border-[#F2EEE9] pt-4 mt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
                <span class="text-[#737373]">
                    Kategori terbanyak: <strong class="text-[#1A1A1A]">Kartu Identitas Mahasiswa UNHAS</strong>
                </span>
                <a href="{{ route('admin.tags') }}" class="font-bold text-maroon hover:text-[#5A0000] flex items-center gap-1 transition">
                    <span>Detail Profil</span>
                    <span class="text-sm">→</span>
                </a>
            </div>

        </div>

        <!-- ===================================================== -->
        <!-- CARD 2: WILAYAH INTERAKSI TERBANYAK (SULSEL)          -->
        <!-- ===================================================== -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <div>
                <!-- Header -->
                <div class="mb-6">
                    <h3 class="text-base font-bold text-[#1A1A1A]">
                        Wilayah Interaksi Terbanyak (Sulawesi Selatan)
                    </h3>
                    <p class="text-xs text-[#737373] mt-0.5 font-normal">
                        Konsentrasi geolokasi node transmisi interaksi aktif
                    </p>
                </div>

                <!-- Region Progress Bars List -->
                <div class="space-y-4">
                    @foreach ($regions as $region)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-[#1A1A1A]">{{ $region['rank'] }}. {{ $region['name'] }}</span>
                                    @if ($region['badge'])
                                        <span class="{{ $region['badge_class'] }} text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full tracking-wider">
                                            {{ $region['badge'] }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[#737373] text-[11.5px]">{{ $region['taps'] }}</span>
                                    <span class="font-bold text-[#1A1A1A] min-w-[32px] text-right">{{ $region['percentage'] }}%</span>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="w-full h-2.5 bg-[#F6F5F2] rounded-full overflow-hidden border border-[#ECE8E1]/60">
                                <div 
                                    class="h-full {{ $region['bar_color'] }} rounded-full transition-all duration-500" 
                                    style="width: {{ $region['percentage'] }}%;"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Card Footer: Node Regional Connection Status -->
            <div class="border-t border-[#F2EEE9] pt-4 mt-6 flex items-center gap-2 text-xs text-[#737373]">
                <span class="w-2 h-2 rounded-full bg-[#2E7D32] animate-pulse shrink-0"></span>
                <span>Terhubung otomatis via Node Regional <strong class="text-[#1A1A1A]">Makassar Core-01</strong> (Latency &lt; 42ms)</span>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
<script>
    function toggleDateRangeDropdown() {
        const menu = document.getElementById('date-range-dropdown');
        menu.classList.toggle('hidden');
    }

    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('date-range-dropdown');
        if (dropdown && !dropdown.contains(e.target) && !e.target.closest('button[onclick="toggleDateRangeDropdown()"]')) {
            dropdown.classList.add('hidden');
        }
    });
</script>
@endpush
