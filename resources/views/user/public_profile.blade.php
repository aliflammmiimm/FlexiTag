<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $displayName = $profile?->display_name ?? $user?->name ?? 'Andi Muhammad';
        $editionName = ($tag?->batch_id && !str_starts_with($tag->batch_id, '#')) 
            ? $tag->batch_id 
            : 'EDISI PUSAKA KUNCI PA\'BARRE ALLO';

        // Avatar photo resolution
        $avatarUrl = null;
        if ($profile?->photo_path) {
            $avatarUrl = file_exists(public_path($profile->photo_path)) 
                ? asset($profile->photo_path) 
                : asset('storage/' . $profile->photo_path);
        } elseif (str_contains(strtolower($displayName), 'andi') && file_exists(public_path('images/avatar_andi.jpg'))) {
            $avatarUrl = asset('images/avatar_andi.jpg');
        }

        // Initials fallback
        $parts = explode(' ', trim($displayName));
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $p) {
            $initials .= mb_substr($p, 0, 1);
        }
        $initials = strtoupper($initials) ?: 'AM';

        // Current Active Mode resolution
        $currentMode = $activeMode ?? ($profile?->active_mode ?? 'kampus');

        if ($currentMode === 'kerja') {
            $headline = $modeData['headline'] ?? $modeData['posisi'] ?? ($profile?->headline ?: 'Software Engineer & Embedded IoT Developer');
            $bioText = $modeData['bio'] ?? ($profile?->bio ?: 'Fokus membangun sistem web modern dan perangkat keras pintar berbasis mikrokontroler.');
            $locationText = $modeData['lokasi'] ?? $modeData['lokasi_kantor'] ?? ($profile?->bio && !str_contains($profile->bio, 'Angkatan') && strlen($profile->bio) < 50 ? $profile->bio : 'Makassar, Sulawesi Selatan');
            $phoneLabel = 'TELEPON & WHATSAPP KERJA';
            $emailLabel = 'EMAIL PROFESIONAL';
            $phone = $modeData['telepon_kerja'] ?? $user?->no_whatsapp ?? '+62 812-3456-7890';
            $email = $modeData['email_kerja'] ?? ($user?->email ? (str_contains($user->email, '@student') ? str_replace('@student.unm.ac.id', '@flexitag.id', $user->email) : $user->email) : 'andi.muhammad@flexitag.id');
            $modeBadge = ['label' => 'Mode Kerja', 'icon' => '💼', 'section_title' => 'Portofolio & Jejaring Karir'];
        } elseif ($currentMode === 'umkm') {
            $headline = $modeData['tagline'] ?? $profile?->headline ?? 'Katalog Produk & Usaha UMKM';
            $bioText = $profile?->bio ?? null;
            $locationText = $modeData['alamat_toko'] ?? 'Makassar, Sulawesi Selatan';
            $phoneLabel = 'WHATSAPP ORDER';
            $emailLabel = 'EMAIL USAHA';
            $phone = $modeData['whatsapp_order'] ?? $user?->no_whatsapp ?? '+62 812-3456-7890';
            $email = $user?->email ?? '';
            $modeBadge = ['label' => 'Mode UMKM', 'icon' => '🏪', 'section_title' => 'Katalog Produk & Usaha'];
        } elseif ($currentMode === 'hilang') {
            $headline = $modeData['nama_barang'] ?? 'Gantungan Kunci Pintar & Kunci Rumah / Motor';
            $bioText = $modeData['pesan_darurat'] ?? 'Halo orang baik! Gantungan kunci ini sangat berharga bagi saya karena berisi kunci rumah utama dan data penting. Ada imbalan terima kasih bagi siapa pun yang bersedia mengembalikan. Terima kasih banyak atas kejujuran Anda!';
            $locationText = $modeData['kemungkinan_tertinggal'] ?? $modeData['instruksi_pengembalian'] ?? 'Area Kampus Unhas Tamalanrea / Sekitar Losari, Makassar';
            $phoneLabel = 'KONTAK DARURAT';
            $emailLabel = 'EMAIL RELAY';
            $phone = $modeData['kontak_darurat_1'] ?? $user?->no_whatsapp ?? '+62 812-3456-7890';
            $email = $user?->email ?? '';
            $modeBadge = ['label' => 'Mode Barang Hilang', 'icon' => '🛡️', 'section_title' => 'Informasi Kontak Darurat'];
        } else {
            // default 'kampus'
            $headline = $modeData['headline'] ?? $profile?->headline ?? 'Teknik Komputer • Universitas Negeri Makassar';
            $bioText = null;
            $locationText = $modeData['bio'] ?? $profile?->bio ?? 'Angkatan 2023 • Makassar, Sulawesi Selatan';
            $phoneLabel = 'WHATSAPP & SELULER';
            $emailLabel = 'EMAIL AKADEMIK';
            $phone = $modeData['telepon_kampus'] ?? $user?->no_whatsapp ?? '+62 812-3456-7890';
            $email = $modeData['email_kampus'] ?? $user?->email ?? 'andi.muhammad@student.unm.ac.id';
            $modeBadge = ['label' => 'Mode Kampus', 'icon' => '🎓', 'section_title' => 'Jejaring & Aktivitas Kampus'];
        }

        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '+')) {
            $cleanPhone = substr($cleanPhone, 1);
        }
    @endphp

    <title>{{ $displayName }} — FlexiTag Smart Merchandise</title>
    <meta name="description" content="{{ $headline }} — Profil Digital Dinamis FlexiTag">

    @if (!($privacy?->indexable ?? false))
        <meta name="robots" content="noindex, nofollow">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/flexitag-profile.css') }}">
