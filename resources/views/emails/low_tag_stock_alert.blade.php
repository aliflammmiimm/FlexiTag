<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Peringatan: Stok Kode Aktivasi Tag Menipis</title>
</head>
<body style="font-family: 'Poppins', -apple-system, BlinkMacSystemFont, Arial, sans-serif; background-color: #FAF9F6; margin: 0; padding: 24px; color: #1F1915;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 580px; margin: 0 auto; background-color: #FFFFFF; border-radius: 18px; border: 1px solid #FECACA; overflow: hidden; box-shadow: 0 4px 18px rgba(185, 28, 28, 0.08);">
        
        <!-- Header Alert Red -->
        <tr>
            <td style="background: linear-gradient(135deg, #7F1D1D 0%, #450A0A 100%); padding: 26px 24px; text-align: center; border-bottom: 3px solid #F87171;">
                <div style="color: #FECACA; font-size: 11px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 4px;">SISTEM MONITORING STOK HARDWARE</div>
                <h1 style="color: #FFFFFF; font-size: 21px; font-weight: 800; margin: 0; line-height: 1.3;">
                    ⚠️ Peringatan: Stok Kode Tag Menipis!
                </h1>
                <p style="color: #FCA5A5; font-size: 13px; margin: 6px 0 0;">
                    Sisa stok belum diaktivasi: <strong style="color: #FFFFFF; background: #991B1B; padding: 2px 8px; border-radius: 4px;">{{ $remainingCount }} Unit (&lt; 10)</strong>
                </p>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 28px 24px;">
                <p style="font-size: 14px; line-height: 1.6; margin-top: 0; margin-bottom: 16px;">
                    Halo <strong>Tim Pengelola / Administrator FlexiTag</strong>,
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #57534E; margin-bottom: 20px;">
                    Sistem mendeteksi bahwa jumlah gantungan kunci NFC yang <strong>belum diaktivasi dan belum terpakai</strong> saat ini tersisa kurang dari 10 unit, yaitu tersisa 
                    <strong style="color: #991B1B;">{{ $remainingCount }} unit</strong>.
                </p>

                <!-- Box Peringatan Dampak -->
                <div style="background-color: #FEF2F2; border: 1.5px solid #FCA5A5; border-radius: 12px; padding: 16px; margin-bottom: 22px;">
                    <div style="color: #991B1B; font-weight: 700; font-size: 13px; margin-bottom: 6px;">
                        🚨 Dampak Jika Stok Kehabisan:
                    </div>
                    <p style="font-size: 13px; color: #7F1D1D; margin: 0; line-height: 1.5;">
                        Ketika pesanan pengguna baru disetujui dan statusnya diubah menjadi <em>"Siap Diambil / Dikirim"</em>, sistem tidak akan dapat memilih dan mengirimkan kode aktivasi secara otomatis ke email pengguna jika stok kosong.
                    </p>
                </div>

                <!-- Statistik Stok Hardware Saat Ini -->
                <div style="background-color: #FAF8F5; border: 1px solid #ECE7DF; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
                    <h3 style="color: #4A0A0E; font-size: 13px; font-weight: 700; text-transform: uppercase; margin: 0 0 12px;">
                        📊 Ringkasan Inventaris Tag NFC:
                    </h3>
                    <table width="100%" cellspacing="0" cellpadding="6" style="font-size: 13px;">
                        <tr style="border-bottom: 1px dashed #EAE4D9;">
                            <td style="color: #78716C;">Sisa Stok Tag Siap Alokasi:</td>
                            <td style="text-align: right;"><strong style="color: #991B1B; font-size: 15px;">{{ $remainingCount }} unit</strong></td>
                        </tr>
                        <tr style="border-bottom: 1px dashed #EAE4D9;">
                            <td style="color: #78716C;">Total Tag Aktif Terhubung:</td>
                            <td style="text-align: right;"><strong style="color: #166534;">{{ $activeCount }} unit</strong></td>
                        </tr>
                        <tr>
                            <td style="color: #78716C;">Total Keseluruhan Diproduksi:</td>
                            <td style="text-align: right;"><strong style="color: #1F1915;">{{ $totalCount }} unit</strong></td>
                        </tr>
                    </table>
                </div>

                <!-- Action Button -->
                <div style="text-align: center; margin-bottom: 24px;">
                    <a href="{{ route('admin.generate') }}" style="display: inline-block; background-color: #8B0000; color: #FFFFFF; font-size: 14px; font-weight: 700; text-decoration: none; padding: 14px 28px; border-radius: 30px; border: 1px solid #D4AF37; box-shadow: 0 4px 12px rgba(139, 0, 0, 0.25);">
                        + Generate Batch Tag Baru Sekarang →
                    </a>
                </div>

                <p style="font-size: 12px; color: #78716C; margin: 0; line-height: 1.5; text-align: center;">
                    Buka menu <strong>Admin Console → Generate Tag</strong> untuk mencetak batch baru dan mengekspor berkas CSV kode aktivasi.
                </p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #FAF8F5; padding: 16px 24px; text-align: center; border-top: 1px solid #ECE7DF; font-size: 11px; color: #A8A29E;">
                FlexiTag Automated Dispatcher • Notifikasi Sistem Admin
            </td>
        </tr>

    </table>
</body>
</html>
