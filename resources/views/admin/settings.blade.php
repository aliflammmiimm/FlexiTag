@extends('layouts.admin')

@section('title', 'Pengaturan Sistem & Admin')

@section('content')
    <!-- Flash Success Alert -->
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

    <!-- Validation Errors Alert -->
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

    <!-- Page Title & Header Info (Without Breadcrumb as requested) -->
    <div class="mb-7">
        <h2 class="text-2xl sm:text-[26px] font-bold text-[#1A1A1A] tracking-tight">
            Pengaturan Sistem &amp; Admin
        </h2>
        <p class="text-xs text-[#737373] mt-1 font-normal max-w-2xl leading-relaxed">
            Kelola konfigurasi kredensial administrator, nomor relay darurat, dan preferensi otomatisasi platform FlexiTag.
        </p>
    </div>

    <!-- Main Settings Form -->
    <form action="{{ route('admin.settings.update') }}" method="POST" id="settings-form">
        @csrf

        <!-- ===================================================== -->
        <!-- CARD 1: PROFIL PENGELOLA & KEAMANAN                   -->
        <!-- ===================================================== -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] mb-7">
            
            <!-- Section Header -->
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
                            Informasi akun administrator utama dan otentikasi console.
                        </p>
                    </div>
                </div>

                <!-- Super Admin Node Pill Badge -->
                <span class="inline-flex items-center gap-1.5 bg-[#FCF5E3] text-[#8C6D23] font-semibold text-xs px-3.5 py-1.5 rounded-full shadow-2xs shrink-0">
                    <span class="w-2 h-2 rounded-full bg-[#E5B32A]"></span>
                    <span>Super Admin Node</span>
                </span>
            </div>

            <!-- Form Grid: 4 Credentials Fields -->
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
                        value="{{ old('name', $user->name ?? 'Baharuddin') }}"
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
                        value="{{ old('jabatan_admin', $user->jabatan_admin ?? 'Ops Lead Makassar') }}"
                        class="w-full bg-[#F8F7F4] hover:bg-[#F2F1ED] focus:bg-white text-xs font-bold text-[#1A1A1A] py-3 px-4 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] shadow-xs transition"
                    >
                    <span class="text-[11px] text-[#8A8A85] font-normal block mt-1.5">
                        Akses penuh tingkat Administrator Konsol.
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
                        value="{{ old('email', $user->email ?? 'admin@flexitag.id') }}"
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
                        value="{{ old('no_wa_cs_pusat', $settings->no_wa_cs_pusat ?? '+62 812-3456-7890') }}"
                        class="w-full bg-[#F8F7F4] hover:bg-[#F2F1ED] focus:bg-white text-xs font-bold text-[#1A1A1A] py-3 px-4 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] shadow-xs transition font-mono"
                    >
                    <span class="text-[11px] text-[#8A8A85] font-normal block mt-1.5">
                        Nomor pusat bantuan dan kontak eskalasi darurat.
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
                        <h4 class="text-xs font-bold text-[#1A1A1A]">Keamanan Kata Sandi</h4>
                        <p class="text-[11px] text-[#737373] mt-0.5">Terakhir diperbarui 28 hari yang lalu.</p>
                    </div>
                </div>

                <!-- Ubah Kata Sandi Button -->
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

        <!-- ===================================================== -->
        <!-- CARD 2: KONFIGURASI PLATFORM & RELAY                  -->
        <!-- ===================================================== -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] mb-7">
            
            <!-- Section Header -->
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

            <!-- Toggles List -->
            <div class="divide-y divide-[#F2EEE9]">
                
                <!-- Toggle 1: Sistem Relay WhatsApp -->
                <div class="py-4.5 flex items-center justify-between gap-4">
                    <div class="pr-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-[#1A1A1A]">
                                Sistem Relay WhatsApp (Mode Barang Hilang)
                            </span>
                            <span class="bg-gold-accent text-[#380208] text-[9.5px] font-extrabold uppercase px-2 py-0.5 rounded-full">
                                AKTIF
                            </span>
                        </div>
                        <p class="text-xs text-[#737373] mt-0.5 leading-relaxed">
                            Teruskan pesan penemu barang secara otomatis ke WhatsApp pemilik melalui gateway pesan terenkripsi.
                        </p>
                    </div>

                    <!-- Toggle Switch -->
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

                    <!-- Toggle Switch -->
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

                    <!-- Toggle Switch -->
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

        <!-- ===================================================== -->
        <!-- BOTTOM VERIFICATION & SAVE BAR                        -->
        <!-- ===================================================== -->
        <div class="bg-white rounded-3xl p-4.5 sm:p-5 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col sm:flex-row items-center justify-between gap-4">
            
            <!-- Left Info -->
            <div class="flex items-center gap-2.5 text-xs text-[#737373]">
                <svg class="w-4.5 h-4.5 text-[#8A8A85] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                </svg>
                <span>Semua perubahan otomatis diverifikasi oleh sistem enkripsi server lokal Makassar Node.</span>
            </div>

            <!-- Save Button -->
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

    <!-- ========================================================= -->
    <!-- INTERACTIVE MODAL: UBAH KATA SANDI ADMIN                  -->
    <!-- ========================================================= -->
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
@endsection

@push('scripts')
<script>
    function openPasswordModal() {
        document.getElementById('password-modal').classList.remove('hidden');
    }

    function closePasswordModal() {
        document.getElementById('password-modal').classList.add('hidden');
    }
</script>
@endpush