</head>
<body>

<div class="profile-viewport-wrapper">
    <main class="profile-container" id="profileApp">

        @if ($isLocked ?? false)
            {{-- Status Tag Nonaktif (Kill Switch Aktif) --}}
            <div class="locked-tag-screen">
                <div class="locked-icon-shield">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h2 style="font-size: 18px; font-weight: 800; color: #991B1B; margin-bottom: 8px;">Tag Sedang Dikunci Sementara</h2>
                <p style="font-size: 13px; color: #57534E; line-height: 1.5; margin-bottom: 20px;">
                    Pemilik tag atau sistem telah mengaktifkan sakelar darurat (Kill Switch) pada perangkat ini. Informasi profil dilindungi dan disembunyikan.
                </p>
                <div style="font-size: 11px; color: #8C827A;">
                    FlexiTag Smart Protection System • ID: {{ $tag->tag_code }}
                </div>
            </div>
        @elseif ($currentMode === 'hilang')
            {{-- ========================================================================= --}}
            {{-- MODE BARANG HILANG AKTIF (SAFE RECOVERY GATEWAY)                           --}}
            {{-- ========================================================================= --}}
            <header class="lost-banner-header">
                <div class="lost-top-row">
                    <div class="badge-nfc-tap-verified">
                        <span class="dot-green"></span>
                        <span>NFC Tap Terverifikasi</span>
                    </div>
                    <div class="badge-tag-id">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                        <span>{{ $modeData['batch_display'] ?? ($tag?->batch_id ?: '#SS-8809') }}</span>
                    </div>
                </div>

                <div class="lost-mode-pill-wrap">
                    <div class="lost-mode-pill">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#8B0000" stroke-width="2.4">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        <span>MODE BARANG HILANG AKTIF</span>
                    </div>
                    <p class="lost-mode-sub">MaccaTag Sulawesi Heritage Edition • Safe Recovery Gateway</p>
                </div>
            </header>

            {{-- Emblem Siren & Hero Title --}}
            <section class="lost-hero-section">
                <div class="lost-emblem-circle">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                    </svg>
                    <div class="lost-emblem-sub-badge" title="Pencarian Aktif">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                </div>

                <h1 class="lost-hero-title">Menemukan Barang Ini?</h1>
                <p class="lost-hero-desc">
                    Bantu kembalikan barang ini kepada pemiliknya dengan menghubunginya melalui tombol aman di bawah. Identitas Anda dan pemilik tetap terlindungi secara privat.
                </p>
            </section>

            {{-- Card Detail Informasi Barang --}}
            <section class="lost-details-card">
                <div class="lost-status-header">
                    <div class="pill-status-searching">
                        <span class="dot-orange"></span>
                        <span>STATUS: SEDANG DICARI</span>
                    </div>
                    <span class="auto-protected-text">Auto-Protected</span>
                </div>

                {{-- Detail 1: Jenis Barang --}}
                <div class="lost-info-row">
                    <div class="lost-info-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 2l-2 2m-1.5 1.5L16 7l-1.5-1.5-1.5 1.5 1.5 1.5-3 3a6.5 6.5 0 1 0 4 4l3-3 1.5 1.5 1.5-1.5-1.5-1.5 1.5-1.5 2-2z"></path>
                            <circle cx="7.5" cy="16.5" r="1.5"></circle>
                        </svg>
                    </div>
                    <div class="lost-info-body">
                        <span class="lost-info-label">JENIS BARANG</span>
                        <span class="lost-info-value">{{ $modeData['nama_barang'] ?? 'Gantungan Kunci Pintar & Kunci Rumah / Motor' }}</span>
                    </div>
                </div>

                {{-- Detail 2: Perangkat NFC Tag --}}
                <div class="lost-info-row">
                    <div class="lost-info-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                    <div class="lost-info-body">
                        <span class="lost-info-label">PERANGKAT NFC TAG</span>
                        <span class="lost-info-value">{{ $modeData['perangkat_tag'] ?? 'FlexiTag Pa\'barre Allo (Sulawesi Heritage Edition)' }}</span>
                    </div>
                </div>

                {{-- Detail 3: Kemungkinan Tertinggal --}}
                <div class="lost-info-row">
                    <div class="lost-info-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                        </svg>
                    </div>
                    <div class="lost-info-body">
                        <span class="lost-info-label">KEMUNGKINAN TERTINGGAL DI</span>
                        <span class="lost-info-value">{{ $modeData['kemungkinan_tertinggal'] ?? $modeData['instruksi_pengembalian'] ?? 'Area Kampus Unhas Tamalanrea / Sekitar Losari, Makassar' }}</span>
                    </div>
                </div>

                {{-- Pesan Personal Pemilik --}}
                <div class="lost-personal-message-box">
                    <div class="lost-message-title">
                        <span>❝</span>
                        <span>Pesan Personal dari Pemilik</span>
                    </div>
                    <p class="lost-message-quote">
                        "{{ $modeData['pesan_darurat'] ?? 'Halo orang baik! Gantungan kunci ini sangat berharga bagi saya karena berisi kunci rumah utama dan data penting. Ada imbalan terima kasih bagi siapa pun yang bersedia mengembalikan. Terima kasih banyak atas kejujuran Anda!' }}"
                    </p>
                </div>

                {{-- Jaminan Privasi --}}
                <div class="lost-privacy-guarantee">
                    <svg class="lock-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <div>
                        <strong>Privasi Terjamin:</strong> Nomor telepon dan identitas kedua belah pihak terlindungi di balik enkripsi MaccaTag.
                    </div>
                </div>
            </section>

            {{-- Tombol Aksi Hubungi & Kirim Lokasi --}}
            <section class="lost-actions-section">
                {{-- Tombol 1: Hubungi Pemilik (Kontak Aman) --}}
                <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode('Halo, saya menemukan barang FlexiTag Anda (' . $tag->tag_code . '). Saya ingin membantu mengembalikannya secara aman.') }}" 
                   target="_blank" rel="noopener" class="btn-lost-contact-owner" id="btnLostContactOwner">
                    <div class="btn-lost-icon-bubble">
                        <span class="dot-live"></span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="btn-lost-title">Hubungi Pemilik (Kontak Aman)</span>
                        <span class="btn-lost-sub">Kirim pesan chat anonim via gateway terenkripsi WhatsApp / Web</span>
                    </div>
                    <span class="btn-lost-arrow">➔</span>
                </a>

                {{-- Tombol 2: Kirim Titik Lokasi Temuan --}}
                <button type="button" class="btn-lost-share-location" id="btnShareLocation" onclick="shareCurrentLocation('{{ $cleanPhone }}', '{{ $tag->tag_code }}')">
                    <div class="btn-lost-gps-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="3"></circle>
                            <line x1="12" y1="2" x2="12" y2="4"></line>
                            <line x1="12" y1="20" x2="12" y2="22"></line>
                            <line x1="2" y1="12" x2="4" y2="12"></line>
                            <line x1="20" y1="12" x2="22" y2="12"></line>
                        </svg>
                    </div>
                    <div style="text-align: left;">
                        <span class="btn-lost-gps-title">Kirim Titik Lokasi Temuan</span>
                        <span class="btn-lost-gps-sub">Bantu pemilik menjemput dengan koordinat aman GPS</span>
                    </div>
                    <span class="btn-lost-chevron">›</span>
                </button>

                {{-- Tombol 3: Barang Sudah Berhasil Dikembalikan? --}}
                <button type="button" class="btn-lost-returned" onclick="openReturnedModal()">
                    <svg class="check-icon-teal" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Barang Sudah Berhasil Dikembalikan?</span>
                </button>
            </section>

            {{-- Footer Khusus Mode Barang Hilang --}}
            <footer style="padding: 10px 20px 28px; text-align: center;">
                <div class="lost-footer-diamonds">
                    <span>◆</span>
                    <span style="color: #E5B239;">◆</span>
                    <span>◆</span>
                </div>
                <h3 class="lost-footer-thanks">Terima kasih telah menjadi orang jujur &amp; membantu sesama.</h3>
                <p class="lost-footer-sub">Kebaikan kecil Anda sangat berarti bagi pemilik barang ini.</p>
                <div class="lost-footer-legal">
                    POWERED BY MACCATAG • SMART NFC MERCHANDISE<br>
                    SULAWESI HERITAGE EDITION • ZERO APP REQUIRED
                </div>
            </footer>
        @else
            {{-- 1. Banner Header Marun dengan Gelombang Konsentris NFC --}}
            <header class="profile-banner-header">
                {{-- Ornamen Gelombang Konsentris Tap NFC --}}
                <div class="banner-ripple-wrap" aria-hidden="true">
                    <span class="ripple-ring ring-1"></span>
                    <span class="ripple-ring ring-2"></span>
                    <span class="ripple-ring ring-3"></span>
                    <span class="ripple-ring ring-4"></span>
                </div>

                @if ($currentMode === 'kerja')
                    {{-- Ornamen Sulapa Eppa' Line Grid (Khusus Mode Kerja) --}}
                    <div class="banner-sulapa-grid" aria-hidden="true">
                        <svg viewBox="0 0 160 160" fill="none" stroke="#E5B239" stroke-width="1.2">
                            <rect x="25" y="25" width="110" height="110" transform="rotate(45 80 80)"/>
                            <rect x="45" y="45" width="70" height="70" transform="rotate(45 80 80)"/>
                            <line x1="80" y1="0" x2="80" y2="160"/>
                            <line x1="0" y1="80" x2="160" y2="80"/>
                        </svg>
                    </div>
                @endif

                {{-- Badges di Sudut Kiri Atas Banner --}}
                <div class="banner-badges-stack">
                    <div class="badge-nfc-verified">
                        <span class="dot"></span>
                        <span>NFC Terverifikasi</span>
                    </div>
                    <div class="badge-active-mode">
                        <span>{{ $modeBadge['icon'] }}</span>
                        <span>{{ $modeBadge['label'] }}</span>
                    </div>
                </div>
            </header>

            {{-- 2. Avatar Pengguna dengan Ring Emas & Badge NFC --}}
            <div class="profile-avatar-row">
                <div class="profile-avatar-holder">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="profile-avatar-img">
                    @else
                        <div class="profile-avatar-fallback">{{ $initials }}</div>
                    @endif

                    @if ($currentMode === 'kerja')
                        {{-- Speaker Audio Pronounce Badge (Mode Kerja) --}}
                        <button type="button" class="avatar-speaker-badge" title="Dengarkan Pengucapan Nama" onclick="pronounceName('{{ addslashes($displayName) }}')" aria-label="Dengarkan pengucapan nama">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                                <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                            </svg>
                        </button>
                    @else
                        {{-- Badge NFC Gelombang di Sudut Kanan Bawah Avatar --}}
                        <div class="avatar-nfc-badge" title="NFC Tag Aktif">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
                                <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
                                <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                                <circle cx="12" cy="20" r="1" fill="currentColor"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 3. Identitas Pengguna --}}
            <section class="profile-identity-section">
                <div class="profile-name-row">
                    <h1 class="profile-full-name">{{ $displayName }}</h1>
                    {{-- Verified Gold Rosette Badge --}}
                    <svg class="verified-gold-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Terverifikasi">
                        <path d="M12 2L14.7 4.5L18.3 4.8L19.5 8.2L22.5 10.3L21.7 13.9L23 17.3L19.7 18.9L18.1 22.2L14.5 21.8L12 24L9.5 21.8L5.9 22.2L4.3 18.9L1 17.3L2.3 13.9L1.5 10.3L4.5 8.2L5.7 4.8L9.3 4.5L12 2Z" fill="#E5B239"/>
                        <path d="M8.5 12.2L10.8 14.5L15.5 9.8" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                {{-- Pill Edisi Pusaka --}}
                <div>
                    <span class="profile-edition-pill">• {{ $editionName }} •</span>
                </div>

                {{-- Headline --}}
                <p class="profile-headline">{{ $headline }}</p>

                {{-- Deskripsi Bio (Khusus Mode Kerja) --}}
                @if ($bioText)
                    <p class="profile-bio-text">{{ $bioText }}</p>
                @endif

                {{-- Angkatan & Lokasi --}}
                <div class="profile-location-cohort">
                    <span class="loc-pin">📍</span>
                    <span>{{ $locationText }}</span>
                </div>
            </section>

            {{-- 4. Tombol Aksi Utama --}}
            <section class="profile-primary-actions">
                <a href="{{ route('public.tag.vcard', $tag->tag_code) }}" class="btn-save-contact" id="btnSaveContact">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/>
                        <line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                    <span>Simpan Kontak</span>
                </a>

                @if ($currentMode === 'kerja')
                    <a href="{{ route('public.tag.cv', $tag->tag_code) }}" class="btn-download-cv" id="btnDownloadCv">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span>Unduh CV (PDF)</span>
                    </a>
                @else
                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode('Halo ' . $displayName . ', saya menemukan kontak Anda melalui FlexiTag.') }}" 
                       target="_blank" rel="noopener" class="btn-chat-wa" id="btnChatWa">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                        <span>Chat WhatsApp</span>
                    </a>
                @endif
            </section>

            {{-- 5. Kartu Kontak Cepat (WhatsApp & Email) --}}
            @if (($privacy?->show_phone ?? true) || ($privacy?->show_email ?? true))
                <section class="profile-contact-card">
                    @if ($privacy?->show_phone ?? true)
                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" rel="noopener" class="contact-row-item">
                            <div class="contact-icon-circle">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                            </div>
                            <div class="contact-info-col">
                                <span class="contact-label-sub">{{ $phoneLabel }}</span>
                                <span class="contact-value-main">{{ $phone }}</span>
                            </div>
                            <span class="contact-chevron">›</span>
                        </a>
                    @endif

                    @if (($privacy?->show_phone ?? true) && ($privacy?->show_email ?? true))
                        <div class="contact-card-divider"></div>
                    @endif

                    @if ($privacy?->show_email ?? true)
                        <a href="mailto:{{ $email }}" class="contact-row-item">
                            <div class="contact-icon-circle">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="4"/>
                                    <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"/>
                                </svg>
                            </div>
                            <div class="contact-info-col">
                                <span class="contact-label-sub">{{ $emailLabel }}</span>
                                <span class="contact-value-main">{{ $email }}</span>
                            </div>
                            <span class="contact-chevron">›</span>
                        </a>
                    @endif
                </section>
            @endif

            {{-- 6. Seksi Jejaring & Daftar Tautan --}}
            <section class="profile-links-section">
                <div class="links-header-row">
                    <div class="links-header-title">
                        @if ($currentMode === 'kerja')
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E5B239" stroke-width="2.3" style="flex-shrink: 0;" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"/>
                                <circle cx="12" cy="12" r="5" fill="#E5B239"/>
                            </svg>
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#E5B239" style="flex-shrink: 0;" aria-hidden="true">
                                <path d="M19 4h-2V3a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v1H5a3 3 0 0 0-3 3v2a5 5 0 0 0 4.1 4.9A7.002 7.002 0 0 0 11 17.92V20H8a1 1 0 0 0 0 2h8a1 1 0 0 0 0-2h-3v-2.08a7.002 7.002 0 0 0 4.9-4.02A5 5 0 0 0 22 9V7a3 3 0 0 0-3-3zM4 9V7a1 1 0 0 1 1-1h2v4.18A3.003 3.003 0 0 1 4 9zm16 0a3.003 3.003 0 0 1-3 1.18V6h2a1 1 0 0 1 1 1v2z"/>
                            </svg>
                        @endif
                        <span>{{ $modeBadge['section_title'] }}</span>
                    </div>
                @php
                    if ($currentMode === 'kerja') {
                        $kerjaLinks = $modeData['links'] ?? null;
                        if (!$kerjaLinks && $links->count() > 0) {
                            $kerjaLinks = $links->map(function($l) use ($tag) {
                                $hasPipe = str_contains($l->label, '|');
                                return [
                                    'type' => $l->type,
                                    'title' => $hasPipe ? trim(explode('|', $l->label)[0]) : $l->label,
                                    'subtitle' => $hasPipe ? trim(explode('|', $l->label)[1]) : match($l->type) {
                                        'linkedin' => 'Rekam jejak karir, pengalaman, & keahlian',
                                        'github', 'code' => 'Source code eksperimen embedded & web',
                                        'behance' => 'Studi kasus perancangan antarmuka sistem',
                                        default => 'Koleksi aplikasi web, mobile, & riset IoT',
                                    },
                                    'url' => route('public.tag.link', ['tag_code' => $tag->tag_code, 'link_id' => $l->id]),
                                ];
                            })->toArray();
                        }
                        if (!$kerjaLinks && (!empty($modeData['linkedin_url']) || !empty($modeData['github_url']))) {
                            $kerjaLinks = [];
                            if (!empty($modeData['portofolio_url'])) {
                                $kerjaLinks[] = [
                                    'type' => 'portfolio',
                                    'title' => 'Lihat Portofolio Digital',
                                    'subtitle' => 'Koleksi aplikasi web, mobile, & riset IoT',
                                    'url' => $modeData['portofolio_url'],
                                ];
                            }
                            if (!empty($modeData['linkedin_url'])) {
                                $kerjaLinks[] = [
                                    'type' => 'linkedin',
                                    'title' => 'LinkedIn Profesional',
                                    'subtitle' => 'Rekam jejak karir, pengalaman, & keahlian',
                                    'url' => $modeData['linkedin_url'],
                                ];
                            }
                            if (!empty($modeData['github_url'])) {
                                $kerjaLinks[] = [
                                    'type' => 'github',
                                    'title' => 'GitHub & Proyek Open Source',
                                    'subtitle' => 'Source code eksperimen embedded & web',
                                    'url' => $modeData['github_url'],
                                ];
                            }
                            if (!empty($modeData['behance_url'])) {
                                $kerjaLinks[] = [
                                    'type' => 'behance',
                                    'title' => 'Behance & Galeri Desain UI',
                                    'subtitle' => 'Studi kasus perancangan antarmuka sistem',
                                    'url' => $modeData['behance_url'],
                                ];
                            }
                        }
                        if (empty($kerjaLinks)) {
                            $isAndi = str_contains(strtolower($displayName), 'andi');
                            $kerjaLinks = [
                                [
                                    'type' => 'portfolio',
                                    'title' => 'Lihat Portofolio Digital',
                                    'subtitle' => 'Koleksi aplikasi web, mobile, & riset IoT',
                                    'url' => $isAndi ? 'https://andimuhammad.dev' : '#',
                                ],
                                [
                                    'type' => 'linkedin',
                                    'title' => 'LinkedIn Profesional',
                                    'subtitle' => 'Rekam jejak karir, pengalaman, & keahlian',
                                    'url' => $isAndi ? 'https://linkedin.com/in/andimuhammad' : 'https://linkedin.com',
                                ],
                                [
                                    'type' => 'github',
                                    'title' => 'GitHub & Proyek Open Source',
                                    'subtitle' => 'Source code eksperimen embedded & web',
                                    'url' => $isAndi ? 'https://github.com/andimuhammad' : 'https://github.com',
                                ],
                                [
                                    'type' => 'behance',
                                    'title' => 'Behance & Galeri Desain UI',
                                    'subtitle' => 'Studi kasus perancangan antarmuka sistem',
                                    'url' => $isAndi ? 'https://behance.net/andimuhammad' : 'https://behance.net',
                                ],
                            ];
                        }
                        $totalActiveLinks = count($kerjaLinks);
                    } else {
                        $totalActiveLinks = $links->count() > 0 ? $links->count() : 4;
                    }
                @endphp

                <div class="links-header-row">
                    <div class="links-header-title">
                        @if ($currentMode === 'kerja')
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E5B239" stroke-width="2.3" style="flex-shrink: 0;" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"/>
                                <circle cx="12" cy="12" r="5" fill="#E5B239"/>
                            </svg>
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#E5B239" style="flex-shrink: 0;" aria-hidden="true">
                                <path d="M19 4h-2V3a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v1H5a3 3 0 0 0-3 3v2a5 5 0 0 0 4.1 4.9A7.002 7.002 0 0 0 11 17.92V20H8a1 1 0 0 0 0 2h8a1 1 0 0 0 0-2h-3v-2.08a7.002 7.002 0 0 0 4.9-4.02A5 5 0 0 0 22 9V7a3 3 0 0 0-3-3zM4 9V7a1 1 0 0 1 1-1h2v4.18A3.003 3.003 0 0 1 4 9zm16 0a3.003 3.003 0 0 1-3 1.18V6h2a1 1 0 0 1 1 1v2z"/>
                            </svg>
                        @endif
                        <span>{{ $modeBadge['section_title'] }}</span>
                    </div>
                    <span class="links-count-pill">{{ $totalActiveLinks }} Tautan Aktif</span>
                </div>

                <div class="links-cards-stack">
                    @if ($currentMode === 'kerja')
                        @foreach ($kerjaLinks as $kLink)
                            @php
                                $type = $kLink['type'] ?? 'portfolio';
                                $boxClass = match($type) {
                                    'portfolio' => 'icon-box-portfolio',
                                    'linkedin' => 'icon-box-linkedin-dark',
                                    'github', 'code' => 'icon-box-github',
                                    'behance' => 'icon-box-behance',
                                    default => 'icon-box-default',
                                };
                            @endphp
                            <a href="{{ $kLink['url'] ?? '#' }}" target="_blank" rel="noopener" class="link-card-item">
                                <div class="link-icon-box {{ $boxClass }}">
                                    @if ($type === 'portfolio')
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                                            <line x1="12" y1="11" x2="12" y2="17"></line>
                                            <line x1="9" y1="14" x2="15" y2="14"></line>
                                        </svg>
                                    @elseif ($type === 'linkedin')
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                        </svg>
                                    @elseif ($type === 'github' || $type === 'code')
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="3" width="20" height="18" rx="3"></rect>
                                            <path d="M8 10l3 3-3 3"></path>
                                            <line x1="13" y1="16" x2="16" y2="16"></line>
                                        </svg>
                                    @elseif ($type === 'behance')
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M7.8 11.2c1.3-.3 2.1-1.2 2.1-2.4 0-1.8-1.5-2.8-3.7-2.8H1.5v12h4.9c2.5 0 4.1-1.3 4.1-3.4 0-1.7-1.1-2.9-2.7-3.4zm-3.6-3h2c1 0 1.6.4 1.6 1.2 0 .9-.6 1.3-1.6 1.3h-2V8.2zm2.2 7.6h-2.2v-2.8h2.2c1.1 0 1.8.5 1.8 1.4 0 1-.7 1.4-1.8 1.4zm10.7-3.8c-2.4 0-4.1 1.7-4.1 4.1 0 2.5 1.7 4.1 4.2 4.1 1.9 0 3.3-1 3.8-2.6h-2.1c-.3.6-.9.9-1.7.9-1.1 0-1.9-.7-2-1.9h6.1c0-.2.1-.5.1-.7 0-2.4-1.7-3.9-4.3-3.9zm-2 3c.2-.9.8-1.5 1.9-1.5 1 0 1.6.6 1.8 1.5h-3.7zm1.1-4.8h3v1.2h-3v-1.2z"/>
                                        </svg>
                                    @else
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="2" y1="12" x2="22" y2="12"/>
                                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="link-text-col">
                                    <span class="link-title">{{ $kLink['title'] ?? '' }}</span>
                                    <span class="link-subtitle">{{ $kLink['subtitle'] ?? '' }}</span>
                                </div>
                                <span class="link-chevron-arrow">›</span>
                            </a>
                        @endforeach
                    @else
                        {{-- Mode Kampus & Lainnya --}}
                        @forelse ($links as $link)
                        @php
                            // Parsing label & subtitle (contoh "HIMA Teknik Komputer UNM|Pengurus Divisi Riset")
                            $hasPipe = str_contains($link->label, '|');
                            $linkTitle = $hasPipe ? trim(explode('|', $link->label)[0]) : $link->label;
                            $linkSubtitle = $hasPipe ? trim(explode('|', $link->label)[1]) : match($link->type) {
                                'academic', 'organization' => 'Pengurus Divisi Riset & Teknologi',
                                'linkedin' => 'Jejaring akademik, lomba, & sertifikasi',
                                'instagram' => 'Aktivitas kampus & dokumentasi kegiatan',
                                'github', 'code' => 'Kumpulan laporan riset IoT & sistem tertanam',
                                'whatsapp' => 'Hubungi langsung melalui WhatsApp',
                                default => 'Tautan profil terverifikasi',
                            };

                            // Styling kelas icon box
                            $iconBoxClass = match($link->type) {
                                'academic', 'organization' => 'icon-box-academic',
                                'linkedin' => 'icon-box-linkedin',
                                'instagram' => 'icon-box-instagram',
                                'github', 'code' => 'icon-box-github',
                                default => 'icon-box-default',
                            };
                        @endphp

                        <a href="{{ route('public.tag.link', ['tag_code' => $tag->tag_code, 'link_id' => $link->id]) }}" 
                           target="_blank" rel="noopener" class="link-card-item">
                            <div class="link-icon-box {{ $iconBoxClass }}">
                                @if (in_array($link->type, ['academic', 'organization']))
                                    {{-- Graduation Cap SVG --}}
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                    </svg>
                                @elseif ($link->type === 'linkedin')
                                    {{-- LinkedIn In SVG --}}
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                    </svg>
                                @elseif ($link->type === 'instagram')
                                    {{-- Instagram Camera Outline SVG --}}
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                                    </svg>
                                @elseif (in_array($link->type, ['github', 'code']))
                                    {{-- Code Brackets SVG --}}
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="16 18 22 12 16 6"/>
                                        <polyline points="8 6 2 12 8 18"/>
                                    </svg>
                                @else
                                    {{-- Default Globe / Link SVG --}}
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="2" y1="12" x2="22" y2="12"/>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="link-text-col">
                                <span class="link-title">{{ $linkTitle }}</span>
                                <span class="link-subtitle">{{ $linkSubtitle }}</span>
                            </div>

                            <span class="link-chevron-arrow">›</span>
                        </a>
                    @empty
                        {{-- Fallback default 4 tautan aktivitas kampus sesuai referensi desain --}}
                        <div class="link-card-item">
                            <div class="link-icon-box icon-box-academic">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                </svg>
                            </div>
                            <div class="link-text-col">
                                <span class="link-title">HIMA Teknik Komputer UNM</span>
                                <span class="link-subtitle">Pengurus Divisi Riset &amp; Teknologi</span>
                            </div>
                            <span class="link-chevron-arrow">›</span>
                        </div>

                        <div class="link-card-item">
                            <div class="link-icon-box icon-box-linkedin">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </div>
                            <div class="link-text-col">
                                <span class="link-title">LinkedIn Mahasiswa</span>
                                <span class="link-subtitle">Jejaring akademik, lomba, &amp; sertifikasi</span>
                            </div>
                            <span class="link-chevron-arrow">›</span>
                        </div>

                        <div class="link-card-item">
                            <div class="link-icon-box icon-box-instagram">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                                </svg>
                            </div>
                            <div class="link-text-col">
                                <span class="link-title">Instagram (@andimuh_)</span>
                                <span class="link-subtitle">Aktivitas kampus &amp; dokumentasi kegiatan</span>
                            </div>
                            <span class="link-chevron-arrow">›</span>
                        </div>

                        <div class="link-card-item">
                            <div class="link-icon-box icon-box-github">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="16 18 22 12 16 6"/>
                                    <polyline points="8 6 2 12 8 18"/>
                                </svg>
                            </div>
                            <div class="link-text-col">
                                <span class="link-title">Proyek Kuliah &amp; Tugas Akhir</span>
                                <span class="link-subtitle">Kumpulan laporan riset IoT &amp; sistem tertanam</span>
                            </div>
                            <span class="link-chevron-arrow">›</span>
                        </div>
                    @endforelse
                    @endif
                </div>
            </section>

            {{-- 7. Tombol Tampilkan QR Tap & Bagikan --}}
            <section class="profile-share-wrap">
                <button type="button" class="btn-show-qr" id="btnOpenQrModal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2"/>
                        <path d="M17 3h2a2 2 0 0 1 2 2v2"/>
                        <path d="M21 17v2a2 2 0 0 1-2 2h-2"/>
                        <path d="M7 21H5a2 2 0 0 1-2-2v-2"/>
                        <rect x="7" y="7" width="3" height="3"/>
                        <rect x="14" y="7" width="3" height="3"/>
                        <rect x="7" y="14" width="3" height="3"/>
                        <rect x="14" y="14" width="3" height="3"/>
                    </svg>
                    <span>Tampilkan QR Tap &amp; Bagikan</span>
                </button>
            </section>

            {{-- 8. Footer (Karya Digital • Berjiwa Nusantara) --}}
            <footer class="profile-footer-section">
                <div class="foot-ornament-row">
                    <span></span>
                    <i>◆</i>
                    <span></span>
                </div>

                <div class="footer-brand-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
                        <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
                        <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                        <circle cx="12" cy="20" r="1" fill="currentColor"/>
                    </svg>
                    <span>FlexiTag</span>
                </div>

                <p class="footer-powered-sub">Powered by MaccaTech • Sulawesi Heritage Smart Merchandise</p>

                <div class="footer-security-seal">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span>Enkripsi Kunci Aman Sulawesi Selatan</span>
                </div>
            </footer>
        @endif

    </main>
