<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Profil Mode Kerja — FlexiTag</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/flexitag-user-kerja.css') }}">
</head>
<body class="kerja-edit-body">

<div class="kerja-app-container">

    {{-- ========================================================================= --}}
    {{-- TOP APP BAR                                                               --}}
    {{-- ========================================================================= --}}
    <header class="kerja-topbar">
        <a href="{{ route('user.dashboard') }}" class="topbar-back-btn" aria-label="Kembali ke Dashboard">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>

        <div class="topbar-center-heading">
            <h1 class="topbar-center-title">Edit Profil</h1>
            <span class="topbar-center-sub">• PORTOFOLIO &amp; KARIR • {{ $tagCode }}</span>
        </div>

        <span class="topbar-mode-badge">
            <span>💼</span>
            <span>KERJA</span>
        </span>
    </header>

    {{-- ========================================================================= --}}
    {{-- FORM UTAMA                                                                --}}
    {{-- ========================================================================= --}}
    <form method="POST" action="{{ route('user.profile.kerja.update') }}" enctype="multipart/form-data" id="kerjaMainForm" onsubmit="prepareLinksJson()">
        @csrf
        <input type="hidden" name="links_json" id="linksJsonInput" value="{{ json_encode($kerja['links'] ?? []) }}">
        <input type="hidden" name="delete_cv" id="deleteCvInput" value="0">
        <input type="file" name="photo" id="photoFileInput" accept="image/*" style="display:none;" onchange="handlePhotoChange(this)">
        <input type="file" name="cv_file" id="cvFileInput" accept="application/pdf" style="display:none;" onchange="handleCvChange(this)">

        <main class="kerja-main-content">

            {{-- Flash status notification --}}
            @if (session('status'))
                <div class="status-toast-bar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- Validation errors --}}
            @if (isset($errors) && $errors->any())
                <div class="status-toast-bar" style="background:#FEE2E2; border-color:#FECACA; color:#991B1B;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- 1. FITUR CERDAS: SALIN DARI PROFIL MODE KAMPUS --}}
            <section class="copy-kampus-card" aria-label="Sinkronisasi Cepat dari Mode Kampus">
                <div class="copy-kampus-header-row">
                    <div class="copy-kampus-icon-box" aria-hidden="true">
                        <span>📋</span>
                    </div>
                    <div class="copy-kampus-title-wrap">
                        <h2 class="copy-kampus-title">
                            <span>Salin dari Profil Kampus</span>
                            <span class="copy-kampus-badge">1-Klik</span>
                        </h2>
                        <p class="copy-kampus-desc">
                            Belum punya profil karir khusus? Salin nama, kontak, dan tautan dari Mode Kampus secara instan. Anda tetap bebas menyesuaikan posisi, bio, dan mengunggah CV setelahnya.
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-trigger-copy" onclick="copyDataFromKampus()" id="btnCopyFromKampus">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span>Salin Data dari Profil Kampus</span>
                </button>
            </section>

            {{-- 2. UBAH FOTO PROFIL PROFESIONAL --}}
            <section class="avatar-upload-card">
                <div class="pro-avatar-wrap" onclick="document.getElementById('photoFileInput').click()" title="Klik untuk mengganti foto profil">
                    @if (!empty($kerja['avatar_url']))
                        <img src="{{ $kerja['avatar_url'] }}" alt="Foto Profesional" id="avatarPreviewImg" class="pro-avatar-img">
                    @else
                        <div class="pro-avatar-placeholder" id="avatarPlaceholderBox">
                            <span>💼</span>
                        </div>
                        <img src="" alt="Foto Profesional" id="avatarPreviewImg" class="pro-avatar-img" style="display:none;">
                    @endif
                    <button type="button" class="avatar-camera-btn" aria-label="Unggah Foto">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </button>
                </div>
                <h2 class="avatar-card-title">Foto Profil Profesional</h2>
                <p class="avatar-card-sub">Format JPG, PNG, atau WEBP • Maksimal 5 MB <strong>(Rasio 1:1)</strong></p>
            </section>

            {{-- 3. SINKRONISASI TAG CALLOUT --}}
            <section class="sync-callout-card">
                <div class="sync-icon-box" aria-hidden="true">
                    <span>📡</span>
                </div>
                <div class="sync-text-wrap">
                    <strong class="sync-title">Sinkronisasi Tag Mode Kerja</strong>
                    <p class="sync-desc">
                        Informasi ini tampil saat gantungan kunci atau kartu NFC disentuhkan ke smartphone rekan kerja, klien, atau rekruter pada saat menghadiri job fair &amp; networking event.
                    </p>
                </div>
            </section>

            {{-- 4. LIVE PREVIEW CARD (Pratinjau Kartu Tap Publik Mode Kerja) --}}
            <section class="live-preview-card" aria-label="Pratinjau Profil Publik">
                <span class="preview-badge-top">Pratinjau Kartu Tap Publik</span>
                
                <div class="preview-name-row">
                    <h3 class="preview-full-name" id="previewName">{{ old('nama_lengkap', $kerja['nama_lengkap']) }}</h3>
                    {{-- Verified Gold Rosette Badge --}}
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Terverifikasi" style="flex-shrink:0;">
                        <path d="M12 2L14.7 4.5L18.3 4.8L19.5 8.2L22.5 10.3L21.7 13.9L23 17.3L19.7 18.9L18.1 22.2L14.5 21.8L12 24L9.5 21.8L5.9 22.2L4.3 18.9L1 17.3L2.3 13.9L1.5 10.3L4.5 8.2L5.7 4.8L9.3 4.5L12 2Z" fill="#E5B239"/>
                        <path d="M8.5 12.2L10.8 14.5L15.5 9.8" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <span class="preview-edition-pill">• EDISI PUSAKA KUNCI PA'BARRE ALLO •</span>

                <p class="preview-headline" id="previewHeadline">
                    {{ old('posisi', $kerja['posisi']) }}@if(!empty(old('perusahaan', $kerja['perusahaan']))) • {{ old('perusahaan', $kerja['perusahaan']) }}@endif
                </p>

                <p class="preview-bio-text" id="previewBio">
                    {{ old('bio', $kerja['bio']) }}
                </p>

                <div class="preview-location-cohort" id="previewLocation">
                    <span>📍</span>
                    <span id="previewLocationText">{{ old('lokasi_kantor', $kerja['lokasi_kantor']) }}</span>
                </div>

                <div class="preview-cv-pill-row">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    <span>Aksi Cepat: Unduh CV (PDF) Aktif</span>
                </div>
            </section>

            {{-- 5. INFORMASI KARIR & PEKERJAAN --}}
            <div class="section-header-row">
                <h2 class="section-serif-title">Karir &amp; Profesional</h2>
                <span class="section-gold-label">DATA KARIR</span>
            </div>

            <section class="kerja-form-card">
                {{-- Field 1: Nama Lengkap Profesional --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="nama_lengkap" class="field-label">
                            Nama Lengkap / Profesional <span class="field-label-req">*</span>
                        </label>
                        <span class="field-badge-public">Publik</span>
                    </div>
                    <div class="input-with-end-icon">
                        <input type="text"
                               id="nama_lengkap"
                               name="nama_lengkap"
                               class="kerja-input"
                               value="{{ old('nama_lengkap', $kerja['nama_lengkap']) }}"
                               placeholder="Contoh: Andi Muhammad, S.Kom"
                               oninput="updatePreview()"
                               required>
                        <div class="input-end-icon" title="Akun Terverifikasi">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#E5B32A" stroke="#4A0A0E" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2l3.09 3.26 4.45.65 1.34 4.3 3.12 3.23-1.34 4.3.65 4.45-4.3 1.34-3.26 3.09-3.26-3.09-4.3-1.34.65-4.45-1.34-4.3 3.12-3.23 1.34-4.3 4.45-.65L12 2z"></path>
                                <polyline points="9 12 11 14 15 10" fill="none" stroke="#FFFFFF" stroke-width="2.5"></polyline>
                            </svg>
                        </div>
                    </div>
                    <span class="field-subtext">Nama yang ditampilkan paling atas beserta lencana verifikasi emas.</span>
                </div>

                {{-- Two Columns: Posisi & Perusahaan --}}
                <div class="form-two-col">
                    {{-- Field 2: Posisi / Spesialisasi --}}
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label for="posisi" class="field-label">
                                Posisi / Spesialisasi <span class="field-label-req">*</span>
                            </label>
                        </div>
                        <input type="text"
                               id="posisi"
                               name="posisi"
                               class="kerja-input"
                               value="{{ old('posisi', $kerja['posisi']) }}"
                               placeholder="Contoh: Full-Stack Developer &amp; IoT Specialist"
                               oninput="updatePreview()"
                               required>
                        <span class="field-subtext">Spesialisasi keahlian utama Anda.</span>
                    </div>

                    {{-- Field 3: Perusahaan / Instansi / Status --}}
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label for="perusahaan" class="field-label">
                                Perusahaan / Status
                            </label>
                        </div>
                        <input type="text"
                               id="perusahaan"
                               name="perusahaan"
                               class="kerja-input"
                               value="{{ old('perusahaan', $kerja['perusahaan']) }}"
                               placeholder="Contoh: PT Celebes Digital / Open to Work"
                               oninput="updatePreview()">
                        <span class="field-subtext">Tempat bekerja, instansi magang, atau "Freelance".</span>
                    </div>
                </div>

                {{-- Field 4: Lokasi Kerja / Domisili --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="lokasi_kantor" class="field-label">
                            Lokasi Kerja / Domisili <span class="field-label-req">*</span>
                        </label>
                    </div>
                    <input type="text"
                           id="lokasi_kantor"
                           name="lokasi_kantor"
                           class="kerja-input"
                           value="{{ old('lokasi_kantor', $kerja['lokasi_kantor']) }}"
                           placeholder="Contoh: Makassar, Sulawesi Selatan (Hybrid / Remote)"
                           oninput="updatePreview()"
                           required>
                    <span class="field-subtext">Ditampilkan pada baris lokasi profil publik.</span>
                </div>

                {{-- Field 5: Bio Ringkasan Karir --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="bio" class="field-label">
                            Ringkasan Karir / Bio Profesional
                        </label>
                    </div>
                    <textarea id="bio"
                              name="bio"
                              class="kerja-textarea"
                              placeholder="Ceritakan fokus keahlian, teknologi yang dikuasai, atau pengalaman Anda..."
                              oninput="updatePreview()">{{ old('bio', $kerja['bio']) }}</textarea>
                    <span class="field-subtext">Deskripsi singkat di bawah judul profil publik Mode Kerja.</span>
                </div>
            </section>

            {{-- 6. DOKUMEN CV / RESUME DIGITAL --}}
            <div class="section-header-row">
                <h2 class="section-serif-title">Curriculum Vitae (PDF)</h2>
                <span class="section-gold-label">DOKUMEN UNDUH</span>
            </div>

            <section class="kerja-form-card">
                <div class="cv-upload-box" onclick="document.getElementById('cvFileInput').click()">
                    <div class="cv-upload-icon-circle">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="12" y1="18" x2="12" y2="12"></line>
                            <line x1="9" y1="15" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <strong class="cv-upload-title" id="cvUploadTitleText">
                        {{ !empty($kerja['cv_file_path']) ? 'Ganti File CV (PDF)' : 'Unggah File CV / Resume (PDF)' }}
                    </strong>
                    <p class="cv-upload-sub">Klik untuk memilih file PDF dari perangkat • Maksimal 10 MB</p>
                </div>

                {{-- Status File CV Saat Ini --}}
                @if (!empty($kerja['cv_file_path']))
                    <div class="current-cv-badge-row" id="currentCvRow">
                        <div class="current-cv-info">
                            <span>📄</span>
                            <span>CV Aktif: {{ $kerja['cv_original_name'] ?? 'Curriculum_Vitae.pdf' }}</span>
                        </div>
                        <button type="button" class="btn-remove-cv" onclick="removeCurrentCv()">Hapus CV</button>
                    </div>
                @endif
                <div class="current-cv-badge-row" id="selectedCvNewRow" style="display:none; background:#FEF3C7; border-color:#FDE68A;">
                    <div class="current-cv-info" style="color:#92400E;">
                        <span>📎</span>
                        <span id="selectedCvFileName">File baru terpilih: -</span>
                    </div>
                </div>

                <span class="field-subtext">File CV ini akan diunduh saat pengunjung menekan tombol <strong>"Unduh CV (PDF)"</strong> pada profil publik.</span>
            </section>

            {{-- 7. KONTAK KERJA CEPAT --}}
            <div class="section-header-row">
                <h2 class="section-serif-title">Kontak Profesional</h2>
                <span class="section-gold-label">KOMUNIKASI</span>
            </div>

            <section class="kerja-form-card">
                {{-- Field: Telepon & WhatsApp Kerja --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="telepon_kerja" class="field-label">
                            WhatsApp &amp; Seluler Kerja <span class="field-label-req">*</span>
                        </label>
                        <span class="field-badge-public">Chat &amp; Telp</span>
                    </div>
                    <div class="wa-input-group">
                        <span class="wa-prefix-box">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#25D366" stroke-width="2.3">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                            </svg>
                            <span>+62</span>
                        </span>
                        <input type="tel"
                               id="telepon_kerja"
                               name="telepon_kerja"
                               class="wa-number-input"
                               value="{{ old('telepon_kerja', $kerja['telepon_kerja']) }}"
                               placeholder="812-3456-7890"
                               required>
                    </div>
                    <span class="field-subtext">Nomor resmi untuk komunikasi profesional dan kartu kontak cepat.</span>
                </div>

                {{-- Field: Email Profesional --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="email_kerja" class="field-label">
                            Email Profesional <span class="field-label-req">*</span>
                        </label>
                    </div>
                    <div class="input-with-end-icon">
                        <input type="email"
                               id="email_kerja"
                               name="email_kerja"
                               class="kerja-input"
                               value="{{ old('email_kerja', $kerja['email_kerja']) }}"
                               placeholder="nama@perusahaan.com atau nama@gmail.com"
                               required>
                        <div class="input-end-icon" style="color: #78716C;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="4"/>
                                <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"/>
                            </svg>
                        </div>
                    </div>
                    <span class="field-subtext">Email korespondensi bisnis untuk kartu "EMAIL PROFESIONAL".</span>
                </div>
            </section>

            {{-- 8. PORTOFOLIO & JEJARING KARIR --}}
            <div class="section-header-row">
                <h2 class="section-serif-title">Portofolio &amp; Jejaring Karir</h2>
                <span class="section-gold-label">4 TAUTAN AKTIF</span>
            </div>

            <section class="kerja-links-container" id="kerjaLinksWrapper">
                @php
                    $linkItems = $kerja['links'] ?? [];
                @endphp

                @foreach ($linkItems as $idx => $item)
                    @php
                        $type = $item['type'] ?? 'portfolio';
                        $pillClass = match($type) {
                            'portfolio' => 'type-pill-portfolio',
                            'linkedin' => 'type-pill-linkedin',
                            'github', 'code' => 'type-pill-code',
                            'behance' => 'type-pill-behance',
                            default => 'type-pill-portfolio',
                        };
                        $typeLabel = match($type) {
                            'portfolio' => 'Portofolio Digital',
                            'linkedin' => 'LinkedIn Profesional',
                            'github', 'code' => 'GitHub & Repositori',
                            'behance' => 'Behance / Desain UI',
                            default => 'Tautan Karir',
                        };
                        $typeIcon = match($type) {
                            'portfolio' => '🌐',
                            'linkedin' => '💼',
                            'github', 'code' => '💻',
                            'behance' => '🎨',
                            default => '🔗',
                        };
                    @endphp

                    <div class="link-builder-card" data-index="{{ $idx }}">
                        <div class="link-builder-header">
                            <span class="link-builder-type-pill {{ $pillClass }}">
                                <span>{{ $typeIcon }}</span>
                                <span>{{ $typeLabel }}</span>
                            </span>
                            <span class="link-builder-badge-active">● Aktif di Tag</span>
                        </div>

                        <div class="link-mini-inputs">
                            <input type="hidden" class="link-field-type" value="{{ $type }}">
                            <div class="link-mini-row">
                                <input type="text"
                                       class="link-input-sub link-field-title"
                                       placeholder="Judul Tautan"
                                       value="{{ $item['title'] ?? '' }}"
                                       required>
                                <input type="text"
                                       class="link-input-sub link-field-subtitle"
                                       placeholder="Keterangan singkat / spesialisasi"
                                       value="{{ $item['subtitle'] ?? '' }}">
                            </div>
                            <input type="url"
                                   class="link-input-sub link-field-url"
                                   placeholder="https://..."
                                   value="{{ $item['url'] ?? '' }}"
                                   required>
                        </div>
                    </div>
                @endforeach
            </section>

        </main>

        {{-- ========================================================================= --}}
        {{-- STICKY BOTTOM SAVE BAR                                                    --}}
        {{-- ========================================================================= --}}
        <footer class="kerja-bottom-bar">
            <button type="submit" class="btn-save-kerja" id="btnSaveKerja">
                <span>Simpan Perubahan Mode Kerja</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
            <p class="bottom-sync-subtext">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <span>Tersinkronisasi otomatis ke cloud MaccaTech &amp; chip NFC</span>
            </p>
        </footer>

    </form>

</div>

<script>
    // Data cadangan dari Mode Kampus untuk fitur 1-Klik Salin
    const kampusDataForCopy = @json($kampusForCopy ?? []);

    // Live preview update
    function updatePreview() {
        const nameVal = document.getElementById('nama_lengkap').value.trim() || 'Andi Muhammad';
        const posisiVal = document.getElementById('posisi').value.trim() || 'Software Engineer';
        const perusahaanVal = document.getElementById('perusahaan').value.trim();
        const lokasiVal = document.getElementById('lokasi_kantor').value.trim() || 'Makassar, Sulawesi Selatan';
        const bioVal = document.getElementById('bio').value.trim() || 'Fokus membangun sistem web modern dan perangkat keras pintar berbasis mikrokontroler.';

        document.getElementById('previewName').textContent = nameVal;
        document.getElementById('previewHeadline').textContent = perusahaanVal ? `${posisiVal} • ${perusahaanVal}` : posisiVal;
        document.getElementById('previewBio').textContent = bioVal;
        document.getElementById('previewLocationText').textContent = lokasiVal;
    }

    // Photo preview
    function handlePhotoChange(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatarPreviewImg');
                const placeholder = document.getElementById('avatarPlaceholderBox');
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // CV File selection
    function handleCvChange(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const newRow = document.getElementById('selectedCvNewRow');
            const newName = document.getElementById('selectedCvFileName');
            const titleText = document.getElementById('cvUploadTitleText');
            if (newRow && newName) {
                newName.textContent = `File baru terpilih: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                newRow.style.display = 'flex';
            }
            if (titleText) {
                titleText.textContent = 'Ganti File CV Terpilih';
            }
            document.getElementById('deleteCvInput').value = '0';
        }
    }

    function removeCurrentCv() {
        document.getElementById('deleteCvInput').value = '1';
        const currentRow = document.getElementById('currentCvRow');
        if (currentRow) {
            currentRow.style.display = 'none';
        }
        const titleText = document.getElementById('cvUploadTitleText');
        if (titleText) {
            titleText.textContent = 'Unggah File CV / Resume (PDF)';
        }
    }

    // Fitur Cerdas: Salin dari Profil Mode Kampus
    function copyDataFromKampus() {
        if (!kampusDataForCopy || Object.keys(kampusDataForCopy).length === 0) {
            alert('Data Mode Kampus belum ditemukan. Silakan lengkapi Mode Kampus terlebih dahulu.');
            return;
        }

        if (confirm('Salin data dasar dari Mode Kampus ke Mode Kerja? Data yang sudah diisi di form ini akan disesuaikan.')) {
            if (kampusDataForCopy.nama_lengkap) {
                document.getElementById('nama_lengkap').value = kampusDataForCopy.nama_lengkap;
            }
            if (kampusDataForCopy.posisi) {
                document.getElementById('posisi').value = kampusDataForCopy.posisi;
            }
            if (kampusDataForCopy.perusahaan) {
                document.getElementById('perusahaan').value = kampusDataForCopy.perusahaan;
            }
            if (kampusDataForCopy.lokasi_kantor) {
                document.getElementById('lokasi_kantor').value = kampusDataForCopy.lokasi_kantor;
            }
            if (kampusDataForCopy.bio) {
                document.getElementById('bio').value = kampusDataForCopy.bio;
            }
            if (kampusDataForCopy.telepon_kerja) {
                document.getElementById('telepon_kerja').value = kampusDataForCopy.telepon_kerja;
            }
            if (kampusDataForCopy.email_kerja) {
                document.getElementById('email_kerja').value = kampusDataForCopy.email_kerja;
            }

            // Salin tautan jika ada
            if (Array.isArray(kampusDataForCopy.links) && kampusDataForCopy.links.length > 0) {
                const cards = document.querySelectorAll('.link-builder-card');
                kampusDataForCopy.links.forEach((kLink, idx) => {
                    if (cards[idx]) {
                        const titleInput = cards[idx].querySelector('.link-field-title');
                        const subInput = cards[idx].querySelector('.link-field-subtitle');
                        const urlInput = cards[idx].querySelector('.link-field-url');

                        if (titleInput && kLink.title) titleInput.value = kLink.title;
                        if (subInput && kLink.subtitle) subInput.value = kLink.subtitle;
                        if (urlInput && kLink.url) urlInput.value = kLink.url;
                    }
                });
            }

            updatePreview();
            alert('✓ Berhasil menyalin data dari Mode Kampus! Silakan sesuaikan posisi karir dan unggah file CV Anda.');
        }
    }

    // Serialize links before submit
    function prepareLinksJson() {
        const cards = document.querySelectorAll('.link-builder-card');
        const linksData = [];

        cards.forEach((card, index) => {
            const type = card.querySelector('.link-field-type').value;
            const title = card.querySelector('.link-field-title').value.trim();
            const subtitle = card.querySelector('.link-field-subtitle').value.trim();
            const url = card.querySelector('.link-field-url').value.trim();

            if (title && url) {
                linksData.push({
                    id: index + 1,
                    type: type,
                    title: title,
                    subtitle: subtitle,
                    url: url,
                    is_visible: true
                });
            }
        });

        document.getElementById('linksJsonInput').value = JSON.stringify(linksData);
    }
</script>

</body>
</html>
