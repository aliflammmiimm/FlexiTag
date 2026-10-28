<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4A0A0E">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <title>Home — FlexiTag Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/flexitag-user-dashboard.css') }}">
    @vite(['resources/css/components/user-sidebar.css', 'resources/js/user-sidebar.js'])
</head>
<body class="dashboard-body">

<div class="user-app-container">

    {{-- ========================================================================= --}}
    {{-- TOP APP BAR                                                               --}}
    {{-- ========================================================================= --}}
    <header class="user-topbar">
        <div class="topbar-left-group">
            <button type="button" class="hamburger-btn" id="openDrawerBtn" aria-label="Buka Menu Navigasi" onclick="toggleDrawer(true)">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="topbar-branding">
                <span class="topbar-subtitle">FLEXITAG</span>
                <h1 class="topbar-title">Home</h1>
            </div>
        </div>

        <button type="button" class="topbar-inbox-btn" onclick="openLostReportsModal()" aria-label="Laporan Penemuan Barang" title="Laporan Penemuan Barang">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                <span class="topbar-inbox-badge" id="topbarInboxBadge">{{ $unreadMessagesCount }}</span>
            @endif
        </button>
    </header>

    {{-- ========================================================================= --}}
    {{-- MAIN USER DASHBOARD CONTENT                                               --}}
    {{-- ========================================================================= --}}
    <main class="user-main-content">

        {{-- Flash Status Alert --}}
        @if (session('status'))
            <div style="background: #EFFDF5; border: 1.5px solid #BBF7D0; color: #166534; padding: 0.85rem 1.1rem; border-radius: 14px; font-size: 0.84rem; display: flex; align-items: center; gap: 0.6rem; box-shadow: 0 4px 12px rgba(22, 101, 52, 0.08); margin-bottom: 0.35rem;">
                <span style="font-size: 1.1rem; line-height: 1;">✓</span>
                <span style="font-weight: 600;">{{ session('status') }}</span>
            </div>
        @endif

        @if ($primaryTag && $primaryTag->status === 'nonaktif' && $primaryTag->disabled_by === 'admin')
            <div style="background: #FEF2F2; border: 1.5px solid #FECACA; color: #991B1B; padding: 0.85rem 1.1rem; border-radius: 14px; font-size: 0.82rem; display: flex; align-items: center; gap: 0.6rem; box-shadow: 0 4px 12px rgba(153, 27, 27, 0.08); margin-bottom: 0.65rem;">
                <span style="font-size: 1.1rem; line-height: 1;">⛔</span>
                <span><strong>Pemberitahuan:</strong> Perangkat FlexiTag Anda ({{ $primaryTag->tag_code }}) sedang dinonaktifkan oleh Administrator. Hubungi pengelola untuk pemulihan.</span>
            </div>
        @endif

        {{-- 1. Halo Greeting Card --}}
        <section class="greeting-card">
            {{-- Pa'barre Allo Sunburst Watermark --}}
            <svg class="greeting-watermark" viewBox="0 0 100 100" fill="none" stroke="#FFFFFF" stroke-width="2">
                <circle cx="50" cy="50" r="45" stroke-dasharray="4 2"/>
                <circle cx="50" cy="50" r="28"/>
                <circle cx="50" cy="50" r="14"/>
                <line x1="50" y1="5" x2="50" y2="95"/>
                <line x1="5" y1="50" x2="95" y2="50"/>
                <line x1="18" y1="18" x2="82" y2="82"/>
                <line x1="18" y1="82" x2="82" y2="18"/>
            </svg>

            <h2>Halo, {{ $displayName }}</h2>
            <p>Kelola mode aktif, tautan tap, dan perangkat pintar FlexiTag Anda.</p>
        </section>

        {{-- 2. Keychain Pintar Hardware Card --}}
        <section class="hardware-card">
            <div class="hardware-left">
                <div class="hardware-thumb">
                    <img src="{{ asset('images/keychain.jpg') }}" alt="Keychain Pintar FlexiTag">
                </div>
                <div class="hardware-info">
                    <h3 class="hardware-title">Keychain Pintar</h3>
                    <span class="hardware-sub">NTAG215 • Edisi Pa'barre Allo</span>
                    <span class="hardware-pill-member">
                        <span>🎖️</span>
                        <span>Member MaccaTech</span>
                    </span>
                </div>
            </div>

            <div class="hardware-right">
                @if ($primaryTag?->status === 'aktif')
                    <span class="status-pill-active">Aktif</span>
                @elseif ($primaryTag?->disabled_by === 'admin')
                    <span style="background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; padding: 3px 9px; border-radius: 20px; font-size: 0.72rem; font-weight: 700;">Diblokir Admin</span>
                @else
                    <span style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; padding: 3px 9px; border-radius: 20px; font-size: 0.72rem; font-weight: 700;">Terkunci</span>
                @endif
                <div class="hardware-actions" style="display: flex; align-items: center; gap: 0.45rem;">
                    <button type="button" class="btn-external-icon" onclick="copyPublicUrl('{{ $publicUrl }}')" title="Salin Tautan Profil Publik" aria-label="Salin Tautan Profil">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                    </button>
                    <button type="button" class="btn-external-icon" onclick="openQrModal('{{ $publicUrl }}', '{{ $primaryTag?->tag_code }}')" title="Tampilkan QR Code Profil" aria-label="Tampilkan QR Code">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        {{-- 3. Telemetry Metrics Grid (2 Columns) --}}
        <section class="metrics-grid">
            {{-- Metric: Total Tap --}}
            <div class="metric-card">
                <div class="metric-head">
                    <span class="metric-label">TOTAL TAP</span>
                    <div class="metric-icon-badge">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                            <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                            <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                            <line x1="12" y1="20" x2="12.01" y2="20"></line>
                        </svg>
                    </div>
                </div>
                <div class="metric-value-row">
                    <span class="metric-big-num" id="totalTapsNum">{{ $totalTaps }}</span>
                    <span class="metric-unit">kali</span>
                </div>
                <div class="metric-pill">
                    <span>📈</span>
                    <span>+{{ $weeklyTaps }} tap minggu ini</span>
                </div>
            </div>

            {{-- Metric: Aktivitas Terakhir --}}
            <div class="metric-card">
                <div class="metric-head">
                    <span class="metric-label">AKTIVITAS TERAKHIR</span>
                    <div class="metric-icon-badge" style="background: #FEE2E2; color: #991B1B;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="metric-value-row">
                    @php
                        $parts = explode(' ', $lastActivity, 2);
                        $actNum = $parts[0] ?? '2 Jam';
                        $actUnit = $parts[1] ?? 'lalu';
                    @endphp
                    <span class="metric-big-num">{{ $actNum }}</span>
                    <span class="metric-unit">{{ $actUnit }}</span>
                </div>
                <div class="metric-pill">
                    <span>🛡️</span>
                    <span>Sulapa Eppa'</span>
                </div>
            </div>
        </section>

        {{-- Emergency Finder Messages Banner (FR-LST-03) --}}
        @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
            <section class="lost-inbox-alert-banner" id="bannerLostAlert" style="margin-bottom: 1.25rem; background: linear-gradient(135deg, #FEF2F2 0%, #FFFBEB 100%); border: 1.5px solid #FCA5A5; border-radius: 16px; padding: 0.95rem 1.15rem; display: flex; align-items: center; justify-content: space-between; gap: 0.85rem; box-shadow: 0 4px 14px rgba(220, 38, 38, 0.08); flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1 1 220px; min-width: 0;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: #DC2626; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 3px 10px rgba(220, 38, 38, 0.25);">
                        📬
                    </div>
                    <div style="min-width: 0;">
                        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.45rem; margin-bottom: 0.15rem;">
                            <span style="font-size: 0.88rem; font-weight: 700; color: #991B1B;">Pemberitahuan Barang Ditemukan!</span>
                            <span class="badge-unread-pill" style="font-size: 0.66rem;">
                                <span class="unread-dot">●</span> {{ $unreadMessagesCount }} Baru
                            </span>
                        </div>
                        <div style="font-size: 0.74rem; color: #7F1D1D; line-height: 1.35;">
                            Penemu telah mengirimkan pesan &amp; informasi kontak untuk tag Anda.
                        </div>
                    </div>
                </div>
                <button type="button" onclick="openLostReportsModal()" style="background: #991B1B; color: #FFFFFF; font-size: 0.76rem; font-weight: 700; padding: 0.6rem 0.95rem; border: none; border-radius: 10px; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.35rem; transition: background 0.2s; box-shadow: 0 2px 6px rgba(153, 27, 27, 0.25); flex-shrink: 0;">
                    <span>Buka Laporan</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </section>
        @endif

        {{-- 4. Pilih Mode Aktif (Interactive Mode Cards) --}}
        <section class="mode-section">
            <div class="mode-section-head">
                <div class="mode-section-title-wrap">
                    <div class="mode-grid-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <h2 class="mode-section-title">Pilih Mode Aktif</h2>
                </div>
                <div class="mode-diamonds">✦ ✦</div>
            </div>
            <p class="mode-section-sub">
                Ubah profil publik seketika saat gantungan kunci FlexiTag disentuh.
            </p>

            <div class="mode-list">
                {{-- Mode 1: Kampus --}}
                <div class="mode-item-card {{ $activeMode === 'kampus' ? 'is-active' : '' }}"
                     id="modeCard-kampus"
                     onclick="switchUserMode('kampus', 'Mode Kampus')">
                    <div class="mode-item-left">
                        <div class="mode-icon-box">🎓</div>
                        <div class="mode-item-text">
                            <span class="mode-item-name">Mode Kampus</span>
                            <span class="mode-item-desc">Sosmed, kontak, &amp; jadwal perkuliahan</span>
                        </div>
                    </div>
                    <div class="mode-item-right" id="modeState-kampus">
                        @if ($activeMode === 'kampus')
                            <span class="mode-active-pill">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Aktif</span>
                            </span>
                            <a href="{{ $publicUrl }}" target="_blank" class="mode-active-arrow" title="Buka Profil Digital Publik" onclick="event.stopPropagation()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        @else
                            <span class="mode-radio-circle"></span>
                        @endif
                    </div>
                </div>

                {{-- Mode 2: Kerja --}}
                <div class="mode-item-card {{ $activeMode === 'kerja' ? 'is-active' : '' }}"
                     id="modeCard-kerja"
                     onclick="switchUserMode('kerja', 'Mode Kerja')">
                    <div class="mode-item-left">
                        <div class="mode-icon-box">💼</div>
                        <div class="mode-item-text">
                            <span class="mode-item-name">Mode Kerja</span>
                            <span class="mode-item-desc">Portofolio digital, CV, dan kartu nama</span>
                        </div>
                    </div>
                    <div class="mode-item-right" id="modeState-kerja">
                        @if ($activeMode === 'kerja')
                            <span class="mode-active-pill">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Aktif</span>
                            </span>
                            <a href="{{ $publicUrl }}" target="_blank" class="mode-active-arrow" title="Buka Profil Digital Publik" onclick="event.stopPropagation()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        @else
                            <span class="mode-radio-circle"></span>
                        @endif
                    </div>
                </div>

                {{-- Mode 3: UMKM --}}
                <div class="mode-item-card {{ $activeMode === 'umkm' ? 'is-active' : '' }}"
                     id="modeCard-umkm"
                     onclick="switchUserMode('umkm', 'Mode UMKM')">
                    <div class="mode-item-left">
                        <div class="mode-icon-box">🏪</div>
                        <div class="mode-item-text">
                            <span class="mode-item-name">Mode UMKM</span>
                            <span class="mode-item-desc">Katalog produk, promo, &amp; QRIS</span>
                        </div>
                    </div>
                    <div class="mode-item-right" id="modeState-umkm">
                        @if ($activeMode === 'umkm')
                            <span class="mode-active-pill">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Aktif</span>
                            </span>
                            <a href="{{ $publicUrl }}" target="_blank" class="mode-active-arrow" title="Buka Profil Digital Publik" onclick="event.stopPropagation()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        @else
                            <span class="mode-radio-circle"></span>
                        @endif
                    </div>
                </div>

                {{-- Mode 4: Barang Hilang --}}
                <div class="mode-item-card {{ $activeMode === 'hilang' ? 'is-active' : '' }}"
                     id="modeCard-hilang"
                     onclick="switchUserMode('hilang', 'Mode Barang Hilang')">
                    <div class="mode-item-left">
                        <div class="mode-icon-box">🛡️</div>
                        <div class="mode-item-text">
                            <span class="mode-item-name">Mode Barang Hilang</span>
                            <span class="mode-item-desc">
                                Relay pesan aman tanpa kontak pribadi • 
                                <a href="{{ route('user.lost-mode.edit') }}" onclick="event.stopPropagation();" class="mode-inline-link">Atur Pesan Darurat</a>
                                @if(isset($lostMessages) && $lostMessages->isNotEmpty())
                                    • <button type="button" onclick="event.stopPropagation(); openLostReportsModal();" class="mode-inline-link" style="background:none; border:none; padding:0; cursor:pointer; color: {{ $unreadMessagesCount > 0 ? '#DC2626' : '#8C6D23' }}; font-weight: 700;">
                                        📬 {{ $unreadMessagesCount > 0 ? $unreadMessagesCount . ' Pesan Baru' : $lostMessages->count() . ' Laporan Masuk' }}
                                    </button>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="mode-item-right" id="modeState-hilang">
                        @if ($activeMode === 'hilang')
                            <span class="mode-active-pill">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Aktif</span>
                            </span>
                            <a href="{{ $publicUrl }}" target="_blank" class="mode-active-arrow" title="Buka Profil Digital Publik" onclick="event.stopPropagation()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                        @else
                            <span class="mode-radio-circle"></span>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- 5. Cloud MaccaTech Encryption & Device Info Card --}}
        <section class="device-card">
            <div class="device-card-head">
                <div class="device-id-wrap">
                    <div class="device-nfc-circle">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                            <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                            <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                            <circle cx="12" cy="20" r="1"></circle>
                        </svg>
                    </div>
                    <div class="device-id-text">
                        <span class="device-id-code">ID: {{ $tagCode }}</span>
                        <span class="device-id-sub">Terenkripsi Cloud MaccaTech</span>
                    </div>
                </div>

                <span class="device-badge-ready">NFC READY</span>
            </div>

            <p class="device-card-desc">
                Perangkat terenkripsi cloud MaccaTech. Profil publik langsung berubah otomatis saat gantungan kunci FlexiTag disentuh smartphone.
            </p>

            <a href="{{ $publicUrl }}" target="_blank" class="btn-public-view">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <span>Lihat Tampilan Publik (NFC Tap View)</span>
            </a>
        </section>

        {{-- 6. Bottom Action Buttons --}}
        <section class="bottom-actions">
            {{-- Edit Profile Button --}}
            <button type="button" class="btn-gold-action" onclick="handleEditProfileClick()" id="btnEditActiveProfile">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
                <span>Edit Isi Profil Aktif</span>
            </button>

            {{-- Privacy Settings Button --}}
            <button type="button" class="btn-maroon-action" onclick="openPrivacyModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <span>Pengaturan Privasi &amp; Akses</span>
            </button>
        </section>

    </main>

    {{-- ========================================================================= --}}
    {{-- UNIFIED USER SIDEBAR / NAVIGATION DRAWER                                  --}}
    {{-- ========================================================================= --}}
    @include('partials.user-sidebar')

