@extends('layouts.public')

@section('title', 'Lacak Status Pesanan')

@section('extra_css')
<style>
    /* Section & Container Responsif */
    .track-section {
        padding: 3rem 0 5rem;
    }
    .track-container {
        max-width: 860px;
        margin: 0 auto;
        padding-inline: 1rem;
    }

    /* Form Pencarian Responsif */
    .track-card {
        background: #ffffff;
        border: 1px solid var(--line);
        border-radius: 16px;
        padding: 1.8rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
    }
    .track-form-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr auto;
        gap: 1rem;
        align-items: end;
    }
    .track-input-group label {
        display: block;
        font-size: .82rem;
        font-weight: 600;
        margin-bottom: .4rem;
        color: var(--ink);
    }
    .track-input {
        width: 100%;
        padding: .7rem .9rem;
        border: 1.5px solid var(--line);
        border-radius: 10px;
        font-size: .92rem;
        font-family: inherit;
        background: #FAF8F5;
        color: var(--ink);
        transition: all 0.2s ease;
        box-sizing: border-box;
    }
    .track-input:focus {
        background: #ffffff;
        border-color: var(--maroon);
        outline: none;
        box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.1);
    }
    .track-btn-submit {
        height: 44px;
        font-weight: 700;
        white-space: nowrap;
        padding: 0 1.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        box-sizing: border-box;
    }

    /* Grid Spesifikasi Pesanan */
    .order-specs-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-top: 1.2rem;
    }
    .spec-item {
        background: #FAF8F5;
        padding: .85rem 1rem;
        border-radius: 10px;
        border: 1px solid var(--line);
        font-size: .85rem;
    }
    .spec-item .spec-label {
        color: var(--muted);
        font-size: .72rem;
        display: block;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: .2rem;
    }
    .spec-item b {
        color: var(--ink);
        line-height: 1.35;
        display: block;
    }

    /* Kotak Kode Aktivasi */
    .activation-card {
        background: linear-gradient(135deg, #FFFDF8 0%, #FCF5E3 100%);
        border: 2px dashed #D4AF37;
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.15);
    }
    .activation-badge {
        display: inline-block;
        background-color: #4A0A0E;
        color: #D4AF37;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 4px 14px;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(74, 10, 14, 0.2);
    }
    .activation-subtitle {
        display: block;
        font-size: .85rem;
        color: #57534E;
        line-height: 1.5;
        margin-top: .4rem;
    }
    .activation-grid {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin: 1.2rem 0;
    }
    .activation-box-item {
        background: #FFFFFF;
        border: 1px solid #EAE4D9;
        border-radius: 12px;
        padding: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }
    .activation-info {
        flex: 1;
        min-width: 240px;
    }
    .activation-unit-label {
        font-size: .8rem;
        color: #78716C;
        display: inline-block;
    }
    .activation-code-display {
        margin-top: .4rem;
    }
    .code-text {
        font-family: monospace, 'Courier New', Courier;
        font-size: 1.6rem;
        font-weight: 800;
        color: #8B0000;
        letter-spacing: 2px;
        background: #FFFDF8;
        border: 1.5px solid #F0C75E;
        border-radius: 8px;
        padding: .4rem 1rem;
        display: inline-block;
        user-select: all;
    }
    .activation-btn-row {
        display: flex;
        align-items: center;
        gap: .6rem;
        flex-wrap: wrap;
    }
    .btn-copy-code {
        background: #8B0000;
        color: #FFFFFF;
        font-size: .86rem;
        font-weight: 700;
        padding: .65rem 1.15rem;
        border-radius: 10px;
        border: 1px solid #6B0000;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        transition: all .2s ease;
        box-shadow: 0 3px 10px rgba(139, 0, 0, 0.2);
    }
    .btn-copy-code:hover {
        background: #6B0000;
        transform: translateY(-1px);
    }
    .btn-copy-code.copied {
        background: #166534 !important;
        border-color: #14532D !important;
        box-shadow: 0 3px 10px rgba(22, 101, 52, 0.25) !important;
    }
    .btn-activate-link {
        background: #FFFFFF;
        color: #4A0A0E;
        font-size: .86rem;
        font-weight: 700;
        padding: .65rem 1.15rem;
        border-radius: 10px;
        border: 1.5px solid #D4AF37;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        transition: all .2s ease;
    }
    .btn-activate-link:hover {
        background: #FCF5E3;
        transform: translateY(-1px);
    }
    .activation-hint-footer {
        background: rgba(255, 255, 255, 0.65);
        border: 1px solid #ECE7DF;
        border-radius: 10px;
        padding: .75rem 1rem;
        font-size: .8rem;
        color: #6B5B45;
        line-height: 1.45;
    }

    /* Banner Edukasi Menunggu Pembayaran */
    .pending-pay-banner {
        background: #FFFBF0;
        border: 1.5px solid #F5D77F;
        padding: 1.2rem 1.4rem;
    }

    /* Linimasa Progres 7 Tahapan */
    .timeline {
        position: relative;
        padding: 1.5rem 0 1rem 1.8rem;
    }
    .timeline::before {
        content: "";
        position: absolute;
        top: 2rem;
        bottom: 2rem;
        left: 20px;
        width: 3px;
        background: var(--line);
    }
    .tl-step {
        position: relative;
        margin-bottom: 1.8rem;
        display: flex;
        gap: 1.25rem;
        align-items: flex-start;
    }
    .tl-step:last-child {
        margin-bottom: 0;
    }
    .tl-dot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .95rem;
        z-index: 2;
        flex-shrink: 0;
        box-sizing: border-box;
    }
    .tl-dot.done {
        background: #EAF5EC;
        color: #2E7D32;
        border: 2px solid #2E7D32;
    }
    .tl-dot.current {
        background: var(--maroon);
        color: var(--gold);
        border: 2px solid var(--gold);
        box-shadow: 0 0 0 4px rgba(212, 175, 55, .25);
    }
    .tl-dot.pending {
        background: #FAF8F5;
        color: var(--muted);
        border: 2px solid var(--line);
    }
    .tl-content {
        flex: 1;
        min-width: 0;
        background: #ffffff;
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }
    .tl-content.current {
        border-color: var(--maroon);
        box-shadow: 0 4px 14px rgba(139, 0, 0, .08);
    }
    .tl-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: .4rem;
        margin-bottom: .3rem;
    }
    .tl-title {
        font-size: .95rem;
        font-weight: 700;
        margin: 0;
        color: var(--ink);
    }
    .tl-content.current .tl-title {
        color: var(--maroon);
    }
    .tl-time {
        font-size: .75rem;
        color: var(--muted);
        white-space: nowrap;
    }
    .tl-desc {
        font-size: .84rem;
        color: var(--ink);
        line-height: 1.5;
        word-break: break-word;
    }

    /* Bantuan Customer Service Box */
    .help-cs-box {
        margin-top: 2rem;
        background: #FAF8F5;
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 1.2rem 1.4rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .btn-action-instagram {
        background: #ffffff;
        color: #C13584 !important;
        border: 1.5px solid #E1306C;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 0.75rem 1.2rem;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-action-instagram:hover {
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        color: #ffffff !important;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(225, 48, 108, 0.25);
    }

    /* ======================================================== */
    /* BREAKPOINTS: TABLET (< 768px)                            */
    /* ======================================================== */
    @media (max-width: 768px) {
        .track-section {
            padding: 2rem 0 4rem;
        }
        .track-form-grid {
            grid-template-columns: 1fr;
            gap: .85rem;
        }
        .track-btn-submit {
            width: 100%;
            height: 46px;
        }
        .order-specs-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: .75rem;
        }
        .track-card {
            padding: 1.4rem 1.1rem;
            border-radius: 14px;
            margin-bottom: 1.4rem;
        }
    }

    /* ======================================================== */
    /* BREAKPOINTS: MOBILE PHONES (< 520px)                     */
    /* ======================================================== */
    @media (max-width: 520px) {
        .track-section {
            padding: 1.25rem 0 3rem;
        }
        .track-container {
            padding-inline: .65rem;
        }
        .track-card {
            padding: 1.1rem .85rem;
            border-radius: 12px;
        }
        .track-input {
            font-size: 16px;
            padding: .65rem .8rem;
        }
        .order-title-block {
            width: 100%;
        }
        .order-status-block {
            width: 100%;
            text-align: left !important;
            margin-top: .6rem;
            padding-top: .6rem;
            border-top: 1px dashed var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .order-status-block span.badge {
            font-size: .78rem !important;
            padding: .35rem .75rem !important;
        }
        .order-specs-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .55rem;
            margin-top: .9rem;
        }
        .spec-item {
            padding: .65rem .75rem;
        }
        .spec-item .spec-label {
            font-size: .68rem;
        }
        .spec-item b {
            font-size: .8rem;
        }

        /* Activation Box Mobile */
        .activation-box-item {
            flex-direction: column;
            align-items: stretch;
            padding: 1rem .85rem;
        }
        .code-text {
            font-size: 1.35rem;
            display: block;
            text-align: center;
            padding: .5rem .6rem;
        }
        .activation-btn-row {
            flex-direction: column;
            width: 100%;
            gap: .5rem;
        }
        .btn-copy-code,
        .btn-activate-link {
            width: 100%;
            justify-content: center;
            padding: .75rem;
            font-size: .85rem;
        }

        /* Timeline Compact Mobile */
        .timeline {
            padding: 1rem 0 .5rem 0;
        }
        .timeline::before {
            left: 17px;
            width: 2px;
            top: 1.5rem;
            bottom: 1.5rem;
        }
        .tl-step {
            gap: .75rem;
            margin-bottom: 1.2rem;
        }
        .tl-dot {
            width: 34px;
            height: 34px;
            font-size: .82rem;
        }
        .tl-content {
            padding: .8rem .85rem;
            border-radius: 10px;
        }
        .tl-header {
            flex-direction: column;
            align-items: flex-start;
            gap: .2rem;
        }
        .tl-title {
            font-size: .88rem;
        }
        .tl-time {
            font-size: .72rem;
        }
        .tl-desc {
            font-size: .8rem;
            line-height: 1.45;
            margin-top: .35rem;
        }

        /* Help Box Mobile */
        .help-cs-box {
            padding: 1rem .85rem;
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }
        .btn-action-instagram {
            width: 100%;
            justify-content: center;
            padding: .75rem;
        }
    }

    /* Layar Super Kecil (< 360px) */
    @media (max-width: 360px) {
        .order-specs-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<section class="block band track-section">
    <div class="track-container">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <div class="eyebrow center"><span>✦ Real-Time Tracking ✦</span></div>
            <h1 class="section-title" style="margin-bottom: .5rem; font-size: clamp(1.4rem, 4vw, 2.1rem);">Lacak Pesanan Gantungan Kunci NFC</h1>
            <p class="section-sub" style="margin-inline: auto; font-size: clamp(.84rem, 2.5vw, .95rem);">Pantau proses pencetakan, perakitan chip NFC, pengujian kualitas, hingga pengiriman pesanan Anda secara transparan.</p>
        </div>

        {{-- FORM PENCARIAN RESPONSIF --}}
        <div class="track-card">
            <form action="{{ route('order.track') }}" method="GET" class="track-form-grid">
                <div class="track-input-group">
                    <label for="input_order_no">Nomor Pesanan:</label>
                    <input 
                        type="text" 
                        id="input_order_no"
                        name="order_no" 
                        class="track-input" 
                        value="{{ $orderNo }}" 
                        placeholder="Contoh: ORD-2026-0001" 
                        required
                    >
                </div>

                <div class="track-input-group">
                    <label for="input_whatsapp">Nomor WhatsApp (Opsional):</label>
                    <input 
                        type="text" 
                        id="input_whatsapp"
                        name="whatsapp" 
                        class="track-input" 
                        value="{{ $whatsapp }}" 
                        placeholder="Nomor WA saat memesan"
                    >
                </div>

                <div>
                    <button type="submit" class="btn btn-maroon track-btn-submit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <span>Lacak Pesanan</span>
                    </button>
                </div>
            </form>
        </div>

        @if ($error)
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:1.2rem;border-radius:12px;margin-bottom:2rem;text-align:center;">
                <b style="font-size:1rem;display:block;margin-bottom:.3rem;">Pesanan Tidak Ditemukan</b>
                <p style="margin:0;font-size:.88rem;">{{ $error }}</p>
                <div style="margin-top:.8rem;">
                    <a href="{{ route('order.create') }}" class="btn btn-gold" style="font-size:.85rem;padding:.5rem 1.2rem;border-radius:8px;">Buat Pesanan Baru</a>
                </div>
            </div>
        @endif

        @if ($order)
            {{-- DETAIL PESANAN YANG DITEMUKAN --}}
            <div class="track-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.8rem;border-bottom:1px solid var(--line);padding-bottom:1rem;">
                    <div class="order-title-block">
                        <span style="font-size:.72rem;font-weight:700;color:var(--muted);letter-spacing:.06em;text-transform:uppercase;">PESANAN TERDAFTAR:</span>
                        <h2 style="font-size:clamp(1.35rem, 3.5vw, 1.7rem);margin:.2rem 0;color:var(--maroon);font-weight:800;">{{ $order->order_no }}</h2>
                        <span style="color:var(--ink);font-size:.84rem;display:block;line-height:1.4;">
                            Pemesan: <strong>{{ $order->customer_name }}</strong> • {{ $order->created_at->translatedFormat('d F Y, H:i') }} WITA
                        </span>
                    </div>

                    <div class="order-status-block" style="text-align:right;">
                        <span style="font-size:.72rem;color:var(--muted);display:block;margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.04em;">STATUS WORKFLOW</span>
                        <span class="badge {{ $order->status_badge['bg_class'] }}" style="font-size:.82rem;padding:.4rem .85rem;border-radius:999px;font-weight:700;">
                            {{ $order->detail_status_label }}
                        </span>
                    </div>
                </div>

                {{-- Spesifikasi Ringkas Responsif --}}
                <div class="order-specs-grid">
                    <div class="spec-item">
                        <span class="spec-label">BENTUK & UKURAN</span>
                        <b>{{ ucfirst($order->shape) }} — {{ $order->size }}</b>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">MOTIF / WARNA</span>
                        <b>{{ $order->color }}</b>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">KUANTITAS</span>
                        <b>{{ $order->quantity }} Unit</b>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">PENGIRIMAN</span>
                        <b>{{ $order->delivery_method === 'ambil' ? 'Ambil di Workshop' : 'Kirim via Kurir' }}</b>
                    </div>
                </div>
            </div>

            {{-- KOTAK KODE AKTIVASI & SALIN LANGSUNG (Tampil jika sudah di tahap Pembayaran Dikonfirmasi / Tag teralokasi) --}}
            @if ($order->tags->isNotEmpty())
                <div class="track-card activation-card">
                    <div class="activation-header">
                        <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;">
                            <span class="activation-badge">🔑 KODE AKTIVASI ANDA</span>
                        </div>
                        <span class="activation-subtitle">
                            Pembayaran pesanan Anda telah diverifikasi! Salin kode aktivasi berikut untuk langsung mengaktifkan akun dan mengonfigurasi profil gantungan kunci NFC Anda:
                        </span>
                    </div>

                    <div class="activation-grid">
                        @foreach ($order->tags as $index => $tag)
                            <div class="activation-box-item">
                                <div class="activation-info">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.35rem;flex-wrap:wrap;gap:.4rem;">
                                        <span class="activation-unit-label">
                                            Ganci #{{ $index + 1 }} • ID Fisik: <strong>{{ $tag->tag_code }}</strong>
                                        </span>
                                        @if ($tag->status === 'aktif')
                                            <span style="background:#EAF5EC;color:#2E7D32;border:1px solid #C8E6C9;font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;">
                                                ✓ Sudah Diaktivasi
                                            </span>
                                        @else
                                            <span style="background:#FFF8E7;color:#B8860B;border:1px solid #FAD7A0;font-size:11px;font-weight:700;padding:2px 8px;border-radius:12px;">
                                                ⚡ Siap Diaktivasi
                                            </span>
                                        @endif
                                    </div>

                                    <div class="activation-code-display">
                                        <span class="code-text" id="tag-code-{{ $tag->id }}">{{ $tag->activation_code }}</span>
                                    </div>
                                </div>

                                <div class="activation-btn-row">
                                    <button 
                                        type="button" 
                                        class="btn-copy-code" 
                                        onclick="copyActivationCode('{{ $tag->activation_code }}', this)"
                                        title="Salin Kode Aktivasi ke Clipboard"
                                    >
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                        </svg>
                                        <span>Salin Kode</span>
                                    </button>

                                    <a href="{{ route('user.activation.show') }}" class="btn-activate-link">
                                        <span>Aktifkan Sekarang →</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="activation-hint-footer">
                        💡 <strong>Petunjuk:</strong> Klik tombol <strong>Salin Kode</strong>, lalu buka halaman aktivasi dan masukkan kode tersebut untuk menghubungkan gantungan kunci ke profil digital Anda.
                    </div>
                </div>
            @elseif ($order->status === \App\Models\Order::STATUS_MENUNGGU_PEMBAYARAN)
                <div class="track-card pending-pay-banner">
                    <div style="display:flex;align-items:flex-start;gap:.8rem;">
                        <div style="font-size:1.6rem;line-height:1;">⏳</div>
                        <div>
                            <b style="color:var(--maroon);font-size:.92rem;display:block;margin-bottom:.2rem;">Kode Aktivasi Sedang Disiapkan</b>
                            <p style="margin:0;font-size:.82rem;color:var(--ink);line-height:1.5;">
                                Kode aktivasi unik untuk gantungan kunci Anda akan otomatis tampil di sini dan dikirimkan ke email <strong>{{ $order->email }}</strong> segera setelah pembayaran pesanan dikonfirmasi oleh sistem atau kasir workshop kami.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- LINIMASA PROGRES 7 TAHAPAN STATUS SRS --}}
            <div class="track-card">
                <h3 style="margin-top:0;font-size:1.15rem;color:var(--maroon);border-bottom:1px solid var(--line);padding-bottom:.8rem;font-weight:800;">
                    Linimasa Produksi & Pemenuhan Pesanan
                </h3>

                @php
                    $stages = [
                        \App\Models\Order::STATUS_MENUNGGU_PEMBAYARAN => ['1', 'Menunggu Pembayaran', 'Pesanan berhasil dibuat, menunggu konfirmasi pembayaran atau struk transfer.'],
                        \App\Models\Order::STATUS_PEMBAYARAN_DIKONFIRMASI => ['2', 'Pembayaran Dikonfirmasi', 'Pembayaran telah divalidasi oleh kasir/admin. Pesanan masuk antrean verifikasi desain.'],
                        \App\Models\Order::STATUS_DESAIN_DISETUJUI => ['3', 'Desain Disetujui', 'File grafis dan tata letak cetak akrilik telah disetujui untuk produksi.'],
                        \App\Models\Order::STATUS_DICETAK => ['4', 'Dicetak', 'Akrilik dan motif sedang dalam proses cetak UV kualitas tinggi.'],
                        \App\Models\Order::STATUS_DIRAKIT_DAN_DIUJI => ['5', 'Dirakit & Diuji (QC)', 'Pemasangan chip NFC NTAG213, pengujian tap respon, dan penulisan link profil.'],
                        \App\Models\Order::STATUS_SIAP_DIAMBIL_DIKIRIM => ['6', 'Siap Diambil / Dikirim', 'Produk selesai dikemas rapi beserta panduan aktivasi, siap diserahkan.'],
                        \App\Models\Order::STATUS_SELESAI => ['7', 'Selesai (Diterima)', 'Gantungan kunci telah diterima pelanggan dan siap digunakan.'],
                    ];

                    // Temukan index tahapan status saat ini
                    $stageKeys = array_keys($stages);
                    $currentIndex = array_search($order->status, $stageKeys);
                    if ($currentIndex === false) {
                        $currentIndex = 0; // fallback jika dibatalkan
                    }

                    // Petakan log tanggal jika ada
                    $logsByStatus = $order->statusLogs->keyBy('status');
                @endphp

                <div class="timeline">
                    @foreach ($stages as $key => [$num, $title, $defaultDesc])
                        @php
                            $isDone = ($currentIndex > array_search($key, $stageKeys));
                            $isCurrent = ($order->status === $key);
                            $isPending = ($currentIndex < array_search($key, $stageKeys));

                            $log = $logsByStatus->get($key);
                            $logTime = $log ? $log->changed_at->translatedFormat('d M Y, H:i') . ' WITA' : null;
                            $logNote = $log?->note ?? $defaultDesc;
                        @endphp

                        <div class="tl-step">
                            <div class="tl-dot {{ $isDone ? 'done' : ($isCurrent ? 'current' : 'pending') }}">
                                @if ($isDone)
                                    ✓
                                @else
                                    {{ $num }}
                                @endif
                            </div>

                            <div class="tl-content {{ $isCurrent ? 'current' : '' }}">
                                <div class="tl-header">
                                    <h4 class="tl-title">{{ $num }}. {{ $title }}</h4>
                                    @if ($logTime)
                                        <span class="tl-time">🕒 {{ $logTime }}</span>
                                    @elseif ($isCurrent)
                                        <span class="tl-time" style="color:var(--gold-deep);font-weight:700;">⚡ Sedang Berlangsung</span>
                                    @endif
                                </div>
                                <div class="tl-desc">{{ $logNote }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Bantuan Customer Service: Hubungi via Instagram --}}
                <div class="help-cs-box">
                    <div>
                        <b style="color:var(--maroon);font-size:.9rem;display:block;">Ada pertanyaan tentang progres pesanan ini?</b>
                        <p style="margin:.25rem 0 0;font-size:.82rem;color:var(--muted);">Tim produksi dan layanan pelanggan MaccaTech siap membantu Anda.</p>
                    </div>

                    @php
                        $igHandle = ltrim(config('flexitag.contact.instagram') ?: 'maccatech.id', '@');
                        $igLink = 'https://instagram.com/' . $igHandle;
                    @endphp
                    <a href="{{ $igLink }}" target="_blank" rel="noopener" class="btn-action-instagram" title="Kunjungi Instagram Resmi MaccaTech">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                        <span>Hubungi via Instagram</span>
                    </a>
                </div>
            </div>
        @endif

    </div>
</section>

{{-- Script Salin Kode Aktivasi dengan Fallback Kompatibel Mobile/Desktop/HTTP --}}
<script>
    function copyActivationCode(code, btn) {
        if (!code) return;

        function showSuccessFeedback() {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = `
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>✓ Tersalin!</span>
            `;
            btn.classList.add('copied');
            setTimeout(function() {
                btn.innerHTML = originalHtml;
                btn.classList.remove('copied');
            }, 2200);
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(code).then(showSuccessFeedback).catch(function() {
                fallbackCopy(code, showSuccessFeedback);
            });
        } else {
            fallbackCopy(code, showSuccessFeedback);
        }
    }

    function fallbackCopy(text, callback) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.top = "0";
        textArea.style.left = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            const successful = document.execCommand('copy');
            if (successful && typeof callback === 'function') {
                callback();
            }
        } catch (err) {
            console.error('Fallback copy failed', err);
            prompt('Salin kode aktivasi secara manual:', text);
        }
        document.body.removeChild(textArea);
    }
</script>
@endsection
