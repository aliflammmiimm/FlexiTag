<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile?->display_name ?? $user?->name ?? 'Profil Publik' }} — {{ config('flexitag.brand') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ft-maroon: #6B0E12;
            --ft-gold: #E5B32A;
            --ft-cream: #FAF8F5;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(180deg, #F9F6F0 0%, #EFE8DC 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            color: #2D2726;
        }
        .profile-card {
            width: 100%;
            max-width: 420px;
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 12px 36px rgba(45, 14, 18, 0.08);
            border: 1px solid #ECE5D8;
            overflow: hidden;
            text-align: center;
            position: relative;
        }
        .profile-banner {
            height: 96px;
            background: radial-gradient(circle at 50% 30%, #8B1218 0%, #4D0609 100%);
            position: relative;
        }
        .profile-avatar-wrap {
            margin-top: -46px;
            position: relative;
            display: inline-block;
        }
        .profile-avatar {
            width: 92px;
            height: 92px;
            border-radius: 50%;
            border: 4px solid #FFFFFF;
            background: var(--ft-maroon);
            color: #FFFFFF;
            font-size: 2rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
            overflow: hidden;
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-body {
            padding: 1rem 1.5rem 2rem;
        }
        .profile-name {
            font-size: 1.35rem;
            font-weight: 800;
            color: #211B1A;
            margin-top: 0.25rem;
        }
        .profile-headline {
            font-size: 0.82rem;
            color: #7A7268;
            margin-top: 0.2rem;
        }
        .mode-pill {
            display: inline-block;
            background: #FCF5E3;
            color: #8C6D23;
            border: 1px solid #E5B32A;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 9999px;
            margin: 0.85rem 0 1.25rem;
        }
        .links-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .link-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            padding: 0.85rem 1rem;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
            transition: all 0.2s ease;
        }
        .link-btn-wa {
            background: #25D366;
            color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(37, 211, 102, 0.3);
        }
        .link-btn-wa:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 211, 102, 0.4);
        }
        .footer-brand {
            font-size: 0.7rem;
            color: #A39B8F;
            border-top: 1px solid #F0ECE3;
            padding-top: 1rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>

@php
    $displayName = $profile?->display_name ?? $user?->name ?? 'Pengguna FlexiTag';
    $parts = explode(' ', trim($displayName));
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $initials .= mb_substr($p, 0, 1);
    }
    $initials = strtoupper($initials) ?: 'FT';

    $modeText = match($profile?->active_mode ?? 'kampus') {
        'kerja' => 'Mode Kerja 💼',
        'umkm' => 'Mode UMKM 🏪',
        'hilang' => 'Mode Penemu 🔒',
        default => 'Mode Kampus 🎓',
    };
@endphp

<div class="profile-card">
    <div class="profile-banner"></div>
    <div class="profile-avatar-wrap">
        <div class="profile-avatar">
            @if ($profile?->photo_path)
                <img src="{{ asset('storage/' . $profile->photo_path) }}" alt="{{ $displayName }}">
            @else
                <span>{{ $initials }}</span>
            @endif
        </div>
    </div>

    <div class="profile-body">
        <h2 class="profile-name">{{ $displayName }}</h2>
        <p class="profile-headline">{{ $profile?->headline ?? 'Toraja Utara / Makassar' }}</p>

        <span class="mode-pill">{{ $modeText }}</span>

        @if (($profile?->active_mode ?? 'kampus') === 'hilang')
            {{-- Mode Hilang / Penemu Display --}}
            <div style="background: #FEF2F2; border: 1.5px solid #FCA5A5; border-radius: 16px; padding: 1.2rem; margin-bottom: 1.5rem; text-align: left;">
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #991B1B; font-weight: 800; font-size: 0.88rem; margin-bottom: 0.5rem;">
                    <span>🛡️</span>
                    <span>PESAN DARURAT PEMILIK</span>
                </div>
                <p style="font-size: 0.82rem; color: #4B1A1A; line-height: 1.5; margin-bottom: 0.75rem;">
                    {{ $modeData['message'] ?? 'Barang ini milik saya dan berharga. Jika Anda menemukannya, mohon bantu hubungi saya.' }}
                </p>
                @if (!empty($modeData['reward']))
                    <div style="background: #FDF6B2; border: 1px solid #FCE96A; border-radius: 8px; padding: 0.5rem 0.75rem; font-size: 0.76rem; color: #723B13; font-weight: 700; margin-bottom: 0.75rem;">
                        🎁 Imbalan bagi penemu: {{ $modeData['reward'] }}
                    </div>
                @endif
                @php
                    $relayContact = $modeData['relay_phone'] ?? $user?->no_whatsapp;
                @endphp
                @if ($relayContact)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $relayContact) }}?text={{ urlencode('Halo, saya menemukan barang bertag FlexiTag Anda: ' . $tag->tag_code) }}" target="_blank" rel="noopener" class="link-btn link-btn-wa" style="margin-top: 0.5rem;">
                        <span>💬 Hubungi Pemilik via Relay Aman</span>
                    </a>
                @endif
            </div>
        @else
            <div class="links-list">
                @if ($user?->no_whatsapp)
                    <a href="https://wa.me/{{ $user->no_whatsapp }}" target="_blank" rel="noopener" class="link-btn link-btn-wa">
                        <span>💬 Hubungi via WhatsApp</span>
                    </a>
                @endif

                @foreach ($links as $link)
                    @if ($link->type !== 'whatsapp')
                        <a href="{{ $link->url }}" target="_blank" rel="noopener" class="link-btn" style="background:#F7F4EC; color:#423A36; border:1px solid #EAE3D5;">
                            <span>🔗 {{ $link->label }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        <div class="footer-brand">
            Ditenagai oleh <b>{{ config('flexitag.brand', 'FlexiTag') }}</b> — {{ config('flexitag.tagline', 'Satu Tap, Seribu Identitas') }}
        </div>
    </div>
</div>

</body>
</html>