</div>

{{-- ========================================================================= --}}
{{-- MODALS                                                                    --}}
{{-- ========================================================================= --}}

{{-- 1. Edit Profile Modal --}}
<div class="user-modal-overlay" id="editProfileModal" onclick="if(event.target === this) closeProfileModal()">
    <div class="user-modal-dialog">
        <div class="modal-header-row">
            <h3 class="modal-title">✏️ Edit Profil Aktif</h3>
            <button type="button" class="modal-close-icon" onclick="closeProfileModal()">✕</button>
        </div>

        <form method="POST" action="{{ route('user.dashboard.updateProfile') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-form-group">
                <label for="display_name">Nama Tampilan Publik</label>
                <input type="text" id="display_name" name="display_name" class="modal-input" value="{{ old('display_name', $displayName) }}" required>
            </div>

            <div class="modal-form-group">
                <label for="headline">Pekerjaan / Subtitle</label>
                <input type="text" id="headline" name="headline" class="modal-input" placeholder="contoh: Pegawai Kopdes / Toraja" value="{{ old('headline', $profile?->headline) }}">
            </div>

            <div class="modal-form-group">
                <label for="no_whatsapp">Nomor WhatsApp Utama</label>
                <input type="text" id="no_whatsapp" name="no_whatsapp" class="modal-input" placeholder="0821-xxxx-xxxx" value="{{ old('no_whatsapp', $user->no_whatsapp) }}">
            </div>

            <div class="modal-form-group">
                <label for="photo">Ganti Foto Profil (Opsional)</label>
                <input type="file" id="photo" name="photo" class="modal-input" accept="image/*">
            </div>

            <button type="submit" class="btn-gold-action" style="width: 100%; margin-top: 1rem;">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

