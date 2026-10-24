<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aktivasi Tag NFC — {{ config('flexitag.brand') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/flexitag-activation.css') }}">
</head>
<body>

@php
    $step = (int) ($step ?? 1);
    $activeTagCount = (int) ($activeTagCount ?? 0);
    $tagCode = $tag?->tag_code ?? session('activation_tag_code') ?? 'a8k2x9';
    $publicTagUrl = url('/u/' . $tagCode);

    $stepWidths = [1 => '0%', 2 => '33%', 3 => '66%', 4 => '100%'];
    $fillWidth = $stepWidths[$step] ?? '0%';

    $fail = session('activation_state');
    $messages = [
        'wrong' => ['Kode aktivasi tidak ditemukan.', 'Periksa kembali kode pada kartu panduan di kemasan Anda.'],
        'used' => ['Kode ini sudah digunakan.', 'Tag dengan kode tersebut sudah terhubung ke akun lain. Hubungi admin bila Anda merasa ini keliru.'],
        'own' => ['Tag ini sudah aktif di akun Anda.', 'Anda tidak perlu mengaktifkannya lagi. Anda dapat melihatnya di dashboard.'],
        'disabled' => ['Tag ini sedang dinonaktifkan.', 'Tag nonaktif tidak dapat diaktifkan lewat alur biasa. Hubungi admin untuk bantuan.'],
        'throttled' => ['Terlalu banyak percobaan gagal.', 'Demi keamanan, coba lagi dalam ' . ($fail['minutes'] ?? 10) . ' menit.'],
    ];

    // Inisial Nama Pengguna untuk Avatar
    $displayName = $profile?->display_name ?? $user?->name ?? 'Andi Muhammad';
    $parts = explode(' ', trim($displayName));
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $initials .= mb_substr($p, 0, 1);
    }
    $initials = strtoupper($initials) ?: 'FT';

    $activeMode = $profile?->active_mode ?? 'kampus';
    $modeBadgeText = match($activeMode) {
        'kerja' => 'Mode Kerja 💼',
        'umkm' => 'Mode UMKM 🏪',
        'hilang' => 'Mode Penemu 🔒',
        default => 'Mode Kampus 🎓',
    };
@endphp

<div class="wizard-wrapper">

    {{-- Brand Header --}}
    <header class="wizard-header">
        <a href="{{ route('home') }}" class="brand-sun-icon" aria-label="Beranda FlexiTag">
            {{-- Sun Medallion Logo SVG --}}
            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="24" cy="24" r="10" stroke="currentColor" stroke-width="2.5"/>
                <circle cx="24" cy="24" r="4.5" fill="currentColor"/>
                {{-- 8 Rays with Dots --}}
                <line x1="24" y1="5" x2="24" y2="10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="24" y1="38" x2="24" y2="43" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="5" y1="24" x2="10" y2="24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="38" y1="24" x2="43" y2="24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="10.5" y1="10.5" x2="14" y2="14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="34" y1="34" x2="37.5" y2="37.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="10.5" y1="37.5" x2="14" y2="34" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="34" y1="14" x2="37.5" y2="10.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
        </a>

        <div class="brand-title-wrap">
            <h1 class="brand-name">{{ config('flexitag.brand', 'FlexiTag') }}</h1>
            @if ($step === 3)
                <span class="nfc-badge">NFC 3.0</span>
            @endif
        </div>

        <p class="brand-tagline">{{ config('flexitag.tagline', 'SATU TAP, SERIBU IDENTITAS') }}</p>

        {{-- Dynamic Status Tag Pill --}}
        @if ($step <= 2)
            <div class="status-pill unactivated">
                <span class="status-dot"></span>
                <span>Status Tag: Belum Diaktivasi</span>
            </div>
        @elseif ($step === 3)
            <div class="status-pill verified">
                <span class="status-dot"></span>
                <span>Status Tag: Kode Terverifikasi ✓</span>
            </div>
        @else
            <div class="status-pill active">
                <span class="status-dot"></span>
                <span>Status Tag: Aktif &amp; Terhubung</span>
            </div>
        @endif
    </header>

    {{-- Stepper Progress Bar --}}
    <nav class="stepper-nav" aria-label="Alur Aktivasi">
        <div class="stepper-track-bg"></div>
        <div class="stepper-track-fill" style="width: {{ $fillWidth }};"></div>

        <ol class="stepper-list">
            {{-- Langkah 1: Akun --}}
            <li class="step-item {{ $step > 1 ? 'completed' : ($step === 1 ? 'active' : 'upcoming') }} {{ $user ? 'clickable' : '' }}"
                @if ($user) onclick="location.href='{{ route('user.activation.show', ['step' => 1]) }}'" @endif>
                <div class="step-circle">
                    @if ($step > 1) ✓ @else 1 @endif
                </div>
                <span class="step-label">Akun</span>
                @if ($step === 1) <div class="step-active-bar"></div> @endif
            </li>

            {{-- Langkah 2: Kode Aktivasi --}}
            <li class="step-item {{ $step > 2 ? 'completed' : ($step === 2 ? 'active' : 'upcoming') }} {{ $user ? 'clickable' : '' }}"
                @if ($user) onclick="location.href='{{ route('user.activation.show', ['step' => 2]) }}'" @endif>
                <div class="step-circle">
                    @if ($step > 2) ✓ @else 2 @endif
                </div>
                <span class="step-label">{{ $step === 2 ? 'Kode Aktivasi' : 'Kode' }}</span>
                @if ($step === 2) <div class="step-active-bar"></div> @endif
            </li>

            {{-- Langkah 3: Profil --}}
            <li class="step-item {{ $step > 3 ? 'completed' : ($step === 3 ? 'active' : 'upcoming') }} {{ ($user && $tag) ? 'clickable' : '' }}"
                @if ($user && $tag) onclick="location.href='{{ route('user.activation.show', ['step' => 3]) }}'" @endif>
                <div class="step-circle">
                    @if ($step > 3) ✓ @else 3 @endif
                </div>
                <span class="step-label">Profil</span>
                @if ($step === 3) <div class="step-active-bar"></div> @endif
            </li>

            {{-- Langkah 4: Selesai --}}
            <li class="step-item {{ $step === 4 ? 'active' : 'upcoming' }}">
                <div class="step-circle">4</div>
                <span class="step-label">Selesai</span>
                @if ($step === 4) <div class="step-active-bar"></div> @endif
            </li>
        </ol>
    </nav>

    {{-- Alert Messages --}}
    @if (session('status'))
        <div style="width: 100%; background: #EFFDF5; border: 1px solid #BBF7D0; color: #166534; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.8rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>✓</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if (session('warning'))
        <div style="width: 100%; background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.8rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>⚠️</span>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    @if (session('info'))
        <div style="width: 100%; background: #EFF6FF; border: 1px solid #BFDBFE; color: #1E40AF; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.8rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>ℹ️</span>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div style="width: 100%; background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.8rem; margin-bottom: 1rem;">
            <p style="font-weight: 700; margin-bottom: 0.2rem;">Periksa kembali:</p>
            <ul style="margin-left: 1.25rem; font-size: 0.76rem;">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Error saat aktivasi kode di step 2 --}}
    @if ($fail && isset($messages[$fail['state']]))
        <div style="width: 100%; background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.8rem; margin-bottom: 1rem;">
            <b>{{ $messages[$fail['state']][0] }}</b>
            <p style="margin: 0.2rem 0 0; font-size: 0.74rem;">{{ $messages[$fail['state']][1] }}</p>
        </div>
    @endif


    {{-- ========================================================================= --}}
    {{-- LANGKAH 1: AKUN (Daftar Akun Baru / Masuk Akun)                           --}}
    {{-- ========================================================================= --}}
    @if ($step === 1)
        {{-- NFC Detected Banner --}}
        <div class="chip-detect-banner">
            <div class="chip-thumb">
                <img src="{{ asset('images/keychain.jpg') }}" alt="Chip NFC FlexiTag Terdeteksi">
                <span class="chip-thumb-pill">• NFC</span>
            </div>
            <div class="chip-detect-text">
                <h4>⚡ Chip NFC NTAG213 Terdeteksi</h4>
                <p>Masuk atau buat akun baru untuk mengklaim gantungan kunci ini.</p>
            </div>
            {{-- Watermark diamond --}}
            <svg class="toraja-watermark" width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="12 2 22 12 12 22 2 12"/>
                <polygon points="12 6 18 12 12 18 6 12"/>
            </svg>
        </div>

        <div class="wizard-card">
            @if ($user)
                {{-- Pengguna sudah login --}}
                <div style="text-align: center; padding: 0.5rem 0;">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--ft-maroon); color: #fff; font-size: 1.4rem; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; box-shadow: 0 4px 12px rgba(107, 14, 18, 0.25);">
                        {{ $initials }}
                    </div>
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: #2D2726;">Anda Masuk Sebagai</h3>
                    <p style="font-size: 0.92rem; font-weight: 700; color: var(--ft-maroon); margin-top: 0.2rem;">{{ $user->name }}</p>
                    <p style="font-size: 0.76rem; color: #736C63; margin-bottom: 1.25rem;">{{ $user->email }}</p>

                    <a href="{{ route('user.activation.show', ['step' => 2]) }}" class="btn-gold-primary">
                        <span>Lanjut ke Kode Aktivasi</span>
                        <span>→</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" style="margin-top: 0.75rem;">
                        @csrf
                        <button type="submit" class="btn-secondary-outline" style="border: none; background: transparent; color: #8C8476; font-size: 0.75rem; text-decoration: underline;">
                            Gunakan akun lain
                        </button>
                    </form>
                </div>
            @else
                {{-- Form untuk Pengunjung / Tamu --}}
                @php
                    $activeTab = request()->query('tab', old('auth_action', 'register'));
                @endphp

                {{-- Segmented Tab Switcher --}}
                <div class="segmented-control" role="tablist">
                    <button type="button" class="segmented-btn {{ $activeTab === 'register' ? 'active' : '' }}" id="tabBtnRegister" onclick="switchTab('register')">
                        Daftar Akun Baru
                    </button>
                    <button type="button" class="segmented-btn {{ $activeTab === 'login' ? 'active' : '' }}" id="tabBtnLogin" onclick="switchTab('login')">
                        Masuk Akun
                    </button>
                </div>

                {{-- Form Daftar Akun Baru --}}
                <form method="POST" action="{{ route('user.activation.auth') }}" id="formRegister" style="{{ $activeTab === 'register' ? '' : 'display: none;' }}">
                    @csrf
                    <input type="hidden" name="auth_action" value="register">

                    <div class="form-group">
                        <div class="form-label-row">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <span class="form-hint">Sesuai KTP / Profil</span>
                        </div>
                        <div class="input-container">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            <input type="text" id="name" name="name" class="form-input" placeholder="Contoh: Andi Muhammad" value="{{ old('name') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-label-row">
                            <label for="regEmail" class="form-label">Alamat Email</label>
                        </div>
                        <div class="input-container">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </span>
                            <input type="email" id="regEmail" name="email" class="form-input" placeholder="nama@email.com" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-label-row">
                            <label for="regPassword" class="form-label">Kata Sandi</label>
                        </div>
                        <div class="input-container">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>
                            <input type="password" id="regPassword" name="password" class="form-input" placeholder="Minimal 8 karakter" required>
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('regPassword')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="terms-checkbox-wrap">
                        <input type="checkbox" id="agree_terms" name="agree_terms" value="1" class="custom-checkbox" checked required>
                        <label for="agree_terms" class="terms-text">
                            Saya menyetujui <a href="#">Syarat &amp; Ketentuan</a> serta <a href="#">Kebijakan Privasi</a> (UU PDP).
                        </label>
                    </div>

                    <button type="submit" class="btn-gold-primary">
                        <span>Lanjut ke Kode Aktivasi</span>
                        <span>→</span>
                    </button>
                </form>

                {{-- Form Masuk Akun --}}
                <form method="POST" action="{{ route('user.activation.auth') }}" id="formLogin" style="{{ $activeTab === 'login' ? '' : 'display: none;' }}">
                    @csrf
                    <input type="hidden" name="auth_action" value="login">

                    <div class="form-group">
                        <div class="form-label-row">
                            <label for="loginEmail" class="form-label">Alamat Email</label>
                        </div>
                        <div class="input-container">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </span>
                            <input type="email" id="loginEmail" name="email" class="form-input" placeholder="nama@email.com" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-label-row">
                            <label for="loginPassword" class="form-label">Kata Sandi</label>
                        </div>
                        <div class="input-container">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>
                            <input type="password" id="loginPassword" name="password" class="form-input" placeholder="Masukkan kata sandi akun" required>
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility('loginPassword')">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-gold-primary" style="margin-top: 1.25rem;">
                        <span>Masuk &amp; Lanjut ke Kode Aktivasi</span>
                        <span>→</span>
                    </button>
                </form>

                <div class="or-divider">
                    <span>atau lanjutkan dengan</span>
                </div>

                {{-- Google Sign In Button --}}
                <button type="button" class="btn-google" onclick="alert('Fitur Masuk dengan Google segera hadir.')">
                    {{-- Google Multi-colored Logo --}}
                    <svg width="18" height="18" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Masuk dengan Google</span>
                </button>
            @endif
        </div>
    @endif


    {{-- ========================================================================= --}}
    {{-- LANGKAH 2: KODE AKTIVASI                                                  --}}
    {{-- ========================================================================= --}}
    @if ($step === 2)
        {{-- Showcase Hero Card --}}
        <div class="showcase-card">
            <div class="showcase-topbar">
                <span>⚡ Chip NFC NTAG213 • ID Terdeteksi</span>
                <span class="pulsing-dot" title="Koneksi siap"></span>
            </div>

            <div class="showcase-img-wrap">
                <img src="{{ asset('images/keychain.jpg') }}" alt="Gantungan Kunci Akrilik Custom Motif Toraja">
                <div class="tap-ready-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                    </svg>
                    <span>TAP READY</span>
                </div>
            </div>

            <h3 class="showcase-title">Gantungan Kunci Akrilik Custom — Edisi Motif Toraja</h3>

            <div class="showcase-meta-row">
                <span class="tag-uid-text">Tag UID: <strong>04 : A2 : 8F : 9C</strong></span>
                <span class="tag-spec-pill">NTAG213 • 144 Bytes</span>
            </div>
        </div>

        {{-- Form Input Kode Aktivasi --}}
        <div class="wizard-card">
            <h2 class="step-card-title">Masukkan Kode Aktivasi</h2>
            <p class="step-card-sub" style="margin-top: 0.35rem;">
                Masukkan kode aktivasi sekali pakai yang tertera pada kartu panduan di dalam kemasan gantungan kunci Anda.
            </p>

            <form method="POST" action="{{ route('user.activation.store') }}" id="formActivation">
                @csrf

                <div class="form-group">
                    <div class="form-label-row">
                        <label for="activation_code" class="form-label">Kode Aktivasi</label>
                        <span class="code-badge-pill">SEKALI PAKAI</span>
                    </div>

                    <div class="input-container" style="background-color: #F8F6F2;">
                        <input type="text"
                               id="activation_code"
                               name="activation_code"
                               class="form-input activation-code-input"
                               placeholder="Contoh: KD-8821-XQ"
                               value="{{ old('activation_code') }}"
                               maxlength="30"
                               autocomplete="off"
                               spellcheck="false"
                               required>
                        <button type="button" class="paste-code-btn" title="Tempel dari Clipboard" onclick="pasteActivationCode()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                        </button>
                    </div>

                    <div class="code-help-row">
                        <span>Format: KD-0000-XX</span>
                        <a href="javascript:void(0)" class="guide-link" onclick="openGuideModal()">Lihat kartu panduan</a>
                    </div>
                </div>

                <button type="submit" class="btn-gold-primary" style="margin-top: 1.25rem;">
                    <span>Aktifkan Gantungan Sekarang</span>
                    <span>→</span>
                </button>
            </form>

            <div class="security-notice-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#65A30D" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
                <span>Kode hanya berlaku 1x &amp; terikat pada ID tag ini. Percobaan salah berulang akan dibatasi secara otomatis demi keamanan akun Anda.</span>
            </div>
        </div>

        {{-- Toast Notifikasi Sukses / Terverifikasi --}}
        @if (session('activation_toast'))
            <div class="toast-banner">
                <div class="toast-icon-circle">✓</div>
                <span>{{ session('activation_toast') }}</span>
            </div>
        @endif
    @endif


    {{-- ========================================================================= --}}
    {{-- LANGKAH 3: PROFIL AWAL                                                    --}}
    {{-- ========================================================================= --}}
    @if ($step === 3)
        <div class="wizard-card">
            <div class="step3-title-row">
                <h2 class="step-card-title">Siapkan Profil Pertama Anda</h2>
                <span class="step-count-pill">Langkah 3/4</span>
            </div>
            <p class="step-card-sub">
                Isi data dasar berikut agar profil langsung tampil saat gantungan di-tap oleh siapa pun.
            </p>

            <form method="POST" action="{{ route('user.activation.profile') }}" enctype="multipart/form-data">
                @csrf

                {{-- Unggah Foto Profil --}}
                <div class="avatar-upload-wrap">
                    <input type="file" id="photoInput" name="photo" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
                    <div class="avatar-upload-circle" onclick="document.getElementById('photoInput').click()">
                        @if ($profile?->photo_path)
                            <img src="{{ asset('storage/' . $profile->photo_path) }}" alt="Foto Profil" class="avatar-preview-img" style="display: block;">
                        @else
                            <img src="" alt="Preview Foto" id="avatarPreview" class="avatar-preview-img">
                            <div class="avatar-placeholder" id="avatarPlaceholder">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span>Unggah</span>
                            </div>
                        @endif
                        <div class="avatar-plus-badge">+</div>
                    </div>
                    <span class="avatar-upload-label">Unggah Foto Profil (Maks 2MB)</span>
                    <span class="avatar-upload-sub">Ketuk untuk pilih dari galeri ponsel</span>
                </div>

                {{-- Nama Tampilan --}}
                <div class="form-group">
                    <div class="form-label-row">
                        <label for="display_name" class="form-label">Nama Tampilan *</label>
                        <span class="form-hint">Muncul paling atas</span>
                    </div>
                    <div class="input-container">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input type="text" id="display_name" name="display_name" class="form-input"
                               placeholder="Contoh: Andi Muhammad Fadil"
                               value="{{ old('display_name', $profile?->display_name ?? $user->name) }}" required>
                    </div>
                </div>

                {{-- Headline / Jabatan / Jurusan --}}
                <div class="form-group">
                    <div class="form-label-row">
                        <label for="headline" class="form-label">Headline / Jabatan / Jurusan</label>
                    </div>
                    <div class="input-container">
                        <span class="input-icon">
                            {{-- Graduation Cap Icon --}}
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                            </svg>
                        </span>
                        <input type="text" id="headline" name="headline" class="form-input"
                               placeholder="Contoh: Mahasiswa Teknik Informatika • Universitas..."
                               value="{{ old('headline', $profile?->headline) }}">
                    </div>
                    <p style="font-size: 0.7rem; color: #8C8476; margin-top: 0.35rem;">Bisa diubah setiap saat melalui aplikasi.</p>
                </div>

                {{-- Pilih Mode Awal Aktif --}}
                <div class="form-group">
                    <div class="form-label-row">
                        <label class="form-label">Pilih Mode Awal Aktif</label>
                        <span class="form-hint">Bisa ganti 1-tap nanti</span>
                    </div>

                    <input type="hidden" name="active_mode" id="activeModeInput" value="{{ old('active_mode', $profile?->active_mode ?? 'kampus') }}">

                    <div class="modes-grid">
                        {{-- Mode Kampus --}}
                        <div class="mode-option-card {{ ($profile?->active_mode ?? 'kampus') === 'kampus' ? 'selected' : '' }}"
                             onclick="selectMode('kampus', this)">
                            <div class="mode-card-top">
                                <span class="mode-icon">🎓</span>
                                <span class="mode-radio-indicator"></span>
                            </div>
                            <span class="mode-card-title">Mode Kampus</span>
                            <span class="mode-card-desc">Kartu ID Mahasiswa &amp; Portofolio</span>
                        </div>

                        {{-- Mode Kerja --}}
                        <div class="mode-option-card {{ ($profile?->active_mode ?? '') === 'kerja' ? 'selected' : '' }}"
                             onclick="selectMode('kerja', this)">
                            <div class="mode-card-top">
                                <span class="mode-icon">💼</span>
                                <span class="mode-radio-indicator"></span>
                            </div>
                            <span class="mode-card-title">Mode Kerja</span>
                            <span class="mode-card-desc">Kartu Nama Digital &amp; LinkedIn</span>
                        </div>

                        {{-- Mode UMKM --}}
                        <div class="mode-option-card {{ ($profile?->active_mode ?? '') === 'umkm' ? 'selected' : '' }}"
                             onclick="selectMode('umkm', this)">
                            <div class="mode-card-top">
                                <span class="mode-icon">🏪</span>
                                <span class="mode-radio-indicator"></span>
                            </div>
                            <span class="mode-card-title">Mode UMKM</span>
                            <span class="mode-card-desc">Katalog Produk &amp; Order WA</span>
                        </div>

                        {{-- Mode Penemu (Lost & Found) --}}
                        <div class="mode-option-card {{ ($profile?->active_mode ?? '') === 'hilang' ? 'selected' : '' }}"
                             onclick="selectMode('hilang', this)">
                            <div class="mode-card-top">
                                <span class="mode-icon">🔒</span>
                                <span class="mode-radio-indicator"></span>
                            </div>
                            <span class="mode-card-title">Mode Penemu</span>
                            <span class="mode-card-desc">Kontak Darurat &amp; Pesan Penemu</span>
                        </div>
                    </div>
                </div>

                {{-- Tautan Kontak Utama --}}
                <div class="form-group">
                    <div class="form-label-row">
                        <label for="no_whatsapp" class="form-label">Tautan Kontak Utama</label>
                    </div>

                    <div class="contact-input-wrap">
                        <div class="contact-prefix-btn">
                            {{-- WhatsApp Icon --}}
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="#25D366">
                                <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2z"/>
                            </svg>
                            <span>WA</span>
                            <small>▾</small>
                        </div>
                        <input type="text" id="no_whatsapp" name="no_whatsapp" class="contact-phone-input"
                               placeholder="0821-9988-7711"
                               value="{{ old('no_whatsapp', $user->no_whatsapp) }}">
                    </div>
                    <p style="font-size: 0.7rem; color: #8C8476; margin-top: 0.35rem;">Tombol aksi kilat saat orang lain menyentuhkan ponsel ke FlexiTag Anda.</p>
                </div>

                <button type="submit" class="btn-gold-primary" style="margin-top: 1.35rem;">
                    <span>Simpan &amp; Selesaikan</span>
                    <span>→</span>
                </button>
            </form>

            <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; font-size: 0.72rem; color: #787167; margin-top: 1rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span>Data terenkripsi &amp; privasi dapat Anda atur sepenuhnya</span>
            </div>
        </div>
    @endif


    {{-- ========================================================================= --}}
    {{-- LANGKAH 4: SELESAI                                                        --}}
    {{-- ========================================================================= --}}
    @if ($step === 4)
        {{-- Celebration Hero Banner --}}
        <div class="celebration-wrap">
            <div class="celebration-seal">
                <div class="seal-inner-circle">
                    {{-- Shield Checkmark --}}
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
            </div>

            <h2 class="celebration-title">Selamat! FlexiTag Anda Sudah Aktif</h2>
            <p class="celebration-sub">
                Gantungan kunci Anda kini terhubung ke profil digital dan siap dibagikan kapan saja dengan satu sentuhan.
            </p>
        </div>

        {{-- Profile Preview Card --}}
        <div class="profile-preview-card">
            <div class="preview-profile-head">
                <div class="preview-user-info">
                    <div class="preview-avatar-circle">
                        @if ($profile?->photo_path)
                            <img src="{{ asset('storage/' . $profile->photo_path) }}" alt="{{ $displayName }}">
                        @else
                            <span>{{ $initials }}</span>
                        @endif
                    </div>
                    <div>
                        <h4 class="preview-name">{{ $displayName }}</h4>
                        <p class="preview-headline">{{ $profile?->headline ?? 'Toraja Utara / Makassar' }}</p>
                    </div>
                </div>

                <span class="preview-mode-pill">{{ $modeBadgeText }}</span>
            </div>

            {{-- Link Tautan Publik --}}
            <div class="preview-link-bar">
                <span class="preview-link-text">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                    <span>flexitag.id/u/{{ $tagCode }}</span>
                </span>
                <button type="button" class="copy-link-btn" id="copyBtn" onclick="copyTagUrl('{{ $publicTagUrl }}')">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span id="copyBtnText">Salin</span>
                </button>
            </div>

            {{-- QR Cadangan --}}
            <div class="preview-qr-box">
                <div class="qr-code-graphic">
                    {{-- Clean Stylized QR Code with Center Gold Medallion --}}
                    <svg width="108" height="108" viewBox="0 0 100 100" fill="none">
                        {{-- Corner Squares --}}
                        <rect x="5" y="5" width="26" height="26" rx="4" fill="#6B0E12"/>
                        <rect x="9" y="9" width="18" height="18" rx="2" fill="#FFFFFF"/>
                        <rect x="13" y="13" width="10" height="10" rx="1" fill="#6B0E12"/>

                        <rect x="69" y="5" width="26" height="26" rx="4" fill="#6B0E12"/>
                        <rect x="73" y="9" width="18" height="18" rx="2" fill="#FFFFFF"/>
                        <rect x="77" y="13" width="10" height="10" rx="1" fill="#6B0E12"/>

                        <rect x="5" y="69" width="26" height="26" rx="4" fill="#6B0E12"/>
                        <rect x="9" y="73" width="18" height="18" rx="2" fill="#FFFFFF"/>
                        <rect x="13" y="77" width="10" height="10" rx="1" fill="#6B0E12"/>

                        {{-- QR Data Modules Pattern --}}
                        <rect x="36" y="8" width="6" height="6" fill="#6B0E12"/>
                        <rect x="46" y="8" width="6" height="6" fill="#6B0E12"/>
                        <rect x="56" y="8" width="6" height="6" fill="#6B0E12"/>
                        <rect x="36" y="18" width="6" height="6" fill="#6B0E12"/>
                        <rect x="56" y="18" width="6" height="6" fill="#6B0E12"/>

                        <rect x="8" y="36" width="6" height="6" fill="#6B0E12"/>
                        <rect x="18" y="36" width="6" height="6" fill="#6B0E12"/>
                        <rect x="8" y="46" width="6" height="6" fill="#6B0E12"/>
                        <rect x="24" y="46" width="6" height="6" fill="#6B0E12"/>
                        <rect x="8" y="56" width="6" height="6" fill="#6B0E12"/>
                        <rect x="18" y="56" width="6" height="6" fill="#6B0E12"/>

                        <rect x="76" y="36" width="6" height="6" fill="#6B0E12"/>
                        <rect x="86" y="36" width="6" height="6" fill="#6B0E12"/>
                        <rect x="70" y="46" width="6" height="6" fill="#6B0E12"/>
                        <rect x="86" y="46" width="6" height="6" fill="#6B0E12"/>
                        <rect x="76" y="56" width="6" height="6" fill="#6B0E12"/>
                        <rect x="86" y="56" width="6" height="6" fill="#6B0E12"/>

                        <rect x="36" y="72" width="6" height="6" fill="#6B0E12"/>
                        <rect x="52" y="72" width="6" height="6" fill="#6B0E12"/>
                        <rect x="42" y="82" width="6" height="6" fill="#6B0E12"/>
                        <rect x="58" y="82" width="6" height="6" fill="#6B0E12"/>

                        {{-- Central Gold Medallion --}}
                        <circle cx="50" cy="50" r="14" fill="#FFFFFF"/>
                        <circle cx="50" cy="50" r="11" fill="#E5B32A"/>
                        <circle cx="50" cy="50" r="5" fill="#6B0E12"/>
                    </svg>
                </div>
                <span class="qr-caption">QR Cadangan (Untuk ponsel tanpa NFC)</span>
            </div>

            <div class="nfc-ready-row">
                <span style="color: #D97706; font-size: 1rem;">⚡</span>
                <span>Siap di-tap ke semua iPhone &amp; Android ber-NFC tanpa perlu unduh aplikasi.</span>
            </div>
        </div>

        {{-- Action Buttons --}}
        <a href="{{ route('user.dashboard') }}" class="btn-gold-primary">
            <span>Buka Dashboard Pemilik</span>
            <span>→</span>
        </a>

        <a href="{{ route('public.tag', ['tag_code' => $tagCode]) }}" class="btn-secondary-outline" target="_blank">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
            <span>Lihat Profil Publik Saya</span>
        </a>
    @endif


    {{-- Heritage Footer --}}
    <footer class="wizard-footer">
        <div class="footer-diamonds">
            <span>✦</span>
            <span>✦</span>
            <span>✦</span>
        </div>
        <p class="footer-heritage-text">KARYA DIGITAL • BERJIWA NUSANTARA</p>
        <p class="footer-copy">© 2026 FlexiTag Makassar • Keamanan Terenkripsi</p>
    </footer>

