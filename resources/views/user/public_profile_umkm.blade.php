<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{ $umkm['nama_usaha'] ?? $profile?->display_name ?? $user?->name ?? 'Profil UMKM' }} — {{ config('flexitag.brand') }}</title>
    
    {{-- Google Fonts - Strictly Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/flexitag-public-umkm.css') }}">
</head>
<body class="public-umkm-body">

@php
    $namaUsaha = $umkm['nama_usaha'] ?? $profile?->display_name ?? $user?->name ?? 'Usaha UMKM Pilihan';
    $deskripsi = $umkm['deskripsi'] ?? $umkm['tagline'] ?? $profile?->headline ?? 'Menyediakan olahan produk berkualitas warisan nusantara.';
    $alamat = $umkm['alamat_toko'] ?? 'Sulawesi Selatan, Indonesia';
    $rawWa = $umkm['whatsapp_order'] ?? $user?->no_whatsapp ?? '';
    
    // Format WA number
    $cleanWa = preg_replace('/[^0-9]/', '', (string)$rawWa);
    if (\Illuminate\Support\Str::startsWith($cleanWa, '0')) {
        $cleanWa = '62' . substr($cleanWa, 1);
    } elseif (!\Illuminate\Support\Str::startsWith($cleanWa, '62') && !empty($cleanWa)) {
        $cleanWa = '62' . $cleanWa;
    }
    
    $displayWa = !empty($cleanWa) ? '+62 ' . substr($cleanWa, 2, 3) . '-' . substr($cleanWa, 5, 4) . '-' . substr($cleanWa, 9) : '+62 812-4211-6899';
    
    $logoUrl = null;
    if (!empty($umkm['logo_path'])) {
        $logoUrl = \Illuminate\Support\Str::startsWith($umkm['logo_path'], 'http')
            ? $umkm['logo_path']
            : asset('storage/' . $umkm['logo_path']);
    }
    
    $products = is_array($umkm['katalog_produk'] ?? null) ? $umkm['katalog_produk'] : [];
    $productCount = count($products);
    
    // Inisial nama usaha untuk fallback avatar
    $parts = explode(' ', trim($namaUsaha));
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $initials .= mb_substr($p, 0, 1);
    }
    $initials = strtoupper($initials) ?: 'UM';

    // Cari tautan sosmed dari relasi links
    $igLink = $links->firstWhere('type', 'instagram')?->url;
    $tiktokLink = $links->firstWhere('type', 'tiktok')?->url;
    $shopeeLink = $links->firstWhere('type', 'shopee')?->url ?? $links->firstWhere('type', 'tokopedia')?->url;
    
    $slugStore = \Illuminate\Support\Str::slug($namaUsaha, '');
@endphp

<div class="public-umkm-canvas">

    {{-- 1. TOP HEADER (NFC VERIFIED BAR) --}}
    <header class="pub-top-header">
        <div class="pub-status-line-1">
            <div class="pub-status-left">
                <span class="pub-pulse-dot"></span>
                <span>NFC Tap Verified • {{ config('flexitag.brand', 'FlexiTag') }}</span>
            </div>
            <div class="pub-status-right">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>TERHUBUNG</span>
            </div>
        </div>

        <div class="pub-status-line-2">
            <span class="pub-mode-badge">
                <span>🏪</span>
                <span>MODE UMKM AKTIF</span>
            </span>
            <span class="pub-tag-id">
                <span>Smart Tag ID: #{{ $tag->tag_code ?? 'FT-8899' }}</span>
            </span>
        </div>
    </header>

    {{-- 2. STORE HERO CARD (OVERLAPPING) --}}
    <section class="pub-hero-wrap">
        <div class="pub-hero-card">
            <div class="pub-hero-top-row">
                <div class="pub-avatar-container">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $namaUsaha }}" class="pub-avatar-img">
                    @else
                        <div class="pub-avatar-fallback">
                            <span>{{ $initials }}</span>
                        </div>
                    @endif
                    <div class="pub-verified-badge" title="Toko Terverifikasi FlexiTag">✓</div>
                </div>

                <div class="pub-hero-meta-right">
                    <div class="pub-rating-badge">
                        <span>⭐</span>
                        <span>4.9 (350+ Ulasan)</span>
                    </div>
                    <div class="pub-open-status">
                        <span class="pub-green-dot"></span>
                        <span>Buka Sekarang (08.00 - 21.30 WITA)</span>
                    </div>
                </div>
            </div>

            <span class="pub-category-pill">
                {{ $umkm['kategori'] ?? 'KULINER & OLEH-OLEH KHAS MAKASSAR' }}
            </span>

            <h1 class="pub-store-name">{{ $namaUsaha }}</h1>
            <p class="pub-store-desc">{{ $deskripsi }}</p>

            {{-- 3-Column Store Highlights --}}
            <div class="pub-perks-grid">
                <div class="pub-perk-item">
                    <span class="pub-perk-label">Sertifikasi</span>
                    <strong class="pub-perk-val">100% Halal</strong>
                </div>
                <div class="pub-perk-item">
                    <span class="pub-perk-label">Pengiriman</span>
                    <strong class="pub-perk-val">Instan &amp; Luar Kota</strong>
                </div>
                <div class="pub-perk-item">
                    <span class="pub-perk-label">Kemasan</span>
                    <strong class="pub-perk-val">Vacuum Safe</strong>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. CALL TO ACTION BUTTONS --}}
    <section class="pub-cta-section">
        {{-- Large WhatsApp Order Button --}}
        <a href="https://wa.me/{{ $cleanWa ?: '6281242116899' }}?text={{ urlencode('Halo ' . $namaUsaha . ', saya terhubung melalui gantungan kunci FlexiTag NFC Anda dan tertarik dengan produk Anda.') }}"
           target="_blank"
           rel="noopener"
           class="btn-wa-order">
            <div class="btn-wa-left">
                <div class="wa-clock-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                    </svg>
                </div>
                <div class="wa-text-wrap">
                    <span class="wa-title">Hubungi &amp; Pesan via WhatsApp</span>
                    <span class="wa-subtitle">Respon cepat • Terhubung langsung dengan owner</span>
                </div>
            </div>
            <div class="wa-arrow-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </div>
        </a>

        {{-- Share Profile & Save Contact Row --}}
        <div class="pub-subactions-row">
            <button type="button" class="btn-subaction" onclick="shareProfile()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="18" cy="5" r="3"></circle>
                    <circle cx="6" cy="12" r="3"></circle>
                    <circle cx="18" cy="19" r="3"></circle>
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                </svg>
                <span id="shareBtnText">Bagikan Profil</span>
            </button>

            <button type="button" class="btn-subaction" onclick="downloadVCard()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                <span>Simpan Kontak</span>
            </button>
        </div>
    </section>

    {{-- 4. KATALOG PRODUK PILIHAN --}}
    <section class="pub-catalog-section">
        <div class="pub-section-header">
            <div class="pub-section-title-wrap">
                <h2 class="pub-section-title">Katalog Produk Pilihan</h2>
                <span class="pub-section-sub">Pesan langsung dari gawai Anda</span>
            </div>
            <span class="pub-count-badge">{{ $productCount }} Menu Utama</span>
        </div>

        {{-- Filter Pills --}}
        <div class="pub-filter-scroll-wrap">
            <button type="button" class="btn-filter-pill is-active" onclick="filterCatalog('all', this)">Semua Produk</button>
            <button type="button" class="btn-filter-pill" onclick="filterCatalog('unggulan', this)">⭐ Unggulan</button>
            <button type="button" class="btn-filter-pill" onclick="filterCatalog('laris', this)">🔥 Paling Laris</button>
        </div>

        {{-- Product Cards List --}}
        <div class="pub-products-list" id="productsCatalogContainer">
            @forelse ($products as $prod)
                @php
                    $pNama = $prod['nama'] ?? 'Menu Kuliner';
                    $pHarga = (int)($prod['harga'] ?? 0);
                    $pSatuan = $prod['satuan'] ?? 'porsi';
                    $pDesc = $prod['deskripsi'] ?? '';
                    $pLabel = $prod['label'] ?? '';
                    $pFoto = $prod['foto_url'] ?? asset('images/umkm/bandeng_tanpa_duri.jpg');
                    
                    $badgeClass = 'pub-badge-gold';
                    if ($pLabel === 'Paling Laris') $badgeClass = 'pub-badge-red';
                    elseif ($pLabel === 'Heritage') $badgeClass = 'pub-badge-heritage';
                    
                    $filterTag = 'all';
                    if ($pLabel === 'Unggulan') $filterTag .= ' unggulan';
                    if ($pLabel === 'Paling Laris') $filterTag .= ' laris';
                    
                    $orderText = urlencode("Halo {$namaUsaha}, saya ingin memesan: {$pNama} (Rp " . number_format($pHarga, 0, ',', '.') . "/{$pSatuan}) melalui FlexiTag NFC.");
                @endphp
                <div class="pub-prod-card" data-tags="{{ $filterTag }}">
                    <div class="pub-prod-thumb-wrap">
                        <img src="{{ $pFoto }}" alt="{{ $pNama }}" onerror="this.src='{{ asset('images/umkm/bandeng_tanpa_duri.jpg') }}'">
                        @if ($pLabel)
                            <span class="pub-prod-tag-pill {{ $badgeClass }}">
                                <span>{{ $prod['badge_icon'] ?? '✨' }}</span>
                                <span>{{ $pLabel }}</span>
                            </span>
                        @endif
                    </div>
                    <div class="pub-prod-content">
                        <div>
                            <h3 class="pub-prod-title">{{ $pNama }}</h3>
                            @if ($pDesc)
                                <p class="pub-prod-desc">{{ $pDesc }}</p>
                            @endif
                        </div>
                        <div class="pub-prod-bottom-row">
                            <div class="pub-prod-price-wrap">
                                <span class="pub-price-lbl">Harga</span>
                                <div>
                                    <span class="pub-price-amt">Rp {{ number_format($pHarga, 0, ',', '.') }}</span>
                                    <span class="pub-price-unit">/{{ $pSatuan }}</span>
                                </div>
                            </div>
                            <a href="https://wa.me/{{ $cleanWa ?: '6281242116899' }}?text={{ $orderText }}"
                               target="_blank"
                               rel="noopener"
                               class="btn-prod-order-pill">
                                <span>Pesan</span>
                                <span>▷</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div style="background: #FFFFFF; border: 1.5px dashed var(--pub-border); border-radius: var(--pub-radius-lg); padding: 2rem 1rem; text-align: center; color: var(--pub-text-muted); font-size: 0.82rem;">
                    <span style="font-size: 2rem; display: block; margin-bottom: 0.4rem;">🏪</span>
                    <strong style="color: var(--pub-maroon); font-size: 0.95rem; display: block; margin-bottom: 0.2rem;">Katalog Sedang Disiapkan</strong>
                    <span>Pemilik usaha sedang memperbarui daftar menu. Silakan hubungi langsung via WhatsApp di atas untuk informasi ketersediaan produk.</span>
                </div>
            @endforelse
        </div>
    </section>

    {{-- 5. INFORMASI TOKO & JAM BUKA --}}
    <section class="pub-info-section">
        <div class="pub-info-card">
            <h3 class="pub-info-card-title">
                <span>🏪</span>
                <span>Informasi Toko &amp; Jam Buka</span>
            </h3>

            {{-- Alamat Fisik --}}
            <div class="pub-address-block">
                <div class="pub-pin-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                <div>
                    <h4 class="pub-address-title">Alamat Fisik / Kedai Utama</h4>
                    <p class="pub-address-text">{{ $alamat }}</p>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($alamat) }}"
                       target="_blank"
                       rel="noopener"
                       class="btn-maps-link">
                        <span>🗺️</span>
                        <span>Buka Petunjuk di Google Maps</span>
                    </a>
                </div>
            </div>

            {{-- 2 Columns Jam Layanan & Kontak --}}
            <div class="pub-info-2col">
                <div class="pub-col-item">
                    <div class="pub-col-header">
                        <span>🕒</span>
                        <span>Jam Layanan</span>
                    </div>
                    <span class="pub-col-sub">Senin – Minggu</span>
                    <strong class="pub-col-val">08.00 - 21.30 WITA</strong>
                </div>

                <div class="pub-col-item">
                    <div class="pub-col-header">
                        <span>📞</span>
                        <span>Kontak Pesanan</span>
                    </div>
                    <span class="pub-col-sub">WhatsApp &amp; Telp</span>
                    <strong class="pub-col-val">{{ $displayWa }}</strong>
                </div>
            </div>

            {{-- Metode Pembayaran Diterima --}}
            <div>
                <h5 class="pub-pay-title">Metode Pembayaran Diterima</h5>
                <div class="pub-pay-badges-wrap">
                    <span class="pub-pay-badge is-qris">💳 QRIS (Semua E-Wallet)</span>
                    <span class="pub-pay-badge">BCA</span>
                    <span class="pub-pay-badge">BRI</span>
                    <span class="pub-pay-badge">Mandiri</span>
                    <span class="pub-pay-badge">Tunai / COD</span>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. KUNJUNGI SALURAN RESMI KAMI --}}
    <section class="pub-social-section">
        <h4 class="pub-social-title">Kunjungi Saluran Resmi Kami</h4>
        <div class="pub-social-grid">
            <a href="{{ $igLink ?: 'https://instagram.com/' . $slugStore }}" target="_blank" rel="noopener" class="pub-social-card">
                <div class="pub-social-icon-box">📸</div>
                <strong class="pub-social-name">Instagram</strong>
                <span class="pub-social-handle">{{ '@' . ($slugStore ?: 'tokoumkm') }}</span>
            </a>

            <a href="{{ $tiktokLink ?: 'https://tiktok.com/@' . $slugStore }}" target="_blank" rel="noopener" class="pub-social-card">
                <div class="pub-social-icon-box">🎵</div>
                <strong class="pub-social-name">TikTok</strong>
                <span class="pub-social-handle">{{ '@' . ($slugStore ?: 'tokoumkm') }}</span>
            </a>

            <a href="{{ $shopeeLink ?: 'https://shopee.co.id/search?keyword=' . urlencode($namaUsaha) }}" target="_blank" rel="noopener" class="pub-social-card">
                <div class="pub-social-icon-box">🛍️</div>
                <strong class="pub-social-name">Shopee Mall</strong>
                <span class="pub-social-handle">Toko Resmi</span>
            </a>
        </div>
    </section>

    {{-- 7. FOOTER --}}
    <footer class="pub-footer">
        <div class="pub-footer-pill">
            <span>📱</span>
            <span>Powered by FlexiTag</span>
        </div>
        <p class="pub-footer-text-1">Smart NFC Merchandise • Edisi Pelaku UMKM Sulawesi</p>
        <p class="pub-footer-text-2">
            Tidak memerlukan aplikasi tambahan (Zero App Required). Tempel &amp; Terhubung langsung.
        </p>
        <div style="margin-top: 14px;">
            <button type="button" onclick="openAbuseModal()" style="background: none; border: none; font-size: 11px; color: #8C827A; text-decoration: underline; cursor: pointer; padding: 4px 8px; font-family: inherit;">
                Laporkan Penyalahgunaan Profil
            </button>
        </div>
    </footer>

