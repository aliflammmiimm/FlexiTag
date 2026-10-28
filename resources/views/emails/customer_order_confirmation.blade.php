<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Konfirmasi Pesanan FlexiTag — #{{ $order->order_no }}</title>
</head>
<body style="font-family: 'Poppins', -apple-system, BlinkMacSystemFont, Arial, sans-serif; background-color: #FAF9F6; margin: 0; padding: 24px; color: #1F1915;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: 0 auto; background-color: #FFFFFF; border-radius: 18px; border: 1px solid #ECE7DF; overflow: hidden; box-shadow: 0 4px 20px rgba(74, 10, 14, 0.08);">
        
        <!-- Header Brand -->
        <tr>
            <td style="background: linear-gradient(135deg, #4A0A0E 0%, #2B0407 100%); padding: 28px 24px; text-align: center; border-bottom: 3px solid #D4AF37;">
                <div style="color: #D4AF37; font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; margin-bottom: 6px;">FLEXITAG MAKASSAR • SMART NFC KEYCHAIN</div>
                <h1 style="color: #FFFFFF; font-size: 22px; font-weight: 800; margin: 0; line-height: 1.3;">
                    🛒 Pesanan Anda Berhasil Kami Terima!
                </h1>
                <p style="color: #F5EBE6; font-size: 13px; margin: 6px 0 0; opacity: 0.9;">
                    Nomor Pesanan: <strong style="color: #F0C75E; letter-spacing: 0.5px;">#{{ $order->order_no }}</strong>
                </p>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 28px 24px;">
                <p style="font-size: 15px; line-height: 1.6; margin-top: 0; margin-bottom: 14px;">
                    Halo, <strong style="color: #4A0A0E;">{{ $order->customer_name }}</strong>! 👋
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #57534E; margin-bottom: 20px;">
                    Terima kasih telah memesan merchandise gantungan kunci akrilik NFC custom di <strong>FlexiTag Makassar</strong>. Rincian pesanan Anda telah berhasil terdaftar ke dalam antrean sistem produksi kami.
                </p>

                <!-- Box Status Pesanan Saat Ini -->
                <div style="background-color: #FFFDF8; border-left: 4px solid #D4AF37; padding: 12px 16px; margin-bottom: 22px; border-radius: 0 10px 10px 0;">
                    <div style="font-size: 12px; color: #78716C; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">STATUS PESANAN SAAT INI:</div>
                    <div style="font-size: 15px; font-weight: 800; color: #8B0000; margin-top: 3px;">
                        {{ $order->detail_status_label }}
                    </div>
                </div>

                <!-- Box Rincian Pesanan -->
                <div style="background-color: #FAF8F5; border: 1px solid #ECE7DF; border-radius: 14px; padding: 18px 20px; margin-bottom: 24px;">
                    <h3 style="color: #4A0A0E; font-size: 13px; font-weight: 700; text-transform: uppercase; margin: 0 0 14px; letter-spacing: 0.06em; border-bottom: 1px dashed #E5DFD5; padding-bottom: 8px;">
                        📋 Rincian Spesifikasi Pesanan:
                    </h3>
                    <table width="100%" cellspacing="0" cellpadding="6" style="font-size: 13px;">
                        <tr>
                            <td style="color: #78716C; width: 38%;">Bentuk &amp; Ukuran:</td>
                            <td style="color: #1F1915; font-weight: 600;">{{ ucfirst($order->shape) }} ({{ $order->size }})</td>
                        </tr>
                        <tr>
                            <td style="color: #78716C;">Motif / Desain:</td>
                            <td style="color: #1F1915; font-weight: 600;">{{ $order->color }}</td>
                        </tr>
                        <tr>
                            <td style="color: #78716C;">Jumlah Pesanan:</td>
                            <td style="color: #1F1915; font-weight: 700;">{{ $order->quantity }} Unit</td>
                        </tr>
                        <tr>
                            <td style="color: #78716C;">Metode Penyerahan:</td>
                            <td style="color: #1F1915; font-weight: 600;">
                                {{ $order->delivery_method === 'ambil' ? 'Ambil di Workshop FlexiTag Makassar' : 'Kirim via Ekspedisi Kurir' }}
                            </td>
                        </tr>
                        @if ($order->delivery_method === 'kirim' && $order->address)
                            <tr>
                                <td style="color: #78716C;">Alamat Tujuan:</td>
                                <td style="color: #1F1915;">{{ $order->address }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td style="color: #78716C;">Metode Pembayaran:</td>
                            <td style="color: #1F1915; font-weight: 600;">
                                {{ $order->payment_method === 'tunai_offline' ? 'Tunai di Kasir Workshop' : 'Pembayaran Digital (QRIS / Midtrans)' }}
                            </td>
                        </tr>
                        <tr style="border-top: 1px dashed #E5DFD5;">
                            <td style="color: #4A0A0E; font-weight: 700; font-size: 14px; padding-top: 10px;">Total Pembayaran:</td>
                            <td style="color: #166534; font-weight: 800; font-size: 16px; padding-top: 10px;">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Informasi Alur Kode Aktivasi -->
                <div style="background-color: #FCF5E3; border: 1.5px dashed #D4AF37; border-radius: 14px; padding: 18px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <span style="font-size: 16px;">🔑</span>
                        <strong style="color: #8B0000; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">Kapan Saya Menerima Kode Aktivasi?</strong>
                    </div>
                    <p style="font-size: 13px; line-height: 1.6; color: #57534E; margin: 0;">
                        Kode aktivasi chip NFC dan panduan setup profil Anda akan <strong>otomatis dikirimkan ke email ini</strong> segera setelah pembayaran pesanan Anda berhasil dikonfirmasi oleh sistem atau kasir workshop kami.
                    </p>
                </div>

                <!-- Tombol Lacak Pesanan -->
                <div style="text-align: center; margin-bottom: 24px;">
                    <a href="{{ route('order.track', ['order_no' => $order->order_no]) }}" style="display: inline-block; background-color: #8B0000; color: #FFFFFF; font-size: 14px; font-weight: 700; text-decoration: none; padding: 13px 32px; border-radius: 30px; border: 1px solid #D4AF37; box-shadow: 0 4px 14px rgba(139, 0, 0, 0.25);">
                        🔍 Lacak Status Pesanan Anda Sekarang →
                    </a>
                </div>

                <p style="font-size: 12px; color: #78716C; line-height: 1.6; text-align: center; margin: 0;">
                    Ada pertanyaan seputar pesanan Anda? Hubungi kami via Instagram 
                    <a href="https://instagram.com/{{ ltrim(config('flexitag.contact.instagram', 'maccatech.id'), '@') }}" target="_blank" rel="noopener" style="color: #C13584; font-weight: 600; text-decoration: underline;">@ {{ ltrim(config('flexitag.contact.instagram', 'maccatech.id'), '@') }}</a> 
                    atau WhatsApp CS di 
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', config('flexitag.contact.whatsapp', '081234567890')) }}" style="color: #166534; font-weight: 600; text-decoration: underline;">{{ config('flexitag.contact.whatsapp', '+62 812-3456-7890') }}</a>.
                </p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #FAF8F5; padding: 18px 24px; text-align: center; border-top: 1px solid #ECE7DF; font-size: 11px; color: #A8A29E;">
                FlexiTag Indonesia • Inovasi Gantungan Kunci NFC Budaya Sulawesi<br>
                Jl. Perintis Kemerdekaan KM 10, Tamalanrea, Kota Makassar, Sulawesi Selatan.
            </td>
        </tr>

    </table>
</body>
</html>

