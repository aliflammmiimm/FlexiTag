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
    @vite(['resources/css/components/user-sidebar.css', 'resources/js/user-sidebar.js'])
</head>
<body class="dashboard-body">

<div class="user-app-container">

    {{-- ========================================================================= --}}
    {{-- TOP APP BAR (KONSISTEN DENGAN HALAMAN HOME)                                --}}
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
                <h1 class="topbar-title">Merch</h1>
            </div>
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
                    <p class="merch-add-desc">Hubungkan gantungan kunci fisik baru langsung ke akun &amp; profil aktif Anda.</p>
                </div>
            </div>

            <div class="merch-add-actions">
                <button type="button" class="btn-scan-tag" id="btnOpenClaimModal" onclick="openClaimTagModal()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Klaim &amp; Pindai Tag Sekarang</span>
                </button>
                <a href="{{ route('user.activation.show') }}" class="merch-add-wizard-link">
                    <span>Atau buka wizard aktivasi lengkap</span>
                    <span>→</span>
                </a>
            </div>
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

                        <p class="tag-id-mode">ID: {{ $tag['tag_code'] }} • Mode: <span id="tag-mode-text-{{ $tag['id'] }}">{{ $tag['mode_short'] }}</span></p>
                        <p class="tag-last-active" id="last-activity-{{ $tag['id'] }}">{{ $tag['activity_text'] }}</p>
                    </div>
                </div>

                {{-- Mode Khusus Ganci (Mode Mandiri per Tag) --}}
                <div class="tag-mode-box">
                    <div class="tag-mode-header">
                        <div class="tag-mode-title-wrap">
                            <span class="tag-mode-icon" aria-hidden="true">🎭</span>
                            <div>
                                <h5 class="tag-mode-heading">Mode Tampilan Ganci Ini</h5>
                                <p class="tag-mode-caption">Profil yang muncul saat orang lain tap ganci ini</p>
                            </div>
                        </div>
                        <span class="tag-mode-badge tag-mode-badge-{{ $tag['mode'] }}" id="tag-badge-{{ $tag['id'] }}">
                            {{ $tag['mode_label'] }}
                        </span>
                    </div>

                    <div class="custom-mode-dropdown" id="custom-dropdown-{{ $tag['id'] }}">
                        <button type="button" 
                                class="custom-mode-trigger" 
                                id="mode-trigger-{{ $tag['id'] }}"
                                onclick="toggleCustomModeDropdown({{ $tag['id'] }})"
                                @if(!$tag['is_active']) disabled @endif
                                aria-haspopup="listbox" 
                                aria-expanded="false">
                            <div class="custom-mode-trigger-content">
                                <span class="custom-mode-trigger-icon" id="trigger-icon-{{ $tag['id'] }}">
                                    @if ($tag['mode'] === 'kampus') 🎓
                                    @elseif ($tag['mode'] === 'kerja') 💼
                                    @elseif ($tag['mode'] === 'umkm') 🏪
                                    @else 🔒
                                    @endif
                                </span>
                                <div class="custom-mode-trigger-text">
                                    <span class="custom-mode-trigger-title" id="trigger-title-{{ $tag['id'] }}">
                                        {{ $tag['mode_label'] }}
                                    </span>
                                    <span class="custom-mode-trigger-sub" id="trigger-sub-{{ $tag['id'] }}">
                                        @if ($tag['mode'] === 'kampus') Profil Akademik &amp; Mahasiswa
                                        @elseif ($tag['mode'] === 'kerja') Portofolio Profesional &amp; CV
                                        @elseif ($tag['mode'] === 'umkm') Katalog Produk &amp; Kasir WA
                                        @else Kontak Darurat Pemilik
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <span class="custom-mode-trigger-chevron" aria-hidden="true">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>

                        {{-- Floating Custom Options Menu --}}
                        <div class="custom-mode-menu" id="mode-menu-{{ $tag['id'] }}" role="listbox">
                            <div class="custom-mode-option {{ $tag['mode'] === 'kampus' ? 'is-selected' : '' }}" 
                                 role="option" 
                                 aria-selected="{{ $tag['mode'] === 'kampus' ? 'true' : 'false' }}"
                                 onclick="selectCustomTagMode('kampus', {{ $tag['id'] }})">
                                <div class="option-icon-box option-icon-kampus">🎓</div>
                                <div class="option-meta">
                                    <div class="option-title-row">
                                        <strong class="option-title">Mode Kampus</strong>
                                        <span class="option-tag-badge badge-kampus">Akademik</span>
                                    </div>
                                    <span class="option-desc">Profil mahasiswa, prodi &amp; organisasi kampus</span>
                                </div>
                                <span class="option-check" aria-hidden="true">✓</span>
                            </div>

                            <div class="custom-mode-option {{ $tag['mode'] === 'kerja' ? 'is-selected' : '' }}" 
                                 role="option" 
                                 aria-selected="{{ $tag['mode'] === 'kerja' ? 'true' : 'false' }}"
                                 onclick="selectCustomTagMode('kerja', {{ $tag['id'] }})">
                                <div class="option-icon-box option-icon-kerja">💼</div>
                                <div class="option-meta">
                                    <div class="option-title-row">
                                        <strong class="option-title">Mode Kerja</strong>
                                        <span class="option-tag-badge badge-kerja">Karir &amp; CV</span>
                                    </div>
                                    <span class="option-desc">Portofolio profesional, resume &amp; kontak karir</span>
                                </div>
                                <span class="option-check" aria-hidden="true">✓</span>
                            </div>

                            <div class="custom-mode-option {{ $tag['mode'] === 'umkm' ? 'is-selected' : '' }}" 
                                 role="option" 
                                 aria-selected="{{ $tag['mode'] === 'umkm' ? 'true' : 'false' }}"
                                 onclick="selectCustomTagMode('umkm', {{ $tag['id'] }})">
                                <div class="option-icon-box option-icon-umkm">🏪</div>
                                <div class="option-meta">
                                    <div class="option-title-row">
                                        <strong class="option-title">Mode UMKM</strong>
                                        <span class="option-tag-badge badge-umkm">Bisnis</span>
                                    </div>
                                    <span class="option-desc">Katalog produk, jam toko &amp; kasir WhatsApp</span>
                                </div>
                                <span class="option-check" aria-hidden="true">✓</span>
                            </div>

                            <div class="custom-mode-option {{ $tag['mode'] === 'hilang' ? 'is-selected' : '' }}" 
                                 role="option" 
                                 aria-selected="{{ $tag['mode'] === 'hilang' ? 'true' : 'false' }}"
                                 onclick="selectCustomTagMode('hilang', {{ $tag['id'] }})">
                                <div class="option-icon-box option-icon-hilang">🔒</div>
                                <div class="option-meta">
                                    <div class="option-title-row">
                                        <strong class="option-title">Mode Barang Hilang</strong>
                                        <span class="option-tag-badge badge-hilang">Darurat</span>
                                    </div>
                                    <span class="option-desc">Kontak penemu, pesan darurat &amp; imbalan aman</span>
                                </div>
                                <span class="option-check" aria-hidden="true">✓</span>
                            </div>
                        </div>
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
                        @if ($tag['mode'] === 'umkm')
                            <a href="{{ route('user.profile.umkm') }}" class="btn-tag-action-gray" id="btn-edit-profile-{{ $tag['id'] }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                                <span>Edit Profil UMKM</span>
                            </a>
                        @elseif ($tag['mode'] === 'kerja')
                            <a href="{{ route('user.profile.kerja') }}" class="btn-tag-action-gray" id="btn-edit-profile-{{ $tag['id'] }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                                <span>Edit Profil Kerja</span>
                            </a>
                        @elseif ($tag['mode'] === 'hilang')
                            <a href="{{ route('user.lost-mode.edit') }}" class="btn-tag-action-gray" id="btn-edit-profile-{{ $tag['id'] }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                                <span>Edit Pesan Hilang</span>
                            </a>
                        @else
                            <a href="{{ route('user.profile.kampus') }}" class="btn-tag-action-gray" id="btn-edit-profile-{{ $tag['id'] }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                                <span>Edit Profil Kampus</span>
                            </a>
                        @endif

                        <a href="{{ route('public.tag', $tag['tag_code']) }}" target="_blank" class="btn-tag-action-preview" title="Buka profil publik ganci ini di tab baru">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                <polyline points="15 3 21 3 21 9"></polyline>
                                <line x1="10" y1="14" x2="21" y2="3"></line>
                            </svg>
                            <span>Uji Tap</span>
                        </a>

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
                <button type="button" class="btn-scan-tag" style="max-width: 260px; margin: 0 auto;" onclick="openClaimTagModal()">
                    + Hubungkan Tag
                </button>
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

    @include('partials.user-sidebar')

    {{-- ========================================================================= --}}
    {{-- MODALS: PRIVACY & RIWAYAT TAP                                             --}}
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
        if (typeof window.toggleUserSidebar === 'function') {
            window.toggleUserSidebar(open);
            return;
        }
        const drawer = document.getElementById('sideDrawer');
        const overlay = document.getElementById('drawerBackdrop') || document.getElementById('drawerOverlay');
        if (!drawer || !overlay) return;
        if (open) {
            drawer.classList.add('is-open');
            overlay.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        } else {
            drawer.classList.remove('is-open');
            overlay.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }
    window.toggleUserSidebar = toggleDrawer;

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

    // 6. Interactive Per-Tag Mode Change (Custom Dropdown Component)
    const MODE_METADATA = {
        'kampus': {
            icon: '🎓',
            title: 'Mode Kampus',
            sub: 'Profil Akademik & Mahasiswa',
            badgeClass: 'tag-mode-badge-kampus',
            routeUrl: '{{ route('user.profile.kampus') }}',
            editLabel: 'Edit Profil Kampus'
        },
        'kerja': {
            icon: '💼',
            title: 'Mode Kerja',
            sub: 'Portofolio Profesional & CV',
            badgeClass: 'tag-mode-badge-kerja',
            routeUrl: '{{ route('user.profile.kerja') }}',
            editLabel: 'Edit Profil Kerja'
        },
        'umkm': {
            icon: '🏪',
            title: 'Mode UMKM',
            sub: 'Katalog Produk & Kasir WA',
            badgeClass: 'tag-mode-badge-umkm',
            routeUrl: '{{ route('user.profile.umkm') }}',
            editLabel: 'Edit Profil UMKM'
        },
        'hilang': {
            icon: '🔒',
            title: 'Mode Barang Hilang',
            sub: 'Kontak Darurat Pemilik',
            badgeClass: 'tag-mode-badge-hilang',
            routeUrl: '{{ route('user.lost-mode.edit') }}',
            editLabel: 'Edit Pesan Hilang'
        }
    };

    function toggleCustomModeDropdown(tagId) {
        const menu = document.getElementById('mode-menu-' + tagId);
        const trigger = document.getElementById('mode-trigger-' + tagId);
        if (!menu || !trigger || trigger.disabled) return;

        const isOpen = menu.classList.contains('is-open');

        // Tutup menu dropdown ganci lain terlebih dahulu
        document.querySelectorAll('.custom-mode-menu.is-open').forEach(el => {
            el.classList.remove('is-open');
        });
        document.querySelectorAll('.custom-mode-trigger[aria-expanded="true"]').forEach(el => {
            el.setAttribute('aria-expanded', 'false');
        });

        if (!isOpen) {
            menu.classList.add('is-open');
            trigger.setAttribute('aria-expanded', 'true');
        }
    }

    function selectCustomTagMode(newMode, tagId) {
        const meta = MODE_METADATA[newMode];
        if (!meta) return;

        const menu = document.getElementById('mode-menu-' + tagId);
        const trigger = document.getElementById('mode-trigger-' + tagId);
        if (menu) menu.classList.remove('is-open');
        if (trigger) {
            trigger.setAttribute('aria-expanded', 'false');
            trigger.disabled = true;
        }

        // Update trigger UI immediately (optimistic UI update)
        const iconEl = document.getElementById('trigger-icon-' + tagId);
        const titleEl = document.getElementById('trigger-title-' + tagId);
        const subEl = document.getElementById('trigger-sub-' + tagId);
        const badgeEl = document.getElementById('tag-badge-' + tagId);
        const modeTextEl = document.getElementById('tag-mode-text-' + tagId);
        const editLink = document.getElementById('btn-edit-profile-' + tagId);

        if (iconEl) iconEl.textContent = meta.icon;
        if (titleEl) titleEl.textContent = meta.title;
        if (subEl) subEl.textContent = meta.sub;
        if (badgeEl) {
            badgeEl.textContent = 'Menyimpan...';
            badgeEl.style.opacity = '0.7';
        }

        // Highlight selected option di dalam menu
        if (menu) {
            menu.querySelectorAll('.custom-mode-option').forEach(opt => {
                opt.classList.remove('is-selected');
                opt.setAttribute('aria-selected', 'false');
            });
            const currentOpt = menu.querySelector(`[onclick*="'${newMode}'"]`);
            if (currentOpt) {
                currentOpt.classList.add('is-selected');
                currentOpt.setAttribute('aria-selected', 'true');
            }
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(`{{ url('/dashboard/merch/tag') }}/${tagId}/mode`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ mode: newMode })
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Gagal mengubah mode tag');
            }
            return data;
        })
        .then(data => {
            if (trigger) trigger.disabled = false;

            if (badgeEl) {
                badgeEl.textContent = data.mode_label;
                badgeEl.className = 'tag-mode-badge ' + meta.badgeClass;
                badgeEl.style.opacity = '1';
            }

            if (modeTextEl) {
                modeTextEl.textContent = data.mode_label.replace('Mode ', '');
            }

            if (editLink) {
                editLink.href = meta.routeUrl;
                const span = editLink.querySelector('span');
                if (span) span.textContent = meta.editLabel;
            }

            showToast(data.message || `Mode ganci berhasil diubah ke ${data.mode_label}`, '✓');
        })
        .catch(err => {
            console.error('Mode change error:', err);
            if (trigger) trigger.disabled = false;
            if (badgeEl) badgeEl.style.opacity = '1';
            showToast(err.message || 'Gagal mengubah mode ganci', '⚠️');
        });
    }

    // Menutup menu jika klik di luar dropdown atau tekan tombol Escape
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-mode-dropdown')) {
            document.querySelectorAll('.custom-mode-menu.is-open').forEach(el => el.classList.remove('is-open'));
            document.querySelectorAll('.custom-mode-trigger[aria-expanded="true"]').forEach(el => el.setAttribute('aria-expanded', 'false'));
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.custom-mode-menu.is-open').forEach(el => el.classList.remove('is-open'));
            document.querySelectorAll('.custom-mode-trigger[aria-expanded="true"]').forEach(el => el.setAttribute('aria-expanded', 'false'));
        }
    });

    // 7. Quick Claim Tag Modal Interactions
    function openClaimTagModal() {
        const modal = document.getElementById('claimTagModal');
        if (modal) {
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                const input = document.getElementById('activation_code_input');
                if (input) input.focus();
            }, 80);
        }
    }

    function closeClaimTagModal() {
        const modal = document.getElementById('claimTagModal');
        if (modal) {
            modal.classList.remove('is-active');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeClaimTagModal();
        }
    });
