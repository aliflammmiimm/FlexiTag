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
        <button type="button" class="hamburger-btn" id="openDrawerBtn" aria-label="Buka Menu Navigasi" onclick="toggleUserSidebar(true)">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="topbar-branding">
            <span class="topbar-subtitle">FLEXITAG</span>
            <h1 class="topbar-title">Settings</h1>
        </div>
    </header>

    {{-- ========================================================================= --}}
    {{-- 2. MAIN SETTINGS CONTENT AREA                                             --}}
    {{-- ========================================================================= --}}
    <main class="settings-content">

        {{-- Flash Messages --}}
        @if (session('status'))
            <div class="settings-alert-ok" role="status">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="settings-alert-bad" role="alert">
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
                    <span class="profile-avatar-badge" title="Pengaturan Identitas" aria-hidden="true">⚙</span>
                </div>

                <div class="profile-meta-wrap">
                    <h3 class="profile-name">{{ $displayName }}</h3>
                    <span class="profile-email">{{ $user->email }}</span>
                    <span class="profile-tag-pill">
                        <span>🎖️</span>
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
                        <span class="hardware-status-dot"></span>
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

            <a href="{{ route('user.dashboard') }}" class="hardware-view-btn">
                Detail
            </a>
        </section>

        {{-- ===================================================================== --}}
        {{-- SECTION: KEAMANAN                                                     --}}
        {{-- ===================================================================== --}}
        <div class="settings-section-head">
            <div class="settings-section-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
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
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
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
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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

            {{-- Row 3: Syarat & Ketentuan --}}
            <button type="button" class="settings-list-row" onclick="openModal('termsModal')">
                <div class="settings-row-left">
                    <div class="settings-row-icon" aria-hidden="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                <h3 class="modal-title-text">🔒 Ubah Kata Sandi</h3>
                <button type="button" class="modal-close-icon" onclick="closeModal('passwordModal')">✕</button>
            </div>

            <form method="POST" action="{{ route('user.settings.password') }}">
                @csrf
                <div class="modal-input-group">
                    <label for="current_password">Kata Sandi Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" class="modal-text-input" required autocomplete="current-password">
                </div>

                <div class="modal-input-group">
                    <label for="password">Kata Sandi Baru (Min. 8 karakter)</label>
                    <input type="password" id="password" name="password" class="modal-text-input" required autocomplete="new-password">
                </div>

                <div class="modal-input-group">
                    <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="modal-text-input" required autocomplete="new-password">
                </div>

                <button type="submit" class="btn-primary-action">
                    Simpan Kata Sandi
                </button>
            </form>
        </div>
    </div>

    {{-- Modal 2: 2FA Info (Truthful Backend State) --}}
    <div class="settings-modal-overlay" id="twoFactorModal" onclick="if(event.target === this) closeModal('twoFactorModal')">
        <div class="settings-modal-dialog">
            <div class="modal-header-row">
                <h3 class="modal-title-text">🛡️ Autentikasi 2 Langkah</h3>
                <button type="button" class="modal-close-icon" onclick="closeModal('twoFactorModal')">✕</button>
            </div>

            <div style="font-size: 0.8rem; color: #4A4A4A; line-height: 1.5; margin-bottom: 1.15rem;">
                Status 2FA Akun: 
                @if ($user->two_factor_enabled)
                    <strong style="color: #2E7D32;">Aktif</strong>. Akun Anda dilindungi lapisan otentikasi ganda.
                @else
                    <strong style="color: #78716C;">Tidak Aktif</strong>.
                    <p style="margin-top: 0.5rem; font-size: 0.74rem; color: #78716C;">
                        Penyedia generator OTP (Authenticator App / SMS Gateway) saat ini dalam tahap integrasi cloud MaccaTech.
                    </p>
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
                <h3 class="modal-title-text">⚙️ Pengaturan Privasi Tag</h3>
                <button type="button" class="modal-close-icon" onclick="closeModal('privacyModal')">✕</button>
            </div>

            <form method="POST" action="{{ route('user.settings.privacy') }}">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem; margin-bottom: 1.25rem;">
                    <label style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8rem; cursor: pointer;">
                        <span>Tampilkan Nomor WhatsApp Publik</span>
                        <input type="checkbox" name="show_phone" value="1" {{ ($privacy?->show_phone ?? false) ? 'checked' : '' }} style="accent-color: #4A0A0E; width: 18px; height: 18px;">
                    </label>

                    <label style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8rem; cursor: pointer;">
                        <span>Tampilkan Alamat Email Publik</span>
                        <input type="checkbox" name="show_email" value="1" {{ ($privacy?->show_email ?? false) ? 'checked' : '' }} style="accent-color: #4A0A0E; width: 18px; height: 18px;">
                    </label>

                    <label style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8rem; cursor: pointer;">
                        <span>Catat Geolokasi Tap Pengunjung</span>
                        <input type="checkbox" name="show_location" value="1" {{ ($privacy?->show_location ?? false) ? 'checked' : '' }} style="accent-color: #4A0A0E; width: 18px; height: 18px;">
                    </label>

                    <label style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8rem; cursor: pointer;">
                        <span>Izinkan Indeks Mesin Pencari</span>
                        <input type="checkbox" name="indexable" value="1" {{ ($privacy?->indexable ?? false) ? 'checked' : '' }} style="accent-color: #4A0A0E; width: 18px; height: 18px;">
                    </label>
                </div>

                <button type="submit" class="btn-primary-action">
                    Simpan Privasi
                </button>
            </form>
        </div>
    </div>

    {{-- Modal 4: Syarat & Ketentuan --}}
    <div class="settings-modal-overlay" id="termsModal" onclick="if(event.target === this) closeModal('termsModal')">
        <div class="settings-modal-dialog">
            <div class="modal-header-row">
                <h3 class="modal-title-text">📄 Syarat &amp; Ketentuan</h3>
                <button type="button" class="modal-close-icon" onclick="closeModal('termsModal')">✕</button>
            </div>

            <div style="font-size: 0.76rem; color: #4A4A4A; line-height: 1.55; max-height: 280px; overflow-y: auto; padding-right: 4px; margin-bottom: 1.15rem;">
                <p style="margin-bottom: 0.6rem;">
                    <strong>1. Kepemilikan Tag Fisik:</strong> Setiap gantungan kunci akrilik FlexiTag terikat dengan kode aktivasi unik dan tanda tangan chip anti-kloning MaccaTech.
                </p>
                <p style="margin-bottom: 0.6rem;">
                    <strong>2. Privasi Data:</strong> Nomor kontak pemilik tidak pernah disebarluaskan saat tag berada dalam Mode Barang Hilang. Semua komunikasi penemu diarahkan melalui formulir relay terenkripsi.
                </p>
                <p>
                    <strong>3. Hak Kekayaan Intelektual:</strong> Ornamen budaya Toraja (Pa'barre Allo) dan Bugis (Sulapa Eppa') dilindungi hak cipta karya kebudayaan FlexiTag Makassar.
                </p>
            </div>

            <button type="button" class="btn-secondary-action" onclick="closeModal('termsModal')">
                Tutup
            </button>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- UNIFIED USER SIDEBAR / NAVIGATION DRAWER                                  --}}
    {{-- ========================================================================= --}}
    @include('partials.user-sidebar')

</div>

</body>
</html>