</div>

{{-- Modal QR Tap & Bagikan --}}
<div class="qr-modal-backdrop" id="qrModal" hidden role="dialog" aria-modal="true" aria-labelledby="qrTitle">
    <div class="qr-modal-card">
        <button type="button" class="qr-modal-close" id="btnCloseQrModal" aria-label="Tutup QR">✕</button>
        <h3 id="qrTitle" style="font-size: 16px; font-weight: 800; color: #500A0E; margin-bottom: 4px;">Pindai QR FlexiTag</h3>
        <p style="font-size: 11.5px; color: #736C63;">Dekatkan ponsel atau pindai kode QR untuk membuka profil digital ini.</p>

        {{-- QR Code Display --}}
        <div class="qr-box-wrap">
            <svg viewBox="0 0 100 100" shape-rendering="crispEdges">
                {{-- Border framing & Corner position squares --}}
                <rect x="0" y="0" width="100" height="100" fill="#FAF8F5"/>
                {{-- Top-Left Position Detection --}}
                <rect x="10" y="10" width="26" height="26" fill="#500A0E" rx="3"/>
                <rect x="14" y="14" width="18" height="18" fill="#FAF8F5"/>
                <rect x="18" y="18" width="10" height="10" fill="#500A0E" rx="2"/>
                {{-- Top-Right Position Detection --}}
                <rect x="64" y="10" width="26" height="26" fill="#500A0E" rx="3"/>
                <rect x="68" y="14" width="18" height="18" fill="#FAF8F5"/>
                <rect x="72" y="18" width="10" height="10" fill="#500A0E" rx="2"/>
                {{-- Bottom-Left Position Detection --}}
                <rect x="10" y="64" width="26" height="26" fill="#500A0E" rx="3"/>
                <rect x="14" y="68" width="18" height="18" fill="#FAF8F5"/>
                <rect x="18" y="72" width="10" height="10" fill="#500A0E" rx="2"/>
                {{-- Dynamic Pattern Blocks --}}
                <rect x="42" y="12" width="6" height="6" fill="#C59418"/>
                <rect x="52" y="12" width="6" height="6" fill="#500A0E"/>
                <rect x="42" y="24" width="6" height="6" fill="#500A0E"/>
                <rect x="52" y="24" width="6" height="6" fill="#C59418"/>
                <rect x="12" y="42" width="6" height="6" fill="#500A0E"/>
                <rect x="24" y="42" width="6" height="6" fill="#C59418"/>
                <rect x="42" y="42" width="16" height="16" fill="#500A0E" rx="3"/>
                <circle cx="50" cy="50" r="4" fill="#E5B239"/>
                <rect x="64" y="42" width="6" height="6" fill="#500A0E"/>
                <rect x="76" y="42" width="6" height="6" fill="#C59418"/>
                <rect x="42" y="64" width="6" height="6" fill="#C59418"/>
                <rect x="52" y="64" width="6" height="6" fill="#500A0E"/>
                <rect x="70" y="64" width="8" height="8" fill="#500A0E"/>
                <rect x="82" y="76" width="8" height="8" fill="#C59418"/>
                <rect x="64" y="82" width="6" height="6" fill="#500A0E"/>
                <rect x="42" y="78" width="6" height="6" fill="#500A0E"/>
                <rect x="52" y="82" width="6" height="6" fill="#C59418"/>
            </svg>
        </div>

        <div style="font-size: 11px; font-weight: 700; color: #500A0E; margin-bottom: 4px;">
            Tag ID: <code>{{ $tag->tag_code }}</code>
        </div>
        <div style="font-size: 10.5px; color: #8C827A; margin-bottom: 14px; word-break: break-all;">
            {{ url('/u/' . $tag->tag_code) }}
        </div>

        <button type="button" class="btn-copy-url" id="btnCopyProfileUrl">
            <span>Salin Tautan Profil</span>
        </button>
    </div>