{{-- 2. Privacy & Security Modal --}}
<div class="user-modal-overlay" id="privacyModal" onclick="if(event.target === this) closePrivacyModal()">
    <div class="user-modal-dialog">
        <div class="modal-header-row">
            <h3 class="modal-title">🔒 Pengaturan Privasi &amp; Akses</h3>
            <button type="button" class="modal-close-icon" onclick="closePrivacyModal()">✕</button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="background: #FAF8F5; border: 1px solid #ECE7DF; border-radius: 14px; padding: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <strong style="font-size: 0.88rem; color: #1F1915;">Relay Kontak Privasi</strong>
                    <span style="color: #22C55E; font-weight: 800; font-size: 0.75rem;">AKTIF</span>
                </div>
                <p style="font-size: 0.74rem; color: #78716C; line-height: 1.4;">
                    Pesan dari penemu akan dialihkan melalui enkripsi cloud MaccaTech tanpa mengungkap nomor pribadi Anda.
                </p>
            </div>

            <div style="background: #FAF8F5; border: 1px solid #ECE7DF; border-radius: 14px; padding: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <strong style="font-size: 0.88rem; color: #1F1915;">Enkripsi Cloud Chip</strong>
                    <span style="color: #E5B32A; font-weight: 800; font-size: 0.75rem;">AES-256</span>
                </div>
                <p style="font-size: 0.74rem; color: #78716C; line-height: 1.4;">
                    Data autentikasi gantungan kunci diverifikasi melalui tanda tangan digital anti-kloning MaccaTech.
                </p>
            </div>

            <button type="button" class="btn-maroon-action" onclick="closePrivacyModal(); showToast('Pengaturan privasi aman!')" style="width: 100%; margin-top: 0.5rem;">
                Selesai
            </button>
        </div>
    </div>
