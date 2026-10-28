<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings — FlexiTag</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite([
        'resources/css/components/user-sidebar.css',
        'resources/css/pages/user-settings.css',
        'resources/js/user-sidebar.js',
        'resources/js/user-settings.js'
    ])
</head>
<body class="user-settings-body">

<div class="settings-viewport-wrap">

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
                <h1 class="topbar-title">Settings</h1>
            </div>
        </div>
    </header>

    {{-- ========================================================================= --}}
    {{-- 2. MAIN SETTINGS CONTENT AREA                                             --}}
    {{-- ========================================================================= --}}
    <main class="settings-content">

        {{-- Flash Messages --}}
        @if (session('status'))
            <div class="settings-alert-ok" role="status">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="settings-alert-bad" role="alert">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Hero Pengaturan Akun Panel --}}
        <section class="settings-hero-card" aria-label="Pengaturan Akun">
            <div class="settings-hero-pattern" aria-hidden="true"></div>

            {{-- Pa'barre Allo Sunburst SVG --}}
            <svg class="settings-hero-sunburst" viewBox="0 0 100 100" fill="none" stroke="#E5B32A" stroke-width="2" aria-hidden="true">
                <circle cx="50" cy="50" r="45" stroke-dasharray="4 2"/>
                <circle cx="50" cy="50" r="28"/>
                <circle cx="50" cy="50" r="14"/>
                <line x1="50" y1="5" x2="50" y2="95"/>
                <line x1="5" y1="50" x2="95" y2="50"/>
                <line x1="18" y1="18" x2="82" y2="82"/>
                <line x1="18" y1="82" x2="82" y2="18"/>
            </svg>

            <span class="settings-hero-badge">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                <span>KONFIGURASI AKUN &amp; PRIVASI</span>
            </span>

            <h2 class="settings-hero-title">Pengaturan Akun</h2>
            <p class="settings-hero-subtitle">Kelola enkripsi kunci fisik, tautan tap, dan identitas digital Anda.</p>
        </section>

        {{-- Kartu Profil Pengguna --}}
        <section class="profile-card" aria-label="Identitas Profil">
            <div class="profile-card-left">
                <div class="profile-avatar-wrap">
                    @if ($profile?->photo_path)
                        <img src="{{ asset('storage/' . $profile->photo_path) }}" alt="{{ $displayName }}">
                    @else
                        <span class="profile-avatar-initials">{{ $initials }}</span>
                    @endif
                    <span class="profile-avatar-badge" title="Akun Terverifikasi" aria-hidden="true">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                </div>

                <div class="profile-meta-wrap">
                    <h3 class="profile-name">{{ $displayName }}</h3>
                    <span class="profile-email">{{ $user->email }}</span>
                    <span class="profile-tag-pill">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                        </svg>
                        <span>{{ $profile?->headline ?: 'Member MaccaTech' }}</span>
                    </span>
                </div>
            </div>

            <a href="{{ route('user.dashboard') }}" class="profile-edit-btn" title="Buka Dashboard Profil" aria-label="Edit Profil">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
            </a>
        </section>

        {{-- Kartu Hardware Terhubung (Data Nyata) --}}
        <section class="hardware-connected-card" aria-label="Hardware Terhubung">
            <div class="hardware-card-left">
                <div class="hardware-icon-wrap" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10"></line>
                    </svg>
                </div>
                <div class="hardware-text-wrap">
                    <div class="hardware-title-row">
                        <span class="hardware-status-dot {{ $hardwareCount > 0 ? '' : 'is-inactive' }}"></span>
                        <span class="hardware-title-text">{{ $hardwareCount }} Hardware Terhubung</span>
                    </div>
                    <span class="hardware-subtitle">
                        @if ($primaryTag)
                            Tag: <strong>{{ $primaryTag->tag_code }}</strong> ({{ ucfirst($primaryTag->status) }})
                        @else
                            Belum ada hardware terhubung
                        @endif
                    </span>
                </div>
            </div>

            <a href="{{ route('user.merch') }}" class="hardware-view-btn">
                Detail
            </a>
        </section>

        {{-- ===================================================================== --}}
        {{-- SECTION: KEAMANAN                                                     --}}
        {{-- ===================================================================== --}}
        <div class="settings-section-head">
            <div class="settings-section-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <span>Keamanan</span>
            </div>
            <span class="section-diamond-ornament">◆</span>
        </div>

        <section class="settings-list-card" aria-label="Opsi Keamanan Akun">
            {{-- Row 1: Ubah Kata Sandi --}}
            <button type="button" class="settings-list-row" onclick="openModal('passwordModal')">
                <div class="settings-row-left">
                    <div class="settings-row-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <div class="settings-row-text">
                        <span class="settings-row-title">Ubah Kata Sandi</span>
                        <span class="settings-row-subtitle">
                            Diperbarui {{ $user->updated_at ? $user->updated_at->diffForHumans() : 'baru-baru ini' }}
                        </span>
                    </div>
                </div>
                <div class="settings-row-right">
                    <svg class="settings-chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </button>

            {{-- Row 2: Autentikasi 2 Langkah (Status Jujur dari DB) --}}
            <button type="button" class="settings-list-row" onclick="openModal('twoFactorModal')">
                <div class="settings-row-left">
                    <div class="settings-row-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <div class="settings-row-text">
                        <span class="settings-row-title">Autentikasi 2 Langkah</span>
                        <span class="settings-row-subtitle">SMS / Authenticator App</span>
                    </div>
                </div>
                <div class="settings-row-right">
                    @if ($user->two_factor_enabled)
                        <span class="settings-status-badge is-active">Aktif</span>
                    @else
                        <span class="settings-status-badge is-inactive">Tidak Aktif</span>
                    @endif
                    <svg class="settings-chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </button>

            {{-- Row 3: Akses & Privasi Tag --}}
            <button type="button" class="settings-list-row" onclick="openModal('privacyModal')">
                <div class="settings-row-left">
                    <div class="settings-row-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </div>
                    <div class="settings-row-text">
                        <span class="settings-row-title">Akses &amp; Privasi Tag</span>
                        <span class="settings-row-subtitle">
                            Visibilitas nomor WA, email, &amp; geolokasi tap
                        </span>
                    </div>
                </div>
                <div class="settings-row-right">
                    <span class="settings-status-badge is-info">Terproteksi</span>
                    <svg class="settings-chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </button>
        </section>

        {{-- ===================================================================== --}}
        {{-- SECTION: BANTUAN & DUKUNGAN                                           --}}
        {{-- ===================================================================== --}}
        <div class="settings-section-head">
            <div class="settings-section-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                    <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
                </svg>
                <span>Bantuan &amp; Dukungan</span>
            </div>
            <span class="section-diamond-ornament">◆</span>
        </div>

        <section class="settings-list-card" aria-label="Bantuan dan Legal">
            {{-- Row 1: Pusat Bantuan / FAQ --}}
            <a href="{{ route('home') }}#faq" class="settings-list-row">
                <div class="settings-row-left">
                    <div class="settings-row-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <div class="settings-row-text">
                        <span class="settings-row-title">Pusat Bantuan / FAQ</span>
                        <span class="settings-row-subtitle">Panduan aktivasi dan pairing merchandise</span>
                    </div>
                </div>
                <div class="settings-row-right">
                    <svg class="settings-chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </a>

            {{-- Row 2: Hubungi Tim Support (WhatsApp Resmi dari Config) --}}
            <a href="{{ $supportWaUrl }}" target="_blank" rel="noopener noreferrer" class="settings-list-row">
                <div class="settings-row-left">
                    <div class="settings-row-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                        </svg>
                    </div>
                    <div class="settings-row-text">
                        <span class="settings-row-title">Hubungi Tim Support</span>
                        <span class="settings-row-subtitle">Chat via WhatsApp resmi FlexiTag</span>
                    </div>
                </div>
                <div class="settings-row-right">
                    <span class="settings-status-badge is-active">Resmi</span>
                    <svg class="settings-chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </a>

            {{-- Row 2.5: Kirim Saran & Laporan Kendala ke Admin --}}
            <button type="button" class="settings-list-row" onclick="openModal('feedbackModal')">
                <div class="settings-row-left">
                    <div class="settings-row-icon" aria-hidden="true" style="background: rgba(240, 199, 94, 0.15); color: #B45309;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div class="settings-row-text">
                        <span class="settings-row-title">Kirim Saran &amp; Laporan Kendala</span>
                        <span class="settings-row-subtitle">Beri masukan ide fitur atau laporkan kendala ke admin</span>
                    </div>
                </div>
                <div class="settings-row-right">
                    <span class="settings-status-badge" style="background: #FEF3C7; color: #92400E;">Admin</span>
                    <svg class="settings-chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </button>

            {{-- Row 3: Syarat & Ketentuan --}}
            <button type="button" class="settings-list-row" onclick="openModal('termsModal')">
                <div class="settings-row-left">
                    <div class="settings-row-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <div class="settings-row-text">
                        <span class="settings-row-title">Syarat &amp; Ketentuan</span>
                        <span class="settings-row-subtitle">Kebijakan privasi dan lisensi produk</span>
                    </div>
                </div>
                <div class="settings-row-right">
                    <svg class="settings-chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </button>
        </section>

    </main>

    {{-- ========================================================================= --}}
    {{-- 3. MODALS                                                                 --}}
    {{-- ========================================================================= --}}

    {{-- Modal 1: Ubah Kata Sandi --}}
    <div class="settings-modal-overlay" id="passwordModal" onclick="if(event.target === this) closeModal('passwordModal')">
        <div class="settings-modal-dialog">
            <div class="modal-header-row">
                <div class="modal-title-wrap">
                    <div class="modal-icon-badge" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <h3 class="modal-title-text">Ubah Kata Sandi</h3>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('passwordModal')" aria-label="Tutup Modal">✕</button>
            </div>

            <form method="POST" action="{{ route('user.settings.password') }}">
                @csrf
                <div class="modal-input-group">
                    <label for="current_password">Kata Sandi Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" class="modal-text-input" placeholder="Masukkan kata sandi saat ini" required autocomplete="current-password">
                </div>

                <div class="modal-input-group">
                    <label for="password">Kata Sandi Baru (Min. 8 karakter)</label>
                    <input type="password" id="password" name="password" class="modal-text-input" placeholder="Kombinasi huruf &amp; angka" required autocomplete="new-password">
                </div>

                <div class="modal-input-group">
                    <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="modal-text-input" placeholder="Ulangi kata sandi baru" required autocomplete="new-password">
                </div>

                <button type="submit" class="btn-primary-action">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Simpan Kata Sandi</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Modal 2: 2FA Info (Truthful Backend State) --}}
    <div class="settings-modal-overlay" id="twoFactorModal" onclick="if(event.target === this) closeModal('twoFactorModal')">
        <div class="settings-modal-dialog">
            <div class="modal-header-row">
                <div class="modal-title-wrap">
                    <div class="modal-icon-badge" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <h3 class="modal-title-text">Autentikasi 2 Langkah</h3>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('twoFactorModal')" aria-label="Tutup Modal">✕</button>
            </div>

            <div class="two-factor-status-card">
                <div class="two-factor-header">
                    <span class="two-factor-label">Status Proteksi Akun</span>
                    @if ($user->two_factor_enabled)
                        <span class="settings-status-badge is-active">Aktif</span>
                    @else
                        <span class="settings-status-badge is-inactive">Tidak Aktif</span>
                    @endif
                </div>

                <p class="two-factor-desc">
                    @if ($user->two_factor_enabled)
                        Akun FlexiTag Anda telah dilindungi lapisan otentikasi ganda saat melakukan login dari perangkat baru.
                    @else
                        Akun Anda saat ini menggunakan autentikasi kata sandi standar. Lapisan verifikasi tambahan disarankan untuk keamanan merchandise.
                    @endif
                </p>

                @if (! $user->two_factor_enabled)
                    <div class="two-factor-note">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>Penyedia generator kode OTP (Authenticator App / SMS Gateway) saat ini dalam tahap integrasi cloud MaccaTech.</span>
                    </div>
                @endif
            </div>

            <button type="button" class="btn-secondary-action" onclick="closeModal('twoFactorModal')">
                Mengerti
            </button>
        </div>
    </div>

    {{-- Modal 3: Pengaturan Privasi Tag --}}
    <div class="settings-modal-overlay" id="privacyModal" onclick="if(event.target === this) closeModal('privacyModal')">
        <div class="settings-modal-dialog">
            <div class="modal-header-row">
                <div class="modal-title-wrap">
                    <div class="modal-icon-badge" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="21" x2="4" y2="14"></line>
                            <line x1="4" y1="10" x2="4" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12" y2="3"></line>
                            <line x1="20" y1="21" x2="20" y2="16"></line>
                            <line x1="20" y1="12" x2="20" y2="3"></line>
                            <line x1="1" y1="14" x2="7" y2="14"></line>
                            <line x1="9" y1="8" x2="15" y2="8"></line>
                            <line x1="17" y1="16" x2="23" y2="16"></line>
                        </svg>
                    </div>
                    <h3 class="modal-title-text">Akses &amp; Privasi Tag</h3>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('privacyModal')" aria-label="Tutup Modal">✕</button>
            </div>

            <form method="POST" action="{{ route('user.settings.privacy') }}">
                @csrf
                <div class="privacy-toggle-list">
                    {{-- Toggle 1: WhatsApp Publik --}}
                    <div class="privacy-toggle-item">
                        <div class="privacy-toggle-info">
                            <span class="privacy-toggle-title">Nomor WhatsApp Publik</span>
                            <span class="privacy-toggle-desc">Tampilkan tautan langsung chat WhatsApp pada profil publik</span>
                        </div>
                        <label class="switch-toggle-wrap" aria-label="Toggle WhatsApp Publik">
                            <input type="checkbox" name="show_phone" value="1" {{ ($privacy?->show_phone ?? false) ? 'checked' : '' }}>
                            <span class="switch-slider"></span>
                        </label>
                    </div>

                    {{-- Toggle 2: Email Publik --}}
                    <div class="privacy-toggle-item">
                        <div class="privacy-toggle-info">
                            <span class="privacy-toggle-title">Alamat Email Publik</span>
                            <span class="privacy-toggle-desc">Izinkan pengunjung melihat dan mengirim email ke alamat akun</span>
                        </div>
                        <label class="switch-toggle-wrap" aria-label="Toggle Email Publik">
                            <input type="checkbox" name="show_email" value="1" {{ ($privacy?->show_email ?? false) ? 'checked' : '' }}>
                            <span class="switch-slider"></span>
                        </label>
                    </div>

                    {{-- Toggle 3: Geolokasi Tap --}}
                    <div class="privacy-toggle-item">
                        <div class="privacy-toggle-info">
                            <span class="privacy-toggle-title">Catat Geolokasi Tap</span>
                            <span class="privacy-toggle-desc">Simpan koordinat kota pengunjung saat tag dipindai untuk analitik</span>
                        </div>
                        <label class="switch-toggle-wrap" aria-label="Toggle Geolokasi Tap">
                            <input type="checkbox" name="show_location" value="1" {{ ($privacy?->show_location ?? false) ? 'checked' : '' }}>
                            <span class="switch-slider"></span>
                        </label>
                    </div>

                    {{-- Toggle 4: Mesin Pencari --}}
                    <div class="privacy-toggle-item">
                        <div class="privacy-toggle-info">
                            <span class="privacy-toggle-title">Indeks Mesin Pencari</span>
                            <span class="privacy-toggle-desc">Izinkan profil publik terindeks di Google dan pencarian web</span>
                        </div>
                        <label class="switch-toggle-wrap" aria-label="Toggle Indeks Mesin Pencari">
                            <input type="checkbox" name="indexable" value="1" {{ ($privacy?->indexable ?? false) ? 'checked' : '' }}>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-primary-action">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Simpan Privasi</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Modal 4: Syarat & Ketentuan --}}
    <div class="settings-modal-overlay" id="termsModal" onclick="if(event.target === this) closeModal('termsModal')">
        <div class="settings-modal-dialog">
            <div class="modal-header-row">
                <div class="modal-title-wrap">
                    <div class="modal-icon-badge" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <h3 class="modal-title-text">Syarat &amp; Ketentuan</h3>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('termsModal')" aria-label="Tutup Modal">✕</button>
            </div>

            <div class="terms-content-box">
                <div class="terms-item-card">
                    <span class="terms-item-num">1</span>
                    <div class="terms-item-body">
                        <span class="terms-item-title">Kepemilikan Tag Fisik</span>
                        <p class="terms-item-desc">
                            Setiap gantungan kunci akrilik FlexiTag terikat dengan kode aktivasi unik dan tanda tangan chip anti-kloning MaccaTech. Kepemilikan fisik sah berada pada akun yang telah menyelesaikan proses aktivasi.
                        </p>
                    </div>
                </div>

                <div class="terms-item-card">
                    <span class="terms-item-num">2</span>
                    <div class="terms-item-body">
                        <span class="terms-item-title">Privasi Data &amp; Mode Hilang</span>
                        <p class="terms-item-desc">
                            Nomor kontak pemilik tidak pernah disebarluaskan secara publik saat tag berada dalam Mode Barang Hilang. Semua komunikasi penemu diarahkan melalui formulir relay terenkripsi demi keamanan Anda.
                        </p>
                    </div>
                </div>

                <div class="terms-item-card">
                    <span class="terms-item-num">3</span>
                    <div class="terms-item-body">
                        <span class="terms-item-title">Hak Kekayaan Intelektual Budaya</span>
                        <p class="terms-item-desc">
                            Ornamen motif budaya Toraja (Pa'barre Allo) dan Bugis (Sulapa Eppa') merupakan karya kebudayaan berlisensi FlexiTag Makassar yang dilindungi hak cipta.
                        </p>
                    </div>
                </div>
            </div>

            <button type="button" class="btn-secondary-action" onclick="closeModal('termsModal')">
                Tutup
            </button>
        </div>
    </div>

    {{-- Modal 5: Kirim Saran & Laporan Kendala ke Admin --}}
    <div class="settings-modal-overlay" id="feedbackModal" onclick="if(event.target === this) closeModal('feedbackModal')">
        <div class="settings-modal-dialog">
            <div class="modal-header-row">
                <div class="modal-title-wrap">
                    <div class="modal-icon-badge" aria-hidden="true" style="background: #FCF5E3; color: #8C6D23;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="modal-title-text">Kirim Saran &amp; Laporan</h3>
                        <p class="modal-title-sub">Pesan Anda akan masuk langsung ke pusat notifikasi administrator</p>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeModal('feedbackModal')" aria-label="Tutup Modal">✕</button>
            </div>

            <form method="POST" action="{{ route('user.settings.feedback') }}">
                @csrf
                <div class="modal-form-group">
                    <label class="modal-field-label" for="feedbackCategory">Kategori Pesan <span class="req-star">*</span></label>
                    <select name="category" id="feedbackCategory" class="modal-text-input" style="padding-right: 2rem;" required>
                        <option value="saran">💡 Saran &amp; Usulan Fitur Baru</option>
                        <option value="kendala">⚠️ Laporan Kendala Teknis / Bug</option>
                        <option value="pertanyaan">❓ Pertanyaan Layanan &amp; Hardware</option>
                    </select>
                </div>

                <div class="modal-form-group">
                    <label class="modal-field-label" for="feedbackContact">Nomor WhatsApp / Kontak Alternatif (Opsional)</label>
                    <input type="text" name="contact" id="feedbackContact" class="modal-text-input" value="{{ $user->no_whatsapp ?? $user->email }}" placeholder="08xxxxxxxxxx">
                    <span class="modal-field-hint">Digunakan bila admin perlu membalas konfirmasi saran Anda.</span>
                </div>

                <div class="modal-form-group">
                    <label class="modal-field-label" for="feedbackMessage">Isi Pesan / Keterangan <span class="req-star">*</span></label>
                    <textarea name="message" id="feedbackMessage" rows="4" class="modal-text-input" style="min-height: 100px; resize: vertical; line-height: 1.5;" placeholder="Tuliskan saran, masukan perbaikan, atau kendala yang Anda alami secara detail..." required minlength="5" maxlength="2000"></textarea>
                </div>

                <button type="submit" class="btn-primary-action">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    <span>Kirim Pesan ke Admin</span>
                </button>
            </form>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- UNIFIED USER SIDEBAR / NAVIGATION DRAWER                                  --}}
    {{-- ========================================================================= --}}
    @include('partials.user-sidebar')

</div>

</body>
</html>