</div>

{{-- Toast Feedback --}}
<div class="copy-toast" id="copyToast">✓ Tautan profil berhasil disalin!</div>

<script>
    (function () {
        var qrModal = document.getElementById('qrModal');
        var btnOpen = document.getElementById('btnOpenQrModal');
        var btnClose = document.getElementById('btnCloseQrModal');
        var btnCopy = document.getElementById('btnCopyProfileUrl');
        var toast = document.getElementById('copyToast');
        var profileUrl = "{{ url('/u/' . $tag->tag_code) }}";

        if (btnOpen && qrModal) {
            btnOpen.addEventListener('click', function () {
                qrModal.removeAttribute('hidden');
            });
        }

        if (btnClose && qrModal) {
            btnClose.addEventListener('click', function () {
                qrModal.setAttribute('hidden', '');
            });
        }

        if (qrModal) {
            qrModal.addEventListener('click', function (e) {
                if (e.target === qrModal) {
                    qrModal.setAttribute('hidden', '');
                }
            });
        }

        if (btnCopy) {
            btnCopy.addEventListener('click', function () {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(profileUrl).then(showToast);
                } else {
                    var el = document.createElement('textarea');
                    el.value = profileUrl;
                    document.body.appendChild(el);
                    el.select();
                    document.execCommand('copy');
                    document.body.removeChild(el);
                    showToast();
                }
            });
        }

        function showToast() {
            if (!toast) return;
            toast.style.display = 'block';
            setTimeout(function () {
                toast.style.display = 'none';
            }, 2500);
        }
    })();

    function pronounceName(name) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            var utterance = new SpeechSynthesisUtterance(name);
            utterance.lang = 'id-ID';
            utterance.rate = 0.92;
            window.speechSynthesis.speak(utterance);
        }
    }

    function shareCurrentLocation(phone, tagCode) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                var mapsUrl = 'https://maps.google.com/?q=' + lat + ',' + lng;
                var msg = 'Halo, saya menemukan perangkat FlexiTag Anda (' + tagCode + '). Ini koordinat GPS lokasi temuan: ' + mapsUrl;
                window.open('https://wa.me/' + phone + '?text=' + encodeURIComponent(msg), '_blank');
            }, function (error) {
                alert('Tidak dapat mendeteksi lokasi GPS otomatis. Silakan hubungi pemilik secara langsung via WhatsApp.');
            }, { timeout: 10000 });
        } else {
            alert('Browser perangkat tidak mendukung deteksi lokasi otomatis.');
        }
    }

    function openReturnedModal() {
        alert('Terima kasih banyak atas integritas dan kejujuran Anda dalam membantu mengembalikan barang ini kepada pemiliknya!');
    }
</script>

</body>
</html>
