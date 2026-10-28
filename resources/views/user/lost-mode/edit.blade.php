<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Profil (Mode Hilang) — FlexiTag</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite([
        'resources/css/pages/user-lost-mode.css',
        'resources/js/user-lost-mode.js'
    ])
</head>
<body class="lost-mode-body">

<div class="lost-mode-viewport-wrap">

    {{-- ========================================================================= --}}
    {{-- 1. TOP APP BAR (KONSISTEN DENGAN EDIT PROFIL UMKM)                         --}}
    {{-- ========================================================================= --}}
    <header class="umkm-topbar">
        <a href="{{ route('user.dashboard') }}" class="topbar-back-btn" aria-label="Kembali ke Dashboard">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>

        <div class="topbar-center-heading">
            <h1 class="topbar-center-title">Edit Profil (Mode Hilang)</h1>
            <span class="topbar-center-sub">• PROTEKSI PRIVASI AKTIF • {{ $primaryTag?->tag_code ?? 'TAG' }}</span>
        </div>

        <span class="topbar-mode-badge" style="background: rgba(229, 179, 42, 0.2); border: 1px solid rgba(229, 179, 42, 0.4); color: #F5BA41;">
            <span>🛡️</span>
            <span>HILANG</span>
        </span>
    </header>

    {{-- ========================================================================= --}}
    {{-- 2. MAIN LOST MODE CONTENT AREA                                            --}}
    {{-- ========================================================================= --}}
    <main class="lost-mode-content">

        {{-- Flash Feedback Alerts --}}
        @if (session('status'))
            <div style="background: #EAF5EC; border: 1px solid #C8E6C9; color: #2E7D32; padding: 0.75rem 0.9rem; border-radius: 12px; font-size: 0.76rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;" role="status">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div style="background: #FEE2E2; border: 1px solid #FECACA; color: #991B1B; padding: 0.75rem 0.9rem; border-radius: 12px; font-size: 0.76rem; font-weight: 600;" role="alert">
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Card 1: Hardware/Tag Card Status --}}
        <section class="hardware-info-card" aria-label="Informasi Tag">
            <div class="hardware-pattern-right" aria-hidden="true"></div>

            <div class="hardware-header-row">
                <div class="hardware-nfc-icon-box" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                        <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                        <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                        <circle cx="12" cy="20" r="1"></circle>
                    </svg>
                </div>

                <div class="hardware-title-col">
                    <h3 class="hardware-tag-name">
                        {{ $primaryTag ? 'Gantungan Kunci Pintar' : 'Tag FlexiTag' }}
                    </h3>
                    <span class="hardware-tag-id">
                        ID: {{ $primaryTag?->tag_code ?? '-' }}
                    </span>
                </div>

                <span class="hardware-status-badge">
                    <span>⚠️</span>
                    <span>Mode Hilang Siaga</span>
                </span>
            </div>
        </section>

        {{-- Card 2: Privasi Terlindungi Callout --}}
        <section class="privacy-overlap-card" aria-label="Informasi Privasi">
            <div class="privacy-card-header">
                <div class="privacy-lock-box" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="privacy-title-text">Privasi Terlindungi</h2>
                    <span style="font-size: 0.65rem; color: #8C6D23; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;">PROTEKSI PRIVASI AKTIF • UU PDP</span>
                </div>
            </div>

            <p class="privacy-desc-text">
                Nomor kontak pribadi pemilik tidak ditampilkan langsung kepada penemu barang. Pesan penemu diteruskan secara aman melalui sistem relay cloud MaccaTech.
            </p>

            <div class="privacy-bottom-pill">
                <span>🔒</span>
                <span>Relay Pesan Aman</span>
            </div>
        </section>

        {{-- FORMULIR PENGATURAN MODE HILANG --}}
        <form method="POST" action="{{ route('user.lost-mode.update') }}" id="lostModeForm">
            @csrf

            {{-- Card 3: Formulir Informasi Darurat --}}
            <section class="emergency-form-card" aria-label="Formulir Darurat">
                <div class="form-heading-row">
                    <div>
                        <h3 class="form-title-text">Formulir Informasi Darurat</h3>
                        <span style="font-size: 0.68rem; color: #78716C;">Atur pesan &amp; informasi kontak saat tag di-tap penemu</span>
                    </div>
                    <span class="form-required-note">* Wajib diisi</span>
                </div>

                {{-- Field 1: Nama / Deskripsi Barang yang Hilang (SRS FR-LST & Section 6.3) --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="namaBarang" class="field-label">
                            Nama / Kategori Barang yang Hilang
                        </label>
                        <span class="field-badge-public">
                            <span class="badge-dot">●</span>
                            <span>Ditampilkan ke Penemu</span>
                        </span>
                    </div>

                    <input type="text" 
                           id="namaBarang" 
                           name="nama_barang" 
                           class="form-text-input" 
                           value="{{ old('nama_barang', $currentNamaBarang) }}" 
                           placeholder="Contoh: Gantungan Kunci & Kunci Motor Honda Vario">

                    <span class="field-helper-text">
                        Judul barang yang terikat dengan gantungan kunci ini saat penemu melihat halaman darurat.
                    </span>
                    @error('nama_barang')
                        <span class="field-error-text">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Field 2: Pesan Penemuan Barang --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="lostMessage" class="field-label">
                            Pesan Penemuan Barang <span style="color:#DC2626;">*</span>
                        </label>
                        <span class="field-char-counter" id="messageCharCount">
                            {{ mb_strlen(old('message', $currentMessage)) }}/250 karakter
                        </span>
                    </div>

                    <textarea id="lostMessage" 
                              name="message" 
                              class="form-textarea" 
                              maxlength="250" 
                              placeholder="Contoh: Halo orang baik! Barang ini milik saya dan sangat berharga karena berisi kunci rumah utama. Jika Anda menemukannya, mohon bantu hubungi saya melalui formulir di bawah ini. Ada imbalan apresiasi atas kejujuran Anda!" 
                              required>{{ old('message', $currentMessage) }}</textarea>

                    <span class="field-helper-text">
                        Pesan ini akan menjadi hal pertama yang dibaca penemu saat men-scan tag Anda.
                    </span>
                    @error('message')
                        <span class="field-error-text">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Field 3: Perkiraan Lokasi Terakhir / Arahan Pengembalian --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="instruksiPengembalian" class="field-label">
                            Perkiraan Lokasi Terakhir / Arahan Pengembalian (Opsional)
                        </label>
                        <span class="field-badge-public">
                            <span class="badge-dot">●</span>
                            <span>Ditampilkan ke Penemu</span>
                        </span>
                    </div>

                    <input type="text" 
                           id="instruksiPengembalian" 
                           name="instruksi_pengembalian" 
                           class="form-text-input" 
                           value="{{ old('instruksi_pengembalian', $currentInstruksi) }}" 
                           placeholder="Contoh: Area Kampus Unhas Tamalanrea / Sekitar Pantai Losari, Makassar">

                    <span class="field-helper-text">
                        Membantu penemu mengetahui area kemungkinan barang tertinggal atau lokasi penitipan aman.
                    </span>
                    @error('instruksi_pengembalian')
                        <span class="field-error-text">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Field 4: Nomor HP Penerima Relay --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="relayPhone" class="field-label">
                            Nomor HP Penerima Relay <span style="color:#DC2626;">*</span>
                        </label>
                        <span class="field-badge-private">
                            <span>🔒</span>
                            <span>Privat (Relay Cloud)</span>
                        </span>
                    </div>

                    <input type="text" 
                           id="relayPhone" 
                           name="relay_phone" 
                           class="form-text-input" 
                           value="{{ old('relay_phone', $currentRelayPhone) }}" 
                           placeholder="contoh: 081299887766" 
                           required>

                    <span class="field-helper-text">
                        Pemberitahuan pesan penemu akan dikirimkan ke nomor WhatsApp ini melalui enkripsi cloud MaccaTech tanpa menampilkan nomor Anda ke publik.
                    </span>
                    @error('relay_phone')
                        <span class="field-error-text">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Field 5: Reward (Optional) --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="lostReward" class="field-label">
                            Reward / Imbalan (Opsional)
                        </label>
                        <span style="font-size: 0.7rem; color: #8C6D23; font-weight: 700;">🎁 Apresiasi Penemu</span>
                    </div>

                    <input type="text" 
                           id="lostReward" 
                           name="reward" 
                           class="form-text-input" 
                           value="{{ old('reward', $currentReward) }}" 
                           placeholder="contoh: Uang tunai Rp 100.000 / Traktir Kopi">

                    <span class="field-helper-text">
                        Kosongkan jika tidak ingin menawarkan imbalan kepada penemu.
                    </span>
                    @error('reward')
                        <span class="field-error-text">{{ $message }}</span>
                    @enderror
                </div>
            </section>

            {{-- Section: Opsi Komunikasi & Keamanan --}}
            <div class="options-group-wrapper">
                <div class="options-section-head">
                    Opsi Komunikasi &amp; Keamanan
                </div>

                <section class="options-card" aria-label="Opsi Komunikasi">
                    {{-- Toggle 1: Form Pesan Penemu --}}
                    <div class="option-row">
                        <div class="option-text-col">
                            <span class="option-title">Aktifkan Form Pesan Penemu</span>
                            <span class="option-subtitle">
                                Izinkan penemu mengirim pesan &amp; kontak mereka melalui server FlexiTag.
                            </span>
                        </div>

                        <label class="switch-toggle-wrap">
                            <input type="checkbox" name="form_enabled" value="1" {{ old('form_enabled', $formEnabled) ? 'checked' : '' }} aria-label="Aktifkan Form Pesan Penemu">
                            <span class="switch-slider"></span>
                        </label>
                    </div>

                    {{-- Toggle 2: Bagikan Lokasi GPS --}}
                    <div class="option-row">
                        <div class="option-text-col">
                            <span class="option-title">Bagikan Lokasi GPS Terakhir Penemu</span>
                            <span class="option-subtitle">
                                Minta izin koordinat lokasi perangkat penemu saat form dikirimkan.
                            </span>
                        </div>

                        <label class="switch-toggle-wrap">
                            <input type="checkbox" name="share_location" value="1" {{ old('share_location', $shareLocation) ? 'checked' : '' }} aria-label="Bagikan Lokasi GPS Terakhir Penemu">
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                </section>
            </div>

            {{-- Hidden input to activate mode hilang --}}
            <input type="hidden" name="activate_mode" value="1">

            {{-- Card 4: Preview Penemu --}}
            @if ($publicPreviewUrl)
                <a href="{{ $publicPreviewUrl }}" target="_blank" rel="noopener" class="preview-link-card">
                    <div class="preview-card-left">
                        <div class="preview-icon-box" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="3" r="3"></circle>
                            </svg>
                        </div>
                        <div>
                            <div class="preview-title">Lihat Pratinjau Tampilan Penemu</div>
                            <div class="preview-sub">Simulasi tampilan saat penemu men-scan tag NFC</div>
                        </div>
                    </div>

                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A8A29E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            @else
                <div class="preview-link-card" style="opacity: 0.7; cursor: default;">
                    <div class="preview-card-left">
                        <div class="preview-icon-box" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="3" r="3"></circle>
                            </svg>
                        </div>
                        <div>
                            <div class="preview-title">Lihat Pratinjau Tampilan Penemu</div>
                            <div class="preview-sub">Tag belum tertaut untuk pratinjau publik</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tombol Simpan Gold Full-Width --}}
            <div class="bottom-submit-wrap">
                <button type="submit" class="btn-save-lost-mode">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        <polyline points="9 12 11 14 15 10"></polyline>
                    </svg>
                    <span>Simpan Mode Hilang</span>
                </button>

                <p class="bottom-helper-note">
                    Data pesan darurat akan tersimpan aman di server FlexiTag.
                </p>
            </div>
        </form>

    </main>
</div>

</body>
</html>