</div>

{{-- 3. Analytics Modal --}}
<div class="user-modal-overlay" id="analyticsModal" onclick="if(event.target === this) closeAnalyticsModal()">
    <div class="user-modal-dialog">
        <div class="modal-header-row">
            <h3 class="modal-title">📊 Statistik Kunjungan Tap</h3>
            <button type="button" class="modal-close-icon" onclick="closeAnalyticsModal()">✕</button>
        </div>

        <div style="text-align: center; margin: 1rem 0;">
            <div style="font-size: 2.2rem; font-weight: 800; color: #4A0A0E;">{{ $totalTaps }}</div>
            <div style="font-size: 0.78rem; color: #78716C;">Total Interaksi Sentuhan NFC ke Gantungan Kunci Anda</div>
        </div>

        <div style="background: #FAF8F5; border: 1px solid #ECE7DF; border-radius: 14px; padding: 1rem; margin-bottom: 1rem;">
            <div style="font-size: 0.78rem; font-weight: 800; margin-bottom: 0.6rem; color: #1F1915;">Distribusi Perangkat Pengunjung</div>
            <div style="display: flex; justify-content: space-between; font-size: 0.78rem; margin-bottom: 0.3rem;">
                <span>iPhone / iOS</span>
                <strong>58%</strong>
            </div>
            <div style="width: 100%; height: 8px; background: #E5E7EB; border-radius: 999px; overflow: hidden; margin-bottom: 0.8rem;">
                <div style="width: 58%; height: 100%; background: #4A0A0E;"></div>
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 0.78rem; margin-bottom: 0.3rem;">
                <span>Android Smartphone</span>
                <strong>42%</strong>
            </div>
            <div style="width: 100%; height: 8px; background: #E5E7EB; border-radius: 999px; overflow: hidden;">
                <div style="width: 42%; height: 100%; background: #E5B32A;"></div>
            </div>
        </div>

        <button type="button" class="btn-gold-action" onclick="closeAnalyticsModal()" style="width: 100%;">
            Tutup
        </button>
    </div>
