<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pesanan Custom Baru Diterima</title>
</head>
<body style="font-family: 'Poppins', -apple-system, BlinkMacSystemFont, Arial, sans-serif; background-color: #FAF9F6; margin: 0; padding: 24px; color: #1F1915;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 580px; margin: 0 auto; background-color: #FFFFFF; border-radius: 18px; border: 1px solid #ECE7DF; overflow: hidden; box-shadow: 0 4px 18px rgba(74, 10, 14, 0.06);">
        <!-- Header -->
        <tr>
            <td style="background: linear-gradient(135deg, #4A0A0E 0%, #2B0407 100%); padding: 24px; text-align: center; border-bottom: 3px solid #D4AF37;">
                <div style="color: #D4AF37; font-size: 11px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 4px;">NOTIFIKASI OPERASIONAL ADMIN</div>
                <h1 style="color: #FFFFFF; font-size: 20px; font-weight: 800; margin: 0;">Pesanan Custom Baru Diterima!</h1>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 28px 24px;">
                <p style="font-size: 14px; line-height: 1.6; margin-top: 0; margin-bottom: 16px;">
                    Halo Tim Pengelola FlexiTag,
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #57534E; margin-bottom: 20px;">
                    Terdapat pesanan kustom baru yang baru saja masuk melalui formulir pemesanan publik:
                </p>

                <!-- Box Detail Pesanan -->
                <div style="background-color: #FAF8F5; border: 1px solid #EAE4D9; border-radius: 12px; padding: 18px; margin-bottom: 22px;">
                    <table width="100%" cellspacing="0" cellpadding="4" style="font-size: 13px;">
                        <tr>
                            <td style="color: #78716C; width: 140px;">No. Pesanan:</td>
                            <td><strong style="color: #4A0A0E;">#{{ $order->order_no }}</strong></td>
                        </tr>
                        <tr>
                            <td style="color: #78716C;">Nama Pemesan:</td>
                            <td><strong>{{ $order->customer_name }}</strong></td>
                        </tr>
                        <tr>
                            <td style="color: #78716C;">WhatsApp:</td>
                            <td><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->whatsapp) }}" style="color: #166534; font-weight: 600; text-decoration: none;">{{ $order->whatsapp }}</a></td>
                        </tr>
                        <tr>
                            <td style="color: #78716C;">Email:</td>
                            <td>{{ $order->email }}</td>
                        </tr>
                        <tr>
                            <td style="color: #78716C;">Spesifikasi:</td>
                            <td>Bentuk {{ ucfirst($order->shape) }} • Warna {{ $order->color }}</td>
                        </tr>
                        <tr>
                            <td style="color: #78716C;">Jumlah Pesanan:</td>
                            <td><strong>{{ $order->quantity }} pcs</strong></td>
                        </tr>
                        <tr>
                            <td style="color: #78716C;">Metode Pengiriman:</td>
                            <td>{{ $order->delivery_method === 'kirim' ? 'Pengiriman Kurir' : 'Ambil di Studio MaccaTech' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #78716C;">Total Pembayaran:</td>
                            <td><strong style="font-size: 15px; color: #166534;">Rp {{ number_format($order->total, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>
                </div>

                <!-- Call to action button -->
                <div style="text-align: center; margin-bottom: 24px;">
                    <a href="{{ route('admin.orders') }}" style="display: inline-block; background: #4A0A0E; color: #FFFFFF; text-decoration: none; font-size: 14px; font-weight: 700; padding: 12px 28px; border-radius: 10px; border: 1px solid #D4AF37;">
                        Kelola Pesanan di Admin Console →
                    </a>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #FAF8F5; padding: 16px 24px; text-align: center; border-top: 1px solid #ECE7DF; font-size: 11px; color: #A8A29E;">
                FlexiTag Automated Dispatcher • MaccaTech Studio Makassar
            </td>
        </tr>
    </table>
</body>
</html>
