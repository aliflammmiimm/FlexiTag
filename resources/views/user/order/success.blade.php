@extends('layouts.public')

@section('title', 'Pesanan Berhasil Dikirim — ' . $order->order_no)

@section('extra_css')
<style>
    .success-block {
        padding: 3rem 0 5rem;
    }
    .success-container {
        max-width: 820px;
    }
    .success-card {
        text-align: center;
        padding: 2.5rem 2rem;
        border-radius: 20px;
        box-shadow: 0 14px 34px rgba(0,0,0,0.06);
        background: #fff;
    }
    .order-number-box {
        background: #FAF8F5;
        border: 2px dashed var(--gold);
        border-radius: 14px;
        padding: 1.2rem;
        margin-bottom: 2rem;
    }
    .order-number-val {
        font-size: 2.1rem;
        font-weight: 800;
        color: var(--maroon);
        letter-spacing: .05em;
        margin: .3rem 0;
    }
    .payment-embedded-card {
        background: #FAF8F5;
        border: 2px solid var(--gold);
        border-radius: 20px;
        padding: 2rem 1.5rem;
        margin-bottom: 2.2rem;
        box-shadow: 0 12px 36px rgba(74, 10, 14, 0.05);
        text-align: center;
    }
    .snap-frame-wrapper {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--line);
        overflow: hidden;
        max-width: 540px;
        margin: 0 auto;
        box-shadow: 0 8px 24px rgba(0,0,0,0.04);
        min-height: 540px;
        position: relative;
    }
    .order-detail-card {
        text-align: left;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .payment-tab-switcher {
        display: inline-flex;
        background: #F0EAE1;
        border-radius: 999px;
        padding: 4px;
        gap: 4px;
        margin: 0 auto 1.5rem;
        max-width: 100%;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.04);
    }
    .payment-tab-btn {
        background: transparent;
        border: none;
        color: var(--ink);
        padding: .6rem 1.25rem;
        border-radius: 999px;
        font-size: .88rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        transition: all .2s ease;
        font-family: inherit;
        white-space: nowrap;
    }
    .payment-tab-btn:hover {
        color: var(--maroon);
    }
    .payment-tab-btn.active {
        background: var(--maroon);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(74, 10, 14, 0.25);
    }
    .cash-payment-box {
        background: #ffffff;
        border: 1.5px solid var(--gold);
        border-radius: 16px;
        padding: 1.8rem;
        max-width: 540px;
        margin: 0 auto;
        text-align: left;
        box-shadow: 0 8px 24px rgba(0,0,0,0.04);
    }

    /* Responsif Tablet dan Smartphone */
    @media (max-width: 680px) {
        .success-block {
            padding: 1.5rem 0 3.5rem !important;
        }
        .success-container {
            width: calc(100% - 1rem) !important;
            padding: 0 !important;
        }
        .success-card {
            padding: 1.4rem 0.85rem !important;
            border-radius: 14px !important;
        }
        .order-number-box {
            padding: 1rem 0.65rem !important;
            margin-bottom: 1.5rem !important;
        }
        .order-number-val {
            font-size: 1.6rem !important;
        }
        .payment-embedded-card {
            padding: 1.25rem 0.35rem !important;
            border-radius: 14px !important;
            margin-bottom: 1.5rem !important;
            border-width: 1.5px !important;
        }
        .payment-embedded-card h2 {
            font-size: 1.25rem !important;
        }
        .snap-frame-wrapper {
            border-radius: 10px !important;
            box-shadow: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        .order-detail-card {
            padding: 1.2rem 0.85rem !important;
        }
        .payment-tab-switcher {
            width: 100% !important;
            flex-direction: column !important;
            border-radius: 14px !important;
            padding: 5px !important;
        }
        .payment-tab-btn {
            width: 100% !important;
            justify-content: center !important;
            padding: .65rem .8rem !important;
            font-size: .82rem !important;
            border-radius: 10px !important;
            white-space: normal !important;
            text-align: center !important;
        }
        .cash-payment-box {
            padding: 1.2rem .85rem !important;
            border-radius: 12px !important;
        }
    }

    @media (max-width: 420px) {
        .success-container {
            width: 100% !important;
            padding: 0 0.35rem !important;
        }
        .success-card {
            padding: 1.2rem 0.45rem !important;
        }
        .payment-embedded-card {
            padding: 1rem 0.2rem !important;
        }
        .cash-payment-box {
            padding: 1rem .65rem !important;
        }
    }

    /* Styling Tombol Aksi */
    .order-action-group {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        max-width: 540px;
        margin: 1.5rem auto 0;
        width: 100%;
    }
    .btn-action-primary {
        background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%);
        color: #1F1915 !important;
        font-weight: 700;
        font-size: 1rem;
        padding: 0.9rem 1.5rem;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(212, 175, 55, 0.35);
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    .btn-action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(212, 175, 55, 0.45);
        color: #000 !important;
    }
    .order-action-secondary {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        width: 100%;
    }
    .btn-action-instagram {
        background: #ffffff;
        color: #C13584 !important;
        border: 1.5px solid #E1306C;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 0.75rem 1rem;
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
    .btn-action-outline {
        background: #ffffff;
        color: var(--maroon) !important;
        border: 1.5px solid var(--maroon);
        font-weight: 600;
        font-size: 0.88rem;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-action-outline:hover {
        background: var(--maroon);
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(74, 10, 14, 0.2);
    }
    @media (max-width: 520px) {
        .order-action-secondary {
            grid-template-columns: 1fr;
            gap: 0.65rem;
        }
        .btn-action-primary {
            font-size: 0.92rem;
            padding: 0.85rem 1.2rem;
        }
        .btn-action-instagram,
        .btn-action-outline {
            font-size: 0.85rem;
            padding: 0.75rem 0.9rem;
        }
    }
</style>
@endsection

@section('content')
<section class="block band success-block">
    <div class="container success-container">
        
        <div class="card success-card">
            
            <div id="status_icon_box" style="width: 72px; height: 72px; border-radius: 50%; background: {{ $order->status === \App\Models\Order::STATUS_PEMBAYARAN_DIKONFIRMASI ? '#EAF5EC' : '#FFF8E7' }}; color: {{ $order->status === \App\Models\Order::STATUS_PEMBAYARAN_DIKONFIRMASI ? '#2E7D32' : '#B8860B' }}; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 1.2rem;">
                {{ $order->status === \App\Models\Order::STATUS_PEMBAYARAN_DIKONFIRMASI ? '✓' : '💳' }}
            </div>

            <span class="chip" style="margin-bottom:.5rem;">✦ Informasi Transaksi FlexiTag ✦</span>
            <h1 style="font-size: 1.8rem; color: var(--maroon); margin: .4rem 0 .6rem;">Terima Kasih, {{ $order->customer_name }}!</h1>
            <p class="txt" style="max-width: 580px; margin: 0 auto 1.5rem;">
                Pesanan gantungan kunci akrilik NFC custom Anda telah kami terima dan terdaftar di dalam sistem antrean produksi FlexiTag.
            </p>

            <div class="order-number-box">
                <span style="font-size: .75rem; font-weight: 700; color: var(--muted); letter-spacing: .08em; text-transform: uppercase;">NOMOR PESANAN ANDA:</span>
                <div class="order-number-val">
                    {{ $order->order_no }}
                </div>
                <small style="color: var(--ink); font-size: .8rem;">Simpan nomor ini untuk memeriksa progres produksi dan pengiriman pada halaman Lacak Pesanan.</small>
            </div>

            {{-- BOX PEMBAYARAN MIDTRANS EMBEDDED TERTANAM & TUNAI --}}
            @if ($order->status === \App\Models\Order::STATUS_MENUNGGU_PEMBAYARAN)
                <div id="payment_embedded_card" class="payment-embedded-card">
                    
                    <div style="display: inline-flex; align-items: center; gap: .5rem; background: #FEF5E7; color: #A86414; padding: .35rem 1.1rem; border-radius: 999px; font-size: .8rem; font-weight: 700; margin-bottom: .8rem; border: 1px solid #FAD7A0;">
                        <span>⏱ Menunggu Pembayaran</span>
                    </div>

                    <h2 style="font-size: 1.45rem; color: var(--maroon); margin: 0 0 .4rem; font-weight: 800;">
                        Selesaikan Pembayaran Anda
                    </h2>
                    
                    <p style="font-size: .9rem; color: var(--muted); max-width: 520px; margin: 0 auto 1.4rem; line-height: 1.5;">
                        Total tagihan: <strong style="font-size: 1.25rem; color: var(--maroon); font-weight: 800;">Rp{{ number_format($order->total, 0, ',', '.') }}</strong><br>
                        Pilih metode penyelesaian pembayaran di bawah:
                    </p>

                    @php
                        $isCash = ($order->payment_method === 'tunai_offline');
                    @endphp

                    {{-- Switcher Tab Pilihan Pembayaran --}}
                    <div class="payment-tab-switcher">
                        <button type="button" id="tab_btn_online" class="payment-tab-btn {{ ! $isCash ? 'active' : '' }}" onclick="switchPaymentTab('online')">
                            <span>⚡</span>
                            <span>Pembayaran Digital (QRIS / VA)</span>
                        </button>
                        <button type="button" id="tab_btn_cash" class="payment-tab-btn {{ $isCash ? 'active' : '' }}" onclick="switchPaymentTab('cash')">
                            <span>💵</span>
                            <span>Bayar Tunai di Kasir Workshop</span>
                        </button>
                    </div>

                    {{-- TAB 1: FRAME TERTANAM MIDTRANS SNAP --}}
                    <div id="pane_online_payment" style="{{ ! $isCash ? '' : 'display:none;' }}">
                        <div class="snap-frame-wrapper">
                            <div id="snap-container" style="width: 100%; min-height: 540px;"></div>
                            
                            <div id="snap-loader" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #ffffff; z-index: 2; color: var(--muted); font-size: .9rem; padding: 2rem;">
                                <div style="font-size: 2.4rem; margin-bottom: .8rem;">💳</div>
                                <strong style="color: var(--maroon); font-size: 1rem; margin-bottom: .2rem;">Menyiapkan Pilihan Pembayaran...</strong>
                                <span style="font-size: .8rem;">Menghubungkan ke sistem pembayaran aman Midtrans</span>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 1.4rem; flex-wrap: wrap; font-size: .78rem; color: var(--muted);">
                            <span style="display: inline-flex; align-items: center; gap: .35rem;">
                                🔒 Transaksi Aman & Terenkripsi oleh Midtrans
                            </span>
                            <span>•</span>
                            <span>Status pesanan otomatis diperbarui begitu Anda selesai membayar</span>
                        </div>
                    </div>

                    {{-- TAB 2: INSTRUKSI BAYAR TUNAI (CASH) DI KASIR --}}
                    <div id="pane_cash_payment" style="{{ $isCash ? '' : 'display:none;' }}">
                        <div class="cash-payment-box">
                            <div style="display: flex; align-items: center; gap: .8rem; margin-bottom: 1.2rem; border-bottom: 1px dashed var(--line); padding-bottom: 1rem;">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: #FCF5E3; color: var(--gold); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; flex-shrink: 0;">
                                    💵
                                </div>
                                <div>
                                    <h3 style="margin: 0; font-size: 1.05rem; color: var(--maroon); font-weight: 700;">
                                        Instruksi Pembayaran Tunai (Cash)
                                    </h3>
                                    <small style="color: var(--muted); font-size: .78rem;">Bayar langsung di kasir workshop atau booth FlexiTag</small>
                                </div>
                            </div>

                            <ol style="margin: 0 0 1.25rem; padding-left: 1.25rem; font-size: .86rem; color: var(--ink); line-height: 1.7;">
                                <li>Datang ke <strong>Kasir Workshop FlexiTag Makassar</strong> atau Booth Pameran kami.</li>
                                <li>Tunjukkan <strong>Nomor Pesanan</strong> ini kepada kasir: <code style="background:#FAF8F5;border:1px solid var(--line);padding:.2rem .45rem;border-radius:6px;font-weight:700;color:var(--maroon);font-size:.9rem;">{{ $order->order_no }}</code></li>
                                <li>Bayarkan uang tunai sejumlah: <strong style="color:var(--maroon);font-size:1.05rem;">Rp{{ number_format($order->total, 0, ',', '.') }}</strong></li>
                                <li>Kasir akan segera memproses antrean produksi Anda dan memberikan bukti pembayaran resmi.</li>
                            </ol>

                            <div style="background: #FAF8F5; border: 1px solid var(--line); border-radius: 10px; padding: .85rem 1rem; font-size: .8rem; color: #555; margin-bottom: 1.25rem;">
                                <b style="color: var(--maroon); display: block; margin-bottom: .2rem;">📍 Lokasi Workshop FlexiTag Makassar:</b>
                                Jl. Perintis Kemerdekaan KM 10, Tamalanrea, Kota Makassar, Sulawesi Selatan.<br>
                                <span style="color: var(--muted); font-size: .75rem;">Jam Operasional: Senin – Sabtu, 09.00 – 17.00 WITA.</span>
                            </div>

                            @php
                                $waCashText = urlencode("Halo Kasir FlexiTag, saya ingin konfirmasi pembayaran tunai untuk pesanan [{$order->order_no}] sejumlah Rp" . number_format($order->total, 0, ',', '.') . " di workshop.");
                                $waCashLink = "https://wa.me/" . preg_replace('/\D+/', '', config('flexitag.contact.whatsapp', '081234567890')) . "?text=" . $waCashText;
                            @endphp
                            <a href="{{ $waCashLink }}" target="_blank" rel="noopener" class="btn btn-outline btn-block" style="border-color:#25D366;color:#128C7E;font-size:.85rem;font-weight:600;display:inline-flex;align-items:center;justify-content:center;gap:.4rem;text-align:center;">
                                <span>💬</span>
                                <span>Konfirmasi Kedatangan ke Kasir via WhatsApp</span>
                            </a>
                        </div>
                    </div>

                </div>
            @elseif ($order->status === \App\Models\Order::STATUS_PEMBAYARAN_DIKONFIRMASI)
                <div style="background: #EAF5EC; border: 1px solid #C8E6C9; border-radius: 14px; padding: 1.3rem; margin-bottom: 2rem; color: #1B5E20; display: flex; align-items: center; gap: 1rem; text-align: left;">
                    <div style="font-size: 2rem; line-height: 1;">✓</div>
                    <div>
                        <strong style="display: block; font-size: .95rem; margin-bottom: .2rem;">Pembayaran Berhasil Terverifikasi!</strong>
                        <span style="font-size: .83rem;">Terima kasih, pembayaran sebesar <strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong> telah kami terima. Tim produksi kami segera memproses pesanan Anda.</span>
                    </div>
                </div>
            @endif

            {{-- Ringkasan Pesanan --}}
            <div class="order-detail-card">
                <h3 style="margin-top: 0; font-size: 1rem; color: var(--maroon); border-bottom: 1px solid var(--line); padding-bottom: .6rem; display: flex; justify-content: space-between; align-items: center;">
                    <span>Detail Spesifikasi Pesanan</span>
                    <span id="order_status_pill" class="badge {{ $order->status_badge['bg_class'] }}" style="font-size: .78rem;">
                        {{ $order->detail_status_label }}
                    </span>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.2rem; font-size: .85rem;">
                    <div>
                        <span style="color:var(--muted);display:block;font-size:.75rem;">PRODUK / BENTUK</span>
                        <b>{{ ucfirst($order->shape) }} — {{ $order->size }}</b>
                    </div>
                    <div>
                        <span style="color:var(--muted);display:block;font-size:.75rem;">MOTIF / DESAIN</span>
                        <b>{{ $order->color }}</b>
                    </div>
                    <div>
                        <span style="color:var(--muted);display:block;font-size:.75rem;">KUANTITAS & TOTAL</span>
                        <b>{{ $order->quantity }} Unit (Rp{{ number_format($order->total, 0, ',', '.') }})</b>
                    </div>
                    <div>
                        <span style="color:var(--muted);display:block;font-size:.75rem;">METODE PENYERAHAN</span>
                        <b>{{ $order->delivery_method === 'ambil' ? 'Ambil di Workshop FlexiTag' : 'Kirim ke Alamat Kurir' }}</b>
                    </div>
                    <div>
                        <span style="color:var(--muted);display:block;font-size:.75rem;">METODE PEMBAYARAN</span>
                        <b id="summary_payment_method">{{ $order->payment_method === 'tunai_offline' ? 'Tunai di Kasir Workshop' : 'Pembayaran Online (Midtrans)' }}</b>
                    </div>
                    @if ($order->delivery_method === 'kirim' && $order->address)
                        <div style="grid-column: 1 / -1;">
                            <span style="color:var(--muted);display:block;font-size:.75rem;">ALAMAT TUJUAN PENGIRIMAN</span>
                            <span>{{ $order->address }}</span>
                        </div>
                    @endif
                    @if ($order->notes)
                        <div style="grid-column: 1 / -1;">
                            <span style="color:var(--muted);display:block;font-size:.75rem;">CATATAN PEMESAN</span>
                            <span style="font-style:italic;">"{{ $order->notes }}"</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tombol Aksi Terpadu --}}
            <div class="order-action-group">
                <a href="{{ route('order.track', ['order_no' => $order->order_no]) }}" class="btn-action-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>Lacak Status Pesanan Ini</span>
                </a>

                <div class="order-action-secondary">
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

                    <a href="{{ route('home') }}" class="btn-action-outline">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>