</div>

{{-- 4. Merch Modal --}}
<div class="user-modal-overlay" id="merchModal" onclick="if(event.target === this) closeMerchModal()">
    <div class="user-modal-dialog">
        <div class="modal-header-row">
            <h3 class="modal-title">🏪 Koleksi Merch FlexiTag</h3>
            <button type="button" class="modal-close-icon" onclick="closeMerchModal()">✕</button>
        </div>

        <p style="font-size: 0.8rem; color: #78716C; margin-bottom: 1rem;">
            Gantungan kunci akrilik NFC premium berukir motif budaya Sulawesi Selatan.
        </p>

        <div style="display: flex; flex-direction: column; gap: 0.8rem;">
            <div style="display: flex; align-items: center; gap: 0.8rem; background: #FAF8F5; border: 1px solid #ECE7DF; border-radius: 12px; padding: 0.75rem;">
                <img src="{{ asset('images/keychain.jpg') }}" alt="Pa'barre Allo" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                <div style="flex: 1;">
                    <strong style="font-size: 0.86rem; display: block;">Edisi Pa'barre Allo</strong>
                    <span style="font-size: 0.72rem; color: #78716C;">Motif Matahari Terbit Toraja</span>
                </div>
                <span style="font-weight: 800; color: #4A0A0E; font-size: 0.85rem;">Rp 65rb</span>
            </div>

            <div style="display: flex; align-items: center; gap: 0.8rem; background: #FAF8F5; border: 1px solid #ECE7DF; border-radius: 12px; padding: 0.75rem;">
                <div style="width: 50px; height: 50px; border-radius: 8px; background: #4A0A0E; display: flex; align-items: center; justify-content: center; color: #E5B32A; font-size: 1.4rem;">
                    ✦
                </div>
                <div style="flex: 1;">
                    <strong style="font-size: 0.86rem; display: block;">Edisi Sulapa Eppa'</strong>
                    <span style="font-size: 0.72rem; color: #78716C;">Filosofi Empat Unsur Bugis</span>
                </div>
                <span style="font-weight: 800; color: #4A0A0E; font-size: 0.85rem;">Rp 65rb</span>
            </div>
        </div>

        <a href="{{ route('home') }}" class="btn-gold-action" style="width: 100%; margin-top: 1.2rem; text-decoration: none;">
            Buka Katalog Lengkap
        </a>
    </div>
</div>

{{-- Floating Action Button Widget (Kotak Mengambang Laporan Penemuan) --}}
@if(isset($lostMessages) && $lostMessages->isNotEmpty())
    <div class="floating-inbox-widget" id="floatingInboxWidget" onclick="openLostReportsModal()" role="button" tabindex="0" title="Buka Kotak Laporan Penemuan">
        <div class="floating-widget-icon-wrap">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                <span class="floating-widget-badge" id="floatingWidgetBadge">{{ $unreadMessagesCount }}</span>
            @endif
        </div>
        <span class="floating-widget-text">Laporan Penemuan</span>
    </div>
@endif