</div>

{{-- Modal Laporkan Penyalahgunaan Profil (FR-PUB-12) --}}
<div id="modalAbuseUmkm" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.65); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div style="width: 100%; max-width: 400px; background: #FFFFFF; border-radius: 24px; padding: 22px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25); text-align: left; font-family: 'Poppins', sans-serif;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 22px;">⚠️</span>
                <div>
                    <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: #991B1B;">Laporkan Penyalahgunaan</h3>
                    <span style="font-size: 11px; color: #78716C;">Laporan ditinjau langsung oleh admin</span>
                </div>
            </div>
            <button type="button" onclick="closeAbuseModal()" style="background: none; border: none; font-size: 16px; color: #9CA3AF; cursor: pointer; padding: 2px 6px;">✕</button>
        </div>

        <form action="{{ route('public.tag.abuse_report', $tag->tag_code) }}" method="POST">
            @csrf
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 4px;">Nama Anda (Opsional)</label>
                <input type="text" name="sender_name" style="width: 100%; box-sizing: border-box; background: #FAF8F5; border: 1.5px solid #E5E1DA; border-radius: 12px; padding: 9px 12px; font-size: 12px; outline: none;" placeholder="Contoh: Pengunjung Peduli" maxlength="100">
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 4px;">Rincian Pelanggaran <span style="color: #DC2626;">*</span></label>
                <textarea name="message" style="width: 100%; box-sizing: border-box; background: #FAF8F5; border: 1.5px solid #E5E1DA; border-radius: 12px; padding: 9px 12px; font-size: 12px; resize: vertical; min-height: 70px; outline: none;" placeholder="Jelaskan alasan pelaporan..." rows="3" maxlength="1000" required></textarea>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: #374151; margin-bottom: 4px;">Kontak / Email Anda (Opsional)</label>
                <input type="text" name="contact" style="width: 100%; box-sizing: border-box; background: #FAF8F5; border: 1.5px solid #E5E1DA; border-radius: 12px; padding: 9px 12px; font-size: 12px; outline: none;" placeholder="Contoh: email@anda.com" maxlength="100">
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px;">
                <button type="submit" style="width: 100%; background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%); color: #FFFFFF; border: none; border-radius: 14px; padding: 12px; font-size: 13px; font-weight: 750; cursor: pointer; font-family: inherit;">
                    Kirim Laporan ke Admin
                </button>
                <button type="button" onclick="closeAbuseModal()" style="background: none; border: none; color: #9CA3AF; font-size: 11.5px; font-weight: 600; cursor: pointer; padding: 6px; font-family: inherit;">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // 1. Filter Katalog Produk berdasarkan Kategori / Label
    function filterCatalog(tag, btn) {
        document.querySelectorAll('.btn-filter-pill').forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');

        const cards = document.querySelectorAll('.pub-prod-card');
        cards.forEach(card => {
            const cardTags = card.getAttribute('data-tags') || '';
            if (tag === 'all' || cardTags.includes(tag)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // 2. Fitur Bagikan Profil (Native Share / Salin URL)
    function shareProfile() {
        const shareData = {
            title: "{{ addslashes($namaUsaha) }}",
            text: "{{ addslashes($deskripsi) }}",
            url: window.location.href
        };

        if (navigator.share) {
            navigator.share(shareData).catch(() => {});
        } else {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const btnText = document.getElementById('shareBtnText');
                const orig = btnText.textContent;
                btnText.textContent = 'Tersalin! ✓';
                setTimeout(() => { btnText.textContent = orig; }, 2000);
            }).catch(() => {
                alert('Tautan profil: ' + window.location.href);
            });
        }
    }

    // 3. Simpan Kontak vCard (.vcf)
    function downloadVCard() {
        const vcard = "BEGIN:VCARD\r\n" +
            "VERSION:3.0\r\n" +
            "FN:{{ addslashes($namaUsaha) }}\r\n" +
            "ORG:{{ addslashes($namaUsaha) }}\r\n" +
            "TITLE:Pelaku UMKM FlexiTag\r\n" +
            "TEL;TYPE=CELL,VOICE:+{{ $cleanWa ?: '6281242116899' }}\r\n" +
            "NOTE:{{ addslashes($deskripsi) }}\r\n" +
            "URL:" + window.location.href + "\r\n" +
            "END:VCARD";

        const blob = new Blob([vcard], { type: 'text/vcard;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = "{{ \Illuminate\Support\Str::slug($namaUsaha) }}.vcf";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    function openAbuseModal() {
        const modal = document.getElementById('modalAbuseUmkm');
        if (modal) modal.style.display = 'flex';
    }

    function closeAbuseModal() {
        const modal = document.getElementById('modalAbuseUmkm');
        if (modal) modal.style.display = 'none';
    }
</script>

</body>
</html>
