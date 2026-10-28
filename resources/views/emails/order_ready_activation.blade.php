<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pembayaran Dikonfirmasi — Kode Aktivasi FlexiTag Anda</title>
</head>
<body style="font-family: 'Poppins', -apple-system, BlinkMacSystemFont, Arial, sans-serif; background-color: #FAF9F6; margin: 0; padding: 24px; color: #1F1915;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: 0 auto; background-color: #FFFFFF; border-radius: 18px; border: 1px solid #ECE7DF; overflow: hidden; box-shadow: 0 4px 20px rgba(74, 10, 14, 0.08);">
        
        <!-- Header Brand -->
        <tr>
            <td style="background: linear-gradient(135deg, #4A0A0E 0%, #2B0407 100%); padding: 28px 24px; text-align: center; border-bottom: 3px solid #D4AF37;">
                <div style="color: #D4AF37; font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; margin-bottom: 6px;">FLEXITAG SMART KEYCHAIN</div>
                <h1 style="color: #FFFFFF; font-size: 22px; font-weight: 800; margin: 0; line-height: 1.3;">
                    🎉 Pembayaran Terkonfirmasi!
                </h1>
                <p style="color: #F5EBE6; font-size: 13px; margin: 6px 0 0; opacity: 0.9;">
                    No. Pesanan: <strong style="color: #F0C75E;">#{{ $order->order_no }}</strong>
                </p>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 28px 24px;">
                <p style="font-size: 15px; line-height: 1.6; margin-top: 0; margin-bottom: 14px;">
                    Halo, <strong style="color: #4A0A0E;">{{ $order->customer_name }}</strong>! 👋
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #57534E; margin-bottom: 14px;">
                    Terima kasih! Pembayaran pesanan merchandise gantungan kunci akrilik NFC kustom Anda telah berhasil kami konfirmasi dan pesanan Anda kini telah masuk ke antrean produksi di workshop FlexiTag Makassar.
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #57534E; margin-bottom: 20px;">
                    Sambil menunggu proses cetak dan perakitan fisik selesai hingga siap {{ $order->delivery_method === 'ambil' ? 'diambil di workshop' : 'dikirimkan kurir' }}, Anda sudah bisa langsung menggunakan <strong>Kode Aktivasi</strong> di bawah ini untuk mengaktifkan akun dan mengatur profil publik Anda:
                </p>

                <!-- Box Kode Aktivasi -->
                <div style="background: linear-gradient(135deg, #FFFDF8 0%, #FCF5E3 100%); border: 2px dashed #D4AF37; border-radius: 14px; padding: 20px; margin-bottom: 24px; text-align: center;">
                    <span style="display: inline-block; background-color: #4A0A0E; color: #D4AF37; font-size: 10px; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; padding: 4px 12px; border-radius: 20px; margin-bottom: 12px;">
                        🔑 KODE AKTIVASI GANTUNGAN KUNCI ANDA
                    </span>

                    @if ($tags->count() === 1)
                        @php $firstTag = $tags->first(); @endphp
                        <div style="margin: 8px 0 12px;">
                            <div style="font-size: 12px; color: #78716C; margin-bottom: 4px;">Kode Aktivasi Khusus:</div>
                            <div style="font-family: monospace, 'Courier New', Courier; font-size: 26px; font-weight: 800; color: #8B0000; letter-spacing: 3px; background: #FFFFFF; border: 1px solid #EAE4D9; border-radius: 8px; padding: 10px 16px; display: inline-block;">
                                {{ $firstTag->activation_code }}
                            </div>
                        </div>
                        <div style="font-size: 12px; color: #78716C;">
                            Kode Fisik Tag: <strong>{{ $firstTag->tag_code }}</strong>
                        </div>
                    @else
                        <p style="font-size: 13px; color: #57534E; margin: 4px 0 14px;">
                            Anda memesan <strong>{{ $tags->count() }} unit gantungan kunci</strong>. Berikut adalah daftar kode aktivasi masing-masing unit:
                        </p>
                        <table width="100%" cellspacing="0" cellpadding="8" style="background-color: #FFFFFF; border: 1px solid #EAE4D9; border-radius: 10px; font-size: 13px; text-align: left;">
                            <tr style="background-color: #F8F6F0; color: #78716C; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                <th style="padding: 8px 12px; border-bottom: 1px solid #EAE4D9;">Unit #</th>
                                <th style="padding: 8px 12px; border-bottom: 1px solid #EAE4D9;">Kode Tag</th>
                                <th style="padding: 8px 12px; border-bottom: 1px solid #EAE4D9;">Kode Aktivasi</th>
                            </tr>
                            @foreach ($tags as $index => $tag)
                                <tr style="border-bottom: 1px solid #F2EEE9;">
                                    <td style="padding: 8px 12px; color: #78716C;">#{{ $index + 1 }}</td>
                                    <td style="padding: 8px 12px; font-weight: 600; color: #1F1915;">{{ $tag->tag_code }}</td>
                                    <td style="padding: 8px 12px;">
                                        <code style="font-family: monospace; font-size: 14px; font-weight: 800; color: #8B0000; background: #FAF5EA; padding: 2px 6px; border-radius: 4px;">{{ $tag->activation_code }}</code>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @endif

                    <div style="margin-top: 18px;">
                        <a href="{{ route('user.activation.show') }}" style="display: inline-block; background-color: #8B0000; color: #FFFFFF; font-size: 14px; font-weight: 700; text-decoration: none; padding: 12px 28px; border-radius: 30px; border: 1px solid #D4AF37; box-shadow: 0 4px 12px rgba(139, 0, 0, 0.2);">
                            Aktifkan Gantungan Kunci Sekarang →
                        </a>
                    </div>
                </div>

                <!-- 4 Langkah Cara Aktivasi -->
                <div style="background-color: #FAF8F5; border: 1px solid #ECE7DF; border-radius: 12px; padding: 18px; margin-bottom: 22px;">
                    <h3 style="color: #4A0A0E; font-size: 13px; font-weight: 700; text-transform: uppercase; margin: 0 0 12px; letter-spacing: 0.05em;">
                        📋 Panduan Mudah Aktivasi (Hanya 1 Menit):
                    </h3>
                    <ol style="margin: 0; padding-left: 20px; font-size: 13px; line-height: 1.7; color: #44403C;">
                        <li>Kunjungi halaman aktivasi FlexiTag di tautan: <a href="{{ route('user.activation.show') }}" style="color: #8B0000; font-weight: 600; text-decoration: underline;">{{ route('user.activation.show') }}</a></li>
                        <li><strong>Masuk</strong> atau <strong>Daftar akun baru</strong> jika Anda pengguna pertama.</li>
                        <li>Masukkan <strong>Kode Aktivasi</strong> yang tertera di atas pada kotak formulir.</li>
                        <li>Pilih mode awal (Profil Kampus, Profesional/Kerja, atau UMKM) dan simpan.</li>
                        <li>Selesai! Dekatkan bagian belakang smartphone Anda ke gantungan kunci untuk langsung mencoba.</li>
                    </ol>
                </div>

                <!-- Rincian Penyerahan / Pengambilan -->
                <div style="background-color: #FFFFFF; border: 1px solid #EAE4D9; border-radius: 12px; padding: 16px; margin-bottom: 22px; font-size: 13px;">
                    <div style="font-weight: 700; color: #1F1915; margin-bottom: 8px;">
                        @if ($order->delivery_method === 'ambil')
                            🏢 Lokasi &amp; Jam Pengambilan di Tempat:
                        @else
                            📦 Informasi Pengiriman Kurir:
                        @endif
                    </div>
                    @if ($order->delivery_method === 'ambil')
                        <p style="margin: 0 0 6px; color: #57534E; line-height: 1.5;">
                            <strong>Workshop FlexiTag Makassar</strong><br>
                            Jl. Perintis Kemerdekaan KM 10, Tamalanrea, Kota Makassar, Sulawesi Selatan.<br>
                            <span style="color: #78716C; font-size: 12px;">Waktu Operasional: Senin – Sabtu, pukul 09.00 – 17.00 WITA.</span>
                        </p>
                        <p style="margin: 6px 0 0; color: #8B0000; font-size: 12px;">
                            * Harap tunjukkan email ini atau sebutkan Nomor Pesanan <strong>#{{ $order->order_no }}</strong> kepada staf kasir/workshop kami.
                        </p>
                    @else
                        <p style="margin: 0 0 6px; color: #57534E; line-height: 1.5;">
                            Paket gantungan kunci Anda sedang dikemas dengan bubble wrap aman dan akan segera diserahkan ke jasa ekspedisi kurir menuju alamat tujuan:
                        </p>
                        <div style="background: #FAF9F6; padding: 8px 12px; border-radius: 6px; border: 1px solid #ECE7DF; color: #1F1915; font-size: 12px; line-height: 1.5;">
                            {{ $order->address }}
                        </div>
                    @endif
                </div>

                <!-- Detail Ringkasan Pesanan -->
                <table width="100%" cellspacing="0" cellpadding="6" style="font-size: 12px; color: #78716C; border-top: 1px dashed #EAE4D9; padding-top: 12px;">
                    <tr>
                        <td width="35%">Spesifikasi Ganci:</td>
                        <td style="color: #1F1915; font-weight: 600;">{{ $order->shape }} • {{ $order->color }} ({{ $order->size }})</td>
                    </tr>
                    <tr>
                        <td>Kuantitas:</td>
                        <td style="color: #1F1915; font-weight: 600;">{{ $order->quantity }} pcs</td>
                    </tr>
                    <tr>
                        <td>Total Biaya:</td>
                        <td style="color: #166534; font-weight: 700; font-size: 13px;">Rp {{ number_format($order->total, 0, ',', '.') }} (Lunas)</td>
                    </tr>
                </table>

                <p style="font-size: 12px; color: #78716C; margin-top: 24px; line-height: 1.5; text-align: center;">
                    Butuh bantuan atau pertanyaan seputar aktivasi? Hubungi tim support kami via WhatsApp di 
                    <a href="https://wa.me/6281234567890" style="color: #166534; font-weight: 600; text-decoration: none;">+62 812-3456-7890</a>.
                </p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #FAF8F5; padding: 18px 24px; text-align: center; border-top: 1px solid #ECE7DF; font-size: 11px; color: #A8A29E;">
                FlexiTag Indonesia • Inovasi Gantungan Kunci NFC Budaya Sulawesi<br>
                Email dikirim otomatis ke {{ $order->email }} terkait pesanan #{{ $order->order_no }}.
            </td>
        </tr>

    </table>
</body>
</html>
