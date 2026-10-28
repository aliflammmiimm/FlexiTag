<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi Reset Kata Sandi</title>
</head>
<body style="font-family: 'Poppins', -apple-system, BlinkMacSystemFont, Arial, sans-serif; background-color: #FAF9F6; margin: 0; padding: 24px; color: #1F1915;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 580px; margin: 0 auto; background-color: #FFFFFF; border-radius: 18px; border: 1px solid #ECE7DF; overflow: hidden; box-shadow: 0 4px 18px rgba(74, 10, 14, 0.06);">
        <!-- Header -->
        <tr>
            <td style="background: linear-gradient(135deg, #4A0A0E 0%, #2B0407 100%); padding: 26px 24px; text-align: center; border-bottom: 3px solid #D4AF37;">
                <div style="color: #D4AF37; font-size: 11px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 4px;">FLEXITAG MACCA TECH</div>
                <h1 style="color: #FFFFFF; font-size: 20px; font-weight: 800; margin: 0;">Verifikasi Reset Kata Sandi</h1>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 30px 24px;">
                <p style="font-size: 15px; line-height: 1.6; margin-top: 0; margin-bottom: 14px;">
                    Halo <strong>{{ $user->name }}</strong>,
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #57534E; margin-bottom: 22px;">
                    Kami menerima permintaan pengaturan ulang kata sandi untuk akun FlexiTag yang terhubung dengan email <strong>{{ $user->email }}</strong>. Gunakan kode verifikasi 6 digit di bawah ini untuk melanjutkan:
                </p>

                <!-- Box Kode Verifikasi 6 Digit -->
                <div style="background-color: #FAF8F5; border: 2px dashed #D4AF37; border-radius: 14px; padding: 24px 16px; margin: 24px 0; text-align: center;">
                    <div style="font-size: 11px; font-weight: 700; color: #78716C; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 8px;">KODE VERIFIKASI 6 DIGIT</div>
                    <div style="font-family: 'Courier New', Courier, monospace; font-size: 38px; font-weight: 800; letter-spacing: 10px; color: #4A0A0E; margin: 8px 0; padding-left: 10px;">
                        {{ $code }}
                    </div>
                    <div style="font-size: 12px; font-weight: 600; color: #854D0E; margin-top: 8px;">
                        ⏳ Berlaku selama 60 menit. Jangan berikan kode ini kepada siapa pun!
                    </div>
                </div>

                <p style="font-size: 13.5px; line-height: 1.6; color: #57534E; margin-bottom: 24px; text-align: center;">
                    Masukkan kode 6 digit di atas langsung pada formulir di situs web FlexiTag untuk menyimpan kata sandi baru Anda.
                </p>

                <!-- Tombol Buka Website -->
                <div style="text-align: center; margin-bottom: 26px;">
                    <a href="{{ route('password.reset', ['email' => $user->email]) }}" style="display: inline-block; background: #4A0A0E; color: #FFFFFF; text-decoration: none; font-size: 14px; font-weight: 700; padding: 12px 28px; border-radius: 10px; border: 1px solid #D4AF37;">
                        Buka Halaman Input Token di Website →
                    </a>
                </div>

                <!-- Catatan Keamanan -->
                <div style="background-color: #F5F5F4; border-radius: 10px; padding: 14px 16px; font-size: 12px; color: #78716C; line-height: 1.55;">
                    <strong style="color: #44403C;">🛡️ Informasi Keamanan:</strong><br>
                    Jika Anda tidak pernah meminta pengaturan ulang kata sandi ini, abaikan email ini. Akun Anda tetap aman dan kata sandi Anda tidak akan berubah sebelum diverifikasi.
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #FAF8F5; border-top: 1px solid #ECE7DF; padding: 18px 24px; text-align: center; font-size: 11.5px; color: #78716C;">
                <div style="font-weight: 700; color: #4A0A0E; margin-bottom: 4px;">FlexiTag Smart Keychain System</div>
                <div>Solusi Gantungan Kunci Cerdas NFC/QR dengan Identitas Digital Terintegrasi.</div>
            </td>
        </tr>
    </table>
</body>
</html>
