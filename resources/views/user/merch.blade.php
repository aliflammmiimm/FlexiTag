<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Tag & Merch — FlexiTag</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/flexitag-user-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flexitag-user-merch.css') }}">
</head>
<body class="dashboard-body">

<div class="user-app-container">

    {{-- ========================================================================= --}}
    {{-- TOP APP BAR (KONSISTEN DENGAN HALAMAN HOME)                                --}}
    {{-- ========================================================================= --}}
    <header class="user-topbar">
        <button type="button" class="hamburger-btn" id="openDrawerBtn" aria-label="Buka Menu Navigasi" onclick="toggleDrawer(true)">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="topbar-branding">
            <span class="topbar-subtitle">FLEXITAG</span>
            <h1 class="topbar-title">Merch</h1>
        </div>
    </header>

    {{-- ========================================================================= --}}
    {{-- MAIN USER DASHBOARD CONTENT                                               --}}
    {{-- ========================================================================= --}}
    <main class="user-main-content">

        {{-- Flash Status Alerts --}}
        @if (session('status'))
            <div style="background: #EFFDF5; border: 1.5px solid #BBF7D0; color: #166534; padding: 0.85rem 1.1rem; border-radius: 14px; font-size: 0.84rem; display: flex; align-items: center; gap: 0.6rem; box-shadow: 0 4px 12px rgba(22, 101, 52, 0.08); margin-bottom: 0.85rem;">
                <span style="font-size: 1.1rem; line-height: 1;">✓</span>
                <span style="font-weight: 600;">{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div style="background: #FEF2F2; border: 1.5px solid #FECACA; color: #991B1B; padding: 0.85rem 1.1rem; border-radius: 14px; font-size: 0.84rem; display: flex; align-items: center; gap: 0.6rem; box-shadow: 0 4px 12px rgba(153, 27, 27, 0.08); margin-bottom: 0.85rem;">
                <span style="font-size: 1.1rem; line-height: 1;">⚠️</span>
                <span style="font-weight: 600;">{{ session('error') }}</span>
            </div>
        @endif

        {{-- 1. Hero Card: NFC Heritage Vault --}}
        <section class="merch-hero-card">
            {{-- Pa'barre Allo Sunburst Watermark --}}
            <svg class="merch-hero-watermark" viewBox="0 0 100 100" fill="none" stroke="#FFFFFF" stroke-width="2">
                <circle cx="50" cy="50" r="45" stroke-dasharray="4 2"/>
                <circle cx="50" cy="50" r="28"/>
                <circle cx="50" cy="50" r="14"/>
                <line x1="50" y1="5" x2="50" y2="95"/>
                <line x1="5" y1="50" x2="95" y2="50"/>
                <line x1="18" y1="18" x2="82" y2="82"/>
                <line x1="18" y1="82" x2="82" y2="18"/>
            </svg>

            <div class="merch-hero-top">
                <div class="hero-vault-badge">
                    <span class="badge-dot"></span>
                    <span>NFC HERITAGE VAULT</span>
                </div>

                <div class="hero-shield-icon" title="Dilindungi Enkripsi Zero-Knowledge">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        <polyline points="9 12 11 14 15 10"></polyline>
                    </svg>
                </div>
            </div>

            <h2 class="merch-hero-title">Manajemen Tag & Merch</h2>
            <p class="merch-hero-sub">{{ $totalTagCount }} Perangkat Terdaftar • Chip NTAG215 Enkripsi</p>
        </section>

        {{-- 2. Card: + Tambah Produk FlexiTag --}}
        <section class="merch-add-card">
            <div class="merch-add-header">
                <div class="merch-add-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="2"></circle>
                        <path d="M16.24 7.76a6 6 0 0 1 0 8.49m-8.48-.01a6 6 0 0 1 0-8.49m11.31-2.82a10 10 0 0 1 0 14.14m-14.14 0a10 10 0 0 1 0-14.14"></path>
                    </svg>
                </div>
                <div class="merch-add-text">
                    <h3 class="merch-add-title">+ Tambah Produk FlexiTag</h3>
                    <p class="merch-add-desc">Pindai QR fisik atau dekatkan chip NFC ke punggung gawai Anda.</p>
                </div>
            </div>

            <a href="{{ route('user.activation.show') }}" class="btn-scan-tag" id="btnScanTag">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="2"></circle>
                    <path d="M16.24 7.76a6 6 0 0 1 0 8.49m-8.48-.01a6 6 0 0 1 0-8.49"></path>
                </svg>
                <span>Pindai Tag Sekarang</span>
            </a>
        </section>

        {{-- 3. Section Header: Merchandise Terhubung --}}
        <div class="merch-section-header">
            <h3 class="merch-section-title">Merchandise Terhubung ({{ $totalTagCount }} Tag)</h3>
            <button type="button" class="btn-sync-merch" id="btnSyncMerch" onclick="syncMerch(this)">
                <span>Sinkronkan</span>
                <span class="sync-icon">🔄</span>
            </button>
        </div>

        {{-- 4. Tag List --}}
        @forelse ($tags as $tag)
            <article class="merch-tag-card" id="tag-card-{{ $tag['id'] }}">
                {{-- Top Row: Image, Info, Status Badge --}}
                <div class="merch-tag-head">
                    <div class="tag-thumb-wrap">
                        <img src="{{ $tag['image'] }}" alt="{{ $tag['title'] }}" class="tag-thumb-img">
                    </div>

                    <div class="tag-meta-col">
                        <div class="tag-meta-top">
                            <h4 class="tag-product-name">{{ $tag['title'] }}</h4>

                            @if ($tag['is_active'])
                                <span class="badge-status-aktif" id="status-badge-{{ $tag['id'] }}">
                                    <span class="dot-aktif"></span>
                                    <span>Aktif</span>
                                </span>
                            @elseif ($tag['disabled_by'] === 'admin')
                                <span class="badge-status-terkunci" style="background: #FEE2E2; color: #991B1B; border-color: #FECACA;" id="status-badge-{{ $tag['id'] }}">
                                    <span>⛔</span>
                                    <span>Diblokir Admin</span>
                                </span>
                            @else
                                <span class="badge-status-terkunci" id="status-badge-{{ $tag['id'] }}">
                                    <span>🔒</span>
                                    <span>Terkunci</span>
                                </span>
                            @endif
                        </div>

                        <p class="tag-id-mode">ID: {{ $tag['tag_code'] }} • Mode: {{ $tag['mode_short'] }}</p>
                        <p class="tag-last-active" id="last-activity-{{ $tag['id'] }}">{{ $tag['activity_text'] }}</p>
                    </div>
                </div>

                {{-- Kill Switch Box --}}
                @if ($tag['is_active'])
                    <div class="kill-switch-box" id="kill-box-{{ $tag['id'] }}">
                        <div class="kill-switch-info">
                            <h5 class="kill-switch-title">Kunci Tag Sementara (Kill Switch)</h5>
                            <p class="kill-switch-desc">Nonaktifkan pembacaan data publik secara instan tanpa menghapus konfigurasi profil.</p>
                        </div>

                        <form method="POST" action="{{ route('user.merch.toggleTagStatus', $tag['id']) }}" id="toggle-form-{{ $tag['id'] }}">
                            @csrf
                            <label class="switch-label" title="Kunci Tag Sementara">
                                <input type="checkbox" onchange="handleKillSwitchToggle(this, {{ $tag['id'] }})">
                                <span class="switch-slider"></span>
                            </label>
                        </form>
                    </div>

                    {{-- Action Buttons Row: Active State --}}
                    <div class="tag-action-row" id="action-row-{{ $tag['id'] }}">
                        @if ($activeMode === 'umkm')
                            <a href="{{ route('user.profile.umkm') }}" class="btn-tag-action-gray">Edit Profil Tag</a>
                        @else
                            <a href="{{ route('user.dashboard') }}" class="btn-tag-action-gray">Edit Profil Tag</a>
                        @endif

                        <button type="button" class="btn-tag-action-gray" onclick="openPrivacyModal('{{ $tag['tag_code'] }}')">
                            Privasi
                        </button>
                    </div>
                @elseif ($tag['disabled_by'] === 'admin')
                    {{-- Kill Switch Box: Admin Blocked State (BR-04) --}}
                    <div class="kill-switch-box-locked" id="kill-box-{{ $tag['id'] }}" style="background: #FEF2F2; border-color: #FECACA;">
                        <div class="locked-alert-text" style="color: #991B1B;">
                            <span class="locked-alert-icon">⛔</span>
                            <span>Tag dinonaktifkan oleh Administrator FlexiTag. Data profil publik tidak dapat diakses.</span>
                        </div>

                        <div class="locked-toggle-row">
                            <span class="locked-toggle-label" style="color: #6B7280;">Kunci Tag Sementara</span>
                            <span style="font-size: 0.74rem; color: #DC2626; font-weight: 600;">(Ditangguhkan Admin)</span>
                        </div>
                    </div>

                    {{-- Action Buttons Row: Admin Blocked State --}}
                    <div class="tag-action-row" id="action-row-{{ $tag['id'] }}">
                        <a href="https://wa.me/6281234567890?text=Halo%20Admin%20FlexiTag,%20saya%20ingin%20menanyakan%20status%20tag%20{{ $tag['tag_code'] }}" target="_blank" class="btn-tag-action-maroon" style="background: #991B1B;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <span>Hubungi Admin</span>
                        </a>

                        <button type="button" class="btn-tag-action-gray" onclick="openTapHistoryModal('{{ $tag['tag_code'] }}', {{ $tag['total_taps'] }})">
                            Riwayat Tap
                        </button>
                    </div>
                @else
                    {{-- Kill Switch Box: Locked State by Owner --}}
                    <div class="kill-switch-box-locked" id="kill-box-{{ $tag['id'] }}">
                        <div class="locked-alert-text">
                            <span class="locked-alert-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </span>
                            <span>Tag terkunci oleh proteksi Kill Switch. Tidak dapat dibaca oleh gawai publik.</span>
                        </div>

                        <div class="locked-toggle-row">
                            <span class="locked-toggle-label">Kunci Tag Sementara</span>

                            <form method="POST" action="{{ route('user.merch.toggleTagStatus', $tag['id']) }}" id="toggle-form-{{ $tag['id'] }}">
                                @csrf
                                <label class="switch-label" title="Buka Kunci Tag">
                                    <input type="checkbox" checked onchange="handleKillSwitchToggle(this, {{ $tag['id'] }})">
                                    <span class="switch-slider"></span>
                                </label>
                            </form>
                        </div>
                    </div>

                    {{-- Action Buttons Row: Locked State by Owner --}}
                    <div class="tag-action-row" id="action-row-{{ $tag['id'] }}">
                        <form method="POST" action="{{ route('user.merch.toggleTagStatus', $tag['id']) }}" style="display: contents;">
                            @csrf
                            <button type="submit" class="btn-tag-action-maroon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                                </svg>
                                <span>Buka Kunci Tag</span>
                            </button>
                        </form>

                        <button type="button" class="btn-tag-action-gray" onclick="openTapHistoryModal('{{ $tag['tag_code'] }}', {{ $tag['total_taps'] }})">
                            Riwayat Tap
                        </button>
                    </div>
                @endif
            </article>
        @empty
            <div style="background: #FFFFFF; border: 1px dashed #D1D5DB; border-radius: 16px; padding: 2rem 1.5rem; text-align: center; color: #6B7280; margin-bottom: 1.5rem;">
                <p style="font-weight: 600; color: #374151; margin-bottom: 0.35rem;">Belum ada perangkat FlexiTag yang terhubung</p>
                <p style="font-size: 0.82rem; margin-bottom: 1.25rem;">Gunakan tombol "Pindai Tag Sekarang" di atas untuk menambahkan merchandise FlexiTag Anda.</p>
                <a href="{{ route('user.activation.show') }}" class="btn-scan-tag" style="max-width: 260px; margin: 0 auto;">
                    + Hubungkan Tag
                </a>
            </div>
        @endforelse

        {{-- 5. Security Protocol Card --}}
        <section class="merch-protocol-card">
            <div class="protocol-icon-badge">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <polyline points="9 12 11 14 15 10"></polyline>
                </svg>
            </div>
            <div class="protocol-text-col">
                <h4 class="protocol-heading">PROTOKOL KEAMANAN</h4>
                <p class="protocol-desc">Dilindungi Enkripsi Zero-Knowledge FlexiTag & Hardware Kill Switch • Makassar Heritage Edition.</p>
            </div>
        </section>

        {{-- 6. Footer Tagline --}}
        <footer class="merch-footer-tagline">
            FlexiTag — Satu Tap, Seribu Identitas • © 2026 Makassar
        </footer>

    </main>

    {{-- ========================================================================= --}}
    {{-- SLIDE-OUT NAVIGATION DRAWER (KONSISTEN DENGAN HALAMAN HOME)                --}}
    {{-- ========================================================================= --}}
    <div class="drawer-overlay" id="drawerOverlay" onclick="toggleDrawer(false)"></div>

    <aside class="side-drawer" id="sideDrawer" aria-label="Menu Navigasi Pengguna">
        <div class="drawer-header">
            <div class="drawer-brand-row">
                <div class="drawer-logo-badge">FT</div>
                <div class="drawer-brand-text">
                    <span class="drawer-brand-name">FlexiTag</span>
                    <span class="drawer-brand-tagline">MENU NAVIGASI</span>
                </div>
            </div>

            <button type="button" class="drawer-close-btn" onclick="toggleDrawer(false)" aria-label="Tutup Menu">
                ✕
            </button>
        </div>

        <div class="drawer-divider"></div>

        {{-- Navigation Menu List --}}
        <nav class="drawer-nav-list">
            {{-- Home --}}
            <a href="{{ route('user.dashboard') }}" class="drawer-nav-item {{ request()->routeIs('user.dashboard') ? 'is-highlighted' : '' }}">
                <span class="drawer-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </span>
                <span>Home</span>
            </a>

            {{-- Merch (Active on this page) --}}
            <a href="{{ route('user.merch') }}" class="drawer-nav-item {{ request()->routeIs('user.merch') ? 'is-highlighted' : '' }}">
                <span class="drawer-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l1 12h16l1-12z"></path>
                        <path d="M3 9h18"></path>
                        <path d="M16 5a4 4 0 0 0-8 0"></path>
                    </svg>
                </span>
                <span>Merch</span>
            </a>

            {{-- Analytics --}}
            <a href="javascript:void(0)" onclick="openAnalyticsModal(); toggleDrawer(false)" class="drawer-nav-item">
                <span class="drawer-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="8" y1="17" x2="8" y2="13"></line>
                        <line x1="12" y1="17" x2="12" y2="9"></line>
                        <line x1="16" y1="17" x2="16" y2="11"></line>
                    </svg>
                </span>
                <span>Analytics</span>
            </a>

            {{-- Settings --}}
            <a href="javascript:void(0)" onclick="openProfileModal(); toggleDrawer(false)" class="drawer-nav-item">
                <span class="drawer-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                </span>
                <span>Settings</span>
            </a>
        </nav>

        {{-- Drawer Bottom Profile & Logout --}}
        <div class="drawer-footer">
            <div class="drawer-profile-box">
                <div class="drawer-avatar-wrap">
                    @if ($profile?->photo_path)
                        <img src="{{ asset('storage/' . $profile->photo_path) }}" alt="{{ $displayName }}">
                    @else
                        <span>{{ $initials }}</span>
                    @endif
                    <span class="avatar-online-dot"></span>
                </div>

                <div class="drawer-profile-info">
                    <span class="drawer-user-name">{{ $displayName }}</span>
                    <span class="drawer-user-job">{{ $headline }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" id="drawerLogoutForm">
                @csrf
                <button type="submit" class="drawer-logout-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ========================================================================= --}}
    {{-- MODALS: PRIVACY, RIWAYAT TAP, ANALYTICS, SETTINGS                         --}}
    {{-- ========================================================================= --}}

    {{-- 1. Modal Pengaturan Privasi Tag --}}
    <div class="user-modal-overlay" id="privacyModal" onclick="handleBackdropClick(event, 'privacyModal')">
        <div class="user-modal-dialog">
            <div class="modal-header-row">
                <h3 class="modal-title">Pengaturan Privasi Tag</h3>
                <button type="button" class="modal-close-icon" onclick="closeModal('privacyModal')">✕</button>
            </div>

            <p style="font-size: 0.8rem; color: #6B7280; margin-bottom: 1.25rem;">
                Kelola informasi yang dapat dilihat pengunjung saat melakukan tap pada tag <strong id="privacyTagCode">FLX-XXXX</strong>.
            </p>

            <form method="POST" action="{{ route('user.dashboard.updatePrivacy') }}" id="privacySettingsForm">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem; margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; justify-content: space-between; font-size: 0.88rem; color: #374151; cursor: pointer;">
                        <span>Tampilkan Nomor WhatsApp</span>
                        <input type="checkbox" name="show_phone" value="1" checked style="width: 18px; height: 18px; accent-color: #44070B;">
                    </label>

                    <label style="display: flex; align-items: center; justify-content: space-between; font-size: 0.88rem; color: #374151; cursor: pointer;">
                        <span>Tampilkan Alamat Email</span>
                        <input type="checkbox" name="show_email" value="1" checked style="width: 18px; height: 18px; accent-color: #44070B;">
                    </label>

                    <label style="display: flex; align-items: center; justify-content: space-between; font-size: 0.88rem; color: #374151; cursor: pointer;">
                        <span>Tampilkan Lokasi Kota</span>
                        <input type="checkbox" name="show_location" value="1" style="width: 18px; height: 18px; accent-color: #44070B;">
                    </label>

                    <label style="display: flex; align-items: center; justify-content: space-between; font-size: 0.88rem; color: #374151; cursor: pointer;">
                        <span>Izinkan Pengindeksan Publik</span>
                        <input type="checkbox" name="indexable" value="1" checked style="width: 18px; height: 18px; accent-color: #44070B;">
                    </label>
                </div>

                <button type="submit" class="btn-scan-tag" style="background: #44070B; color: #FFFFFF; box-shadow: none;">
                    Simpan Privasi
                </button>
            </form>
        </div>
    </div>

    {{-- 2. Modal Riwayat Tap --}}
    <div class="user-modal-overlay" id="tapHistoryModal" onclick="handleBackdropClick(event, 'tapHistoryModal')">
        <div class="user-modal-dialog">
            <div class="modal-header-row">
                <h3 class="modal-title">Riwayat Interaksi Tap</h3>
                <button type="button" class="modal-close-icon" onclick="closeModal('tapHistoryModal')">✕</button>
            </div>

            <p style="font-size: 0.82rem; color: #6B7280; margin-bottom: 1rem;">
                Aktivitas pemindaian chip fisik <strong id="historyTagCode">FLX-XXXX</strong> oleh gawai publik.
            </p>

            <div id="tapHistoryList" style="display: flex; flex-direction: column; gap: 0.65rem; margin-bottom: 1.25rem;">
                <div style="background: #F9F8F6; border: 1px solid #ECE7DE; border-radius: 12px; padding: 0.75rem 0.95rem; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-weight: 600; font-size: 0.84rem; color: #1F2937;">Tap NFC Fisik</div>
                        <div style="font-size: 0.74rem; color: #6B7280;">Panakkukang, Makassar • Android 14</div>
                    </div>
                    <span style="font-size: 0.72rem; color: #858079; font-weight: 500;">15 mnt lalu</span>
                </div>

                <div style="background: #F9F8F6; border: 1px solid #ECE7DE; border-radius: 12px; padding: 0.75rem 0.95rem; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-weight: 600; font-size: 0.84rem; color: #1F2937;">Tap NFC Fisik</div>
                        <div style="font-size: 0.74rem; color: #6B7280;">Tamalanrea, Makassar • iOS 17</div>
                    </div>
                    <span style="font-size: 0.72rem; color: #858079; font-weight: 500;">Kemarin</span>
                </div>
            </div>

            <button type="button" class="btn-tag-action-gray" style="width: 100%;" onclick="closeModal('tapHistoryModal')">
                Tutup
            </button>
        </div>
    </div>

    {{-- 3. Modal Analytics & Settings (Drawer Modals) --}}
    <div class="user-modal-overlay" id="analyticsModal" onclick="handleBackdropClick(event, 'analyticsModal')">
        <div class="user-modal-dialog">
            <div class="modal-header-row">
                <h3 class="modal-title">Statistik & Analytics</h3>
                <button type="button" class="modal-close-icon" onclick="closeModal('analyticsModal')">✕</button>
            </div>
            <p style="font-size: 0.85rem; color: #4B5563; margin-bottom: 1.25rem;">
                Ringkasan interaksi dan telemetri perangkat FlexiTag Anda:
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem;">
                <div style="background: #F9F8F6; border: 1px solid #ECE7DE; border-radius: 14px; padding: 1rem; text-align: center;">
                    <div style="font-size: 0.72rem; color: #6B7280; font-weight: 600;">TOTAL TAP</div>
                    <div style="font-size: 1.4rem; font-weight: 700; color: #44070B;">184</div>
                </div>
                <div style="background: #F9F8F6; border: 1px solid #ECE7DE; border-radius: 14px; padding: 1rem; text-align: center;">
                    <div style="font-size: 0.72rem; color: #6B7280; font-weight: 600;">PERANGKAT AKTIF</div>
                    <div style="font-size: 1.4rem; font-weight: 700; color: #166534;">{{ $activeTagCount }} / {{ $totalTagCount }}</div>
                </div>
            </div>
            <button type="button" class="btn-tag-action-gray" style="width: 100%;" onclick="closeModal('analyticsModal')">Tutup</button>
        </div>
    </div>

    <div class="user-modal-overlay" id="profileModal" onclick="handleBackdropClick(event, 'profileModal')">
        <div class="user-modal-dialog">
            <div class="modal-header-row">
                <h3 class="modal-title">Pengaturan Akun</h3>
                <button type="button" class="modal-close-icon" onclick="closeModal('profileModal')">✕</button>
            </div>
            <p style="font-size: 0.84rem; color: #6B7280; margin-bottom: 1.25rem;">
                Pengguna: <strong>{{ $displayName }}</strong> ({{ $user->email }})
            </p>
            <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                <a href="{{ route('user.dashboard') }}" class="btn-scan-tag" style="background: #44070B; color: #FFFFFF; box-shadow: none;">
                    Buka Dashboard Profil
                </a>
                <button type="button" class="btn-tag-action-gray" style="width: 100%;" onclick="closeModal('profileModal')">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- Toast Floating Notification --}}
    <div id="merchToast" style="position: fixed; bottom: 25px; left: 50%; transform: translateX(-50%) translateY(100px); background: #1F2937; color: #FFFFFF; padding: 0.75rem 1.25rem; border-radius: 30px; font-size: 0.82rem; font-weight: 500; display: flex; align-items: center; gap: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.25); z-index: 3000; opacity: 0; transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease; pointer-events: none;">
        <span id="toastIcon">✓</span>
        <span id="toastMessage">Pesan notifikasi</span>
    </div>

</div>

{{-- ========================================================================= --}}
{{-- JAVASCRIPT LOGIC                                                          --}}
{{-- ========================================================================= --}}
<script>
    // 1. Navigation Drawer Toggle
    function toggleDrawer(open) {
        const drawer = document.getElementById('sideDrawer');
        const overlay = document.getElementById('drawerOverlay');
        if (open) {
            drawer.classList.add('is-open');
            overlay.classList.add('is-visible');
            document.body.style.overflow = 'hidden';
        } else {
            drawer.classList.remove('is-open');
            overlay.classList.remove('is-visible');
            document.body.style.overflow = '';
        }
    }

    // 2. Modals Control
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('is-active');
            document.body.style.overflow = '';
        }
    }

    function handleBackdropClick(e, modalId) {
        if (e.target && e.target.id === modalId) {
            closeModal(modalId);
        }
    }

    function openAnalyticsModal() {
        openModal('analyticsModal');
    }

    function openProfileModal() {
        openModal('profileModal');
    }

    function openPrivacyModal(tagCode) {
        const tagCodeEl = document.getElementById('privacyTagCode');
        if (tagCodeEl) tagCodeEl.textContent = tagCode;
        openModal('privacyModal');
    }

    function openTapHistoryModal(tagCode, totalTaps) {
        const tagCodeEl = document.getElementById('historyTagCode');
        if (tagCodeEl) tagCodeEl.textContent = tagCode;
        openModal('tapHistoryModal');
    }

    // 3. Floating Toast
    function showToast(message, icon = '✓') {
        const toast = document.getElementById('merchToast');
        const toastMsg = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');
        if (!toast) return;

        toastMsg.textContent = message;
        toastIcon.textContent = icon;
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(-50%) translateY(0)';

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(100px)';
        }, 3200);
    }

    // 4. Sync Merch Button
    function syncMerch(btn) {
        if (!btn) return;
        btn.classList.add('is-syncing');

        setTimeout(() => {
            btn.classList.remove('is-syncing');
            showToast('Semua merchandise FlexiTag berhasil disinkronkan.', '🔄');
        }, 650);
    }

    // 5. Interactive Kill Switch Toggle (AJAX + Form Fallback)
    function handleKillSwitchToggle(checkbox, tagId) {
        const form = document.getElementById('toggle-form-' + tagId);
        if (!form) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Gunakan AJAX fetch untuk pergantian instan tanpa reload, dengan fallback submit biasa
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal memperbarui status');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message, data.is_locked ? '🔒' : '✓');
                // Reload halaman agar seluruh container card render dengan state terbaru (locked alert & switch)
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                throw new Error(data.message || 'Gagal mengubah status');
            }
        })
        .catch(err => {
            console.warn('Fallback ke form submit biasa:', err);
            form.submit();
        });
    }
</script>

</body>
</html>
