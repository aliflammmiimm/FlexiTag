<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Profil Mode Kampus — FlexiTag</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/flexitag-user-kampus.css') }}">
</head>
<body class="kampus-edit-body">

<div class="kampus-app-container">

    {{-- ========================================================================= --}}
    {{-- TOP APP BAR                                                               --}}
    {{-- ========================================================================= --}}
    <header class="kampus-topbar">
        <a href="{{ route('user.dashboard') }}" class="topbar-back-btn" aria-label="Kembali ke Dashboard">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>

        <div class="topbar-center-heading">
            <h1 class="topbar-center-title">Edit Profil</h1>
            <span class="topbar-center-sub">• JEJARING AKADEMIK • {{ $tagCode }}</span>
        </div>

        <span class="topbar-mode-badge">
            <span>🎓</span>
            <span>KAMPUS</span>
        </span>
    </header>

    {{-- ========================================================================= --}}
    {{-- FORM UTAMA                                                                --}}
    {{-- ========================================================================= --}}
    <form method="POST" action="{{ route('user.profile.kampus.update') }}" enctype="multipart/form-data" id="kampusMainForm" onsubmit="prepareLinksJson()">
        @csrf
        <input type="hidden" name="links_json" id="linksJsonInput" value="{{ json_encode($kampus['links'] ?? []) }}">
        <input type="file" name="photo" id="photoFileInput" accept="image/*" style="display:none;" onchange="handlePhotoChange(this)">

        <main class="kampus-main-content">

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

            {{-- 1. UBAH FOTO PROFIL MAHASISWA --}}
            <section class="avatar-upload-card">
                <div class="student-avatar-wrap" onclick="document.getElementById('photoFileInput').click()" title="Klik untuk mengganti foto profil">
                    @if (!empty($kampus['avatar_url']))
                        <img src="{{ $kampus['avatar_url'] }}" alt="Foto Mahasiswa" id="avatarPreviewImg" class="student-avatar-img">
                    @else
                        <div class="student-avatar-placeholder" id="avatarPlaceholderBox">
                            <span>🎓</span>
                        </div>
                        <img src="" alt="Foto Mahasiswa" id="avatarPreviewImg" class="student-avatar-img" style="display:none;">
                    @endif
                    <button type="button" class="avatar-camera-btn" aria-label="Unggah Foto">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </button>
                </div>
                <h2 class="avatar-card-title">Foto Profil Mahasiswa</h2>
                <p class="avatar-card-sub">Maksimal 5 MB • Format JPG, PNG, atau WEBP <strong>(Rasio 1:1)</strong></p>
            </section>

            {{-- 2. SINKRONISASI TAG CALLOUT --}}
            <section class="sync-callout-card">
                <div class="sync-icon-box">
                    <span>📡</span>
                </div>
                <div class="sync-text-wrap">
                    <strong class="sync-title">Sinkronisasi Tag Mode Kampus</strong>
                    <p class="sync-desc">
                        Perubahan langsung terhubung ke profil digital publik saat gantungan kunci atau kartu FlexiTag disentuh ke smartphone rekan mahasiswa, dosen, atau recruiter.
                    </p>
                </div>
            </section>

            {{-- 3. LIVE PREVIEW CARD (Tampilan Ringkasan Profil) --}}
            <section class="live-preview-card" aria-label="Pratinjau Profil Publik">
                <span class="preview-badge-top">Pratinjau Kartu Tap Publik</span>
                
                <div class="preview-name-row">
                    <h3 class="preview-full-name" id="previewName">{{ old('nama_lengkap', $kampus['nama_lengkap']) }}</h3>
                    {{-- Verified Gold Rosette Badge --}}
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Terverifikasi" style="flex-shrink:0;">
                        <path d="M12 2L14.7 4.5L18.3 4.8L19.5 8.2L22.5 10.3L21.7 13.9L23 17.3L19.7 18.9L18.1 22.2L14.5 21.8L12 24L9.5 21.8L5.9 22.2L4.3 18.9L1 17.3L2.3 13.9L1.5 10.3L4.5 8.2L5.7 4.8L9.3 4.5L12 2Z" fill="#E5B239"/>
                        <path d="M8.5 12.2L10.8 14.5L15.5 9.8" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <span class="preview-edition-pill">• EDISI PUSAKA KUNCI PA'BARRE ALLO •</span>

                <p class="preview-headline" id="previewHeadline">
                    {{ old('program_studi', $kampus['program_studi']) }} • {{ old('universitas', $kampus['universitas']) }}
                </p>

                <div class="preview-location-cohort" id="previewLocation">
                    <span>📍</span>
                    <span id="previewCohortText">Angkatan {{ old('angkatan', $kampus['angkatan']) }} • {{ old('domisili', $kampus['domisili']) }}</span>
                </div>
            </section>

            {{-- 4. INFORMASI IDENTITAS MAHASISWA --}}
            <div class="section-header-row">
                <h2 class="section-serif-title">Identitas Mahasiswa</h2>
                <span class="section-gold-label">DATA AKADEMIK</span>
            </div>

            <section class="kampus-form-card">
                {{-- Field 1: Nama Lengkap Mahasiswa --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="nama_lengkap" class="field-label">
                            Nama Lengkap / Tampilan <span class="field-label-req">*</span>
                        </label>
                        <span class="field-badge-public">Publik</span>
                    </div>
                    <div class="input-with-end-icon">
                        <input type="text"
                               id="nama_lengkap"
                               name="nama_lengkap"
                               class="kampus-input"
                               value="{{ old('nama_lengkap', $kampus['nama_lengkap']) }}"
                               placeholder="Contoh: Andi Muhammad"
                               oninput="updatePreview()"
                               required>
                        <div class="input-end-icon" title="Akun Terverifikasi">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#E5B32A" stroke="#4A0A0E" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2l3.09 3.26 4.45.65 1.34 4.3 3.12 3.23-1.34 4.3.65 4.45-4.3 1.34-3.26 3.09-3.26-3.09-4.3-1.34.65-4.45-1.34-4.3 3.12-3.23 1.34-4.3 4.45-.65L12 2z"></path>
                                <polyline points="9 12 11 14 15 10" fill="none" stroke="#FFFFFF" stroke-width="2.5"></polyline>
                            </svg>
                        </div>
                    </div>
                    <span class="field-subtext">Nama yang tampil paling atas di profil publik beserta lencana verifikasi.</span>
                </div>

                {{-- Two Columns: Program Studi & Universitas --}}
                <div class="form-two-col">
                    {{-- Field 2: Program Studi / Jurusan --}}
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label for="program_studi" class="field-label">
                                Program Studi / Jurusan <span class="field-label-req">*</span>
                            </label>
                        </div>
                        <input type="text"
                               id="program_studi"
                               name="program_studi"
                               class="kampus-input"
                               value="{{ old('program_studi', $kampus['program_studi']) }}"
                               placeholder="Contoh: Teknik Komputer"
                               oninput="updatePreview()"
                               required>
                    </div>

                    {{-- Field 3: Perguruan Tinggi / Universitas --}}
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label for="universitas" class="field-label">
                                Perguruan Tinggi / Universitas <span class="field-label-req">*</span>
                            </label>
                        </div>
                        <input type="text"
                               id="universitas"
                               name="universitas"
                               class="kampus-input"
                               value="{{ old('universitas', $kampus['universitas']) }}"
                               placeholder="Contoh: Universitas Negeri Makassar"
                               oninput="updatePreview()"
                               required>
                    </div>
                </div>

                {{-- Two Columns: Tahun Angkatan & Domisili --}}
                <div class="form-two-col">
                    {{-- Field 4: Tahun Angkatan --}}
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label for="angkatan" class="field-label">
                                Tahun Angkatan <span class="field-label-req">*</span>
                            </label>
                        </div>
                        <input type="text"
                               id="angkatan"
                               name="angkatan"
                               class="kampus-input"
                               value="{{ old('angkatan', $kampus['angkatan']) }}"
                               placeholder="Contoh: 2023"
                               oninput="updatePreview()"
                               required>
                    </div>

                    {{-- Field 5: Domisili / Kota Kampus --}}
                    <div class="form-field-group">
                        <div class="field-label-row">
                            <label for="domisili" class="field-label">
                                Domisili / Kota Kampus <span class="field-label-req">*</span>
                            </label>
                        </div>
                        <input type="text"
                               id="domisili"
                               name="domisili"
                               class="kampus-input"
                               value="{{ old('domisili', $kampus['domisili']) }}"
                               placeholder="Contoh: Makassar, Sulawesi Selatan"
                               oninput="updatePreview()"
                               required>
                    </div>
                </div>
            </section>

            {{-- 5. KONTAK CEPAT AKADEMIK --}}
            <div class="section-header-row">
                <h2 class="section-serif-title">Kontak Cepat Akademik</h2>
                <span class="section-gold-label">TAP &amp; CHAT</span>
            </div>

            <section class="kampus-form-card">
                {{-- Field: WhatsApp & Seluler --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="telepon_kampus" class="field-label">
                            WhatsApp &amp; Seluler <span class="field-label-req">*</span>
                        </label>
                        <span class="field-badge-public">Chat Langsung</span>
                    </div>
                    <div class="wa-input-group">
                        <span class="wa-prefix-box">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#25D366" stroke-width="2.3">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                            </svg>
                            <span>+62</span>
                        </span>
                        <input type="tel"
                               id="telepon_kampus"
                               name="telepon_kampus"
                               class="wa-number-input"
                               value="{{ old('telepon_kampus', $kampus['telepon_kampus']) }}"
                               placeholder="812-3456-7890"
                               required>
                    </div>
                    <span class="field-subtext">Digunakan untuk tombol "Chat WhatsApp" dan kartu "WHATSAPP &amp; SELULER".</span>
                </div>

                {{-- Field: Email Akademik --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="email_kampus" class="field-label">
                            Email Akademik / Mahasiswa <span class="field-label-req">*</span>
                        </label>
                    </div>
                    <div class="input-with-end-icon">
                        <input type="email"
                               id="email_kampus"
                               name="email_kampus"
                               class="kampus-input"
                               value="{{ old('email_kampus', $kampus['email_kampus']) }}"
                               placeholder="andi.muhammad@student.unm.ac.id"
                               required>
                        <div class="input-end-icon" style="color: #78716C;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="4"/>
                                <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"/>
                            </svg>
                        </div>
                    </div>
                    <span class="field-subtext">Alamat email universitas resmi untuk kartu "EMAIL AKADEMIK".</span>
                </div>
            </section>

            {{-- 6. JEJARING & AKTIVITAS KAMPUS --}}
            <div class="section-header-row">
                <h2 class="section-serif-title">Jejaring &amp; Aktivitas Kampus</h2>
                <span class="section-gold-label">4 TAUTAN AKTIF</span>
            </div>

            <section class="campus-links-container" id="campusLinksWrapper">
                @php
                    $linkItems = $kampus['links'] ?? [];
                @endphp

                @foreach ($linkItems as $idx => $item)
                    @php
                        $type = $item['type'] ?? 'academic';
                        $pillClass = match($type) {
                            'academic', 'organization' => 'type-pill-academic',
                            'linkedin' => 'type-pill-linkedin',
                            'instagram' => 'type-pill-instagram',
                            'github', 'code' => 'type-pill-code',
                            default => 'type-pill-academic',
                        };
                        $typeLabel = match($type) {
                            'academic', 'organization' => 'HIMA / Organisasi',
                            'linkedin' => 'LinkedIn Mahasiswa',
                            'instagram' => 'Instagram Mahasiswa',
                            'github', 'code' => 'Proyek Kuliah / GitHub',
                            default => 'Tautan Kampus',
                        };
                        $typeIcon = match($type) {
                            'academic', 'organization' => '🎓',
                            'linkedin' => '💼',
                            'instagram' => '📷',
                            'github', 'code' => '💻',
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
                                       placeholder="Keterangan / Divisi / Jabatan"
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
        <footer class="kampus-bottom-bar">
            <button type="submit" class="btn-save-kampus" id="btnSaveKampus">
                <span>Simpan Perubahan Profil Kampus</span>
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
    // Live preview update
    function updatePreview() {
        const nameVal = document.getElementById('nama_lengkap').value.trim() || 'Andi Muhammad';
        const prodiVal = document.getElementById('program_studi').value.trim() || 'Teknik Komputer';
        const univVal = document.getElementById('universitas').value.trim() || 'Universitas Negeri Makassar';
        const angkatanVal = document.getElementById('angkatan').value.trim() || '2023';
        const domisiliVal = document.getElementById('domisili').value.trim() || 'Makassar, Sulawesi Selatan';

        document.getElementById('previewName').textContent = nameVal;
        document.getElementById('previewHeadline').textContent = `${prodiVal} • ${univVal}`;
        document.getElementById('previewCohortText').textContent = `Angkatan ${angkatanVal} • ${domisiliVal}`;
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
