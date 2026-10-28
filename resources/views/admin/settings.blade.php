@extends('layouts.admin')

@section('title', 'Pengaturan Sistem & Administrasi Lanjutan')

@section('content')
    <!-- Flash Success & Error Alerts -->
    @if (session('success'))
        <div class="mb-6 bg-[#EAF5EC] border border-[#BBE3C2] text-[#2E7D32] px-5 py-3.5 rounded-2xl flex items-center justify-between shadow-xs animate-in fade-in slide-in-from-top-2 duration-300">
            <div class="flex items-center gap-2.5 text-xs font-semibold">
                <svg class="w-4 h-4 text-[#2E7D32] shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-[#2E7D32] hover:opacity-75 p-1 cursor-pointer">✕</button>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-[#FBE8E8] border border-[#F5C2C2] text-maroon px-5 py-3.5 rounded-2xl flex items-center justify-between shadow-xs animate-in fade-in slide-in-from-top-2 duration-300">
            <div class="flex items-center gap-2.5 text-xs font-semibold">
                <svg class="w-4 h-4 text-maroon shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-maroon hover:opacity-75 p-1 cursor-pointer">✕</button>
        </div>
    @endif

    @if (session('info'))
        <div class="mb-6 bg-[#FCF5E3] border border-[#F4E1A6] text-[#8C6D23] px-5 py-3.5 rounded-2xl flex items-center justify-between shadow-xs animate-in fade-in slide-in-from-top-2 duration-300">
            <div class="flex items-center gap-2.5 text-xs font-semibold">
                <svg class="w-4 h-4 text-[#8C6D23] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>{{ session('info') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-[#8C6D23] hover:opacity-75 p-1 cursor-pointer">✕</button>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="mb-6 bg-[#FBE8E8] border border-[#F5C2C2] text-maroon px-5 py-3.5 rounded-2xl text-xs font-semibold shadow-xs">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-maroon shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>Terdapat kesalahan saat memproses data:</span>
            </div>
            <ul class="list-disc list-inside ml-6 space-y-0.5 font-normal text-[#5A0000]">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Page Title & Header Info -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl sm:text-[26px] font-bold text-[#1A1A1A] tracking-tight">
                Pengaturan &amp; Administrasi Lanjutan
            </h2>
            <p class="text-xs text-[#737373] mt-1 font-normal max-w-2xl leading-relaxed">
                Pusat konfigurasi platform FlexiTag: kredensial admin, manajemen pengguna, katalog motif, dan pembukuan kas operasional.
            </p>
        </div>

        <span class="inline-flex items-center gap-1.5 bg-[#FCF5E3] text-[#8C6D23] font-semibold text-xs px-3.5 py-1.5 rounded-full shadow-2xs self-start sm:self-auto shrink-0">
            <span class="w-2 h-2 rounded-full bg-[#E5B32A]"></span>
            <span>Makassar Core-01 Node</span>
        </span>
    </div>

    <!-- ========================================================= -->
    <!-- MULTI-TAB NAVIGATION BAR (FR-ADM-07 & FR-ADM-08)          -->
    <!-- ========================================================= -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-7 text-xs font-semibold scrollbar-none">
        
        <!-- Tab 1: Umum & Platform -->
        <a 
            href="{{ route('admin.settings', ['tab' => 'general']) }}"
            class="px-4.5 py-2.5 rounded-2xl flex items-center gap-2 transition shrink-0 {{ $currentTab === 'general' ? 'bg-[#540404] text-white shadow-sm font-bold' : 'bg-white hover:bg-[#F9F7F4] text-[#4A4A4A] border border-[#ECE8E1]' }}"
        >
            <svg class="w-4 h-4 {{ $currentTab === 'general' ? 'text-[#D4AF37]' : 'text-[#8A8A85]' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/>
            </svg>
            <span>Umum &amp; Platform</span>
        </a>

        <!-- Tab 2: Manajemen Pengguna -->
        <a 
            href="{{ route('admin.settings', ['tab' => 'users']) }}"
            class="px-4.5 py-2.5 rounded-2xl flex items-center gap-2 transition shrink-0 {{ $currentTab === 'users' ? 'bg-[#540404] text-white shadow-sm font-bold' : 'bg-white hover:bg-[#F9F7F4] text-[#4A4A4A] border border-[#ECE8E1]' }}"
        >
            <svg class="w-4 h-4 {{ $currentTab === 'users' ? 'text-[#D4AF37]' : 'text-[#8A8A85]' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
            </svg>
            <span>Manajemen Pengguna</span>
        </a>

        <!-- Tab 3: Katalog Template Motif -->
        <a 
            href="{{ route('admin.settings', ['tab' => 'designs']) }}"
            class="px-4.5 py-2.5 rounded-2xl flex items-center gap-2 transition shrink-0 {{ $currentTab === 'designs' ? 'bg-[#540404] text-white shadow-sm font-bold' : 'bg-white hover:bg-[#F9F7F4] text-[#4A4A4A] border border-[#ECE8E1]' }}"
        >
            <svg class="w-4 h-4 {{ $currentTab === 'designs' ? 'text-[#D4AF37]' : 'text-[#8A8A85]' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"/>
            </svg>
            <span>Katalog Motif Desain</span>
        </a>

        <!-- Tab 4: Buku Kas Sederhana -->
        <a 
            href="{{ route('admin.settings', ['tab' => 'financial']) }}"
            class="px-4.5 py-2.5 rounded-2xl flex items-center gap-2 transition shrink-0 {{ $currentTab === 'financial' ? 'bg-[#540404] text-white shadow-sm font-bold' : 'bg-white hover:bg-[#F9F7F4] text-[#4A4A4A] border border-[#ECE8E1]' }}"
        >
            <svg class="w-4 h-4 {{ $currentTab === 'financial' ? 'text-[#D4AF37]' : 'text-[#8A8A85]' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.007-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Buku Kas Sederhana</span>
        </a>

    </div>

    @if ($currentTab === 'general')
        <!-- ========================================================= -->
        <!-- TAB 1 CONTENT: UMUM & PLATFORM (FR-ADM-07)                -->
        <!-- ========================================================= -->
        <form action="{{ route('admin.settings.update') }}" method="POST" id="settings-form">
            @csrf

            <!-- CARD 1: PROFIL PENGELOLA & KEAMANAN -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] mb-7">
                
                <div class="flex items-center justify-between pb-5 border-b border-[#F2EEE9] mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#F6F5F2] text-maroon flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#1A1A1A]">
                                Profil Pengelola &amp; Keamanan
                            </h3>
                            <p class="text-xs text-[#737373] mt-0.5 font-normal">
                                Informasi akun administrator console utama Makassar Node.
                            </p>
                        </div>
                    </div>

                    <span class="inline-flex items-center gap-1.5 bg-[#FCF5E3] text-[#8C6D23] font-semibold text-xs px-3.5 py-1.5 rounded-full shadow-2xs shrink-0">
                        <span class="w-2 h-2 rounded-full bg-[#E5B32A]"></span>
                        <span>{{ ucfirst($user->role ?? 'admin') }} Node</span>
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <!-- 1. Nama Lengkap Admin -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-[#1A1A1A] mb-1.5">
                            Nama Lengkap Admin
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required
                            value="{{ old('name', Auth::user()->name ?? ($user->name ?? '')) }}"
                            class="w-full bg-[#F8F7F4] hover:bg-[#F2F1ED] focus:bg-white text-xs font-bold text-[#1A1A1A] py-3 px-4 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] shadow-xs transition"
                        >
                        <span class="text-[11px] text-[#8A8A85] font-normal block mt-1.5">
                            Nama pengelola operasional Makassar Core.
                        </span>
                    </div>

                    <!-- 2. Jabatan / Role -->
                    <div>
                        <label for="jabatan_admin" class="block text-xs font-bold text-[#1A1A1A] mb-1.5">
                            Jabatan / Role
                        </label>
                        <input 
                            type="text" 
                            name="jabatan_admin" 
                            id="jabatan_admin" 
                            required
                            value="{{ old('jabatan_admin', Auth::user()->jabatan_admin ?? 'Administrator') }}"
                            class="w-full bg-[#F8F7F4] hover:bg-[#F2F1ED] focus:bg-white text-xs font-bold text-[#1A1A1A] py-3 px-4 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] shadow-xs transition"
                        >
                        <span class="text-[11px] text-[#8A8A85] font-normal block mt-1.5">
                            Akses penuh tingkat Administrator Konsol ({{ ucfirst(Auth::user()->role ?? 'admin') }}).
                        </span>
                    </div>

                    <!-- 3. Email Admin -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-[#1A1A1A] mb-1.5">
                            Email Admin
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            required
                            value="{{ old('email', Auth::user()->email ?? ($user->email ?? 'admin@flexitag.id')) }}"
                            class="w-full bg-[#F8F7F4] hover:bg-[#F2F1ED] focus:bg-white text-xs font-bold text-[#1A1A1A] py-3 px-4 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] shadow-xs transition"
                        >
                        <span class="text-[11px] text-[#8A8A85] font-normal block mt-1.5">
                            Digunakan untuk laporan insiden dan notifikasi sistem.
                        </span>
                    </div>

                    <!-- 4. Nomor WhatsApp CS -->
                    <div>
                        <label for="no_wa_cs_pusat" class="block text-xs font-bold text-[#1A1A1A] mb-1.5">
                            Nomor WhatsApp CS
                        </label>
                        <input 
                            type="text" 
                            name="no_wa_cs_pusat" 
                            id="no_wa_cs_pusat" 
                            required
                            placeholder="+6281234567890"
                            pattern="(\+628|628)[0-9]{8,13}"
                            value="{{ old('no_wa_cs_pusat', $settings->no_wa_cs_pusat ?? '+6281234567890') }}"
                            class="w-full bg-[#F8F7F4] hover:bg-[#F2F1ED] focus:bg-white text-xs font-bold text-[#1A1A1A] py-3 px-4 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] shadow-xs transition font-mono"
                        >
                        <span class="text-[11px] text-[#8A8A85] font-normal block mt-1.5">
                            Wajib diawali kode negara (+628... atau 628...). Nomor pusat bantuan.
                        </span>
                    </div>
                </div>

                <!-- Inner Banner: Keamanan Kata Sandi -->
                <div class="rounded-2xl bg-[#F6F5F2] p-4 sm:p-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border border-[#EFECE6]">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-white rounded-xl shadow-xs text-[#1A1A1A] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-[#380208]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-[#1A1A1A]">Keamanan Kata Sandi Administrator</h4>
                            <p class="text-[11px] text-[#737373] mt-0.5">Disarankan memperbarui kata sandi secara berkala demi keamanan.</p>
                        </div>
                    </div>

                    <button 
                        type="button" 
                        onclick="openPasswordModal()"
                        class="bg-white hover:bg-[#F9F7F4] border border-[#ECE8E1] px-4 py-2 rounded-full text-xs font-bold text-[#1A1A1A] shadow-xs flex items-center gap-1.5 transition cursor-pointer self-start sm:self-auto shrink-0"
                    >
                        <svg class="w-3.5 h-3.5 text-[#8C6D23]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                        </svg>
                        <span>Ubah Kata Sandi Admin</span>
                    </button>
                </div>

            </div>

            <!-- CARD 2: KONFIGURASI PLATFORM & RELAY -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] mb-7">
                
                <div class="flex items-center gap-3 pb-5 border-b border-[#F2EEE9] mb-5">
                    <div class="w-10 h-10 rounded-2xl bg-[#F6F5F2] text-maroon flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-[#1A1A1A]">
                            Konfigurasi Platform &amp; Relay
                        </h3>
                        <p class="text-xs text-[#737373] mt-0.5 font-normal">
                            Pengaturan otomatisasi komunikasi pesan dan ketersediaan layanan publik.
                        </p>
                    </div>
                </div>

                <div class="divide-y divide-[#F2EEE9]">
                    
                    <!-- Toggle 1: Sistem Relay Pesan Darurat -->
                    <div class="py-4.5 flex items-center justify-between gap-4">
                        <div class="pr-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-[#1A1A1A]">
                                    Sistem Relay Pesan Darurat (Mode Barang Hilang)
                                </span>
                                <span class="bg-gold-accent text-[#380208] text-[9.5px] font-extrabold uppercase px-2 py-0.5 rounded-full">
                                    AKTIF
                                </span>
                            </div>
                            <p class="text-xs text-[#737373] mt-0.5 leading-relaxed">
                                Aktifkan penerusan pesan otomatis dari penemu barang langsung ke Email (SMTP) &amp; Kotak Masuk Dashboard pemilik.
                            </p>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input 
                                type="checkbox" 
                                name="relay_wa_otomatis_aktif" 
                                value="1" 
                                class="sr-only peer"
                                {{ old('relay_wa_otomatis_aktif', $settings->relay_wa_otomatis_aktif ?? true) ? 'checked' : '' }}
                            >
                            <div class="w-12 h-6.5 bg-[#E5E0D8] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2.5px] after:left-[3px] after:bg-white after:border-[#ECE8E1] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#8B0000] transition-colors shadow-inner"></div>
                        </label>
                    </div>

                    <!-- Toggle 2: Notifikasi Pesanan Baru via Email -->
                    <div class="py-4.5 flex items-center justify-between gap-4">
                        <div class="pr-4">
                            <span class="text-xs font-bold text-[#1A1A1A]">
                                Notifikasi Pesanan Baru via Email
                            </span>
                            <p class="text-xs text-[#737373] mt-0.5 leading-relaxed">
                                Kirim peringatan real-time ke email administrator saat ada pesanan pre-order atau batch tag baru masuk.
                            </p>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input 
                                type="checkbox" 
                                name="notifikasi_email_pesanan" 
                                value="1" 
                                class="sr-only peer"
                                {{ old('notifikasi_email_pesanan', $settings->notifikasi_email_pesanan ?? true) ? 'checked' : '' }}
                            >
                            <div class="w-12 h-6.5 bg-[#E5E0D8] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2.5px] after:left-[3px] after:bg-white after:border-[#ECE8E1] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#8B0000] transition-colors shadow-inner"></div>
                        </label>
                    </div>

                    <!-- Toggle 3: Mode Pemeliharaan (Maintenance) -->
                    <div class="py-4.5 flex items-center justify-between gap-4">
                        <div class="pr-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-[#1A1A1A]">
                                    Mode Pemeliharaan (Maintenance)
                                </span>
                                <span class="bg-[#ECE8E1] text-[#737373] text-[9.5px] font-bold uppercase px-2 py-0.5 rounded-full">
                                    STANDBY
                                </span>
                            </div>
                            <p class="text-xs text-[#737373] mt-0.5 leading-relaxed">
                                Nonaktifkan sementara halaman publik profil tap saat migrasi database atau pembaruan firmware tag.
                            </p>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input 
                                type="checkbox" 
                                name="mode_maintenance" 
                                value="1" 
                                class="sr-only peer"
                                {{ old('mode_maintenance', $settings->mode_maintenance ?? false) ? 'checked' : '' }}
                            >
                            <div class="w-12 h-6.5 bg-[#E5E0D8] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2.5px] after:left-[3px] after:bg-white after:border-[#ECE8E1] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#8B0000] transition-colors shadow-inner"></div>
                        </label>
                    </div>

                </div>

            </div>

            <!-- BOTTOM VERIFICATION & SAVE BAR -->
            <div class="bg-white rounded-3xl p-4.5 sm:p-5 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2.5 text-xs text-[#737373]">
                    <svg class="w-4.5 h-4.5 text-[#8A8A85] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                    <span>Semua perubahan konfigurasi diverifikasi oleh sistem enkripsi server lokal Makassar Node.</span>
                </div>

                <button 
                    type="submit" 
                    class="bg-gold-accent hover:bg-[#E5BC50] active:scale-95 text-[#1A1A1A] font-bold text-xs sm:text-sm px-6 py-3 rounded-full shadow-xs transition flex items-center gap-2 cursor-pointer shrink-0"
                >
                    <svg class="w-4 h-4 text-[#1A1A1A]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>

        </form>

    @elseif ($currentTab === 'users')
        <!-- ========================================================= -->
        <!-- TAB 2 CONTENT: MANAJEMEN PENGGUNA (FR-ADM-08)             -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)]">
            
            <!-- Toolbar & Action Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[#F2EEE9] mb-6">
                <div>
                    <h3 class="text-base font-bold text-[#1A1A1A]">
                        Daftar Akun Pengguna &amp; Staff ({{ $users->total() }})
                    </h3>
                    <p class="text-xs text-[#737373] mt-0.5">
                        Kelola akun pengelola internal (Super Admin / Staff) dan pengguna terdaftar tag pintar FlexiTag.
                    </p>
                </div>

                <!-- Add User Button -->
                <button 
                    type="button" 
                    onclick="openAddUserModal()"
                    class="bg-[#8B0000] hover:bg-[#6e0202] text-white font-bold text-xs px-4.5 py-2.5 rounded-full shadow-xs flex items-center gap-2 transition cursor-pointer self-start md:self-auto shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>Tambah Akun Baru</span>
                </button>
            </div>

            <!-- Search & Filter Bar -->
            <form action="{{ route('admin.settings') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 mb-6">
                <input type="hidden" name="tab" value="users">

                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <svg class="w-4 h-4 text-[#8A8A85] absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input 
                        type="text" 
                        name="search_user" 
                        value="{{ $searchUser }}" 
                        placeholder="Cari nama, email, atau no WhatsApp..." 
                        class="w-full bg-[#F8F7F4] hover:bg-[#F2F1ED] focus:bg-white text-xs text-[#1A1A1A] pl-10 pr-4 py-2.5 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] shadow-xs transition"
                    >
                </div>

                <!-- Role Filter Dropdown -->
                <select 
                    name="role_filter" 
                    onchange="this.form.submit()" 
                    class="w-full sm:w-44 bg-[#F8F7F4] hover:bg-[#F2F1ED] text-xs font-semibold text-[#1A1A1A] py-2.5 px-3 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] shadow-xs transition cursor-pointer"
                >
                    <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>Semua Peran</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="staff" {{ $roleFilter === 'staff' ? 'selected' : '' }}>Staff Operasional</option>
                    <option value="user" {{ $roleFilter === 'user' ? 'selected' : '' }}>Pengguna Tag</option>
                </select>

                <button 
                    type="submit" 
                    class="bg-[#F6F5F2] hover:bg-[#ECE8E1] text-[#1A1A1A] text-xs font-bold px-4 py-2.5 rounded-2xl border border-[#ECE8E1] transition"
                >
                    Cari
                </button>
            </form>

            <!-- Users Table -->
            <div class="overflow-x-auto rounded-2xl border border-[#ECE8E1]">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#FAF9F6] text-[#737373] border-b border-[#ECE8E1] uppercase font-bold text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Pengguna</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">Status Akun</th>
                            <th class="py-3 px-4 text-center">Kepemilikan Tag</th>
                            <th class="py-3 px-4">WhatsApp</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F2EEE9]">
                        @forelse ($users as $u)
                            <tr class="hover:bg-[#FDFBF7] transition">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-[#FCF5E3] text-[#8C6D23] font-bold flex items-center justify-center shrink-0 text-xs">
                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-[#1A1A1A] flex items-center gap-1.5">
                                                <span>{{ $u->name }}</span>
                                                @if ($u->id === Auth::id())
                                                    <span class="text-[9px] bg-[#EAF5EC] text-[#2E7D32] px-1.5 py-0.2 rounded font-bold">Anda</span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-[#737373]">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1.5 {{ $u->role_badge['bg'] }} {{ $u->role_badge['text'] }} text-[10.5px] font-bold px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $u->role_badge['dot'] }}"></span>
                                        <span>{{ $u->role_badge['label'] }}</span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1 {{ $u->status_badge['bg'] }} {{ $u->status_badge['text'] }} {{ $u->status_badge['border'] }} text-[10.5px] font-bold px-2.5 py-0.5 rounded-full border">
                                        {{ $u->status_badge['label'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-bold text-[#1A1A1A]">{{ $u->tags_count }}</span>
                                    <span class="text-[#8A8A85] text-[11px]"> Tag</span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-[11.5px] text-[#4A4A4A]">
                                    {{ $u->no_whatsapp ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Edit Modal Trigger -->
                                        <button 
                                            type="button" 
                                            onclick='openEditUserModal(@json($u))'
                                            class="p-1.5 rounded-lg hover:bg-[#F2EEE9] text-[#737373] hover:text-[#1A1A1A] transition cursor-pointer"
                                            title="Edit data akun"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                            </svg>
                                        </button>

                                        @if ($u->id !== Auth::id())
                                            <!-- Toggle Block Form -->
                                            <form action="{{ route('admin.settings.users.toggleStatus', $u->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button 
                                                    type="submit" 
                                                    class="p-1.5 rounded-lg hover:bg-[#FBE8E8] {{ $u->is_active ? 'text-maroon' : 'text-[#2E7D32]' }} transition cursor-pointer"
                                                    title="{{ $u->is_active ? 'Blokir akun' : 'Aktifkan akun' }}"
                                                >
                                                    @if ($u->is_active)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    @endif
                                                </button>
                                            </form>

                                            <!-- Delete User Form -->
                                            <form action="{{ route('admin.settings.users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}? Tag terkait akan dilepaskan ke inventaris.')">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="p-1.5 rounded-lg hover:bg-[#FEE2E2] text-[#991B1B] transition cursor-pointer"
                                                    title="Hapus akun permanen"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-[#8A8A85]">
                                    Tidak ada data pengguna yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="mt-5">
                    {{ $users->links() }}
                </div>
            @endif

        </div>

    @elseif ($currentTab === 'designs')
        <!-- ========================================================= -->
        <!-- TAB 3 CONTENT: KATALOG TEMPLATE MOTIF (FR-ADM-08)         -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)]">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[#F2EEE9] mb-6">
                <div>
                    <h3 class="text-base font-bold text-[#1A1A1A]">
                        Galeri Template Motif Akrilik ({{ $designs->count() }})
                    </h3>
                    <p class="text-xs text-[#737373] mt-0.5">
                        Katalog desain ganci pintar (Edisi Toraja, Bugis, Minimalis &amp; UMKM) yang tersedia di form pemesanan pelanggan.
                    </p>
                </div>

                <!-- Add Design Button -->
                <button 
                    type="button" 
                    onclick="openAddDesignModal()"
                    class="bg-[#8B0000] hover:bg-[#6e0202] text-white font-bold text-xs px-4.5 py-2.5 rounded-full shadow-xs flex items-center gap-2 transition cursor-pointer self-start md:self-auto shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>Tambah Motif Baru</span>
                </button>
            </div>

            <!-- Design Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @forelse ($designs as $design)
                    <div class="rounded-3xl border border-[#ECE8E1] bg-[#FAF9F6] p-4 flex flex-col justify-between hover:shadow-md transition group">
                        <div>
                            <!-- Design Thumbnail / Preview Area -->
                            <div class="w-full h-40 rounded-2xl bg-[#F0EDE6] border border-[#ECE8E1] flex items-center justify-center relative overflow-hidden mb-3.5">
                                @if ($design->image_path)
                                    <img 
                                        src="{{ $design->image_url }}" 
                                        alt="{{ $design->name }}" 
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        onerror="this.onerror=null; this.src='{{ asset('images/keychain.jpg') }}';"
                                    >
                                @else
                                    <div class="w-16 h-16 rounded-full bg-[#FAF9F6] border-2 border-dashed border-[#D4AF37]/50 flex items-center justify-center text-[#8C6D23]">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Status Badge Top Right -->
                                <span class="absolute top-2.5 right-2.5 text-[9px] font-bold px-2 py-0.5 rounded-full shadow-xs {{ $design->is_active ? 'bg-[#EAF5EC] text-[#2E7D32]' : 'bg-[#FEE2E2] text-[#991B1B]' }}">
                                    {{ $design->is_active ? 'Tersedia' : 'Dinonaktifkan' }}
                                </span>
                            </div>

                            <!-- Category Badge -->
                            <span class="inline-block {{ $design->category_badge['bg'] }} {{ $design->category_badge['text'] }} text-[9.5px] font-extrabold uppercase px-2 py-0.5 rounded-full mb-1.5 tracking-wider">
                                {{ $design->category_badge['label'] }}
                            </span>

                            <h4 class="text-xs font-bold text-[#1A1A1A] line-clamp-2 leading-snug">
                                {{ $design->name }}
                            </h4>
                        </div>

                        <!-- Card Action Footer -->
                        <div class="mt-4 pt-3 border-t border-[#ECE8E1] flex items-center justify-between text-xs">
                            <!-- Toggle Active Status Form -->
                            <form action="{{ route('admin.settings.designs.toggleStatus', $design->id) }}" method="POST">
                                @csrf
                                <button 
                                    type="submit" 
                                    class="text-[11px] font-bold {{ $design->is_active ? 'text-[#8A8A85] hover:text-[#1A1A1A]' : 'text-[#2E7D32] hover:underline' }} cursor-pointer"
                                >
                                    {{ $design->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>

                            <!-- Delete Design Form -->
                            <form action="{{ route('admin.settings.designs.destroy', $design->id) }}" method="POST" onsubmit="return confirm('Hapus motif {{ $design->name }} dari katalog?')">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit" 
                                    class="text-[11px] font-bold text-maroon hover:underline cursor-pointer"
                                >
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-12 text-[#8A8A85] text-xs">
                        Belum ada template motif yang terdaftar dalam katalog.
                    </div>
                @endforelse
            </div>

        </div>

    @elseif ($currentTab === 'financial')
        <!-- ========================================================= -->
        <!-- TAB 4 CONTENT: BUKU KAS SEDERHANA (FR-ADM-08)             -->
        <!-- ========================================================= -->
        <!-- 3 Summary Cash Flow Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-7">
            
            <!-- Card 1: Total Pemasukan -->
            <div class="bg-white rounded-3xl p-6 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase">
                            Total Pemasukan Kas
                        </span>
                        <div class="w-10 h-10 rounded-full bg-[#EAF5EC] text-[#2E7D32] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-[#2E7D32] tracking-tight">
                        Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                    </div>
                </div>
                <div class="text-[11px] text-[#737373] mt-3 pt-3 border-t border-[#F2EEE9]">
                    Akumulasi penjualan ganci dan pre-order
                </div>
            </div>

            <!-- Card 2: Total Pengeluaran -->
            <div class="bg-white rounded-3xl p-6 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase">
                            Total Pengeluaran Kas
                        </span>
                        <div class="w-10 h-10 rounded-full bg-[#FBE8E8] text-maroon flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-maroon tracking-tight">
                        Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </div>
                </div>
                <div class="text-[11px] text-[#737373] mt-3 pt-3 border-t border-[#F2EEE9]">
                    Pembelian chip NTAG, akrilik, dan operasional
                </div>
            </div>

            <!-- Card 3: Saldo Kas Bersih -->
            <div class="bg-white rounded-3xl p-6 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase">
                            Saldo Kas Operasional
                        </span>
                        <div class="w-10 h-10 rounded-full bg-[#FCF5E3] text-[#8C6D23] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="2" y="5" width="20" height="14" rx="2"/>
                                <line x1="2" y1="10" x2="22" y2="10"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-[#1A1A1A] tracking-tight">
                        Rp {{ number_format($saldoKas, 0, ',', '.') }}
                    </div>
                </div>
                <div class="text-[11px] font-bold text-[#8C6D23] mt-3 pt-3 border-t border-[#F2EEE9]">
                    Saldo bersih kas riil MaccaTech
                </div>
            </div>

        </div>

        <!-- Ledger Table & Form Container -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)]">
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-[#F2EEE9] mb-6">
                <div>
                    <h3 class="text-base font-bold text-[#1A1A1A]">
                        Riwayat Transaksi Pembukuan Kas ({{ $financialEntries->total() }})
                    </h3>
                    <p class="text-xs text-[#737373] mt-0.5">
                        Catatan mutasi pemasukan penjualan dan pengeluaran operasional MaccaTech.
                    </p>
                </div>

                <!-- Action Buttons: Sync, Export CSV, and Add Transaction -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Sync Orders Button -->
                    <form action="{{ route('admin.settings.financial.syncOrders') }}" method="POST" class="inline">
                        @csrf
                        <button 
                            type="submit" 
                            class="bg-[#FAF9F6] hover:bg-[#F2EEE9] text-[#4A4A4A] border border-[#ECE8E1] font-bold text-xs px-3.5 py-2.5 rounded-full shadow-2xs flex items-center gap-1.5 transition cursor-pointer"
                            title="Sinkronkan seluruh pembayaran pesanan masuk yang belum tercatat di kas"
                        >
                            <svg class="w-3.5 h-3.5 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                            </svg>
                            <span>Sinkronkan Pesanan</span>
                        </button>
                    </form>

                    <!-- Export CSV Button -->
                    <a 
                        href="{{ route('admin.settings.financial.exportCsv', ['search_finance' => $searchFinance, 'finance_type' => $financeType]) }}" 
                        class="bg-[#FAF9F6] hover:bg-[#F2EEE9] text-[#4A4A4A] border border-[#ECE8E1] font-bold text-xs px-3.5 py-2.5 rounded-full shadow-2xs flex items-center gap-1.5 transition cursor-pointer"
                        title="Unduh laporan pembukuan kas format CSV"
                    >
                        <svg class="w-3.5 h-3.5 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        <span>Ekspor CSV</span>
                    </a>

                    <!-- Add Transaction Button -->
                    <button 
                        type="button" 
                        onclick="openAddFinancialModal()"
                        class="bg-[#8B0000] hover:bg-[#6e0202] text-white font-bold text-xs px-4.5 py-2.5 rounded-full shadow-xs flex items-center gap-2 transition cursor-pointer shrink-0"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        <span>Catat Transaksi Baru</span>
                    </button>
                </div>
            </div>

            <!-- Search & Filter Controls -->
            <div class="mb-5 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-[#FAF9F6] p-3 rounded-2xl border border-[#ECE8E1]">
                <form method="GET" action="{{ route('admin.settings') }}" class="flex flex-1 flex-col sm:flex-row gap-2.5">
                    <input type="hidden" name="tab" value="financial">
                    
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#8A8A85]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="search_finance" 
                            value="{{ $searchFinance }}" 
                            placeholder="Cari transaksi kas berdasarkan kategori atau catatan..." 
                            class="w-full pl-9 pr-3 py-2 bg-white rounded-xl text-xs border border-[#ECE8E1] focus:outline-none focus:border-[#8B0000]"
                        >
                    </div>

                    <!-- Type Filter Dropdown -->
                    <div class="sm:w-52">
                        <select 
                            name="finance_type" 
                            onchange="this.form.submit()" 
                            class="w-full py-2 px-3 bg-white rounded-xl text-xs border border-[#ECE8E1] focus:outline-none focus:border-[#8B0000] cursor-pointer"
                        >
                            <option value="all" {{ $financeType === 'all' ? 'selected' : '' }}>Semua Tipe Transaksi</option>
                            <option value="pemasukan" {{ $financeType === 'pemasukan' ? 'selected' : '' }}>↓ Pemasukan (Penjualan)</option>
                            <option value="pengeluaran" {{ $financeType === 'pengeluaran' ? 'selected' : '' }}>↑ Pengeluaran (Biaya/Bahan)</option>
                        </select>
                    </div>

                    <button type="submit" class="bg-[#1A1A1A] hover:bg-[#333333] text-white font-semibold text-xs px-4 py-2 rounded-xl transition cursor-pointer">
                        Cari
                    </button>

                    @if ($searchFinance || $financeType !== 'all')
                        <a href="{{ route('admin.settings', ['tab' => 'financial']) }}" class="bg-[#ECE8E1] hover:bg-[#ded9d0] text-[#4A4A4A] font-semibold text-xs px-3.5 py-2 rounded-xl transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Ledger Table -->
            <div class="overflow-x-auto rounded-2xl border border-[#ECE8E1]">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#FAF9F6] text-[#737373] border-b border-[#ECE8E1] uppercase font-bold text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Tipe Transaksi</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4">Nominal</th>
                            <th class="py-3 px-4">Keterangan / Catatan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F2EEE9]">
                        @forelse ($financialEntries as $entry)
                            <tr class="hover:bg-[#FDFBF7] transition">
                                <td class="py-3.5 px-4 font-mono text-[11px] text-[#4A4A4A] whitespace-nowrap">
                                    {{ $entry->entry_date ? $entry->entry_date->format('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($entry->type === 'pemasukan')
                                        <span class="inline-flex items-center gap-1 bg-[#EAF5EC] text-[#2E7D32] text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full">
                                            <span>↓</span>
                                            <span>Pemasukan</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-[#FBE8E8] text-maroon text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full">
                                            <span>↑</span>
                                            <span>Pengeluaran</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-bold text-[#1A1A1A]">
                                    {{ $entry->category }}
                                </td>
                                <td class="py-3.5 px-4 font-bold whitespace-nowrap {{ $entry->type === 'pemasukan' ? 'text-[#2E7D32]' : 'text-maroon' }}">
                                    {{ $entry->type === 'pemasukan' ? '+' : '-' }} Rp {{ number_format((float) $entry->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-[#737373] text-[11.5px] max-w-xs truncate" title="{{ $entry->note }}">
                                    {{ $entry->note ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Edit Button -->
                                        <button 
                                            type="button" 
                                            onclick='openEditFinancialModal(@json($entry))'
                                            class="p-1.5 rounded-lg hover:bg-[#F2EEE9] text-[#4A4A4A] hover:text-[#1A1A1A] transition cursor-pointer"
                                            title="Ubah data transaksi kas"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.settings.financial.destroy', $entry->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus transaksi ini dari buku kas?')">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="p-1.5 rounded-lg hover:bg-[#FEE2E2] text-[#991B1B] transition cursor-pointer"
                                                title="Hapus entri transaksi"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-[#8A8A85]">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-10 h-10 text-[#CCCCCC] mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <rect x="2" y="5" width="20" height="14" rx="2"/>
                                            <line x1="2" y1="10" x2="22" y2="10"/>
                                        </svg>
                                        @if ($searchFinance || $financeType !== 'all')
                                            <p class="font-semibold text-xs text-[#4A4A4A]">Tidak ada transaksi kas yang sesuai dengan kata kunci / filter pencarian.</p>
                                            <a href="{{ route('admin.settings', ['tab' => 'financial']) }}" class="mt-2 text-maroon hover:underline font-bold text-xs">Reset Filter</a>
                                        @else
                                            <p class="font-semibold text-xs text-[#4A4A4A]">Belum ada transaksi yang dicatat dalam buku kas operasional.</p>
                                            <p class="text-[11px] text-[#8A8A85] mt-1">Gunakan tombol "Catat Transaksi Baru" atau "Sinkronkan Pesanan" untuk mengisi pembukuan.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($financialEntries->hasPages())
                <div class="mt-5">
                    {{ $financialEntries->links() }}
                </div>
            @endif


        </div>
    @endif

    <!-- ========================================================= -->
    <!-- INTERACTIVE MODALS                                        -->
    <!-- ========================================================= -->

    <!-- MODAL 1: UBAH KATA SANDI ADMIN -->
    <div id="password-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-[#ECE8E1] animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-[#ECE8E1]">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-[#F6F5F2] text-maroon flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-[#1A1A1A]">Ubah Kata Sandi Admin</h3>
                </div>
                <button type="button" onclick="closePasswordModal()" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1 cursor-pointer">✕</button>
            </div>

            <form action="{{ route('admin.settings.password') }}" method="POST" class="mt-4 space-y-4 text-xs">
                @csrf
                <div>
                    <label for="current_password" class="block font-bold text-[#1A1A1A] mb-1">
                        Kata Sandi Saat Ini <span class="text-maroon">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="current_password" 
                        id="current_password" 
                        required
                        placeholder="••••••••"
                        class="w-full bg-[#F8F7F4] focus:bg-white text-xs text-[#1A1A1A] py-2.5 px-3.5 rounded-xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] transition"
                    >
                </div>

                <div>
                    <label for="password" class="block font-bold text-[#1A1A1A] mb-1">
                        Kata Sandi Baru <span class="text-maroon">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required
                        placeholder="Minimal 6 karakter"
                        class="w-full bg-[#F8F7F4] focus:bg-white text-xs text-[#1A1A1A] py-2.5 px-3.5 rounded-xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] transition"
                    >
                </div>

                <div>
                    <label for="password_confirmation" class="block font-bold text-[#1A1A1A] mb-1">
                        Konfirmasi Kata Sandi Baru <span class="text-maroon">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        required
                        placeholder="Ketik ulang kata sandi baru"
                        class="w-full bg-[#F8F7F4] focus:bg-white text-xs text-[#1A1A1A] py-2.5 px-3.5 rounded-xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] transition"
                    >
                </div>

                <div class="mt-6 flex justify-end gap-2.5 pt-3 border-t border-[#ECE8E1]">
                    <button 
                        type="button" 
                        onclick="closePasswordModal()" 
                        class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] font-semibold text-xs px-4 py-2 rounded-full transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="bg-gold-accent hover:bg-[#E5BC50] text-[#1A1A1A] font-bold text-xs px-5 py-2 rounded-full transition shadow-xs cursor-pointer"
                    >
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: TAMBAH PENGGUNA BARU -->
    <div id="add-user-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-[#ECE8E1] animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-[#ECE8E1]">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-[#F6F5F2] text-maroon flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-[#1A1A1A]">Tambah Akun Pengguna Baru</h3>
                </div>
                <button type="button" onclick="closeAddUserModal()" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1 cursor-pointer">✕</button>
            </div>

            <form action="{{ route('admin.settings.users.store') }}" method="POST" class="mt-4 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Nama Lengkap <span class="text-maroon">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Muhammad Fajrin" class="w-full bg-[#F8F7F4] focus:bg-white text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1] focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#1A1A1A] mb-1">Email <span class="text-maroon">*</span></label>
                        <input type="email" name="email" required placeholder="fajrin@example.com" class="w-full bg-[#F8F7F4] focus:bg-white text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                    </div>
                    <div>
                        <label class="block font-bold text-[#1A1A1A] mb-1">Nomor WhatsApp</label>
                        <input type="text" name="no_whatsapp" placeholder="081234567890" class="w-full bg-[#F8F7F4] focus:bg-white text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#1A1A1A] mb-1">Peran Akun <span class="text-maroon">*</span></label>
                        <select name="role" required class="w-full bg-[#F8F7F4] focus:bg-white text-xs py-2.5 px-3 rounded-xl border border-[#ECE8E1]">
                            <option value="user">Pengguna Tag Standar</option>
                            <option value="staff">Staff Operasional</option>
                            <option value="admin">Super Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-[#1A1A1A] mb-1">Jabatan Spesifik</label>
                        <input type="text" name="jabatan_admin" placeholder="Contoh: Divisi Perakitan" class="w-full bg-[#F8F7F4] focus:bg-white text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Kata Sandi Awal <span class="text-maroon">*</span></label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full bg-[#F8F7F4] focus:bg-white text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                </div>

                <div class="mt-6 flex justify-end gap-2.5 pt-3 border-t border-[#ECE8E1]">
                    <button type="button" onclick="closeAddUserModal()" class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] font-semibold text-xs px-4 py-2 rounded-full cursor-pointer">Batal</button>
                    <button type="submit" class="bg-[#8B0000] hover:bg-[#6e0202] text-white font-bold text-xs px-5 py-2 rounded-full shadow-xs cursor-pointer">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: EDIT PENGGUNA -->
    <div id="edit-user-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-[#ECE8E1] animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-[#ECE8E1]">
                <h3 class="text-base font-bold text-[#1A1A1A]">Edit Akun Pengguna</h3>
                <button type="button" onclick="closeEditUserModal()" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1 cursor-pointer">✕</button>
            </div>

            <form id="edit-user-form" action="" method="POST" class="mt-4 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="edit-user-name" required class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#1A1A1A] mb-1">Email</label>
                        <input type="email" name="email" id="edit-user-email" required class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                    </div>
                    <div>
                        <label class="block font-bold text-[#1A1A1A] mb-1">Nomor WhatsApp</label>
                        <input type="text" name="no_whatsapp" id="edit-user-phone" class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#1A1A1A] mb-1">Peran Akun</label>
                        <select name="role" id="edit-user-role" required class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3 rounded-xl border border-[#ECE8E1]">
                            <option value="user">Pengguna Tag Standar</option>
                            <option value="staff">Staff Operasional</option>
                            <option value="admin">Super Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-[#1A1A1A] mb-1">Jabatan Spesifik</label>
                        <input type="text" name="jabatan_admin" id="edit-user-jabatan" class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Ganti Kata Sandi (Kosongkan bila tidak diubah)</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                </div>

                <div class="mt-6 flex justify-end gap-2.5 pt-3 border-t border-[#ECE8E1]">
                    <button type="button" onclick="closeEditUserModal()" class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] font-semibold text-xs px-4 py-2 rounded-full cursor-pointer">Batal</button>
                    <button type="submit" class="bg-[#8B0000] hover:bg-[#6e0202] text-white font-bold text-xs px-5 py-2 rounded-full shadow-xs cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: TAMBAH MOTIF DESAIN -->
    <div id="add-design-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-[#ECE8E1] animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-[#ECE8E1]">
                <h3 class="text-base font-bold text-[#1A1A1A]">Tambah Template Motif Baru</h3>
                <button type="button" onclick="closeAddDesignModal()" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1 cursor-pointer">✕</button>
            </div>

            <form action="{{ route('admin.settings.designs.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Nama Motif <span class="text-maroon">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Edisi Toraja Pa'teddong" class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                </div>

                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Kategori Edisi <span class="text-maroon">*</span></label>
                    <select name="category" required class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3 rounded-xl border border-[#ECE8E1]">
                        <option value="motif_lokal">Motif Lokal (Toraja / Bugis)</option>
                        <option value="pribadi">Pribadi & Minimalis</option>
                        <option value="organisasi">Organisasi & Kampus</option>
                        <option value="umkm">Branding UMKM</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Unggah Pratinjau Desain (PNG/JPG/SVG, maks 5MB)</label>
                    <input type="file" name="image" accept="image/*" class="w-full bg-[#F8F7F4] text-xs py-2 px-3 rounded-xl border border-[#ECE8E1] file:mr-3 file:py-1 file:px-2.5 file:rounded-full file:border-0 file:text-[11px] file:font-semibold file:bg-[#8B0000] file:text-white cursor-pointer">
                </div>

                <div class="mt-6 flex justify-end gap-2.5 pt-3 border-t border-[#ECE8E1]">
                    <button type="button" onclick="closeAddDesignModal()" class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] font-semibold text-xs px-4 py-2 rounded-full cursor-pointer">Batal</button>
                    <button type="submit" class="bg-[#8B0000] hover:bg-[#6e0202] text-white font-bold text-xs px-5 py-2 rounded-full shadow-xs cursor-pointer">Simpan Motif</button>
                </div>
            </form>
        </div>
    </div>

    <!-- CATEGORY SUGGESTIONS DATALIST (Shared across Add & Edit) -->
    <datalist id="financial-categories">
        <option value="Penjualan Ganci (Online Web)">
        <option value="Penjualan Ganci (Offline Kasir)">
        <option value="Penjualan Aksesoris & Gantungan">
        <option value="Pembelian Chip NFC NTAG213">
        <option value="Pembelian Bahan Akrilik & Resin">
        <option value="Ring Gantungan Kunci & Aksesoris">
        <option value="Kemasan & Packaging Box">
        <option value="Biaya Listrik & Operasional Workshop">
        <option value="Promosi & Pemasaran">
        <option value="Biaya Ekspedisi / Pengiriman">
        <option value="Lain-lain">
    </datalist>

    <!-- MODAL 5: CATAT TRANSAKSI KAS BARU -->
    <div id="add-financial-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-[#ECE8E1] animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-[#ECE8E1]">
                <h3 class="text-base font-bold text-[#1A1A1A]">Catat Transaksi Kas Baru</h3>
                <button type="button" onclick="closeAddFinancialModal()" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1 cursor-pointer">✕</button>
            </div>

            <form action="{{ route('admin.settings.financial.store') }}" method="POST" class="mt-4 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#1A1A1A] mb-1">Tipe Mutasi <span class="text-maroon">*</span></label>
                        <select name="type" required class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3 rounded-xl border border-[#ECE8E1]">
                            <option value="pemasukan">Pemasukan (Penjualan)</option>
                            <option value="pengeluaran">Pengeluaran (Bahan/Biaya)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-[#1A1A1A] mb-1">Tanggal <span class="text-maroon">*</span></label>
                        <input type="date" name="entry_date" required value="{{ date('Y-m-d') }}" class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3 rounded-xl border border-[#ECE8E1]">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Nominal (Rupiah) <span class="text-maroon">*</span></label>
                    <input type="number" name="amount" min="1000" step="500" required placeholder="Contoh: 150000" class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                </div>

                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Kategori Transaksi <span class="text-maroon">*</span></label>
                    <input type="text" id="add-financial-category" name="category" list="financial-categories" required placeholder="Ketik atau pilih kategori..." class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <span class="text-[10px] text-[#8A8A85] self-center mr-1">Rekomendasi:</span>
                        <button type="button" onclick="setFinanceCategory('add', 'Penjualan Ganci (Online Web)')" class="text-[10.5px] bg-[#FAF9F6] hover:bg-[#ECE8E1] text-[#4A4A4A] px-2 py-0.5 rounded-md border border-[#ECE8E1] transition cursor-pointer">Penjualan Online</button>
                        <button type="button" onclick="setFinanceCategory('add', 'Penjualan Ganci (Offline Kasir)')" class="text-[10.5px] bg-[#FAF9F6] hover:bg-[#ECE8E1] text-[#4A4A4A] px-2 py-0.5 rounded-md border border-[#ECE8E1] transition cursor-pointer">Penjualan Offline</button>
                        <button type="button" onclick="setFinanceCategory('add', 'Pembelian Chip NFC NTAG213')" class="text-[10.5px] bg-[#FAF9F6] hover:bg-[#ECE8E1] text-[#4A4A4A] px-2 py-0.5 rounded-md border border-[#ECE8E1] transition cursor-pointer">Chip NFC</button>
                        <button type="button" onclick="setFinanceCategory('add', 'Pembelian Bahan Akrilik & Resin')" class="text-[10.5px] bg-[#FAF9F6] hover:bg-[#ECE8E1] text-[#4A4A4A] px-2 py-0.5 rounded-md border border-[#ECE8E1] transition cursor-pointer">Bahan Akrilik</button>
                        <button type="button" onclick="setFinanceCategory('add', 'Biaya Listrik & Operasional Workshop')" class="text-[10.5px] bg-[#FAF9F6] hover:bg-[#ECE8E1] text-[#4A4A4A] px-2 py-0.5 rounded-md border border-[#ECE8E1] transition cursor-pointer">Operasional</button>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Catatan / Keterangan</label>
                    <textarea name="note" rows="2" placeholder="Catatan tambahan transaksi..." class="w-full bg-[#F8F7F4] text-xs py-2 px-3 rounded-xl border border-[#ECE8E1]"></textarea>
                </div>

                <div class="mt-6 flex justify-end gap-2.5 pt-3 border-t border-[#ECE8E1]">
                    <button type="button" onclick="closeAddFinancialModal()" class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] font-semibold text-xs px-4 py-2 rounded-full cursor-pointer">Batal</button>
                    <button type="submit" class="bg-[#8B0000] hover:bg-[#6e0202] text-white font-bold text-xs px-5 py-2 rounded-full shadow-xs cursor-pointer">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 6: UBAH DATA TRANSAKSI KAS -->
    <div id="edit-financial-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-[#ECE8E1] animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-[#ECE8E1]">
                <h3 class="text-base font-bold text-[#1A1A1A]">Ubah Entri Transaksi Kas</h3>
                <button type="button" onclick="closeEditFinancialModal()" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1 cursor-pointer">✕</button>
            </div>

            <form id="edit-financial-form" action="" method="POST" class="mt-4 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#1A1A1A] mb-1">Tipe Mutasi <span class="text-maroon">*</span></label>
                        <select id="edit-financial-type" name="type" required class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3 rounded-xl border border-[#ECE8E1]">
                            <option value="pemasukan">Pemasukan (Penjualan)</option>
                            <option value="pengeluaran">Pengeluaran (Bahan/Biaya)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-[#1A1A1A] mb-1">Tanggal <span class="text-maroon">*</span></label>
                        <input type="date" id="edit-financial-date" name="entry_date" required class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3 rounded-xl border border-[#ECE8E1]">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Nominal (Rupiah) <span class="text-maroon">*</span></label>
                    <input type="number" id="edit-financial-amount" name="amount" min="1000" step="500" required class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                </div>

                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Kategori Transaksi <span class="text-maroon">*</span></label>
                    <input type="text" id="edit-financial-category" name="category" list="financial-categories" required class="w-full bg-[#F8F7F4] text-xs py-2.5 px-3.5 rounded-xl border border-[#ECE8E1]">
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <span class="text-[10px] text-[#8A8A85] self-center mr-1">Rekomendasi:</span>
                        <button type="button" onclick="setFinanceCategory('edit', 'Penjualan Ganci (Online Web)')" class="text-[10.5px] bg-[#FAF9F6] hover:bg-[#ECE8E1] text-[#4A4A4A] px-2 py-0.5 rounded-md border border-[#ECE8E1] transition cursor-pointer">Penjualan Online</button>
                        <button type="button" onclick="setFinanceCategory('edit', 'Penjualan Ganci (Offline Kasir)')" class="text-[10.5px] bg-[#FAF9F6] hover:bg-[#ECE8E1] text-[#4A4A4A] px-2 py-0.5 rounded-md border border-[#ECE8E1] transition cursor-pointer">Penjualan Offline</button>
                        <button type="button" onclick="setFinanceCategory('edit', 'Pembelian Chip NFC NTAG213')" class="text-[10.5px] bg-[#FAF9F6] hover:bg-[#ECE8E1] text-[#4A4A4A] px-2 py-0.5 rounded-md border border-[#ECE8E1] transition cursor-pointer">Chip NFC</button>
                        <button type="button" onclick="setFinanceCategory('edit', 'Pembelian Bahan Akrilik & Resin')" class="text-[10.5px] bg-[#FAF9F6] hover:bg-[#ECE8E1] text-[#4A4A4A] px-2 py-0.5 rounded-md border border-[#ECE8E1] transition cursor-pointer">Bahan Akrilik</button>
                        <button type="button" onclick="setFinanceCategory('edit', 'Biaya Listrik & Operasional Workshop')" class="text-[10.5px] bg-[#FAF9F6] hover:bg-[#ECE8E1] text-[#4A4A4A] px-2 py-0.5 rounded-md border border-[#ECE8E1] transition cursor-pointer">Operasional</button>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-[#1A1A1A] mb-1">Catatan / Keterangan</label>
                    <textarea id="edit-financial-note" name="note" rows="2" class="w-full bg-[#F8F7F4] text-xs py-2 px-3 rounded-xl border border-[#ECE8E1]"></textarea>
                </div>

                <div class="mt-6 flex justify-end gap-2.5 pt-3 border-t border-[#ECE8E1]">
                    <button type="button" onclick="closeEditFinancialModal()" class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] font-semibold text-xs px-4 py-2 rounded-full cursor-pointer">Batal</button>
                    <button type="submit" class="bg-[#8B0000] hover:bg-[#6e0202] text-white font-bold text-xs px-5 py-2 rounded-full shadow-xs cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Password Modal
    function openPasswordModal() {
        document.getElementById('password-modal').classList.remove('hidden');
    }
    function closePasswordModal() {
        document.getElementById('password-modal').classList.add('hidden');
    }

    // Add User Modal
    function openAddUserModal() {
        document.getElementById('add-user-modal').classList.remove('hidden');
    }
    function closeAddUserModal() {
        document.getElementById('add-user-modal').classList.add('hidden');
    }

    // Edit User Modal
    function openEditUserModal(user) {
        document.getElementById('edit-user-form').action = `/admin/settings/users/${user.id}`;
        document.getElementById('edit-user-name').value = user.name || '';
        document.getElementById('edit-user-email').value = user.email || '';
        document.getElementById('edit-user-phone').value = user.no_whatsapp || '';
        document.getElementById('edit-user-role').value = user.role || 'user';
        document.getElementById('edit-user-jabatan').value = user.jabatan_admin || '';
        document.getElementById('edit-user-modal').classList.remove('hidden');
    }
    function closeEditUserModal() {
        document.getElementById('edit-user-modal').classList.add('hidden');
    }

    // Add Design Modal
    function openAddDesignModal() {
        document.getElementById('add-design-modal').classList.remove('hidden');
    }
    function closeAddDesignModal() {
        document.getElementById('add-design-modal').classList.add('hidden');
    }

    // Add Financial Modal
    function openAddFinancialModal() {
        document.getElementById('add-financial-modal').classList.remove('hidden');
    }
    function closeAddFinancialModal() {
        document.getElementById('add-financial-modal').classList.add('hidden');
    }

    // Edit Financial Modal
    function openEditFinancialModal(entry) {
        document.getElementById('edit-financial-form').action = `/admin/settings/financial/${entry.id}`;
        document.getElementById('edit-financial-type').value = entry.type || 'pemasukan';
        let dateStr = entry.entry_date ? String(entry.entry_date).substring(0, 10) : '';
        document.getElementById('edit-financial-date').value = dateStr;
        document.getElementById('edit-financial-amount').value = Math.round(Number(entry.amount) || 0);
        document.getElementById('edit-financial-category').value = entry.category || '';
        document.getElementById('edit-financial-note').value = entry.note || '';
        document.getElementById('edit-financial-modal').classList.remove('hidden');
    }
    function closeEditFinancialModal() {
        document.getElementById('edit-financial-modal').classList.add('hidden');
    }

    // Set Category Preset Helper
    function setFinanceCategory(target, categoryName) {
        if (target === 'add') {
            const input = document.getElementById('add-financial-category');
            if (input) input.value = categoryName;
        } else if (target === 'edit') {
            const input = document.getElementById('edit-financial-category');
            if (input) input.value = categoryName;
        }
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePasswordModal();
            closeAddUserModal();
            closeEditUserModal();
            closeAddDesignModal();
            closeAddFinancialModal();
            closeEditFinancialModal();
        }
    });

    // Close modals on backdrop click
    ['password-modal', 'add-user-modal', 'edit-user-modal', 'add-design-modal', 'add-financial-modal', 'edit-financial-modal'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    });
</script>
@endpush

