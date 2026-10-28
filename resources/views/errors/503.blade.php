<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Sistem Dalam Pemeliharaan — FlexiTag MaccaTech</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ft-maroon: #4A0A0E;
            --ft-maroon-dark: #2B0407;
            --ft-gold: #D4AF37;
            --ft-gold-soft: #FEF3C7;
            --ft-sand: #FAF9F6;
            --ft-text-dark: #1F1915;
            --ft-text-muted: #78716C;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--ft-sand);
            color: var(--ft-text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            position: relative;
            overflow-x: hidden;
        }
        .bg-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(var(--ft-gold) 0.75px, transparent 0.75px);
            background-size: 24px 24px;
            opacity: 0.18;
            pointer-events: none;
        }
        .maintenance-card {
            background: #FFFFFF;
            width: 100%;
            max-width: 520px;
            border-radius: 24px;
            border: 1px solid #ECE7DF;
            box-shadow: 0 16px 48px rgba(74, 10, 14, 0.08);
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            z-index: 10;
        }
        .icon-shield-wrap {
            width: 76px;
            height: 76px;
            margin: 0 auto 1.5rem;
            border-radius: 22px;
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            border: 2px solid var(--ft-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.25);
            font-size: 2.2rem;
        }
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
            border-radius: 999px;
            padding: 0.3rem 0.85rem;
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }
        .badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #DC2626;
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }
        h1.maintenance-title {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--ft-maroon);
            margin-bottom: 0.85rem;
            line-height: 1.35;
        }
        p.maintenance-desc {
            font-size: 0.88rem;
            color: var(--ft-text-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .info-box {
            background: #FAF8F5;
            border: 1px solid #ECE7DF;
            border-radius: 14px;
            padding: 1rem 1.15rem;
            font-size: 0.8rem;
            color: #57534E;
            line-height: 1.5;
            margin-bottom: 1.75rem;
            text-align: left;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .btn-wa-support {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--ft-maroon) 0%, var(--ft-maroon-dark) 100%);
            color: #FFFFFF;
            text-decoration: none;
            padding: 0.85rem 1.5rem;
            border-radius: 14px;
            font-size: 0.85rem;
            font-weight: 700;
            width: 100%;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(74, 10, 14, 0.22);
            border: 1px solid var(--ft-gold);
        }
        .btn-wa-support:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 10, 14, 0.3);
            background: var(--ft-maroon-dark);
        }
        .maintenance-footer {
            margin-top: 1.75rem;
            font-size: 0.72rem;
            color: #A8A29E;
        }
    </style>
</head>
<body>

<div class="bg-pattern" aria-hidden="true"></div>

<main class="maintenance-card">
    <div class="icon-shield-wrap" aria-hidden="true">
        🛠️
    </div>

    <div class="badge-status">
        <span class="badge-dot"></span>
        <span>Pemeliharaan Sistem Rutin</span>
    </div>

    <h1 class="maintenance-title">Sistem Sedang Ditingkatkan</h1>

    <p class="maintenance-desc">
        Kami sedang melakukan optimalisasi infrastruktur dan pembaruan berkala pada ekosistem cerdas <strong>FlexiTag</strong> agar performa tap NFC dan akses profil Anda semakin cepat.
    </p>

    <div class="info-box">
        <span style="font-size: 1.2rem; line-height: 1;">💡</span>
        <div>
            <strong>Informasi untuk Pemilik:</strong> Seluruh konfigurasi profil, data tag, dan pesan darurat Anda tetap tersimpan aman di server kami. Akses akan segera dipulihkan secepatnya.
        </div>
    </div>

    @php
        $csWa = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'no_wa_cs_pusat')->value('value') ?? '+62 812-3456-7890';
        $cleanWa = preg_replace('/[^0-9]/', '', (string)$csWa);
        if (str_starts_with($cleanWa, '0')) {
            $cleanWa = '62' . substr($cleanWa, 1);
        }
    @endphp

    <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('Halo Tim MaccaTech, saya butuh informasi terkait status pemeliharaan sistem FlexiTag.') }}" 
       target="_blank" 
       rel="noopener noreferrer" 
       class="btn-wa-support">
        <span>💬 Hubungi Customer Support via WhatsApp</span>
    </a>

    <div class="maintenance-footer">
        Ekosistem Smart Merchandise • MaccaTech Studio Makassar
    </div>
</main>

</body>
</html>
