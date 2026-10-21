@extends('layouts.admin')

@section('title', 'Buat Batch NFC Tag Baru')

@section('content')
    <!-- Page Title & Header Stats Row -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 mb-7">
        <div>
            <h2 class="text-2xl sm:text-[26px] font-bold text-[#1A1A1A] tracking-tight">
                Buat Batch NFC Tag Baru
            </h2>
            <p class="text-xs text-[#737373] mt-1 font-normal max-w-xl leading-relaxed">
                Konfigurasi dan generate kumpulan kode aktivasi unik chip NTAG215 untuk merchandise fisik FlexiTag.
            </p>
        </div>

        <!-- Top Right Mini Summary Badges -->
        <div class="flex items-center gap-4 shrink-0">
            <!-- Metric 1: Total Batch Aktif -->
            <div class="bg-white rounded-2xl px-4 py-3 border border-[#ECE8E1] shadow-xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-[#FCF5E3] text-[#B8860B] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase block">
                        Total Batch Aktif
                    </span>
                    <span class="text-lg font-bold text-[#1A1A1A] block leading-tight">
                        {{ $metrics['total_batch'] }} Batch
                    </span>
                </div>
            </div>

            <!-- Metric 2: Total Tag Terbit -->
            <div class="bg-white rounded-2xl px-4 py-3 border border-[#ECE8E1] shadow-xs flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-[#F9ECEC] text-[#8B3A3A] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.53 16.11a6 6 0 0 1 6.95 0m-9.5-3.56a11 11 0 0 1 12.08 0M12 20h.01"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase block">
                        Total Tag Terbit
                    </span>
                    <span class="text-lg font-bold text-[#1A1A1A] block leading-tight">
                        {{ $metrics['total_tags'] }} Tag
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert / Flash Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-[#EAF5EC] border border-[#C3E6CB] text-xs text-[#1E7E34] flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-[#FFF5F5] border border-[#FFD6D6] text-xs text-[#8B0000] flex items-start gap-2.5">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div>
                @foreach ($errors->all() as $error)
                    <p class="font-medium">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- MAIN 2-COLUMN SECTION: FORM (LEFT) + HISTORY (RIGHT)      -->
    <!-- ========================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-7">

        <!-- ===================================================== -->
        <!-- LEFT COLUMN: KONFIGURASI BATCH BARU (5 COLS)          -->
        <!-- ===================================================== -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <form action="{{ route('admin.generate.store') }}" method="POST" id="generate-tag-form">
                @csrf

                <!-- Card Header -->
                <div class="flex items-start justify-between mb-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#7F6B2A]"></span>
                            <h3 class="text-base font-bold text-[#1A1A1A]">Konfigurasi Batch Baru</h3>
                        </div>
                        <p class="text-xs text-[#737373] mt-1 font-normal">
                            Isi parameter di bawah untuk memproduksi batch kode unik NFC.
                        </p>
                    </div>

                    <div class="w-8 h-8 rounded-full bg-[#F6F5F2] flex items-center justify-center text-[#8A8A85]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                        </svg>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- 1. Pilih Jenis Produk -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="jenis_produk" class="text-xs font-semibold text-[#1A1A1A]">
                                Pilih Jenis Produk <span class="text-maroon">*</span>
                            </label>
                            <span class="text-[11px] text-[#8A8A85] font-normal">Varian Ganci</span>
                        </div>
                        <div class="relative">
                            <select 
                                name="jenis_produk" 
                                id="jenis_produk" 
                                required
                                class="w-full bg-[#F6F5F2] hover:bg-[#F2F1ED] focus:bg-white text-xs text-[#1A1A1A] font-medium py-3 px-4 pr-10 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] transition appearance-none cursor-pointer"
                            >
                                <option value="" disabled selected>Pilih varian bentuk ganci...</option>
                                <option value="Ganci Akrilik Bulat">Ganci Bentuk Bulat</option>
                                <option value="Ganci Akrilik Persegi">Ganci Bentuk Persegi</option>
                                <option value="Ganci Motif Toraja">Ganci Edisi Motif Toraja</option>
                                <option value="Ganci Motif Bugis">Ganci Edisi Motif Bugis</option>
                                <option value="Ganci Custom Desain">Ganci Custom Desain</option>
                            </select>
                            <div class="absolute inset-y-0 right-3.5 flex items-center pointer-events-none text-[#8A8A85]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Pilih Edisi Budaya -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="edisi_budaya" class="text-xs font-semibold text-[#1A1A1A]">
                                Pilih Edisi Budaya <span class="text-maroon">*</span>
                            </label>
                            <span class="text-[11px] text-[#A67C1E] font-semibold">Motif Sulawesi Selatan</span>
                        </div>
                        <div class="relative">
                            <select 
                                name="edisi_budaya" 
                                id="edisi_budaya" 
                                required
                                class="w-full bg-[#F6F5F2] hover:bg-[#F2F1ED] focus:bg-white text-xs text-[#1A1A1A] font-medium py-3 px-4 pr-10 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] transition appearance-none cursor-pointer"
                            >
                                <option value="" disabled selected>Pilih ragam motif...</option>
                                <option value="Pa'teddong (Toraja)">Pa'teddong (Toraja)</option>
                                <option value="Saqbe (Bugis)">Saqbe (Bugis)</option>
                                <option value="Sulapa Eppa' (Makassar)">Sulapa Eppa' (Makassar)</option>
                                <option value="Pa'barre Allo (Toraja)">Pa'barre Allo (Toraja)</option>
                                <option value="Lagosi (Wajo/Bugis)">Lagosi (Wajo/Bugis)</option>
                            </select>
                            <div class="absolute inset-y-0 right-3.5 flex items-center pointer-events-none text-[#8A8A85]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Jumlah Batch Tag (Radio Buttons) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold text-[#1A1A1A]">
                                Jumlah Batch Tag <span class="text-maroon">*</span>
                            </label>
                            <span class="text-[11px] text-[#8A8A85] font-normal">Kapasitas Produksi</span>
                        </div>
                        
                        <!-- Radio options grid -->
                        <div class="grid grid-cols-4 gap-2.5">
                            @foreach ([10, 50, 100, 250] as $amount)
                                <label class="cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="jumlah_tag" 
                                        value="{{ $amount }}" 
                                        class="sr-only peer" 
                                        {{ $amount === 50 ? 'checked' : '' }}
                                        onchange="updateRadioStyles()"
                                    >
                                    <div class="amount-pill py-2.5 px-3 rounded-2xl text-center text-xs font-medium border border-[#ECE8E1] bg-[#F6F5F2] text-[#4A4A4A] peer-checked:bg-gold-accent peer-checked:text-[#1A1A1A] peer-checked:font-bold peer-checked:border-[#D4AF37] peer-checked:shadow-xs transition duration-200">
                                        {{ $amount }} Tag
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 4. Prefix Kode Aktivasi -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="prefix_kode" class="text-xs font-semibold text-[#1A1A1A]">
                                Prefix Kode Aktivasi <span class="text-maroon">*</span>
                            </label>
                            <span class="text-[11px] text-[#8A8A85] font-normal">Standar URI Tag</span>
                        </div>
                        <div class="relative flex items-center bg-[#F6F5F2] hover:bg-[#F2F1ED] focus-within:bg-white rounded-2xl px-4 py-3 border border-[#ECE8E1] transition">
                            <svg class="w-4 h-4 text-[#8A8A85] mr-2.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75zM6.75 16.5h.75v.75h-.75zM16.5 6.75h.75v.75h-.75zM13.5 13.5h3v3h-3zM16.5 16.5h3v3h-3zM19.5 13.5h.75v.75h-.75zM13.5 19.5h.75v.75h-.75z"/>
                            </svg>
                            <input 
                                type="text" 
                                name="prefix_kode" 
                                id="prefix_kode" 
                                value="FLX-2026-" 
                                placeholder="FLX-2026-"
                                required
                                class="w-full bg-transparent text-xs text-[#1A1A1A] font-bold tracking-wider placeholder-[#9E9E98] focus:outline-none"
                            >
                        </div>
                        
                        <!-- Format hint -->
                        <div class="flex items-start gap-1.5 text-[11px] text-[#737373] mt-2">
                            <svg class="w-3.5 h-3.5 text-[#8A8A85] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span class="leading-relaxed">Format kode unik belakang (tag_code &amp; activation_code) di-generate secara acak kriptografis (non-sequential) untuk mencegah tebakan URL publik.</span>
                        </div>
                    </div>

                    <!-- 5. Standar Enkripsi Hardware Banner -->
                    <div class="bg-[#F6F5F2] rounded-2xl p-4 border border-[#ECE8E1]/80 mt-5">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#1A1A1A]">
                            <svg class="w-4 h-4 text-[#7A735E]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                            <span>Standar Enkripsi Hardware</span>
                        </div>
                        <p class="text-[11px] text-[#737373] leading-relaxed mt-1 font-normal">
                            Chip NTAG215 dienkripsi otomatis dengan standard 540-byte memory format kompatibel Android &amp; iOS tanpa perantara aplikasi.
                        </p>
                    </div>

                    <!-- 6. Submit Button: Generate & Download CSV -->
                    <button 
                        type="submit" 
                        class="w-full bg-gold-accent hover:bg-[#E5BC50] active:scale-[0.99] text-[#1A1A1A] font-bold text-xs sm:text-sm py-3.5 px-6 rounded-full shadow-[0_4px_14px_rgba(240,199,94,0.35)] flex items-center justify-center gap-2 transition cursor-pointer mt-5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        <span>Generate &amp; Download CSV</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ===================================================== -->
        <!-- RIGHT COLUMN: RIWAYAT PEMBUATAN BATCH (7 COLS)        -->
        <!-- ===================================================== -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
            <div>
                <!-- Header & Filter Switcher -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h3 class="text-base font-bold text-[#1A1A1A]">Riwayat Pembuatan Batch</h3>
                            <span class="bg-[#F6F5F2] text-[#666] text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                18 Total
                            </span>
                        </div>
                        <p class="text-xs text-[#737373] mt-0.5 font-normal">
                            Daftar kumpulan kode NFC yang telah diexport ke CSV
                        </p>
                    </div>

                    <!-- Filter Pill Switcher -->
                    <div class="inline-flex items-center bg-[#F6F5F2] p-1 rounded-2xl text-xs font-semibold shrink-0">
                        <button type="button" class="bg-white shadow-xs rounded-xl px-3 py-1.5 text-[#1A1A1A] font-bold">
                            Semua Batch (18)
                        </button>
                        <button type="button" class="text-[#737373] hover:text-[#1A1A1A] px-3 py-1.5 transition">
                            Bulan Ini
                        </button>
                    </div>
                </div>

                <!-- History Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-[10px] font-bold uppercase tracking-wider text-[#8A8A85] border-b border-[#ECE8E1]">
                                <th class="pb-3 px-3">ID Batch</th>
                                <th class="pb-3 px-3">Edisi Budaya &amp; Produk</th>
                                <th class="pb-3 px-3">Jumlah</th>
                                <th class="pb-3 px-3 text-right">Tanggal Dibuat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F2EEE9]">
                            @foreach ($batches as $batch)
                                <tr class="hover:bg-[#FAF9F6] transition-colors">
                                    <!-- ID Batch -->
                                    <td class="py-4 px-3 font-bold text-maroon whitespace-nowrap">
                                        {{ $batch['id_batch'] }}
                                    </td>

                                    <!-- Edisi Budaya & Produk -->
                                    <td class="py-4 px-3">
                                        <div class="font-bold text-[#1A1A1A]">{{ $batch['edisi_label'] }}</div>
                                        <div class="text-[11px] text-[#737373] font-normal">{{ $batch['produk_label'] }}</div>
                                    </td>

                                    <!-- Jumlah Tag -->
                                    <td class="py-4 px-3 whitespace-nowrap">
                                        @if ($batch['is_gold'])
                                            <span class="inline-block bg-[#FDF3D6] text-[#B8860B] font-bold text-[11px] px-3 py-0.5 rounded-full">
                                                {{ $batch['jumlah_tag'] }} Tag
                                            </span>
                                        @else
                                            <span class="inline-block bg-[#F0ECE7] text-[#555] font-bold text-[11px] px-3 py-0.5 rounded-full">
                                                {{ $batch['jumlah_tag'] }} Tag
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Tanggal Dibuat -->
                                    <td class="py-4 px-3 text-right text-xs text-[#737373] font-normal whitespace-nowrap">
                                        {{ $batch['tanggal_dibuat'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table Footer: Pagination -->
            <div class="border-t border-[#ECE8E1] pt-4 mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <span class="text-[#737373]">
                    Menampilkan <strong class="text-[#1A1A1A]">1 – 6</strong> dari <strong class="text-[#1A1A1A]">18</strong> batch terbit
                </span>

                <!-- Pagination Buttons -->
                <div class="flex items-center gap-1.5 text-xs">
                    <button class="w-7 h-7 rounded-lg text-[#8A8A85] hover:bg-[#F6F5F2] flex items-center justify-center transition disabled:opacity-50">
                        ‹
                    </button>
                    <button class="w-7 h-7 rounded-lg bg-gold-accent text-[#1A1A1A] font-bold flex items-center justify-center shadow-xs">
                        1
                    </button>
                    <button class="w-7 h-7 rounded-lg text-[#737373] hover:bg-[#F6F5F2] flex items-center justify-center transition">
                        2
                    </button>
                    <button class="w-7 h-7 rounded-lg text-[#737373] hover:bg-[#F6F5F2] flex items-center justify-center transition">
                        3
                    </button>
                    <button class="w-7 h-7 rounded-lg text-[#8A8A85] hover:bg-[#F6F5F2] flex items-center justify-center transition">
                        ›
                    </button>
                </div>
            </div>

        </div>

    </div>
@endsection
