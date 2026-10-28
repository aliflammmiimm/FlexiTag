<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pemberitahuan Barang Ditemukan</title>
</head>
<body style="font-family: 'Poppins', -apple-system, BlinkMacSystemFont, Arial, sans-serif; background-color: #FAF9F6; margin: 0; padding: 24px; color: #1F1915;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 580px; margin: 0 auto; background-color: #FFFFFF; border-radius: 18px; border: 1px solid #ECE7DF; overflow: hidden; box-shadow: 0 4px 18px rgba(74, 10, 14, 0.06);">
        <!-- Header -->
        <tr>
            <td style="background: linear-gradient(135deg, #4A0A0E 0%, #2B0407 100%); padding: 24px; text-align: center; border-bottom: 3px solid #D4AF37;">
                <div style="color: #D4AF37; font-size: 11px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 4px;">FLEXITAG MACCA TECH</div>
                <h1 style="color: #FFFFFF; font-size: 20px; font-weight: 800; margin: 0;">Pemberitahuan Barang Ditemukan!</h1>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 28px 24px;">
                <p style="font-size: 14px; line-height: 1.6; margin-top: 0; margin-bottom: 16px;">
                    Halo <strong>{{ $user->name }}</strong>,
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #57534E; margin-bottom: 20px;">
                    Seseorang telah memindai gantungan kunci FlexiTag Anda dengan kode <strong>{{ $tag->tag_code }}</strong> dan mengirimkan laporan penemuan melalui formulir darurat Mode Hilang.
                </p>

                <!-- Box Laporan -->
                <div style="background-color: #FAF8F5; border: 1px solid #EAE4D9; border-left: 4px solid #DC2626; border-radius: 12px; padding: 18px; margin-bottom: 22px;">
                    <div style="font-size: 12px; color: #78716C; margin-bottom: 4px;">Penemu Barang:</div>
                    <div style="font-size: 15px; font-weight: 700; color: #1F1915; margin-bottom: 12px;">
                        {{ $lostMessage->sender_name ?: 'Penemu Anonim' }}
                    </div>

                    <div style="font-size: 12px; color: #78716C; margin-bottom: 4px;">Lokasi Penemuan:</div>
                    <div style="font-size: 13px; font-weight: 600; color: #B45309; margin-bottom: 12px;">
                        📍 {{ $lostMessage->location }}
                    </div>

                    <div style="font-size: 12px; color: #78716C; margin-bottom: 4px;">Isi Pesan Penemu:</div>
                    <div style="font-size: 13px; color: #1F1915; line-height: 1.6; background: #FFFFFF; border: 1px solid #ECE7DF; padding: 12px; border-radius: 8px; font-style: italic; margin-bottom: 12px;">
                        &ldquo;{{ trim($lostMessage->message) }}&rdquo;
                    </div>

                    @if($lostMessage->contact)
                        <div style="font-size: 12px; color: #78716C; margin-bottom: 4px;">Kontak Penemu:</div>
                        <div style="font-size: 13px; font-weight: 700; color: #166534;">
                            📞 {{ $lostMessage->contact }}
                        </div>
                    @endif
                </div>

                <!-- Call to action button -->
                <div style="text-align: center; margin-bottom: 24px;">
                    <a href="{{ route('user.dashboard') }}" style="display: inline-block; background: #4A0A0E; color: #FFFFFF; text-decoration: none; font-size: 14px; font-weight: 700; padding: 12px 28px; border-radius: 10px; border: 1px solid #D4AF37;">
                        Buka Dashboard &amp; Hubungi Penemu →
                    </a>
                </div>

                <p style="font-size: 12px; color: #78716C; line-height: 1.5; margin: 0; text-align: center;">
                    Pesan ini dikirimkan melalui Sistem Relay Aman FlexiTag. Kontak pribadi Anda terlindungi dan tidak dibocorkan kepada penemu barang.
                </p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #FAF8F5; padding: 16px 24px; text-align: center; border-top: 1px solid #ECE7DF; font-size: 11px; color: #A8A29E;">
                FlexiTag Smart Merchandise • Edisi Budaya Sulawesi • MaccaTech Studio
            </td>
        </tr>
    </table>
</body>
</html>