</script>

{{-- Modal Pop-Up Klaim Tag Cepat Langsung di Merch --}}
<div class="claim-modal-overlay" id="claimTagModal" onclick="if(event.target === this) closeClaimTagModal()">
    <div class="claim-modal-dialog">
        <div class="claim-modal-head">
            <div class="claim-modal-title-row">
                <div class="claim-icon-emblem" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#F5BA41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="4"></rect>
                        <rect x="7" y="7" width="10" height="10" rx="1.5"></rect>
                        <path d="M12 7v10"></path>
                        <path d="M7 12h10"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="claim-modal-title">Klaim Tag / Merchandise Baru</h3>
                    <p class="claim-modal-subtitle">Hubungkan gantungan kunci ke profil digital Anda</p>
                </div>
            </div>
            <button type="button" class="claim-close-btn" onclick="closeClaimTagModal()" aria-label="Tutup Modal">✕</button>
        </div>

        <form method="POST" action="{{ route('user.merch.claimTag') }}" id="claimTagForm">
            @csrf
            <div class="claim-input-group">
                <label for="activation_code_input">Kode Aktivasi Merchandise</label>
                <div class="claim-input-wrap">
                    <span class="claim-input-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input type="text" 
                           id="activation_code_input" 
                           name="activation_code" 
                           class="claim-text-input" 
                           placeholder="Contoh: KD-8821-XQ atau FT-KMP-001" 
                           required 
                           autocomplete="off"
                           autocapitalize="characters"
                           oninput="this.value = this.value.toUpperCase()">
                </div>
                <span class="claim-input-hint">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>Kode unik tertera di bagian belakang kartu kemasan FlexiTag.</span>
                </span>
            </div>

            <div class="claim-info-box">
                <span class="claim-info-icon">⚡</span>
                <p class="claim-info-text">
                    Tag baru otomatis mewarisi profil aktif Anda (<strong>{{ ucfirst($activeMode) }}</strong>) tanpa perlu mengatur ulang nama atau foto.
                </p>
            </div>

            <div class="claim-modal-actions">
                <button type="submit" class="btn-claim-submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Hubungkan Tag Sekarang</span>
                </button>
                <button type="button" class="btn-claim-cancel" onclick="closeClaimTagModal()">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