{{-- Midtrans Snap SDK --}}
<script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    const orderNo = "{{ $order->order_no }}";
    let snapToken = "{{ $order->snap_token }}";
    let isPendingPayment = {{ $order->status === \App\Models\Order::STATUS_MENUNGGU_PEMBAYARAN ? 'true' : 'false' }};

    function initSnapEmbed(token) {
        if (!token) {
            console.error('Snap token missing');
            return;
        }

        try {
            window.snap.embed(token, {
                embedId: 'snap-container',
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    pollStatusAndRefresh();
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    pollStatusAndRefresh();
                },
                onError: function(result) {
                    console.error('Payment error:', result);
                    alert('Terjadi kendala pada pembayaran. Silakan coba kembali.');
                },
                onClose: function() {
                    console.log('Customer closed payment embed');
                    pollStatusAndRefresh();
                }
            });

            // Sembunyikan placeholder loader setelah frame termuat
            setTimeout(function() {
                const loader = document.getElementById('snap-loader');
                if (loader) {
                    loader.style.transition = 'opacity 0.4s ease';
                    loader.style.opacity = '0';
                    setTimeout(function() {
                        loader.style.display = 'none';
                    }, 400);
                }
            }, 1000);

        } catch (e) {
            console.error('snap.embed error:', e);
        }
    }

    function fetchTokenAndEmbed() {
        fetch(`{{ url('/pesan') }}/${orderNo}/snap-token`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.snap_token) {
                snapToken = data.snap_token;
                initSnapEmbed(snapToken);
            } else {
                const loader = document.getElementById('snap-loader');
                if (loader) {
                    loader.innerHTML = '<span style="color:#C62828;">Gagal memuat pembayaran otomatis. Silakan muat ulang halaman.</span>';
                }
            }
        })
        .catch(err => {
            console.error('Fetch snap token error:', err);
            const loader = document.getElementById('snap-loader');
            if (loader) {
                loader.innerHTML = '<span style="color:#C62828;">Koneksi terganggu. Silakan muat ulang halaman.</span>';
            }
        });
    }

    function pollStatusAndRefresh() {
        fetch(`{{ url('/pesan') }}/${orderNo}/check-status`)
            .then(res => res.json())
            .then(data => {
                if (data.is_paid) {
                    window.location.reload();
                }
            })
            .catch(err => console.log('Check status error:', err));
    }

    function tryLoadSnap() {
        if (typeof window.snap !== 'undefined' && typeof window.snap.embed === 'function') {
            if (snapToken) {
                initSnapEmbed(snapToken);
            } else {
                fetchTokenAndEmbed();
            }
        } else {
            setTimeout(tryLoadSnap, 300);
        }
    }

    function switchPaymentTab(tab) {
        const btnOnline = document.getElementById('tab_btn_online');
        const btnCash = document.getElementById('tab_btn_cash');
        const paneOnline = document.getElementById('pane_online_payment');
        const paneCash = document.getElementById('pane_cash_payment');
        const summaryPayment = document.getElementById('summary_payment_method');

        if (tab === 'online') {
            if (btnOnline) btnOnline.classList.add('active');
            if (btnCash) btnCash.classList.remove('active');
            if (paneOnline) paneOnline.style.display = 'block';
            if (paneCash) paneCash.style.display = 'none';
            if (summaryPayment) summaryPayment.innerText = 'Pembayaran Online (Midtrans)';

            fetch(`{{ url('/pesan') }}/${orderNo}/metode-online`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).catch(e => console.log('Update online method:', e));

            tryLoadSnap();
        } else {
            if (btnCash) btnCash.classList.add('active');
            if (btnOnline) btnOnline.classList.remove('active');
            if (paneCash) paneCash.style.display = 'block';
            if (paneOnline) paneOnline.style.display = 'none';
            if (summaryPayment) summaryPayment.innerText = 'Tunai di Kasir Workshop';

            fetch(`{{ url('/pesan') }}/${orderNo}/metode-tunai`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).catch(e => console.log('Update cash method:', e));
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (isPendingPayment) {
            const paneOnline = document.getElementById('pane_online_payment');
            if (paneOnline && paneOnline.style.display !== 'none') {
                tryLoadSnap();
            }

            // Cek status berkala setiap 5 detik
            setInterval(function() {
                fetch(`{{ url('/pesan') }}/${orderNo}/check-status`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.is_paid) {
                            window.location.reload();
                        }
                    })
                    .catch(() => {});
            }, 5000);
        }
    });
</script>
@endsection
