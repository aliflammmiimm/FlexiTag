<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        <button type="button" class="hamburger-btn" id="openDrawerBtn" aria-label="Buka Menu Navigasi" onclick="toggleDrawer(true)">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="topbar-branding">
            <span class="topbar-subtitle">FLEXITAG</span>
            <h1 class="topbar-title">Home</h1>
        </div>
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
                <a href="{{ $publicUrl }}" target="_blank" class="btn-external-icon" title="Buka profil publik tap">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 17"></polyline>
                    </svg>
                </a>
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
                                <a href="{{ route('user.lost-mode.edit') }}" onclick="event.stopPropagation();" style="color: #4A0A0E; font-weight: 700; text-decoration: underline;">Atur Pesan Darurat</a>
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
        if (currentActiveMode === 'umkm') {
            window.location.href = "{{ route('user.profile.umkm') }}";
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
