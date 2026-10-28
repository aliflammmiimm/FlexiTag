@extends('layouts.admin')

@section('title', 'Inventaris & Status NFC Tag')

@section('content')
    <!-- Success Notification Alert -->
    @if (session('success'))
        <div class="mb-5 p-4 rounded-2xl bg-[#EAF5EC] border border-[#C8E6C9] text-xs text-[#2E7D32] flex items-center justify-between shadow-xs animate-in fade-in duration-200">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-full bg-[#C8E6C9] flex items-center justify-center font-bold text-xs">✓</div>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-[#2E7D32] hover:text-[#1B5E20] font-bold p-1 cursor-pointer">✕</button>
        </div>
    @endif

    <!-- Low Tag Stock Alert Banner (< 10) -->
    @if ($metrics['is_low_stock'] ?? false)
        <div class="mb-5 p-4 sm:p-5 rounded-2xl bg-[#FEF2F2] border-2 border-[#FCA5A5] text-xs text-[#991B1B] flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs animate-in fade-in duration-200">
            <div class="flex items-start sm:items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#FEE2E2] flex items-center justify-center shrink-0 text-xl font-bold">
                    ⚠️
                </div>
                <div>
                    <strong class="font-bold text-sm block text-[#7F1D1D]">Peringatan Sistem: Stok Kode Aktivasi Tag Menipis (&lt; 10 Unit)!</strong>
                    <span class="text-[#991B1B] text-xs">
                        Tersisa hanya <strong>{{ $metrics['stok_tag_tersedia'] ?? 0 }} unit</strong> tag belum aktif yang siap dialokasikan. Segera generate batch tag baru agar sistem tidak kehabisan kode aktivasi ketika pesanan siap dikirim/diambil.
                    </span>
                </div>
            </div>
            <a href="{{ route('admin.generate') }}" class="bg-[#8B0000] hover:bg-[#6e0202] text-[#D4AF37] font-bold text-xs px-5 py-2.5 rounded-full whitespace-nowrap shrink-0 shadow-xs transition flex items-center gap-1.5 self-start sm:self-auto">
                <span>+ Generate Batch Tag Baru</span>
                <span>→</span>
            </a>
        </div>
    @endif

    <!-- Top System Badges Row -->
    <div class="flex flex-wrap items-center justify-between sm:justify-end gap-2 mb-3">
        <!-- Chip Info -->
        <div class="inline-flex items-center gap-2 bg-[#F0ECE7]/80 border border-[#ECE8E1] px-3 sm:px-3.5 py-1.5 rounded-full text-[11px] sm:text-xs font-medium text-[#555]">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#7A735E]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
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
        <div class="inline-flex items-center gap-2 bg-[#FCE8E8] text-maroon font-bold text-[11px] sm:text-xs px-3 sm:px-3.5 py-1.5 rounded-full border border-[#F5C2C2]">
            <span class="w-2 h-2 rounded-full bg-[#8B0000] animate-pulse"></span>
            <span>Database Sync: Online</span>
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
        <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
            <!-- Search Form -->
            <form action="{{ route('admin.tags') }}" method="GET" class="relative flex-1 sm:flex-initial w-full sm:w-auto">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <input type="hidden" name="mode" value="{{ $modeFilter ?? 'all' }}">
                <svg class="w-4 h-4 text-[#8A8A85] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}"
                    placeholder="Cari Tag Code / Pemilik..." 
                    class="w-full sm:w-64 bg-white border border-[#ECE8E1] rounded-2xl pl-10 pr-4 py-2 text-xs text-[#1A1A1A] placeholder-[#9E9E98] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] shadow-xs transition"
                >
            </form>

            <!-- Export CSV Button (FR-ADM-04) -->
            <a 
                href="{{ route('admin.tags.exportCsv', ['status' => $statusFilter, 'search' => $search]) }}" 
                class="bg-white hover:bg-[#F9F7F4] border border-[#ECE8E1] rounded-2xl px-3.5 py-2 text-xs font-semibold text-[#1A1A1A] flex items-center gap-1.5 shadow-xs transition"
                title="Unduh data inventaris tag ke berkas CSV"
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
                <span>+ Cetak Batch Baru</span>
            </a>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- TOP 3 METRIC CARDS                                        -->
    <!-- ========================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-7">

        <!-- Card 1: TOTAL TAG DIPRODUKSI -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase block">
                            Inventaris Hardware
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
                        {{ number_format($metrics['total_diproduksi']) }}
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
                <span>Produksi Batch Sulawesi Heritage</span>
            </div>
        </div>

        <!-- Card 2: TAG TERHUBUNG (AKTIF) -->
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
                        {{ number_format($metrics['tag_terhubung']) }}
                    </h3>
                    <span class="bg-[#FDF3D6] text-[#B8860B] text-xs font-bold px-2.5 py-0.5 rounded-full">
                        {{ $metrics['rasio_aktif'] }} teraktivasi
                    </span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-[#F2EEE9] flex items-center gap-1.5 text-xs text-[#737373]">
                <span class="text-[#D4AF37]">📶</span>
                <span>Tersinkronisasi dengan profil aktif pengguna</span>
            </div>
        </div>

        <!-- Card 3: TAG BELUM AKTIF (STOK) -->
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
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                    </div>
                </div>

                <div class="flex items-baseline gap-2.5 mt-3">
                    <h3 class="text-3xl font-extrabold text-[#1A1A1A] tracking-tight">
                        {{ number_format($metrics['tag_stok']) }}
                    </h3>
                    <span class="bg-[#FCE8E8] text-maroon text-xs font-bold px-2.5 py-0.5 rounded-full">
                        {{ $metrics['tag_diblokir'] }} Nonaktif / Diblokir
                    </span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-[#F2EEE9] flex items-center justify-between text-xs text-[#737373]">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full {{ ($metrics['is_low_stock'] ?? false) ? 'bg-[#DC2626] animate-pulse' : 'bg-[#16A34A]' }}"></span>
                    <span><strong>{{ $metrics['stok_tag_tersedia'] ?? 0 }}</strong> siap alokasi acak ke pemesan</span>
                </div>
                @if ($metrics['is_low_stock'] ?? false)
                    <span class="text-[10px] font-bold text-[#DC2626] bg-[#FEE2E2] px-2 py-0.5 rounded-full animate-pulse">Kritis &lt; 10</span>
                @endif
            </div>
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- MAIN INVENTORY CARD                                       -->
    <!-- ========================================================= -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ECE8E1] shadow-[0_4px_20px_rgba(0,0,0,0.02)]">

        <!-- Top Table Controls: Status Filter Pills & NFC Scanner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-[#ECE8E1] mb-6">
            
            <!-- Filter Pills -->
            <div class="flex overflow-x-auto no-scrollbar sm:flex-wrap items-center gap-1.5 text-xs font-semibold bg-[#F6F5F2] p-1.5 rounded-2xl sm:rounded-full border border-[#ECE8E1] w-full sm:w-auto max-w-full">
                <a 
                    href="{{ route('admin.tags', ['status' => 'all', 'mode' => $modeFilter ?? 'all', 'search' => $search]) }}" 
                    class="whitespace-nowrap px-4 py-2 rounded-full transition {{ $statusFilter === 'all' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Semua ({{ $metrics['total_diproduksi'] }})
                </a>
                <a 
                    href="{{ route('admin.tags', ['status' => 'aktif', 'mode' => $modeFilter ?? 'all', 'search' => $search]) }}" 
                    class="whitespace-nowrap px-4 py-2 rounded-full transition {{ $statusFilter === 'aktif' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Aktif ({{ $metrics['tag_terhubung'] }})
                </a>
                <a 
                    href="{{ route('admin.tags', ['status' => 'belum_aktif', 'mode' => $modeFilter ?? 'all', 'search' => $search]) }}" 
                    class="whitespace-nowrap px-4 py-2 rounded-full transition {{ $statusFilter === 'belum_aktif' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Belum Aktif ({{ $metrics['tag_stok'] }})
                </a>
                <a 
                    href="{{ route('admin.tags', ['status' => 'diblokir', 'mode' => $modeFilter ?? 'all', 'search' => $search]) }}" 
                    class="whitespace-nowrap px-4 py-2 rounded-full transition {{ in_array($statusFilter, ['diblokir', 'nonaktif']) ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Nonaktif / Blokir ({{ $metrics['tag_diblokir'] }})
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
                    <span>Simulasi Scan Tag</span>
                </button>
            </div>
        </div>

        @if (($modeFilter ?? 'all') !== 'all')
            <div class="mb-4 flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 bg-[#FAF4EB] border border-[#E5B32A]/40 text-[#8C6D23] px-3.5 py-1 rounded-full text-xs font-semibold">
                    <span>Filter Mode: <strong>Mode {{ ucfirst($modeFilter) }}</strong></span>
                    <a href="{{ route('admin.tags', ['status' => $statusFilter, 'search' => $search]) }}" class="hover:text-maroon ml-1 font-bold text-sm" title="Hapus filter mode">✕</a>
                </span>
            </div>
        @endif

        <!-- Inventory Table -->
        <div class="overflow-x-auto pb-16">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-[10px] font-bold uppercase tracking-wider text-[#8A8A85] border-b border-[#ECE8E1]">
                        <th class="pb-3.5 px-3">Kode Tag &amp; Batch</th>
                        <th class="pb-3.5 px-3">Status Hardware</th>
                        <th class="pb-3.5 px-3">Pemilik Terdaftar</th>
                        <th class="pb-3.5 px-3">Mode Publik Aktif</th>
                        <th class="pb-3.5 px-3">Tanggal Siklus</th>
                        <th class="pb-3.5 px-3 text-right">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F2EEE9]">
                    @forelse ($tags as $tag)
                        @php
                            $badge = $tag->status_badge;
                            $owner = $tag->user;
                            $profile = $owner?->profile;
                            $tagMode = $tag->mode ?: ($profile?->active_mode ?? 'kampus');
                        @endphp
                        <tr class="hover:bg-[#FAF9F6] transition-colors">
                            
                            <!-- 1. Kode Tag & Batch -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-[#FAF4EB] text-[#8B0000] flex items-center justify-center shrink-0 border border-[#EFECE6]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <rect x="4" y="4" width="16" height="16" rx="2"/>
                                            <rect x="9" y="9" width="6" height="6"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="font-bold text-xs text-maroon block tracking-wider font-mono">
                                            {{ $tag->tag_code }}
                                        </span>
                                        <span class="text-[10.5px] text-[#737373] block mt-0.5">
                                            Batch: <strong class="text-[#1A1A1A]">{{ $tag->batch_id ?? '#B-883' }}</strong>
                                        </span>
                                        <div class="mt-1 flex flex-wrap items-center gap-1">
                                            <span class="inline-flex items-center gap-1 text-[10px] bg-[#FCF5E3] text-[#8C6D23] px-1.5 py-0.5 rounded font-mono font-bold border border-[#E5B32A]/35" title="Kode Aktivasi Pengguna (di kartu panduan)">
                                                <span>Akt:</span>
                                                <span>{{ $tag->activation_code }}</span>
                                            </span>
                                            @if ($tag->order)
                                                <a href="{{ route('admin.orders', ['search' => $tag->order->order_no]) }}" class="inline-flex items-center gap-1 text-[9.5px] bg-[#EEF2FF] text-[#3730A3] hover:underline px-1.5 py-0.5 rounded font-medium border border-[#C7D2FE]" title="Tag dialokasikan ke pesanan #{{ $tag->order->order_no }}">
                                                    <span>📦 Order:</span>
                                                    <span class="font-mono font-bold">{{ $tag->order->order_no }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Status Tag (Compact Badge) -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 {{ $badge['bg_class'] }} font-bold text-[11px] px-3 py-1 rounded-full shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot_class'] }}"></span>
                                    {{ $badge['label'] }}
                                </span>
                                @if ($tag->disabled_by)
                                    <span class="block text-[9.5px] text-[#8A8A85] mt-0.5">
                                        Oleh: {{ $tag->disabled_by === 'admin' ? 'Pengelola (Admin)' : 'Pemilik (Kill Switch)' }}
                                    </span>
                                @endif
                            </td>

                            <!-- 3. Pemilik Terdaftar -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                @if ($owner)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-[#F0ECE7] text-[#1A1A1A] font-bold text-[10px] flex items-center justify-center shrink-0 border border-[#ECE8E1]">
                                            {{ strtoupper(substr($owner->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-xs text-[#1A1A1A] block">
                                                {{ $profile->display_name ?? $owner->name }}
                                            </span>
                                            <span class="text-[11px] text-[#737373] font-normal block">
                                                {{ $owner->email }}
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

                            <!-- 4. Mode Publik Aktif -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                @if ($owner)
                                    @if ($tagMode === 'kampus')
                                        <span class="inline-flex items-center gap-1.5 bg-[#F3EEFA] text-[#6A40A0] font-semibold text-[11px] px-3 py-1 rounded-full">
                                            🎓 Mode Kampus
                                        </span>
                                    @elseif ($tagMode === 'umkm')
                                        <span class="inline-flex items-center gap-1.5 bg-[#FFF3E6] text-[#B85D19] font-semibold text-[11px] px-3 py-1 rounded-full">
                                            🏪 Mode UMKM
                                        </span>
                                    @elseif ($tagMode === 'kerja')
                                        <span class="inline-flex items-center gap-1.5 bg-[#EBF0F5] text-[#2C4863] font-semibold text-[11px] px-3 py-1 rounded-full">
                                            💼 Mode Kerja
                                        </span>
                                    @elseif ($tagMode === 'hilang')
                                        <span class="inline-flex items-center gap-1.5 bg-[#FCE8E8] text-[#8B0000] font-bold text-[11px] px-3 py-1 rounded-full animate-pulse">
                                            🚨 Mode Hilang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 bg-[#F3EEFA] text-[#6A40A0] font-semibold text-[11px] px-3 py-1 rounded-full">
                                            🎓 Mode Kampus
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-[#8A8A85] font-normal pl-3">—</span>
                                @endif
                            </td>

                            <!-- 5. Tanggal Siklus -->
                            <td class="py-4 px-3 whitespace-nowrap text-[11px] text-[#737373]">
                                <span class="block">Diproduksi: <strong>{{ $tag->produced_at ? $tag->produced_at->translatedFormat('d M Y') : now()->translatedFormat('d M Y') }}</strong></span>
                                <span class="block text-[10px] text-[#8A8A85] mt-0.5">
                                    {{ $tag->activated_at ? 'Aktif sejak: '.$tag->activated_at->translatedFormat('d M Y') : 'Status: Menunggu Pairing' }}
                                </span>
                            </td>

                            <!-- 6. Aksi: Primary Button + Three Dots Dropdown -->
                            <td class="py-4 px-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5 relative">
                                    
                                    <!-- Primary Button -->
                                    @if ($tag->status === 'aktif')
                                        <button 
                                            type="button" 
                                            onclick="openTagDetailModal('{{ $tag->tag_code }}', '{{ $tag->activation_code }}', '{{ $owner->name ?? '' }}', '{{ $tag->batch_id ?? '' }}', '{{ $tagMode }}', '{{ $tag->public_url }}')"
                                            class="bg-gold-accent hover:bg-[#E5BC50] text-[#1A1A1A] font-semibold text-xs px-3.5 py-1.5 rounded-full shadow-xs transition cursor-pointer"
                                        >
                                            Detail Tag
                                        </button>
                                    @elseif ($tag->status === 'belum_aktif')
                                        <button 
                                            type="button" 
                                            onclick="openPairingModal({{ $tag->id }}, '{{ $tag->tag_code }}')"
                                            class="bg-[#F0ECE7] hover:bg-[#E5DFD6] text-[#4A4A4A] font-semibold text-xs px-3.5 py-1.5 rounded-full transition cursor-pointer"
                                        >
                                            Tautkan
                                        </button>
                                    @elseif ($tag->disabled_by === 'admin')
                                        <form action="{{ route('admin.tags.unblock', $tag->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button 
                                                type="submit" 
                                                class="bg-[#EAF5EC] hover:bg-[#C8E6C9] text-[#2E7D32] font-semibold text-xs px-3.5 py-1.5 rounded-full transition cursor-pointer"
                                            >
                                                Buka Blokir
                                            </button>
                                        </form>
                                    @else
                                        {{-- Tag dikunci mandiri oleh pemilik (Kill Switch) --}}
                                        <button 
                                            type="button" 
                                            onclick="openTagDetailModal('{{ $tag->tag_code }}', '{{ $tag->activation_code }}', '{{ $owner->name ?? '' }}', '{{ $tag->batch_id ?? '' }}', '{{ $tagMode }}', '{{ $tag->public_url }}')"
                                            class="bg-[#FEF3C7] hover:bg-[#FDE68A] text-[#92400E] font-semibold text-xs px-3.5 py-1.5 rounded-full transition cursor-pointer"
                                            title="Tag dikunci sementara oleh pemilik melalui Kill Switch"
                                        >
                                            Kill Switch
                                        </button>
                                    @endif

                                    <!-- Three Dots Menu Toggle Button -->
                                    <div class="relative">
                                        <button 
                                            type="button" 
                                            onclick="toggleActionMenu(event, 'menu-{{ $tag->id }}')"
                                            class="p-1.5 text-[#8A8A85] hover:text-[#1A1A1A] hover:bg-[#F0ECE7] rounded-full transition cursor-pointer"
                                            title="Opsi Lanjutan Tag"
                                        >
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                            </svg>
                                        </button>

                                        <!-- Three Dots Context Dropdown Menu -->
                                        <div 
                                            id="menu-{{ $tag->id }}" 
                                            class="action-menu hidden absolute right-0 mt-1 w-56 bg-white rounded-2xl shadow-xl border border-[#ECE8E1] py-2 z-50 text-left text-xs"
                                        >
                                            <!-- Option 1: Copy Public NDEF URL -->
                                            <a 
                                                href="javascript:void(0)" 
                                                onclick="copyToClipboard('{{ $tag->public_url }}')" 
                                                class="flex items-center gap-2.5 px-4 py-2 hover:bg-[#FAF9F6] text-[#1A1A1A] font-medium transition"
                                            >
                                                <span class="text-base">📋</span>
                                                <span>Salin URL NDEF Publik</span>
                                            </a>

                                            <!-- Option 2: Visit Public URL -->
                                            <a 
                                                href="{{ $tag->public_url }}" 
                                                target="_blank" 
                                                class="flex items-center gap-2.5 px-4 py-2 hover:bg-[#FAF9F6] text-[#1A1A1A] font-medium transition"
                                            >
                                                <span class="text-base">🔗</span>
                                                <span>Buka Halaman Publik (/u)</span>
                                            </a>

                                            <!-- Option 3: Pair / Re-link -->
                                            <a 
                                                href="javascript:void(0)" 
                                                onclick="openPairingModal({{ $tag->id }}, '{{ $tag->tag_code }}')" 
                                                class="flex items-center gap-2.5 px-4 py-2 hover:bg-[#FAF9F6] text-[#1A1A1A] font-medium transition"
                                            >
                                                <span class="text-base">👤</span>
                                                <span>{{ $tag->user_id ? 'Ganti Pemilik' : 'Tautkan ke Pengguna' }}</span>
                                            </a>

                                            <!-- Option 3b: Ubah Mode Tag (if paired) -->
                                            @if ($tag->user_id)
                                                <a 
                                                    href="javascript:void(0)" 
                                                    onclick="openChangeModeModal({{ $tag->id }}, '{{ $tag->tag_code }}', '{{ $tagMode }}')" 
                                                    class="flex items-center gap-2.5 px-4 py-2 hover:bg-[#FAF9F6] text-[#1A1A1A] font-medium transition cursor-pointer"
                                                >
                                                    <span class="text-base">🎭</span>
                                                    <span>Ubah Mode Tag</span>
                                                </a>
                                            @endif

                                            <div class="h-px bg-[#F2EEE9] my-1"></div>

                                            <!-- Option 4: Unlink User (if paired) -->
                                            @if ($tag->user_id)
                                                <form action="{{ route('admin.tags.unlink', $tag->id) }}" method="POST" onsubmit="return confirm('Lepas kepemilikan tag {{ $tag->tag_code }} dari {{ $owner->name ?? 'pengguna' }}? Status akan kembali menjadi Belum Aktif.')">
                                                    @csrf
                                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 hover:bg-[#FFF5F5] text-[#737373] hover:text-[#1A1A1A] font-medium transition text-left cursor-pointer">
                                                        <span class="text-base">⚠️</span>
                                                        <span>Lepas Tautan Pemilik</span>
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Option 5: Block / Unblock Tag (BR-04) -->
                                            @if ($tag->status !== 'nonaktif')
                                                <form action="{{ route('admin.tags.block', $tag->id) }}" method="POST" onsubmit="return confirm('Blokir tag {{ $tag->tag_code }}? Tag ini tidak akan bisa menampilkan profil publik sampai dibuka oleh admin.')">
                                                    @csrf
                                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 hover:bg-[#FFF5F5] text-[#8B0000] font-semibold transition text-left cursor-pointer">
                                                        <span class="text-base">🔒</span>
                                                        <span>Blokir Tag (Admin)</span>
                                                    </button>
                                                </form>
                                            @elseif ($tag->disabled_by === 'admin')
                                                <form action="{{ route('admin.tags.unblock', $tag->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 hover:bg-[#EAF5EC] text-[#2E7D32] font-semibold transition text-left cursor-pointer">
                                                        <span class="text-base">🔓</span>
                                                        <span>Buka Blokir Tag</span>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.tags.unblock', $tag->id) }}" method="POST" onsubmit="return confirm('Tag {{ $tag->tag_code }} saat ini dikunci mandiri oleh pemilik via Kill Switch. Anda yakin ingin membuka kuncinya sebagai pengelola?')">
                                                    @csrf
                                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 hover:bg-[#FEF3C7] text-[#92400E] font-semibold transition text-left cursor-pointer">
                                                        <span class="text-base">🔓</span>
                                                        <span>Buka Kunci Kill Switch (Override)</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-[#737373]">
                                <div class="w-12 h-12 rounded-full bg-[#F6F5F2] flex items-center justify-center mx-auto mb-2 text-[#8A8A85]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <rect x="4" y="4" width="16" height="16" rx="2"/>
                                        <rect x="9" y="9" width="6" height="6"/>
                                    </svg>
                                </div>
                                <p class="font-bold text-[#1A1A1A]">Tidak ada data tag NFC</p>
                                <p class="text-[11px] text-[#8A8A85] mt-1">Coba sesuaikan kata kunci pencarian atau tab filter status di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer: Pagination -->
        <div class="border-t border-[#ECE8E1] pt-5 mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
            <span class="text-[#737373]">
                Menampilkan <strong class="text-[#1A1A1A]">{{ $tags->firstItem() ?? 0 }} – {{ $tags->lastItem() ?? 0 }}</strong> dari <strong class="text-[#1A1A1A]">{{ $tags->total() }}</strong> tag NFC
            </span>

            <!-- Pagination Links -->
            <div>
                {{ $tags->links() }}
            </div>
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- INTERACTIVE MODAL 1: NFC WEB SCANNER SIMULATOR            -->
    <!-- ========================================================= -->
    <div id="nfc-scanner-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-sm w-full text-center shadow-2xl border border-[#ECE8E1] animate-in fade-in zoom-in-95 duration-200">
            <div class="w-16 h-16 mx-auto rounded-full bg-[#FBE8E8] text-maroon flex items-center justify-center mb-4 relative">
                <span class="absolute inset-0 rounded-full bg-maroon/20 animate-ping"></span>
                <svg class="w-8 h-8 relative z-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.53 16.11a6 6 0 0 1 6.95 0m-9.5-3.56a11 11 0 0 1 12.08 0M12 20h.01"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-[#1A1A1A]">Simulasi Pemindaian NFC</h3>
            <p class="text-xs text-[#737373] mt-1 leading-relaxed">
                Uji coba respons halaman publik tag NFC dengan memilih atau mengetik kode tag di bawah ini:
            </p>
            
            <div class="mt-4 text-left">
                <label for="sim-tag-code" class="block text-[11px] font-bold text-[#1A1A1A] mb-1">Pilih / Ketik Kode Tag:</label>
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        id="sim-tag-code" 
                        placeholder="FLX-8X9Y2Z" 
                        value="{{ $tags->first()->tag_code ?? 'FLX-8X9Y2Z' }}"
                        class="w-full bg-[#F6F5F2] border border-[#ECE8E1] rounded-xl px-3 py-2 text-xs font-mono text-[#1A1A1A] focus:outline-none focus:border-maroon"
                    >
                    <button 
                        type="button" 
                        onclick="testScanTag()" 
                        class="bg-[#8B0000] hover:bg-[#6B0000] text-gold-accent px-4 py-2 rounded-xl text-xs font-bold transition shrink-0 cursor-pointer"
                    >
                        Scan ⚡
                    </button>
                </div>
            </div>

            <button 
                type="button" 
                onclick="closeNfcModal()" 
                class="mt-5 w-full bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] font-semibold text-xs py-2.5 rounded-full transition cursor-pointer"
            >
                Tutup Simulator
            </button>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- INTERACTIVE MODAL 2: DETAIL & KELOLA TAG                   -->
    <!-- ========================================================= -->
    <div id="tag-detail-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-[#ECE8E1]">
            <div class="flex items-center justify-between pb-3 border-b border-[#ECE8E1]">
                <div>
                    <h3 class="text-base font-bold text-[#1A1A1A]">Detail Keping Chip NFC</h3>
                    <p class="text-[11px] text-[#737373]">Spesifikasi NTAG215 Sulawesi Heritage</p>
                </div>
                <button type="button" onclick="closeTagDetailModal()" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1 cursor-pointer">✕</button>
            </div>

            <div class="mt-4 space-y-3 text-xs bg-[#F8F7F4] p-4 rounded-2xl border border-[#ECE8E1]">
                <div class="flex justify-between items-center">
                    <span class="text-[#737373]">Tag Code (Publik):</span>
                    <strong id="modal-tag-code" class="text-maroon font-bold text-sm font-mono">FLX-XXXXXX</strong>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#737373]">Kode Aktivasi (Kartu):</span>
                    <strong id="modal-tag-activation" class="text-[#8C6D23] font-bold text-xs font-mono bg-[#FCF5E3] px-2 py-0.5 rounded border border-[#E5B32A]/30">KD-XXXX-XX</strong>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#737373]">Batch ID:</span>
                    <span id="modal-tag-batch" class="font-bold text-[#1A1A1A]">#B-883</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#737373]">Pemilik Terdaftar:</span>
                    <span id="modal-tag-owner" class="font-semibold text-[#1A1A1A]">Nama Pemilik</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#737373]">Mode Publik Aktif:</span>
                    <span id="modal-tag-mode" class="font-bold text-[#6A40A0] uppercase text-[11px]">Mode</span>
                </div>
                <div class="pt-2 border-t border-[#ECE8E1]">
                    <span class="text-[#737373] block mb-1">URL Publik (/u):</span>
                    <div class="flex items-center gap-2">
                        <input type="text" id="modal-tag-url" readonly class="w-full bg-white px-2.5 py-1.5 rounded-lg border border-[#ECE8E1] text-[11px] font-mono text-[#1A1A1A]">
                        <button type="button" onclick="copyToClipboard(document.getElementById('modal-tag-url').value)" class="bg-gold-accent hover:bg-[#E5BC50] text-[#1A1A1A] px-2.5 py-1.5 rounded-lg text-[11px] font-bold shrink-0 cursor-pointer">Salin</button>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeTagDetailModal()" class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] text-xs font-semibold px-4 py-2 rounded-full transition cursor-pointer">
                    Tutup
                </button>
                <a id="modal-tag-visit-btn" href="#" target="_blank" class="bg-[#8B0000] hover:bg-[#6B0000] text-gold-accent text-xs font-bold px-4 py-2 rounded-full transition flex items-center gap-1 cursor-pointer">
                    <span>Kunjungi Profil</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- INTERACTIVE MODAL 3: PAIRING / TAUTKAN TAG KE USER         -->
    <!-- ========================================================= -->
    <div id="tag-pairing-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-[#ECE8E1]">
            <div class="flex items-center justify-between pb-3 border-b border-[#ECE8E1]">
                <div>
                    <h3 class="text-base font-bold text-[#1A1A1A]">Tautkan Tag ke Pengguna</h3>
                    <p class="text-[11px] text-[#737373]">Penetapan kepemilikan tag fisik (BR-01 &amp; BR-02)</p>
                </div>
                <button type="button" onclick="closePairingModal()" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1 cursor-pointer">✕</button>
            </div>

            <form id="tag-pairing-form" method="POST" action="" class="mt-4 space-y-4">
                @csrf
                <div class="bg-[#F8F7F4] p-3 rounded-2xl border border-[#ECE8E1] text-xs flex justify-between items-center">
                    <span class="text-[#737373]">Kode Tag:</span>
                    <strong id="pairing-modal-code" class="text-maroon font-mono font-bold text-sm">FLX-XXXXXX</strong>
                </div>

                <div>
                    <label for="pairing-user-select" class="block text-xs font-bold text-[#1A1A1A] mb-1.5">
                        Pilih Akun Pemilik <span class="text-maroon">*</span>
                    </label>
                    <select 
                        name="user_id" 
                        id="pairing-user-select" 
                        required 
                        class="w-full bg-[#F6F5F2] hover:bg-[#F2F1ED] focus:bg-white text-xs font-semibold text-[#1A1A1A] py-2.5 px-3.5 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] transition cursor-pointer"
                    >
                        <option value="" disabled selected>Pilih pengguna terdaftar...</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                    <span class="text-[10.5px] text-[#8A8A85] block mt-1">
                        Tag akan otomatis berubah status menjadi Aktif dan sinkron dengan profil pengguna.
                    </span>
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-[#ECE8E1]">
                    <button type="button" onclick="closePairingModal()" class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] text-xs font-semibold px-4 py-2 rounded-full transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="bg-gold-accent hover:bg-[#E5BC50] text-[#1A1A1A] text-xs font-bold px-5 py-2 rounded-full shadow-xs transition cursor-pointer">
                        Simpan Tautan Tag
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- INTERACTIVE MODAL 4: UBAH MODE TAG (ADMIN)                -->
    <!-- ========================================================= -->
    <div id="change-mode-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-sm w-full shadow-2xl border border-[#ECE8E1]">
            <div class="flex items-center justify-between pb-3 border-b border-[#ECE8E1]">
                <div>
                    <h3 class="text-base font-bold text-[#1A1A1A]">Ubah Mode Tag</h3>
                    <p class="text-[11px] text-[#737373]">Setel mode mandiri untuk keping tag ini</p>
                </div>
                <button type="button" onclick="closeChangeModeModal()" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1 cursor-pointer">✕</button>
            </div>

            <form id="change-mode-form" method="POST" action="" class="mt-4 space-y-4">
                @csrf
                <div class="bg-[#F8F7F4] p-3 rounded-2xl border border-[#ECE8E1] text-xs flex justify-between items-center">
                    <span class="text-[#737373]">Kode Tag:</span>
                    <strong id="change-mode-modal-code" class="text-maroon font-mono font-bold text-sm">FLX-XXXXXX</strong>
                </div>

                <div>
                    <label for="admin-mode-select" class="block text-xs font-bold text-[#1A1A1A] mb-1.5">
                        Pilih Mode Tampilan <span class="text-maroon">*</span>
                    </label>
                    <select 
                        name="mode" 
                        id="admin-mode-select" 
                        required 
                        class="w-full bg-[#F6F5F2] hover:bg-[#F2F1ED] focus:bg-white text-xs font-semibold text-[#1A1A1A] py-2.5 px-3.5 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] transition cursor-pointer"
                    >
                        <option value="kampus">🎓 Mode Kampus (Akademik)</option>
                        <option value="kerja">💼 Mode Kerja (Portofolio & CV)</option>
                        <option value="umkm">🏪 Mode UMKM (Katalog Produk & Toko)</option>
                        <option value="hilang">🚨 Mode Barang Hilang (Kontak Darurat)</option>
                    </select>
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-[#ECE8E1]">
                    <button type="button" onclick="closeChangeModeModal()" class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] text-xs font-semibold px-4 py-2 rounded-full transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#8B0000] hover:bg-[#6B0000] text-gold-accent text-xs font-bold px-5 py-2 rounded-full shadow-xs transition cursor-pointer">
                        Simpan Mode
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
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
    });

    // Close open menus and modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.action-menu').forEach(el => el.classList.add('hidden'));
            closeTagDetailModal();
            closePairingModal();
            closeChangeModeModal();
            closeNfcModal();
        }
    });

    // Copy to clipboard helper
    function copyToClipboard(text) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(function() {
            alert('Tautan NDEF disalin ke clipboard: ' + text);
        }).catch(function() {
            prompt('Salin tautan:', text);
        });
    }

    // NFC Scanner Modal Controls
    function openNfcModal() {
        document.getElementById('nfc-scanner-modal').classList.remove('hidden');
    }
    function closeNfcModal() {
        document.getElementById('nfc-scanner-modal').classList.add('hidden');
    }
    function testScanTag() {
        const code = document.getElementById('sim-tag-code').value.trim();
        if (code) {
            window.open('{{ url("/u") }}/' + code, '_blank');
        }
    }

    // Tag Detail Modal Controls
    function openTagDetailModal(code, actCode, owner, batch, mode, url) {
        document.getElementById('modal-tag-code').textContent = code;
        const actEl = document.getElementById('modal-tag-activation');
        if (actEl) actEl.textContent = actCode || '—';
        document.getElementById('modal-tag-owner').textContent = owner || 'Belum Terhubung';
        document.getElementById('modal-tag-batch').textContent = batch || '#B-883';
        document.getElementById('modal-tag-mode').textContent = mode ? 'Mode ' + mode : 'Belum Dipilih';
        document.getElementById('modal-tag-url').value = url;
        document.getElementById('modal-tag-visit-btn').href = url;
        document.getElementById('tag-detail-modal').classList.remove('hidden');
    }
    function closeTagDetailModal() {
        document.getElementById('tag-detail-modal').classList.add('hidden');
    }

    // Pairing Modal Controls
    function openPairingModal(tagId, tagCode) {
        document.getElementById('pairing-modal-code').textContent = tagCode;
        const form = document.getElementById('tag-pairing-form');
        form.action = '{{ url("/admin/tags") }}/' + tagId + '/pair';
        document.getElementById('tag-pairing-modal').classList.remove('hidden');
    }
    function closePairingModal() {
        document.getElementById('tag-pairing-modal').classList.add('hidden');
    }

    // Change Mode Modal Controls
    function openChangeModeModal(tagId, tagCode, currentMode) {
        document.getElementById('change-mode-modal-code').textContent = tagCode;
        const select = document.getElementById('admin-mode-select');
        if (select && currentMode) {
            select.value = currentMode;
        }
        const form = document.getElementById('change-mode-form');
        form.action = '{{ url("/admin/tags") }}/' + tagId + '/mode';
        document.getElementById('change-mode-modal').classList.remove('hidden');
    }
    function closeChangeModeModal() {
        document.getElementById('change-mode-modal').classList.add('hidden');
    }
</script>
@endpush