</div>

{{-- Guide Card Modal (Step 2) --}}
<div class="modal-overlay" id="guideModal" onclick="if(event.target === this) closeGuideModal()">
    <div class="modal-content">
        <button type="button" class="modal-close-btn" onclick="closeGuideModal()">✕</button>

        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--ft-maroon); margin-bottom: 0.5rem;">Kartu Panduan Aktivasi</h3>
        <p style="font-size: 0.78rem; color: #6C665E; margin-bottom: 1rem; line-height: 1.45;">
            Buka kemasan kotak gantungan kunci FlexiTag Anda. Pada bagian belakang kartu panduan, terdapat kode aktivasi unik bertuliskan <strong>KD-XXXX-XX</strong>.
        </p>

        <div style="background: #FFFBF5; border: 1.5px dashed #E5B32A; border-radius: 12px; padding: 1rem; text-align: center; margin-bottom: 1rem;">
            <span style="font-size: 0.68rem; font-weight: 700; color: #8A7130; letter-spacing: 0.1em; text-transform: uppercase;">Contoh Kartu Panduan</span>
            <div style="font-family: monospace; font-size: 1.25rem; font-weight: 800; color: #6B0E12; letter-spacing: 0.2em; margin: 0.65rem 0 0.35rem;">
                KD-8821-XQ
            </div>
            <small style="color: #A39A8E; font-size: 0.68rem;">Kode sekali pakai untuk registrasi kepemilikan tag</small>
        </div>

        <button type="button" class="btn-gold-primary" onclick="applySampleCode('KD-8821-XQ')">
            Gunakan Kode Ini
        </button>
    </div>
