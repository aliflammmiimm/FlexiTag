@extends('layouts.admin')

@section('title', 'Ringkasan Operasional')

@section('content')
    <!-- Page Title & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
        <div>
            <h2 class="text-2xl sm:text-[26px] font-bold text-[#1A1A1A] tracking-tight">
                Ringkasan Operasional
            </h2>
            <p class="text-xs text-[#737373] mt-1 font-normal">
                Pemantauan ekosistem perangkat NFC FlexiTag dan integrasi digital Sulawesi Selatan.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <!-- Date Filter Dropdown (FR-ADM-01) -->
            <div class="relative">
                <button 
                    type="button" 
                    onclick="toggleDashboardPeriodDropdown()" 
                    class="bg-white border border-[#E8E4DC] rounded-full px-4 py-2 text-xs font-semibold text-[#1A1A1A] shadow-xs flex items-center gap-2 cursor-pointer hover:border-[#D4AF37] hover:bg-[#FAF9F6] transition"
                    title="Pilih rentang waktu filter analitik operasional"
                >
                    <svg class="w-4 h-4 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                    <span>{{ $periodLabel }}</span>
                    <svg class="w-3.5 h-3.5 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="dashboard-period-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-[#ECE8E1] py-1.5 z-40 text-xs">
                    <a href="{{ route('admin.dashboard', ['period' => 'pekan_ini']) }}" class="flex items-center justify-between px-4 py-2 hover:bg-[#F6F5F2] {{ $period === 'pekan_ini' || empty($period) ? 'font-bold text-maroon bg-[#FBF7F7]' : 'text-[#4A4A4A]' }}">
                        <span>Pekan Ini</span>
                        @if ($period === 'pekan_ini' || empty($period))
                            <span class="text-maroon text-xs">✓</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.dashboard', ['period' => 'bulan_ini']) }}" class="flex items-center justify-between px-4 py-2 hover:bg-[#F6F5F2] {{ $period === 'bulan_ini' ? 'font-bold text-maroon bg-[#FBF7F7]' : 'text-[#4A4A4A]' }}">
                        <span>Bulan Ini</span>
                        @if ($period === 'bulan_ini')
                            <span class="text-maroon text-xs">✓</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.dashboard', ['period' => 'tahun_ini']) }}" class="flex items-center justify-between px-4 py-2 hover:bg-[#F6F5F2] {{ $period === 'tahun_ini' ? 'font-bold text-maroon bg-[#FBF7F7]' : 'text-[#4A4A4A]' }}">
                        <span>Tahun Ini</span>
                        @if ($period === 'tahun_ini')
                            <span class="text-maroon text-xs">✓</span>
                        @endif
                    </a>
                    <div class="border-t border-[#F2EEE9] my-1"></div>
                    <a href="{{ route('admin.dashboard', ['period' => 'semua']) }}" class="flex items-center justify-between px-4 py-2 hover:bg-[#F6F5F2] {{ $period === 'semua' ? 'font-bold text-maroon bg-[#FBF7F7]' : 'text-[#4A4A4A]' }}">
                        <span>Semua Waktu</span>
                        @if ($period === 'semua')
                            <span class="text-maroon text-xs">✓</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- 4 TOP METRIC CARDS                                        -->
    <!-- ========================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-7">

        <!-- Card 1: TOTAL PENDAPATAN -->
        <a href="{{ route('admin.settings', ['tab' => 'buku-kas']) }}" class="block bg-white hover:border-[#D4AF37]/60 rounded-2xl p-5 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] transition group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold tracking-wider text-[#8A8A85] uppercase group-hover:text-maroon transition-colors">
                    Total Pendapatan
                </span>
                <div class="w-8 h-8 rounded-lg bg-[#F0EBE1] text-[#7A735E] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <line x1="2" y1="10" x2="22" y2="10"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-bold text-[#1A1A1A] tracking-tight">
                    {{ $metrics['total_pendapatan'] }}
                </h3>
                @if ($metrics['is_growth_positive'])
                    <p class="text-xs font-semibold text-[#2E7D32] flex items-center gap-1 mt-1">
                        <span>📈 {{ $metrics['growth_pendapatan'] }}</span>
                        <span class="text-[#737373] font-normal">{{ $metrics['growth_vs'] }}</span>
                    </p>
                @else
                    <p class="text-xs font-semibold text-maroon flex items-center gap-1 mt-1">
                        <span>📉 {{ $metrics['growth_pendapatan'] }}</span>
                        <span class="text-[#737373] font-normal">{{ $metrics['growth_vs'] }}</span>
                    </p>
                @endif
            </div>
        </a>

        <!-- Card 2: TOTAL PESANAN -->
        <a href="{{ route('admin.orders') }}" class="block bg-white hover:border-[#D4AF37]/60 rounded-2xl p-5 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] transition group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold tracking-wider text-[#8A8A85] uppercase group-hover:text-maroon transition-colors">
                    Total Pesanan
                </span>
                <div class="w-8 h-8 rounded-lg bg-[#F7EBEB] text-[#8B3A3A] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-bold text-[#1A1A1A] tracking-tight">
                    {{ $metrics['total_pesanan'] }}
                </h3>
                <p class="text-xs font-semibold text-[#1A1A1A] flex items-center gap-1 mt-1">
                    <span>↑ {{ $metrics['new_orders'] }}</span>
                </p>
            </div>
        </a>

        <!-- Card 3: TAG AKTIF -->
        <a href="{{ route('admin.tags', ['status' => 'aktif']) }}" class="block bg-white hover:border-[#D4AF37]/60 rounded-2xl p-5 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] transition group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold tracking-wider text-[#8A8A85] uppercase group-hover:text-maroon transition-colors">
                    Tag Aktif
                </span>
                <div class="w-8 h-8 rounded-lg bg-[#FCF5E3] text-[#B8860B] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                        <circle cx="6" cy="6" r="1"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-bold text-[#1A1A1A] tracking-tight">
                    {{ $metrics['tag_aktif'] }}
                </h3>
                <p class="text-xs text-[#555] flex items-center gap-1.5 mt-1 font-medium">
                    <span class="w-2 h-2 rounded-full bg-[#7F6B2A]"></span>
                    <span>{{ $metrics['rasio_aktif'] }} dari {{ $metrics['total_tag'] }} total tag</span>
                </p>
            </div>
        </a>

        <!-- Card 4: PERLU DIKIRIM -->
        <a href="{{ route('admin.orders', ['status' => 'perlu_dikirim']) }}" class="block bg-white hover:border-[#D4AF37]/60 rounded-2xl p-5 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] transition group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold tracking-wider text-[#8A8A85] uppercase group-hover:text-maroon transition-colors">
                    Perlu Diproses / Kirim
                </span>
                <div class="w-8 h-8 rounded-lg bg-[#FCF5E3] text-[#B8860B] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25H4.5a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25h1.5m8.25-11.25V16.5"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-bold text-[#1A1A1A] tracking-tight">
                    {{ $metrics['perlu_dikirim'] }}
                </h3>
                <p class="text-xs font-semibold text-[#8B0000] flex items-center gap-1 mt-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                    </svg>
                    <span>
                        @if ($metrics['siap_pickup'] > 0)
                            {{ $metrics['siap_pickup'] }} siap pickup kurir
                        @else
                            Dalam antrean produksi
                        @endif
                    </span>
                </p>
            </div>
        </a>

    </div>

    <!-- ========================================================= -->
    <!-- MIDDLE ROW: WEEKLY CHART + CUSTOMER MODE DISTRIBUTION     -->
    <!-- ========================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-7">

        <!-- LEFT: Tren Penjualan & Aktivasi Mingguan (Col 7) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <div>
                <!-- Card Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                    <div>
                        <h3 class="text-base font-bold text-[#1A1A1A]">
                            Tren Penjualan &amp; Aktivasi Mingguan
                        </h3>
                        <p class="text-xs text-[#737373] mt-0.5 font-normal">
                            Perbandingan volume transaksi produk fisik vs interaksi aktivasi tap sepekan
                        </p>
                    </div>

                    <!-- Legend -->
                    <div class="flex items-center gap-4 text-xs font-medium shrink-0">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-xs bg-[#540404]"></span>
                            <span class="text-[#4A4A4A]">Penjualan Fisik</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-xs bg-[#7F6B2A]"></span>
                            <span class="text-[#4A4A4A]">Aktivasi Tap</span>
                        </div>
                    </div>
                </div>

                <!-- Paired Bar Chart Visualization -->
                <div class="pt-6 pb-2">
                    <div class="h-44 sm:h-48 flex items-end justify-between gap-2 sm:gap-6 px-2 sm:px-4 border-b border-[#F0ECE7]">
                        @foreach ($weeklyTrends as $trend)
                            <div class="flex flex-col items-center flex-1 h-full justify-end relative group">
                                
                                <!-- Peak Badge on Highest Activity Day -->
                                @if ($trend['is_peak'])
                                    <div class="absolute -top-7 z-10">
                                        <span class="bg-[#F0C75E] text-[#380208] text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full shadow-xs">
                                            Puncak
                                        </span>
                                    </div>
                                @endif

                                <!-- Bars Container -->
                                <div class="flex items-end gap-1 sm:gap-1.5 w-full justify-center">
                                    <!-- Penjualan Bar (Maroon) -->
                                    <div 
                                        class="w-3 sm:w-4 bg-[#540404] rounded-t-xs hover:brightness-125 transition-all duration-300 {{ $trend['penjualan'] === 0 ? 'opacity-30' : '' }}"
                                        style="height: {{ $trend['penjualan_height'] }}px;"
                                        title="{{ $trend['day_full'] }} ({{ $trend['date_formatted'] }}) — Penjualan: {{ $trend['penjualan'] }} unit"
                                    ></div>

                                    <!-- Aktivasi Bar (Olive/Gold) -->
                                    <div 
                                        class="w-3 sm:w-4 bg-[#7F6B2A] rounded-t-xs hover:brightness-125 transition-all duration-300 {{ $trend['aktivasi'] === 0 ? 'opacity-30' : '' }}"
                                        style="height: {{ $trend['aktivasi_height'] }}px;"
                                        title="{{ $trend['day_full'] }} ({{ $trend['date_formatted'] }}) — Aktivasi/Tap: {{ $trend['aktivasi'] }} interaksi"
                                    ></div>
                                </div>

                                <!-- Day Label & Date -->
                                <div class="text-center mt-2">
                                    <span class="block text-[11px] font-medium {{ $trend['is_peak'] ? 'font-bold text-[#540404]' : 'text-[#737373]' }}">
                                        {{ $trend['day'] }}
                                    </span>
                                    <span class="block text-[9px] text-[#A39E93]">
                                        {{ $trend['date_formatted'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Footer Summary Info -->
            <div class="border-t border-[#F2EEE9] pt-4 mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-1.5 text-[#737373]">
                    <span class="w-2 h-2 rounded-full bg-[#2E7D32] shrink-0"></span>
                    <span>Sinkronisasi otomatis dengan server database lokal FlexiTag</span>
                </div>

                <div class="font-bold text-[#1A1A1A]">
                    Rerata {{ $avgTapPerTag }} tap / perangkat aktif
                </div>
            </div>
        </div>

        <!-- RIGHT: Distribusi Mode Pelanggan (Col 5) -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <div>
                <!-- Header -->
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-base font-bold text-[#1A1A1A]">
                            Distribusi Mode Pelanggan
                        </h3>
                        <p class="text-xs text-[#737373] mt-0.5 font-normal">
                            Preferensi fungsional chip NTAG213 pada {{ $totalActiveTagsCount ?? 0 }} tag aktif
                        </p>
                    </div>

                    <!-- Sliders Filter Icon -->
                    <a href="{{ route('admin.tags') }}" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1.5 transition rounded-lg hover:bg-[#F6F5F2]" title="Buka Manajemen Inventaris Tag">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/>
                        </svg>
                    </a>
                </div>

                <!-- Mode Bars List -->
                <div class="space-y-4">
                    @foreach ($modeDistribution as $mode)
                        <a href="{{ route('admin.tags', ['mode' => $mode['key']]) }}" class="block group transition" title="Lihat daftar tag dengan {{ $mode['name'] }}">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $mode['bg_dot'] }}"></span>
                                    <span class="font-semibold text-[#1A1A1A] group-hover:text-maroon transition-colors">{{ $mode['name'] }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="bg-[#F6F5F2] group-hover:bg-[#ECE8E1] text-[#666666] text-[11px] font-medium px-2.5 py-0.5 rounded-full transition-colors">
                                        {{ $mode['count'] }} Tag
                                    </span>
                                    <span class="font-bold text-xs text-[#1A1A1A] w-9 text-right">
                                        {{ $mode['percentage'] }}%
                                    </span>
                                </div>
                            </div>

                            <!-- Progress Track -->
                            <div class="w-full bg-[#F0ECE7] h-2 rounded-full overflow-hidden">
                                <div 
                                    class="{{ $mode['bg_bar'] }} h-full rounded-full transition-all duration-500" 
                                    style="width: {{ $mode['percentage'] }}%;"
                                ></div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Mode Switch Notice Banner -->
            <div class="bg-[#FAF9F6] rounded-2xl p-3.5 flex items-start justify-between gap-2.5 mt-6 border border-[#ECE8E1]">
                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-[#8A8A85] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <p class="text-[11px] text-[#737373] leading-relaxed font-normal">
                        Total <strong>{{ $totalActiveTagsCount ?? 0 }}</strong> tag aktif terhubung. Mode dapat dialihkan mandiri oleh pemilik ganci via tap NFC.
                    </p>
                </div>
                <a href="{{ route('admin.tags') }}" class="text-[11px] text-maroon font-bold shrink-0 hover:underline self-center whitespace-nowrap">
                    Kelola Tag →
                </a>
            </div>
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- BOTTOM SECTION: PESANAN TERBARU (5 TERAKHIR)              -->
    <!-- ========================================================= -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)]">
        
        <!-- Table Section Header -->
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-base font-bold text-[#1A1A1A]">
                    Pesanan Terbaru (5 Terakhir)
                </h3>
                <p class="text-xs text-[#737373] mt-0.5 font-normal">
                    Transaksi fisik yang baru diproses di Sulawesi Selatan
                </p>
            </div>

            <a href="{{ route('admin.orders') }}" class="text-xs font-semibold text-maroon hover:text-[#5A0000] flex items-center gap-1 transition">
                <span>Lihat Semua</span>
                <span class="text-sm">→</span>
            </a>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto rounded-2xl border border-[#ECE8E1]/80">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-[#FAF8F5] text-[#8A8A85] text-[10.5px] font-bold uppercase tracking-wider border-b border-[#ECE8E1]">
                        <th class="py-3.5 px-5">ID Pesanan</th>
                        <th class="py-3.5 px-5">Nama Pembeli</th>
                        <th class="py-3.5 px-5">Produk</th>
                        <th class="py-3.5 px-5">Status Tahapan SRS</th>
                        <th class="py-3.5 px-5">Status Tag</th>
                        <th class="py-3.5 px-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F2EEE9]">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-[#FAF9F6] transition-colors">
                            <!-- ID Pesanan -->
                            <td class="py-4 px-5">
                                <a href="{{ route('admin.orders', ['search' => $order['id_pesanan']]) }}" class="font-bold text-maroon hover:underline">
                                    {{ $order['id_pesanan'] }}
                                </a>
                                @if (isset($order['channel']) && $order['channel'] === 'offline')
                                    <span class="inline-block mt-0.5 bg-[#FEF5E7] text-[#A86414] text-[9.5px] font-semibold px-2 py-0.5 rounded-full border border-[#FAD7A0]">
                                        Offline
                                    </span>
                                @else
                                    <span class="inline-block mt-0.5 bg-[#EBF5FB] text-[#1E70A0] text-[9.5px] font-semibold px-2 py-0.5 rounded-full border border-[#BAE6FD]">
                                        Online Web
                                    </span>
                                @endif
                            </td>

                            <!-- Nama Pembeli -->
                            <td class="py-4 px-5">
                                <div class="font-bold text-[#1A1A1A]">{{ $order['nama_pembeli'] }}</div>
                                <div class="text-[11px] text-[#737373] font-normal">({{ $order['kota'] }})</div>
                            </td>

                            <!-- Produk dengan Ikon -->
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-2.5 font-medium text-[#1A1A1A]">
                                    <svg class="w-4 h-4 text-[#8A8A85] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                                    </svg>
                                    <span>{{ $order['produk'] }}</span>
                                </div>
                                @if (isset($order['quantity']) && $order['quantity'] > 1)
                                    <span class="text-[10px] text-[#8A8A85] mt-0.5 block font-normal">
                                        Jumlah: {{ $order['quantity'] }} pcs
                                    </span>
                                @endif
                            </td>

                            <!-- Status Tahapan SRS (Mapped Badge) -->
                            <td class="py-4 px-5">
                                <span class="inline-flex items-center gap-1.5 {{ $order['badge_class'] ?? 'bg-[#F6F5F2] text-[#737373]' }} font-semibold text-[11px] px-3 py-1 rounded-full shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $order['badge_dot'] ?? 'bg-current' }}"></span>
                                    {{ $order['badge_label'] ?? 'Status' }}
                                </span>
                                @if (isset($order['detail_status']))
                                    <span class="block text-[10px] text-[#8A8A85] mt-0.5">
                                        {{ $order['detail_status'] }}
                                    </span>
                                @endif
                            </td>

                            <!-- Status Tag -->
                            <td class="py-4 px-5">
                                @if (($order['status_tag'] ?? '') === 'aktif')
                                    <span class="inline-flex items-center gap-1 bg-[#EAF5EC] text-[#2E7D32] font-semibold text-[11px] px-3 py-1 rounded-full">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                        </svg>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-[#F0ECE7] text-[#736B5E] font-semibold text-[11px] px-3 py-1 rounded-full">
                                        <span class="w-2 h-2 rounded-full border border-current"></span>
                                        Belum Aktif
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-5 text-center">
                                <a 
                                    href="{{ route('admin.orders', ['search' => $order['id_pesanan']]) }}" 
                                    class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] font-semibold text-[11px] px-3.5 py-1 rounded-full border border-[#E5E0D8] transition inline-block"
                                >
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-[#8A8A85]">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-[#D1CCC4]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                    </svg>
                                    <span class="text-xs font-medium">Belum ada transaksi pesanan yang tercatat.</span>
                                    <a href="{{ route('admin.orders') }}" class="text-xs text-maroon font-semibold hover:underline">
                                        Buka Antrean Pesanan →
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    function toggleDashboardPeriodDropdown() {
        const menu = document.getElementById('dashboard-period-dropdown');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('dashboard-period-dropdown');
        if (dropdown && !dropdown.contains(e.target) && !e.target.closest('button[onclick="toggleDashboardPeriodDropdown()"]')) {
            dropdown.classList.add('hidden');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('dashboard-period-dropdown')?.classList.add('hidden');
        }
    });
</script>
@endpush