{{-- 5. Floating Box Modal: Laporan Penemuan Barang (Kotak Mengambang) --}}
<div class="lost-reports-modal-overlay" id="modalLostReports" onclick="handleLostReportsBackdropClick(event)">
    <div class="lost-reports-floating-card" role="dialog" aria-labelledby="floatingModalTitle" aria-modal="true">
        {{-- Modal Header --}}
        <div class="floating-modal-head">
            <div class="floating-head-left">
                <div class="floating-head-icon" aria-hidden="true">
                    📬
                </div>
                <div class="floating-head-text">
                    <div class="floating-title-row">
                        <h3 class="floating-modal-title" id="floatingModalTitle">Laporan Penemuan Barang</h3>
                        @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                            <span class="badge-unread-pill" id="floatingModalUnreadBadge">
                                <span class="unread-dot">●</span> <span id="floatingModalUnreadCount">{{ $unreadMessagesCount }} Baru</span>
                            </span>
                        @endif
                    </div>
                    <p class="floating-modal-sub">
                        Pesan &amp; koordinasi dari penemu yang memindai gantungan kunci FlexiTag Anda.
                    </p>
                </div>
            </div>

            <div class="floating-head-actions">
                @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                    <button type="button" class="btn-mark-all-read" id="btnMarkAllRead" onclick="markAllLostMessagesRead()" title="Tandai semua pesan telah dibaca">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Tandai Semua</span>
                    </button>
                @endif
                <button type="button" class="btn-close-floating-modal" onclick="closeLostReportsModal()" aria-label="Tutup Dialog">
                    ✕
                </button>
            </div>
        </div>

        {{-- Modal Body --}}
        <div class="floating-modal-body">
            @if(isset($lostMessages) && $lostMessages->isNotEmpty())
                <div class="inbox-msg-list" id="inboxMsgList">
                    @foreach($lostMessages as $msg)
                        <div class="inbox-msg-item {{ is_null($msg->read_at) ? 'is-unread' : '' }}" id="msgCard-{{ $msg->id }}">
                            <div class="inbox-msg-head">
                                <div class="msg-sender-info">
                                    <div class="msg-sender-avatar" aria-hidden="true">
                                        {{ Str::upper(mb_substr($msg->sender_name ?: 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="msg-sender-name">{{ $msg->sender_name ?: 'Penemu Anonim' }}</div>
                                        <span class="msg-time-badge">{{ $msg->created_at ? $msg->created_at->diffForHumans() : 'Baru saja' }}</span>
                                    </div>
                                </div>

                                <div id="msgStatus-{{ $msg->id }}">
                                    @if(is_null($msg->read_at))
                                        <span class="msg-status-pill-unread">
                                            <span>●</span> Belum Dibaca
                                        </span>
                                    @else
                                        <span class="msg-status-pill-read">
                                            ✓ Dibaca
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($msg->location)
                                <div class="msg-location-bar">
                                    <span>📍 <strong>Lokasi:</strong> {{ $msg->location }}</span>
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($msg->location) }}" target="_blank" rel="noopener noreferrer" class="msg-location-link">
                                        <span>Buka Maps</span>
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    </a>
                                </div>
                            @endif

                            <div class="msg-body-box"><span class="msg-quote-mark">&ldquo;</span>{{ trim($msg->message) }}<span class="msg-quote-mark">&rdquo;</span></div>

                            <div class="msg-actions-row">
                                @if($msg->contact)
                                    @php
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $msg->contact);
                                        if (str_starts_with($cleanPhone, '0')) {
                                            $waPhone = '62' . substr($cleanPhone, 1);
                                        } else {
                                            $waPhone = $cleanPhone;
                                        }
                                    @endphp
                                    <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Halo ' . ($msg->sender_name ?: 'Penemu') . ', terima kasih telah menemukan gantungan kunci FlexiTag saya.') }}" target="_blank" rel="noopener noreferrer" class="btn-contact-wa">
                                        <span>💬 Hubungi Penemu ({{ $msg->contact }})</span>
                                    </a>
                                @else
                                    <span style="font-size: 0.72rem; color: #78716C; font-style: italic;">(Penemu tidak mencantumkan kontak)</span>
                                @endif

                                <div class="msg-actions-right">
                                    @if(is_null($msg->read_at))
                                        <button type="button" class="btn-action-read" id="btnRead-{{ $msg->id }}" onclick="markMessageRead({{ $msg->id }})">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                            <span>Tandai Dibaca</span>
                                        </button>
                                    @else
                                        <span class="msg-read-timestamp">
                                            Dibaca {{ $msg->read_at->format('d M H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="inbox-empty-wrap">
                    <div class="inbox-empty-icon">📭</div>
                    <h4 class="inbox-empty-title">Belum Ada Laporan Penemuan</h4>
                    <p class="inbox-empty-desc">
                        Saat seseorang menemukan gantungan kunci Anda dan mengirimkan pesan melalui profil Mode Hilang, laporan penemuan akan muncul di kotak ini.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- 6. QR Code Quick Share Modal --}}
<div class="user-modal-overlay" id="qrCodeModal" onclick="if(event.target === this) closeQrModal()">
    <div class="user-modal-dialog" style="max-width: 380px; text-align: center;">
        <div class="modal-header-row" style="margin-bottom: 0.5rem;">
            <h3 class="modal-title">📱 QR Code Profil FlexiTag</h3>
            <button type="button" class="modal-close-icon" onclick="closeQrModal()">✕</button>
        </div>

        <p style="font-size: 0.78rem; color: #78716C; margin-bottom: 1.25rem;">
            Pindai QR ini untuk membuka profil digital publik Anda secara instan tanpa sensor NFC.
        </p>

        <div style="background: #FFFFFF; border: 2px dashed #D4AF37; border-radius: 18px; padding: 1.25rem; display: inline-block; margin-bottom: 1rem; box-shadow: 0 8px 24px rgba(74, 10, 14, 0.08);">
            <img id="qrCodeImage" src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&amp;margin=10&amp;data={{ urlencode($publicUrl) }}" alt="QR Code Profil" style="width: 200px; height: 200px; display: block; border-radius: 8px;">
        </div>

        <div style="font-size: 0.78rem; font-weight: 700; color: #4A0A0E; margin-bottom: 1.2rem;" id="qrTagCodeText">
            ID: {{ $primaryTag?->tag_code ?? 'FT-DEFAULT' }}
        </div>

        <button type="button" class="btn-gold-action" onclick="copyPublicUrl('{{ $publicUrl }}')" style="width: 100%; margin-bottom: 0.5rem;">
            📋 Salin Tautan Profil
        </button>

        <button type="button" onclick="closeQrModal()" style="background: none; border: none; font-size: 0.78rem; color: #78716C; cursor: pointer; padding: 0.5rem;">
            Tutup
        </button>
    </div>
</div>

{{-- Toast Notification --}}
<div class="dashboard-toast" id="dashToast">
    <span class="dashboard-toast-check">✓</span>
    <span id="dashToastMsg">Mode aktif berhasil diubah!</span>
</div>

{{-- JavaScript Interactions --}}
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // 1. Toggle Side Navigation Drawer
    function toggleDrawer(open) {
        if (typeof window.toggleUserSidebar === 'function') {
            window.toggleUserSidebar(open);
            return;
        }
        const drawer = document.getElementById('sideDrawer');
        const backdrop = document.getElementById('drawerBackdrop');
        if (!drawer || !backdrop) return;
        if (open) {
            drawer.classList.add('is-open');
            backdrop.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        } else {
            drawer.classList.remove('is-open');
            backdrop.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }
    window.toggleUserSidebar = toggleDrawer;

    // Track current active mode to route Edit Profile button properly
    let currentActiveMode = '{{ $activeMode }}';

    function handleEditProfileClick() {
        if (currentActiveMode === 'kampus') {
            window.location.href = "{{ route('user.profile.kampus') }}";
            return;
        }
        if (currentActiveMode === 'kerja') {
            window.location.href = "{{ route('user.profile.kerja') }}";
            return;
        }
        if (currentActiveMode === 'umkm') {
            window.location.href = "{{ route('user.profile.umkm') }}";
            return;
        }
        if (currentActiveMode === 'hilang') {
            window.location.href = "{{ route('user.lost-mode.edit') }}";
            return;
        }
        openProfileModal();
    }

    // 2. Interactive Mode Switcher
    function switchUserMode(modeKey, modeTitle) {
        currentActiveMode = modeKey;
        const modeKeys = ['kampus', 'kerja', 'umkm', 'hilang'];

        // Optimistically update the UI
        modeKeys.forEach(k => {
            const card = document.getElementById('modeCard-' + k);
            const state = document.getElementById('modeState-' + k);

            if (k === modeKey) {
                card.classList.add('is-active');
                state.innerHTML = '<span class="mode-active-pill"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Aktif</span></span><a href="{{ $publicUrl }}" target="_blank" class="mode-active-arrow" title="Buka Profil Digital Publik" onclick="event.stopPropagation()"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></a>';
            } else {
                card.classList.remove('is-active');
                state.innerHTML = '<span class="mode-radio-circle"></span>';
            }
        });

        // Send AJAX request to server
        fetch("{{ route('user.dashboard.switchMode') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ mode: modeKey })
        })
        .then(res => res.json())
        .then(data => {
            showToast('Beralih ke ' + modeTitle + '! Profil publik langsung berubah.');
        })
        .catch(err => {
            console.error('Gagal beralih mode:', err);
            showToast('Mode diubah ke ' + modeTitle);
        });
    }

    // 3. Modals
    function openProfileModal() {
        document.getElementById('editProfileModal').classList.add('is-active');
    }
    function closeProfileModal() {
        document.getElementById('editProfileModal').classList.remove('is-active');
    }

    function openPrivacyModal() {
        document.getElementById('privacyModal').classList.add('is-active');
    }
    function closePrivacyModal() {
        document.getElementById('privacyModal').classList.remove('is-active');
    }

    function openAnalyticsModal() {
        document.getElementById('analyticsModal').classList.add('is-active');
    }
    function closeAnalyticsModal() {
        document.getElementById('analyticsModal').classList.remove('is-active');
    }

    function openMerchModal() {
        document.getElementById('merchModal').classList.add('is-active');
    }
    function closeMerchModal() {
        document.getElementById('merchModal').classList.remove('is-active');
    }

    // 3.5. Lost Item Reports Floating Box Modal (Kotak Mengambang)
    function openLostReportsModal() {
        const modal = document.getElementById('modalLostReports');
        if (modal) {
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeLostReportsModal() {
        const modal = document.getElementById('modalLostReports');
        if (modal) {
            modal.classList.remove('is-active');
            document.body.style.overflow = '';
        }
    }

    function handleLostReportsBackdropClick(event) {
        if (event.target.id === 'modalLostReports') {
            closeLostReportsModal();
        }
    }

    function markMessageRead(messageId) {
        const btn = document.getElementById('btnRead-' + messageId);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span>Menyimpan...</span>';
        }

        fetch(`/mode-hilang/pesan/${messageId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const card = document.getElementById('msgCard-' + messageId);
                if (card) {
                    card.classList.remove('is-unread');
                }
                const statusBox = document.getElementById('msgStatus-' + messageId);
                if (statusBox) {
                    statusBox.innerHTML = '<span class="msg-status-pill-read">✓ Dibaca</span>';
                }
                if (btn) {
                    btn.outerHTML = '<span class="msg-read-timestamp">✓ Baru saja dibaca</span>';
                }
                updateUnreadBadges(data.unread_count);
                showToast('Laporan ditandai telah dibaca.');
            }
        })
        .catch(err => {
            console.error('Gagal menandai pesan:', err);
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span>Tandai Dibaca</span>';
            }
            showToast('Gagal menandai pesan.');
        });
    }

    function markAllLostMessagesRead() {
        const btn = document.getElementById('btnMarkAllRead');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Memproses...';
        }

        fetch("{{ route('user.lost-mode.messages.readAll') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.inbox-msg-item.is-unread').forEach(item => {
                    item.classList.remove('is-unread');
                });
                document.querySelectorAll('.msg-status-pill-unread').forEach(pill => {
                    pill.className = 'msg-status-pill-read';
                    pill.textContent = '✓ Dibaca';
                });
                document.querySelectorAll('.btn-action-read').forEach(b => {
                    b.outerHTML = '<span class="msg-read-timestamp">✓ Baru saja dibaca</span>';
                });
                updateUnreadBadges(0);
                if (btn) btn.style.display = 'none';
                showToast('Semua laporan telah ditandai dibaca.');
            }
        })
        .catch(err => {
            console.error('Gagal menandai semua pesan:', err);
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Tandai Semua';
            }
            showToast('Gagal menandai semua pesan.');
        });
    }

    function updateUnreadBadges(count) {
        const topBadge = document.getElementById('topbarInboxBadge');
        const floatBadge = document.getElementById('floatingWidgetBadge');
        const modalBadge = document.getElementById('floatingModalUnreadBadge');
        const banner = document.getElementById('bannerLostAlert');

        if (count > 0) {
            if (topBadge) {
                topBadge.textContent = count;
                topBadge.style.display = 'flex';
            }
            if (floatBadge) {
                floatBadge.textContent = count;
                floatBadge.style.display = 'flex';
            }
            const modalCount = document.getElementById('floatingModalUnreadCount');
            if (modalCount) modalCount.textContent = count + ' Baru';
        } else {
            if (topBadge) topBadge.style.display = 'none';
            if (floatBadge) floatBadge.style.display = 'none';
            if (modalBadge) modalBadge.style.display = 'none';
            if (banner) banner.style.display = 'none';
        }
    }

    // 3.6. QR Code Modal & Quick Link Copy
    function copyPublicUrl(url) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(() => {
                showToast('Tautan profil publik berhasil disalin!');
            }).catch(() => {
                fallbackCopyText(url);
            });
        } else {
            fallbackCopyText(url);
        }
    }

    function fallbackCopyText(text) {
        const temp = document.createElement('input');
        temp.value = text;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        showToast('Tautan profil publik berhasil disalin!');
    }

    function openQrModal(url, tagCode) {
        const modal = document.getElementById('qrCodeModal');
        const img = document.getElementById('qrCodeImage');
        const text = document.getElementById('qrTagCodeText');
        if (img && url) {
            img.src = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=10&data=${encodeURIComponent(url)}`;
        }
        if (text && tagCode) {
            text.textContent = 'ID Tag: ' + tagCode;
        }
        if (modal) {
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeQrModal() {
        const modal = document.getElementById('qrCodeModal');
        if (modal) {
            modal.classList.remove('is-active');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLostReportsModal();
            closeQrModal();
            closeProfileModal();
            closePrivacyModal();
            closeAnalyticsModal();
            closeMerchModal();
        }
    });

    // 4. Toast
    let toastTimer = null;
    function showToast(message) {
        const toast = document.getElementById('dashToast');
        const msg = document.getElementById('dashToastMsg');
        msg.textContent = message;
        toast.classList.add('is-show');

        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.remove('is-show');
        }, 3200);
    }

    @if (session('status'))
        showToast("{{ session('status') }}");
    @endif
</script>

</body>
</html>