</div>

<script>
    // Tab switching for Step 1
    function switchTab(tab) {
        var formReg = document.getElementById('formRegister');
        var formLogin = document.getElementById('formLogin');
        var btnReg = document.getElementById('tabBtnRegister');
        var btnLogin = document.getElementById('tabBtnLogin');

        if (!formReg || !formLogin) return;

        if (tab === 'login') {
            formReg.style.display = 'none';
            formLogin.style.display = 'block';
            btnReg.classList.remove('active');
            btnLogin.classList.add('active');
        } else {
            formLogin.style.display = 'none';
            formReg.style.display = 'block';
            btnLogin.classList.remove('active');
            btnReg.classList.add('active');
        }
    }

    // Password visibility toggle
    function togglePasswordVisibility(inputId) {
        var input = document.getElementById(inputId);
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    // Modal Guide
    function openGuideModal() {
        var m = document.getElementById('guideModal');
        if (m) m.style.display = 'flex';
    }
    function closeGuideModal() {
        var m = document.getElementById('guideModal');
        if (m) m.style.display = 'none';
    }
    function applySampleCode(code) {
        var input = document.getElementById('activation_code');
        if (input) input.value = code;
        closeGuideModal();
    }

    // Paste from clipboard
    async function pasteActivationCode() {
        try {
            if (navigator.clipboard && navigator.clipboard.readText) {
                var text = await navigator.clipboard.readText();
                if (text) {
                    var input = document.getElementById('activation_code');
                    if (input) input.value = text.trim().toUpperCase();
                }
            } else {
                applySampleCode('KD-8821-XQ');
            }
        } catch (e) {
            applySampleCode('KD-8821-XQ');
        }
    }

    // Profile photo preview
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('avatarPreview');
                var placeholder = document.getElementById('avatarPlaceholder');
                if (preview) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Mode selection (Step 3)
    function selectMode(mode, element) {
        var input = document.getElementById('activeModeInput');
        if (input) input.value = mode;

        var allCards = document.querySelectorAll('.mode-option-card');
        allCards.forEach(function(card) {
            card.classList.remove('selected');
        });
        element.classList.add('selected');
    }

    // Copy public tag link (Step 4)
    async function copyTagUrl(url) {
        try {
            await navigator.clipboard.writeText(url);
            var btnText = document.getElementById('copyBtnText');
            if (btnText) {
                var oldText = btnText.innerText;
                btnText.innerText = 'Tersalin!';
                setTimeout(function() {
                    btnText.innerText = oldText;
                }, 2000);
            }
        } catch (err) {
            // Fallback prompt
            prompt('Salin tautan FlexiTag Anda:', url);
        }
    }
</script>

</body>
</html>
