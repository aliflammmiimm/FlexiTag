<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{ $isOwnerLocked ? 'Tag Dikunci Sementara' : 'Tag Dinonaktifkan' }} — FlexiTag</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body {
            background-color: #FAF9F6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            color: #1F2937;
        }
        .locked-card {
            background: #FFFFFF;
            width: 100%;
            max-width: 440px;
            border-radius: 24px;
            border: 1px solid #ECE7DE;
            box-shadow: 0 10px 40px rgba(74, 10, 14, 0.08);
            overflow: hidden;
            text-align: center;
        }
        .locked-banner {
            background: linear-gradient(135deg, #44070B 0%, #2A0407 100%);
            padding: 2.25rem 1.5rem 1.75rem;
            color: #FFFFFF;
            position: relative;
        }
        .locked-watermark {
            position: absolute;
            top: -20px;
            right: -20px;
            width: 140px;
            height: 140px;
            opacity: 0.12;
            pointer-events: none;
        }
        .locked-icon-bubble {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            border: 2px solid {{ $isOwnerLocked ? '#FACC15' : '#F87171' }};
            color: {{ $isOwnerLocked ? '#FACC15' : '#FCA5A5' }};
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .locked-tag-id {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
            color: #FEF08A;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.6rem;
        }
        .locked-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 0.25rem;
        }
        .locked-body {
            padding: 1.75rem 1.5rem 2rem;
        }
        .locked-alert-box {
            background: {{ $isOwnerLocked ? '#FEF3C7' : '#FEE2E2' }};
            border: 1px solid {{ $isOwnerLocked ? '#FDE68A' : '#FECACA' }};
            border-radius: 16px;
            padding: 1rem 1.15rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .alert-box-title {
            font-size: 0.84rem;
            font-weight: 700;
            color: {{ $isOwnerLocked ? '#92400E' : '#991B1B' }};
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 0.35rem;
        }
        .alert-box-desc {
            font-size: 0.78rem;
            color: {{ $isOwnerLocked ? '#78350F' : '#7F1D1D' }};
            line-height: 1.45;
        }
        .locked-meta-card {
            background: #F9F8F6;
            border: 1px solid #ECE7DE;
            border-radius: 14px;
            padding: 0.95rem 1.1rem;
            font-size: 0.78rem;
            color: #6B7280;
            line-height: 1.4;
            margin-bottom: 1.5rem;
        }
        .locked-footer {
            font-size: 0.74rem;
            color: #9CA3AF;
            border-top: 1px solid #F3EFE9;
            padding-top: 1rem;
        }
    </style>
</head>
<body>

<div class="locked-card">
    <div class="locked-banner">
        {{-- Pa'barre Allo Watermark --}}
        <svg class="locked-watermark" viewBox="0 0 100 100" fill="none" stroke="#FFFFFF" stroke-width="2">
            <circle cx="50" cy="50" r="45" stroke-dasharray="4 2"/>
            <circle cx="50" cy="50" r="28"/>
            <line x1="50" y1="5" x2="50" y2="95"/>
            <line x1="5" y1="50" x2="95" y2="50"/>
        </svg>

        <div class="locked-icon-bubble">
            @if ($isOwnerLocked)
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            @else
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                </svg>
            @endif
        </div>

        <span class="locked-tag-id">{{ $tag->tag_code }}</span>
        <h1 class="locked-title">
            {{ $isOwnerLocked ? 'Proteksi Kill Switch Aktif' : 'Perangkat Dinonaktifkan' }}
        </h1>
    </div>

    <div class="locked-body">
        <div class="locked-alert-box">
            <div class="alert-box-title">
                <span>{{ $isOwnerLocked ? '🔒' : '⚠️' }}</span>
                <span>{{ $isOwnerLocked ? 'Kunci Sementara oleh Pemilik' : 'Penangguhan oleh Administrator' }}</span>
            </div>
            <p class="alert-box-desc">
                @if ($isOwnerLocked)
                    Pemilik perangkat ini sedang mengaktifkan sakelar <strong>Kill Switch</strong> untuk melindungi privasi. Seluruh data profil publik disembunyikan sementara hingga pemilik membuka kembali kuncinya.
                @else
                    Perangkat FlexiTag ini telah dinonaktifkan oleh administrator pengelola sistem karena alasan kebijakan, keamanan, atau tindak lanjut laporan.
                @endif
            </p>
        </div>

        <div class="locked-meta-card">
            🛡️ <strong>Protokol Keamanan FlexiTag:</strong><br>
            Dilindungi arsitektur Zero-Knowledge & Hardware Kill Switch • Makassar Heritage Edition.
        </div>

        <div class="locked-footer">
            FlexiTag — Satu Tap, Seribu Identitas • © 2026 Makassar
        </div>
    </div>
</div>

</body>
</html>
