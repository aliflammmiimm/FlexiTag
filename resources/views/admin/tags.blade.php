@extends('layouts.admin')

@section('title', 'Inventaris & Status NFC Tag')

@section('content')
    <!-- Top System Badges Row -->
    <div class="flex justify-end gap-2.5 mb-2">

        <!-- Hardware & Sync Badges -->
        <div class="flex items-center gap-2.5 shrink-0">
            <!-- Chip Info -->
            <div class="inline-flex items-center gap-2 bg-[#F0ECE7]/80 border border-[#ECE8E1] px-3.5 py-1.5 rounded-full text-xs font-medium text-[#555]">
                <svg class="w-4 h-4 text-[#7A735E]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="4" y="4" width="16" height="16" rx="2"/>
                    <rect x="9" y="9" width="6" height="6"/>
                    <line x1="9" y1="1" x2="9" y2="4"/>
                    <line x1="15" y1="1" x2="15" y2="4"/>
                    <line x1="9" y1="20" x2="9" y2="23"/>
                    <line x1="15" y1="20" x2="15" y2="23"/>
                    <line x1="20" y1="9" x2="23" y2="9"/>
                    <line x1="20" y1="15" x2="23" y2="15"/>
                    <line x1="1" y1="9" x2="4" y2="9"/>
                    <line x1="1" y1="15" x2="4" y2="15"/>
                </svg>
                <span>Chiptype: <strong>NXP NTAG215 (504 Bytes)</strong></span>
            </div>

            <!-- Sync Live Indicator -->
            <div class="inline-flex items-center gap-2 bg-[#FCE8E8] text-maroon font-bold text-xs px-3.5 py-1.5 rounded-full border border-[#F5C2C2]">
                <span class="w-2 h-2 rounded-full bg-maroon animate-pulse"></span>
                <span>Sync Live: Online</span>
            </div>
        </div>
    </div>

    <!-- Page Title & Search Toolbar -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5 mb-7">
        <div>
            <h2 class="text-2xl sm:text-[26px] font-bold text-[#1A1A1A] tracking-tight">
                Inventaris &amp; Status NFC Tag
            </h2>
            <p class="text-xs text-[#737373] mt-1 font-normal max-w-xl leading-relaxed">
                Pantau siklus hidup, status aktivasi chip NTAG215 Toraja-Bugis Heritage series, dan kepemilikan tag fisik secara tersinkronisasi.
            </p>
        </div>

        <!-- Filter & Action Toolbar -->
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <!-- Search Form -->
            <form action="{{ route('admin.tags') }}" method="GET" class="relative">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <svg class="w-4 h-4 text-[#8A8A85] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}"
                    placeholder="Cari Kode Aktivasi / Pemilik..." 
                    class="w-56 sm:w-64 bg-white border border-[#ECE8E1] rounded-2xl pl-10 pr-4 py-2 text-xs text-[#1A1A1A] placeholder-[#9E9E98] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] shadow-xs transition"
                >
            </form>

            <!-- Status Dropdown Filter -->
            <div class="relative">
                <button 
                    type="button" 
                    onclick="toggleStatusDropdown()" 
                    class="bg-white hover:bg-[#F9F7F4] border border-[#ECE8E1] rounded-2xl px-3.5 py-2 text-xs font-semibold text-[#1A1A1A] flex items-center gap-2 shadow-xs transition cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18m-14 7.5h10m-7 7.5h4"/>
                    </svg>
                    <span>{{ $statusFilter === 'aktif' ? 'Status: Aktif' : ($statusFilter === 'belum_aktif' ? 'Status: Stok' : 'Semua Status') }}</span>
                    <svg class="w-3 h-3 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>
                <div id="status-dropdown-menu" class="hidden absolute right-0 mt-1.5 w-44 bg-white rounded-2xl shadow-xl border border-[#ECE8E1] py-1.5 z-40 text-xs">
                    <a href="{{ route('admin.tags', ['status' => 'all', 'search' => $search]) }}" class="block px-4 py-2 hover:bg-[#F6F5F2] {{ $statusFilter === 'all' ? 'font-bold text-maroon' : 'text-[#4A4A4A]' }}">Semua Status</a>
                    <a href="{{ route('admin.tags', ['status' => 'aktif', 'search' => $search]) }}" class="block px-4 py-2 hover:bg-[#F6F5F2] {{ $statusFilter === 'aktif' ? 'font-bold text-maroon' : 'text-[#4A4A4A]' }}">Aktif</a>
                    <a href="{{ route('admin.tags', ['status' => 'belum_aktif', 'search' => $search]) }}" class="block px-4 py-2 hover:bg-[#F6F5F2] {{ $statusFilter === 'belum_aktif' ? 'font-bold text-maroon' : 'text-[#4A4A4A]' }}">Belum Aktif (Stok)</a>
                    <a href="{{ route('admin.tags', ['status' => 'diblokir', 'search' => $search]) }}" class="block px-4 py-2 hover:bg-[#F6F5F2] {{ $statusFilter === 'diblokir' ? 'font-bold text-maroon' : 'text-[#4A4A4A]' }}">Diblokir</a>
                </div>
            </div>

            <!-- Export CSV Button -->
            <a 
                href="javascript:void(0)" 
                onclick="alert('Mengekspor inventaris 500 tag ke file CSV...')" 
                class="bg-white hover:bg-[#F9F7F4] border border-[#ECE8E1] rounded-2xl px-3.5 py-2 text-xs font-semibold text-[#1A1A1A] flex items-center gap-1.5 shadow-xs transition"
            >
                <svg class="w-3.5 h-3.5 text-[#737373]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                <span>Export CSV</span>
            </a>

            <!-- + Cetak Baru Button (Direct to Generate Tag) -->
            <a 
                href="{{ route('admin.generate') }}" 
                class="bg-gold-accent hover:bg-[#E5BC50] active:scale-95 text-[#1A1A1A] font-bold text-xs px-4 py-2 rounded-2xl shadow-xs flex items-center gap-1.5 transition"
            >
                <span>+ Cetak Baru</span>
            </a>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- TOP 3 METRIC CARDS                                        -->
    <!-- ========================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-7">

        <!-- Card 1: BATCH PRODUKSI MAKASSAR -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase block">
                            Batch Produksi Makassar
                        </span>
                        <h4 class="text-xs font-semibold text-[#737373] mt-0.5">
                            Total Tag Diproduksi
                        </h4>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-[#F7EBEB] text-[#8B3A3A] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                </div>

                <div class="flex items-baseline gap-2.5 mt-3">
                    <h3 class="text-3xl font-extrabold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['total_diproduksi'] }}
                    </h3>
                    <span class="bg-[#F6F5F2] text-[#666] text-xs font-bold px-2.5 py-0.5 rounded-full">
                        Unit NTAG215
                    </span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-[#F2EEE9] flex items-center gap-1.5 text-xs text-[#737373]">
                <svg class="w-4 h-4 text-[#4E6333]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Alokasi Kloter I &amp; II (Toraja &amp; Bugis)</span>
            </div>
        </div>

        <!-- Card 2: KONEKSI DINAMIS TERPAUT -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase block">
                            Koneksi Dinamis Terpaut
                        </span>
                        <h4 class="text-xs font-semibold text-[#737373] mt-0.5">
                            Tag Terhubung (Aktif)
                        </h4>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-[#FCF5E3] text-[#B8860B] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.53 16.11a6 6 0 0 1 6.95 0m-9.5-3.56a11 11 0 0 1 12.08 0M12 20h.01"/>
                        </svg>
                    </div>
                </div>

                <div class="flex items-baseline gap-2.5 mt-3">
                    <h3 class="text-3xl font-extrabold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['tag_terhubung'] }}
                    </h3>
                    <span class="bg-[#FDF3D6] text-[#B8860B] text-xs font-bold px-2.5 py-0.5 rounded-full">
                        {{ $metrics['rasio_aktif'] }} teraktivasi
                    </span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-[#F2EEE9] flex items-center gap-1.5 text-xs text-[#737373]">
                <span class="text-[#D4AF37]">📶</span>
                <span>Rata-rata 4.2 tap per hari per pengguna</span>
            </div>
        </div>

        <!-- Card 3: SIAP ENKODING & PAIRING -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase block">
                            Siap Enkoding &amp; Pairing
                        </span>
                        <h4 class="text-xs font-semibold text-[#737373] mt-0.5">
                            Tag Belum Aktif (Stok)
                        </h4>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-[#F0ECE7] text-[#555] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                        </svg>
                    </div>
                </div>

                <div class="flex items-baseline gap-2.5 mt-3">
                    <h3 class="text-3xl font-extrabold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['tag_stok'] }}
                    </h3>
                    <span class="bg-[#F6F5F2] text-[#666] text-xs font-bold px-2.5 py-0.5 rounded-full">
                        Siap distribusi
                    </span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-[#F2EEE9] flex items-center gap-1.5 text-xs text-[#737373]">
                <svg class="w-4 h-4 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009 9.35c.66 0 1.284-.214 1.794-.582a3 3 0 004.412 0c.51.368 1.134.582 1.794.582a2.993 2.993 0 002.25-.965 3.001 3.001 0 003.75.615V21"/>
                </svg>
                <span>Tersedia di Gudang Logistik BTP Tamalanrea</span>
            </div>
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- MAIN INVENTORY CARD WITH FILTER TABS                      -->
    <!-- ========================================================= -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)]">

        <!-- Top Bar: Filter Status Tabs + Terminal NFC Reader Button -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[#ECE8E1]/80 mb-6">
            
            <!-- Filter Status Tabs -->
            <div class="inline-flex items-center bg-[#F6F5F2] p-1 rounded-full text-xs font-semibold gap-1">
                <a 
                    href="{{ route('admin.tags', ['status' => 'all', 'search' => $search]) }}" 
                    class="px-4 py-2 rounded-full transition {{ $statusFilter === 'all' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Semua (500)
                </a>
                <a 
                    href="{{ route('admin.tags', ['status' => 'aktif', 'search' => $search]) }}" 
                    class="px-4 py-2 rounded-full transition {{ $statusFilter === 'aktif' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Aktif (124)
                </a>
                <a 
                    href="{{ route('admin.tags', ['status' => 'belum_aktif', 'search' => $search]) }}" 
                    class="px-4 py-2 rounded-full transition {{ $statusFilter === 'belum_aktif' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Belum Aktif (376)
                </a>
                <a 
                    href="{{ route('admin.tags', ['status' => 'diblokir', 'search' => $search]) }}" 
                    class="px-4 py-2 rounded-full transition {{ $statusFilter === 'diblokir' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Diblokir (0)
                </a>
            </div>

            <!-- Terminal NFC Reader Trigger Button -->
            <div class="flex items-center gap-2.5">
                <span class="text-xs text-[#737373] font-medium hidden sm:inline">Terminal NFC Reader:</span>
                <button 
                    type="button" 
                    onclick="openNfcModal()"
                    class="bg-[#FBE8E8] hover:bg-[#F7D8D8] text-maroon font-bold text-xs px-4 py-2 rounded-full border border-[#F5C2C2] flex items-center gap-2 shadow-xs transition active:scale-95 cursor-pointer"
                >
                    <svg class="w-4 h-4 text-maroon animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.53 16.11a6 6 0 0 1 6.95 0m-9.5-3.56a11 11 0 0 1 12.08 0M12 20h.01"/>
                    </svg>
                    <span>Pindai Tag Fisik</span>
                </button>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-[10px] font-bold uppercase tracking-wider text-[#8A8A85] border-b border-[#ECE8E1]">
                        <th class="pb-3.5 px-3">Kode Aktivasi</th>
                        <th class="pb-3.5 px-3">Batch &amp; Edisi</th>
                        <th class="pb-3.5 px-3">Status Tag</th>
                        <th class="pb-3.5 px-3">Pemilik Terdaftar</th>
                        <th class="pb-3.5 px-3">Mode Saat Ini</th>
                        <th class="pb-3.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F2EEE9]">
                    @forelse ($tags as $tag)
                        <tr class="hover:bg-[#FAF9F6] transition-colors">
                            
                            <!-- 1. Kode Aktivasi & UID -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-[#FAF4EB] text-[#8B0000] flex items-center justify-center shrink-0 border border-[#EFECE6]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <rect x="4" y="4" width="16" height="16" rx="2"/>
                                            <rect x="9" y="9" width="6" height="6"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="font-bold text-xs text-[#1A1A1A] block tracking-wide">
                                            {{ $tag['kode_aktivasi'] }}
                                        </span>
                                        <span class="text-[10px] font-mono text-[#8A8A85] block">
                                            UID: {{ $tag['hardware_uid'] }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Batch & Edisi -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                <div class="font-bold text-xs text-[#1A1A1A]">
                                    {{ $tag['batch_id'] }} • {{ $tag['edisi'] }}
                                </div>
                                <div class="text-[11px] text-[#737373] font-normal">
                                    {{ $tag['sub_edisi'] }}
                                </div>
                            </td>

                            <!-- 3. Status Tag -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                @if ($tag['status'] === 'aktif')
                                    <span class="inline-flex items-center gap-1.5 bg-[#FDF3D6] text-[#B8860B] font-bold text-[11px] px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#B8860B]"></span>
                                        Aktif
                                    </span>
                                @elseif ($tag['status'] === 'tersedia')
                                    <span class="inline-flex items-center gap-1.5 bg-[#F0ECE7] text-[#736B5E] font-semibold text-[11px] px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#736B5E]"></span>
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-[#FCE8E8] text-maroon font-semibold text-[11px] px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-maroon"></span>
                                        Diblokir
                                    </span>
                                @endif
                            </td>

                            <!-- 4. Pemilik Terdaftar -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                @if ($tag['pemilik'])
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-[#F0ECE7] text-[#1A1A1A] font-bold text-[10px] flex items-center justify-center shrink-0 border border-[#ECE8E1]">
                                            {{ $tag['pemilik']['initials'] }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-xs text-[#1A1A1A] block">
                                                {{ $tag['pemilik']['nama'] }}
                                            </span>
                                            <span class="text-[11px] text-[#737373] font-normal block">
                                                {{ $tag['pemilik']['lokasi'] }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-[#F6F5F2] text-[#737373] text-[11px] font-medium px-3 py-1 rounded-full border border-[#ECE8E1]">
                                        <svg class="w-3 h-3 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                                        </svg>
                                        Belum Terhubung
                                    </span>
                                @endif
                            </td>

                            <!-- 5. Mode Saat Ini -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                @if ($tag['mode'] === 'kampus')
                                    <span class="inline-flex items-center gap-1.5 bg-[#F3EEFA] text-[#6A40A0] font-semibold text-[11px] px-3 py-1 rounded-full">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                                        </svg>
                                        Mode Kampus
                                    </span>
                                @elseif ($tag['mode'] === 'umkm')
                                    <span class="inline-flex items-center gap-1.5 bg-[#FFF3E6] text-[#B85D19] font-semibold text-[11px] px-3 py-1 rounded-full">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009 9.35c.66 0 1.284-.214 1.794-.582a3 3 0 004.412 0c.51.368 1.134.582 1.794.582a2.993 2.993 0 002.25-.965 3.001 3.001 0 003.75.615V21"/>
                                        </svg>
                                        Mode UMKM
                                    </span>
                                @elseif ($tag['mode'] === 'kerja')
                                    <span class="inline-flex items-center gap-1.5 bg-[#EBF0F5] text-[#2C4863] font-semibold text-[11px] px-3 py-1 rounded-full">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                        Mode Kerja
                                    </span>
                                @else
                                    <span class="text-sm text-[#8A8A85] font-bold pl-3">—</span>
                                @endif
                            </td>

                            <!-- 6. Aksi: Primary Button + Three Dots Dropdown -->
                            <td class="py-4 px-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5 relative">
                                    
                                    <!-- Primary Button: Kelola atau Tautkan -->
                                    @if ($tag['status'] === 'aktif')
                                        <button 
                                            type="button" 
                                            onclick="openTagDetailModal('{{ $tag['kode_aktivasi'] }}', '{{ $tag['pemilik']['nama'] ?? '' }}', '{{ $tag['hardware_uid'] }}', '{{ $tag['mode'] }}')"
                                            class="bg-gold-accent hover:bg-[#E5BC50] text-[#1A1A1A] font-semibold text-xs px-3.5 py-1.5 rounded-full shadow-xs transition cursor-pointer"
                                        >
                                            Kelola
                                        </button>
                                    @else
                                        <button 
                                            type="button" 
                                            onclick="openPairingModal('{{ $tag['kode_aktivasi'] }}')"
                                            class="bg-[#F0ECE7] hover:bg-[#E5DFD6] text-[#4A4A4A] font-semibold text-xs px-3.5 py-1.5 rounded-full transition cursor-pointer"
                                        >
                                            Tautkan
                                        </button>
                                    @endif

                                    <!-- Three Dots Menu Toggle Button -->
                                    <div class="relative">
                                        <button 
                                            type="button" 
                                            onclick="toggleActionMenu(event, 'menu-{{ $tag['id'] }}')"
                                            class="p-1.5 text-[#8A8A85] hover:text-[#1A1A1A] hover:bg-[#F0ECE7] rounded-full transition cursor-pointer"
                                            title="Opsi Lanjutan Chip"
                                        >
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                            </svg>
                                        </button>

                                        <!-- Three Dots Context Dropdown Menu -->
                                        <div 
                                            id="menu-{{ $tag['id'] }}" 
                                            class="action-menu hidden absolute right-0 mt-1 w-56 bg-white rounded-2xl shadow-xl border border-[#ECE8E1] py-2 z-50 text-left text-xs"
                                        >
                                            <!-- Option 1: View Tap Analytics -->
                                            <a href="javascript:void(0)" onclick="alert('Membuka Telemetri Scan untuk {{ $tag['kode_aktivasi'] }}...')" class="flex items-center gap-2.5 px-4 py-2 hover:bg-[#FAF9F6] text-[#1A1A1A] font-medium transition">
                                                <span class="text-base">📊</span>
                                                <span>Lihat Log Scan (Tap)</span>
                                            </a>

                                            <!-- Option 2: Override Active Mode -->
                                            <a href="javascript:void(0)" onclick="alert('Alihkan Mode Aktif via Admin Override')" class="flex items-center gap-2.5 px-4 py-2 hover:bg-[#FAF9F6] text-[#1A1A1A] font-medium transition">
                                                <span class="text-base">🔄</span>
                                                <span>Alihkan Mode Aktif</span>
                                            </a>

                                            <!-- Option 3: Copy NDEF URI -->
                                            <a href="javascript:void(0)" onclick="navigator.clipboard.writeText('https://flexitag.id/t/{{ $tag['kode_aktivasi'] }}'); alert('Tautan NDEF disalin: https://flexitag.id/t/{{ $tag['kode_aktivasi'] }}');" class="flex items-center gap-2.5 px-4 py-2 hover:bg-[#FAF9F6] text-[#1A1A1A] font-medium transition">
                                                <span class="text-base">📋</span>
                                                <span>Salin NDEF URI String</span>
                                            </a>

                                            <div class="h-px bg-[#F2EEE9] my-1"></div>

                                            <!-- Option 4: Kill-Switch Toggle -->
                                            <a href="javascript:void(0)" onclick="alert('Kill-switch diaktifkan: Tag {{ $tag['kode_aktivasi'] }} sementara dialihkan ke mode nonaktif.')" class="flex items-center gap-2.5 px-4 py-2 hover:bg-[#FFF5F5] text-[#8B0000] font-medium transition">
                                                <span class="text-base">🔒</span>
                                                <span>Aktifkan Kill-Switch</span>
                                            </a>

                                            <!-- Option 5: Unlink User -->
                                            @if ($tag['status'] === 'aktif')
                                                <a href="javascript:void(0)" onclick="confirm('Yakin ingin melepas tautan pemilik tag ini?') && alert('Tautan pemilik dilepas.')" class="flex items-center gap-2.5 px-4 py-2 hover:bg-[#FFF5F5] text-[#737373] font-medium transition">
                                                    <span class="text-base">⚠️</span>
                                                    <span>Lepas Tautan Pengguna</span>
                                                </a>
                                            @endif

                                            <!-- Option 6: Block Chip Permanently -->
                                            <a href="javascript:void(0)" onclick="confirm('Peringatan: Blokir tag ini permanen dari sistem?') && alert('Tag telah diblokir.')" class="flex items-center gap-2.5 px-4 py-2 hover:bg-[#FFF5F5] text-[#8B0000] font-semibold transition">
                                                <span class="text-base">🚫</span>
                                                <span>Blokir Tag Permanen</span>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-[#737373]">
                                Tidak ada data tag NFC yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer: Pagination -->
        <div class="border-t border-[#ECE8E1] pt-5 mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
            <span class="text-[#737373]">
                Menampilkan <strong class="text-[#1A1A1A]">1 – {{ count($tags) }}</strong> dari <strong class="text-[#1A1A1A]">500</strong> tag NFC
            </span>

            <!-- Pagination Numbers -->
            <div class="flex items-center gap-1.5">
                <button class="w-7 h-7 rounded-lg text-[#8A8A85] hover:bg-[#F6F5F2] flex items-center justify-center transition">
                    ‹
                </button>
                <button class="w-7 h-7 rounded-lg bg-maroon text-white font-bold flex items-center justify-center shadow-xs">
                    1
                </button>
                <button class="w-7 h-7 rounded-lg text-[#737373] hover:bg-[#F6F5F2] flex items-center justify-center transition">
                    2
                </button>
                <button class="w-7 h-7 rounded-lg text-[#737373] hover:bg-[#F6F5F2] flex items-center justify-center transition">
                    3
                </button>
                <span class="px-1 text-[#8A8A85]">...</span>
                <button class="w-7 h-7 rounded-lg text-[#737373] hover:bg-[#F6F5F2] flex items-center justify-center transition">
                    84
                </button>
                <button class="w-7 h-7 rounded-lg text-[#8A8A85] hover:bg-[#F6F5F2] flex items-center justify-center transition">
                    ›
                </button>
            </div>
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- INTERACTIVE MODAL 1: NFC WEB SCANNER SIMULATOR            -->
    <!-- ========================================================= -->
    <div id="nfc-scanner-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-sm w-full text-center shadow-2xl border border-[#ECE8E1] animate-in fade-in zoom-in-95 duration-200">
            <div class="w-16 h-16 mx-auto rounded-full bg-[#FBE8E8] text-maroon flex items-center justify-center mb-4 relative">
                <span class="absolute inset-0 rounded-full bg-maroon/20 animate-ping"></span>
                <svg class="w-8 h-8 relative z-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.53 16.11a6 6 0 0 1 6.95 0m-9.5-3.56a11 11 0 0 1 12.08 0M12 20h.01"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-[#1A1A1A]">Terminal NFC Aktif</h3>
            <p class="text-xs text-[#737373] mt-1 leading-relaxed">
                Tempelkan keping ganci NFC fisik (bulat, persegi, motif budaya, atau custom) pada reader USB untuk membaca UID chip.
            </p>
            <div class="mt-4 p-3 rounded-2xl bg-[#F6F5F2] text-xs font-mono text-[#555]">
                Menunggu respon sinyal 13.56 MHz...
            </div>
            <button 
                type="button" 
                onclick="closeNfcModal()" 
                class="mt-5 w-full bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] font-semibold text-xs py-2.5 rounded-full transition cursor-pointer"
            >
                Tutup Terminal
            </button>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- INTERACTIVE MODAL 2: DETAIL & KELOLA TAG                   -->
    <!-- ========================================================= -->
    <div id="tag-detail-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-[#ECE8E1]">
            <div class="flex items-center justify-between pb-3 border-b border-[#ECE8E1]">
                <h3 class="text-base font-bold text-[#1A1A1A]">Detail Chip NFC</h3>
                <button type="button" onclick="closeTagDetailModal()" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1">✕</button>
            </div>
            <div class="mt-4 space-y-3 text-xs">
                <div>
                    <span class="text-[#737373] block text-[11px]">Kode Aktivasi:</span>
                    <strong id="modal-tag-code" class="text-maroon font-bold text-sm">FLX-XXXXXX</strong>
                </div>
                <div>
                    <span class="text-[#737373] block text-[11px]">Hardware UID:</span>
                    <span id="modal-tag-uid" class="font-mono text-[#1A1A1A]">04:XX:XX:XX:XX</span>
                </div>
                <div>
                    <span class="text-[#737373] block text-[11px]">Pemilik Terdaftar:</span>
                    <span id="modal-tag-owner" class="font-semibold text-[#1A1A1A]">Nama Pemilik</span>
                </div>
                <div>
                    <span class="text-[#737373] block text-[11px]">Mode Publik Aktif:</span>
                    <span id="modal-tag-mode" class="font-semibold text-[#6A40A0] uppercase">Mode</span>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeTagDetailModal()" class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] text-xs font-semibold px-4 py-2 rounded-full transition">
                    Tutup
                </button>
                <button type="button" onclick="alert('Pengaturan tag disimpan.'); closeTagDetailModal();" class="bg-gold-accent hover:bg-[#E5BC50] text-[#1A1A1A] text-xs font-bold px-4 py-2 rounded-full transition">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Toggle Status Filter Dropdown
    function toggleStatusDropdown() {
        const menu = document.getElementById('status-dropdown-menu');
        menu.classList.toggle('hidden');
    }

    // Toggle Three Dots Context Dropdown Menu
    function toggleActionMenu(event, menuId) {
        event.stopPropagation();
        
        // Close all other open action menus first
        document.querySelectorAll('.action-menu').forEach(el => {
            if (el.id !== menuId) {
                el.classList.add('hidden');
            }
        });

        const targetMenu = document.getElementById(menuId);
        if (targetMenu) {
            targetMenu.classList.toggle('hidden');
        }
    }

    // Close open menus when clicking anywhere outside
    window.addEventListener('click', function(e) {
        document.querySelectorAll('.action-menu').forEach(el => el.classList.add('hidden'));
        const statusDropdown = document.getElementById('status-dropdown-menu');
        if (statusDropdown) {
            statusDropdown.classList.add('hidden');
        }
    });

    // NFC Scanner Modal Controls
    function openNfcModal() {
        document.getElementById('nfc-scanner-modal').classList.remove('hidden');
    }
    function closeNfcModal() {
        document.getElementById('nfc-scanner-modal').classList.add('hidden');
    }

    // Tag Detail Modal Controls
    function openTagDetailModal(code, owner, uid, mode) {
        document.getElementById('modal-tag-code').textContent = code;
        document.getElementById('modal-tag-owner').textContent = owner || 'Belum Terhubung';
        document.getElementById('modal-tag-uid').textContent = uid;
        document.getElementById('modal-tag-mode').textContent = mode ? 'Mode ' + mode : 'Belum Dipilih';
        document.getElementById('tag-detail-modal').classList.remove('hidden');
    }
    function closeTagDetailModal() {
        document.getElementById('tag-detail-modal').classList.add('hidden');
    }

    function openPairingModal(code) {
        alert('Membuka wizard pairing untuk menautkan keping tag ' + code + ' ke pesanan atau profil pelanggan baru.');
    }
</script>
@endpush
