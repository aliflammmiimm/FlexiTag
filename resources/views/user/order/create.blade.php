@extends('layouts.public')

@section('title', 'Kustomisasi & Pesan Gantungan Kunci NFC')

@section('extra_css')
<style>
    .order-grid { display: grid; grid-template-columns: 1.4fr 0.9fr; gap: 2rem; align-items: start; }
    .form-step { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 1.6rem; margin-bottom: 1.5rem; box-shadow: var(--shadow); }
    .form-step-title { display: flex; align-items: center; gap: .75rem; margin-bottom: 1.25rem; font-size: 1.1rem; color: var(--maroon); font-weight: 700; }
    .form-step-no { width: 30px; height: 30px; border-radius: 50%; background: var(--maroon); color: var(--gold); display: flex; align-items: center; justify-content: center; font-size: .85rem; font-weight: 700; flex-shrink: 0; }
    
    .select-card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: .8rem; }
    .select-card { border: 2px solid var(--line); border-radius: 12px; padding: .85rem; text-align: center; cursor: pointer; transition: all .2s ease; background: #fff; }
    .select-card:hover { border-color: var(--gold); transform: translateY(-2px); }
    .select-card.active { border-color: var(--maroon); background: #fdf7f7; box-shadow: 0 4px 12px rgba(139,0,0,0.08); }
    .select-card input[type="radio"] { display: none; }
    .select-card img { width: 100%; height: 85px; object-fit: cover; border-radius: 8px; margin-bottom: .5rem; }
    
    .form-group { margin-bottom: 1.1rem; }
    .form-label { display: block; font-size: .82rem; font-weight: 600; color: var(--ink); margin-bottom: .4rem; }
    .form-input, .form-select, .form-textarea { width: 100%; padding: .65rem .85rem; border: 1px solid var(--line); border-radius: 8px; font-family: inherit; font-size: .88rem; outline: none; transition: border-color .15s; background: #fff; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: var(--maroon); }
    
    .summary-card { position: sticky; top: 90px; background: #fff; border: 1px solid var(--line); border-radius: 16px; padding: 1.6rem; box-shadow: 0 10px 28px rgba(0,0,0,0.05); }
    .summary-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; padding: .65rem 0; border-bottom: 1px dashed var(--line); font-size: .88rem; }
    .summary-row:last-child { border-bottom: none; }
    .summary-row > span:first-child { flex-shrink: 0; min-width: 105px; color: var(--muted); font-weight: 500; }
    .summary-row > span:last-child, .summary-row > div:last-child { text-align: right; line-height: 1.35; word-break: normal; }
    .summary-total { font-size: 1.5rem; font-weight: 700; color: var(--maroon); }
    
    .radio-pill-group { display: flex; flex-wrap: wrap; gap: .6rem; }
    .radio-pill { cursor: pointer; padding: .55rem 1rem; border: 1px solid var(--line); border-radius: 8px; font-size: .84rem; font-weight: 500; transition: all .2s; }
    .radio-pill.active { border-color: var(--maroon); background: var(--maroon); color: #fff; }
    .radio-pill input { display: none; }

    @media (max-width: 900px) {
        .order-grid { grid-template-columns: 1fr; }
        .summary-card { position: static; }
    }

    @media (max-width: 680px) {
        .form-step { padding: 1.25rem 1rem; }
        .summary-card { padding: 1.25rem 1rem; }
        .summary-row { gap: .8rem; font-size: .85rem; }
        .select-card-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 400px) {
        .form-step { padding: 1.1rem .85rem; }
        .summary-card { padding: 1.1rem .85rem; }
        .summary-row { gap: .6rem; font-size: .82rem; }
        .summary-row > span:first-child { min-width: 90px; }
    }
</style>
@endsection

@section('content')
<section class="block band" style="padding: 2.5rem 0 4rem;">
    <div class="container">
        
        <div style="margin-bottom: 2rem;">
            <div class="eyebrow"><span>✦ Konfigurator Pemesanan ✦</span></div>
            <h1 class="section-title" style="margin-bottom: .5rem;">Kustomisasi & Pesan Gantungan Kunci NFC</h1>
            <p class="section-sub" style="margin-bottom: 0;">Pilih bentuk akrilik, motif dari katalog terlaris, atau unggah desain custom sendiri. Tersedia opsi <strong>Pemesanan Online</strong> maupun <strong>Pesan Langsung di Workshop (Offline)</strong>.</p>
        </div>

        @if ($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:1rem 1.25rem;border-radius:10px;margin-bottom:1.5rem;font-size:.88rem;">
                <b style="display:block;margin-bottom:.3rem;">Mohon periksa kembali formulir Anda:</b>
                <ul style="margin:0;padding-left:1.2rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('order.store') }}" method="POST" enctype="multipart/form-data" id="orderForm">
            @csrf

            <div class="order-grid">
                
                {{-- KOLOM KIRI: FORMULIR TAHAP PEMESANAN --}}
                <div>

                    {{-- LANGKAH 1: KANAL TRANSAKSI (ONLINE vs OFFLINE) --}}
                    <div class="form-step">
                        <div class="form-step-title">
                            <span class="form-step-no">1</span>
                            <span>Kanal Pemesanan & Transaksi</span>
                        </div>
                        <div class="select-card-grid" style="grid-template-columns: 1fr 1fr;">
                            <label class="select-card channel-opt {{ old('channel', 'online') === 'online' ? 'active' : '' }}" onclick="selectChannel('online')">
                                <input type="radio" name="channel" value="online" id="channel_online" {{ old('channel', 'online') === 'online' ? 'checked' : '' }}>
                                <div style="font-size:1.8rem;margin-bottom:.4rem;">🌐</div>
                                <b style="display:block;font-size:.9rem;color:var(--maroon);">Pesan Online</b>
                                <small style="color:var(--muted);font-size:.75rem;">Kirim ke alamat rumah / kurir ekspedisi</small>
                            </label>

                            <label class="select-card channel-opt {{ old('channel') === 'offline' ? 'active' : '' }}" onclick="selectChannel('offline')">
                                <input type="radio" name="channel" value="offline" id="channel_offline" {{ old('channel') === 'offline' ? 'checked' : '' }}>
                                <div style="font-size:1.8rem;margin-bottom:.4rem;">🏬</div>
                                <b style="display:block;font-size:.9rem;color:var(--maroon);">Pesan Langsung (Offline)</b>
                                <small style="color:var(--muted);font-size:.75rem;">Workshop Makassar / Booth Pameran</small>
                            </label>
                        </div>
                    </div>

                    {{-- LANGKAH 2: BENTUK & UKURAN AKRILIK --}}
                    <div class="form-step">
                        <div class="form-step-title">
                            <span class="form-step-no">2</span>
                            <span>Bentuk & Ukuran Akrilik</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pilih Bentuk Akrilik:</label>
                            <div class="select-card-grid">
                                @php
                                    $shapes = [
                                        ['bulat', 'Bulat (Circle)', 'Diameter 4.5 cm', 'storage/designs/ganci_bulat.png'],
                                        ['persegi', 'Persegi (Square)', 'Ukuran 5 x 3.5 cm', 'storage/designs/ganci_umkm.png'],
                                        ['kapsul', 'Kapsul / Oval', 'Ukuran 5.5 x 3 cm', 'images/keychain.jpg'],
                                        ['custom', 'Bebas / Custom', 'Sesuai Potongan Logo', 'images/keychain_square.jpg'],
                                    ];
                                    $currShape = old('shape', $selectedDesign && str_contains(strtolower($selectedDesign->name), 'persegi') ? 'persegi' : 'bulat');
                                @endphp

                                @foreach ($shapes as [$val, $title, $desc, $img])
                                    <label class="select-card shape-opt {{ $currShape === $val ? 'active' : '' }}" onclick="selectShape('{{ $val }}', '{{ $title }}')">
                                        <input type="radio" name="shape" value="{{ $val }}" {{ $currShape === $val ? 'checked' : '' }}>
                                        <img src="{{ asset($img) }}" alt="{{ $title }}" onerror="this.onerror=null;this.src='{{ asset('images/keychain.jpg') }}';">
                                        <b style="display:block;font-size:.82rem;line-height:1.2;">{{ $title }}</b>
                                        <small style="color:var(--muted);font-size:.7rem;">{{ $desc }}</small>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group" style="margin-top:1.2rem;">
                            <label class="form-label">Pilih Ukuran:</label>
                            @php
                                $sizes = ['Standar (Rekomendasi)', 'Sedang (+1 cm)', 'Besar (+2 cm)'];
                                $currSize = old('size', 'Standar (Rekomendasi)');
                            @endphp
                            <div class="radio-pill-group">
                                @foreach ($sizes as $sz)
                                    <label class="radio-pill size-opt {{ $currSize === $sz ? 'active' : '' }}" onclick="selectSize('{{ $sz }}')">
                                        <input type="radio" name="size" value="{{ $sz }}" {{ $currSize === $sz ? 'checked' : '' }}>
                                        {{ $sz }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- LANGKAH 3: MOTIF ATAU DESAIN CUSTOM --}}
                    <div class="form-step">
                        <div class="form-step-title">
                            <span class="form-step-no">3</span>
                            <span>Motif Desain & Personalisasi</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pilih Motif dari Galeri Template:</label>
                            <div class="select-card-grid">
                                @php
                                    $currColor = old('color', $selectedDesign ? $selectedDesign->name : 'Gantungan Kunci Edisi Motif Toraja Pa\'ssulan');
                                @endphp

                                @foreach ($designs as $d)
                                    <label class="select-card color-opt {{ $currColor === $d->name ? 'active' : '' }}" onclick="selectMotif('{{ addslashes($d->name) }}', '{{ $d->image_url }}')">
                                        <input type="radio" name="color" value="{{ $d->name }}" {{ $currColor === $d->name ? 'checked' : '' }}>
                                        <img src="{{ $d->image_url }}" alt="{{ $d->name }}" onerror="this.onerror=null;this.src='{{ asset('images/keychain.jpg') }}';">
                                        <b style="display:block;font-size:.78rem;line-height:1.2;">{{ $d->name }}</b>
                                        <span class="chip" style="font-size:.65rem;margin-top:.3rem;display:inline-block;">{{ $d->category_badge['label'] }}</span>
                                    </label>
                                @endforeach

                                <label class="select-card color-opt {{ $currColor === 'Desain Sendiri (Custom Upload)' ? 'active' : '' }}" onclick="selectMotif('Desain Sendiri (Custom Upload)', '{{ asset('images/keychain.jpg') }}')">
                                    <input type="radio" name="color" value="Desain Sendiri (Custom Upload)" {{ $currColor === 'Desain Sendiri (Custom Upload)' ? 'checked' : '' }}>
                                    <div style="height:85px;display:grid;place-items:center;background:#FAF8F5;border-radius:8px;font-size:2rem;margin-bottom:.5rem;">🎨</div>
                                    <b style="display:block;font-size:.78rem;line-height:1.2;">Desain Sendiri</b>
                                    <small style="color:var(--muted);font-size:.68rem;">Unggah file PNG / SVG</small>
                                </label>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top:1.2rem;">
                            <label class="form-label" for="design_file">Unggah Berkas Desain Sendiri (Opsional bila memilih motif galeri):</label>
                            <input type="file" name="design_file" id="design_file" class="form-input" accept=".png,.jpg,.jpeg,.svg,.pdf,.ai,.psd,.zip">
                            <small style="color:var(--muted);font-size:.74rem;">Format: PNG, JPG, SVG, PDF, AI. Maksimal 10 MB.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="notes">Catatan Personalisasi (Nama Grafir / Teks Khusus):</label>
                            <textarea name="notes" id="notes" rows="2" class="form-textarea" placeholder="Contoh: Cantumkan nama 'Andi Rifki' di sisi belakang dan logo Himpunan Teknik Komputer.">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    {{-- LANGKAH 4: DATA PEMESAN & PENGIRIMAN / PENYERAHAN --}}
                    <div class="form-step">
                        <div class="form-step-title">
                            <span class="form-step-no">4</span>
                            <span id="step_4_title">{{ old('channel') === 'offline' ? 'Data Pemesan & Penyerahan' : 'Data Pemesan & Pengiriman' }}</span>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                            <div class="form-group">
                                <label class="form-label" for="customer_name">Nama Lengkap Pemesan: <span style="color:red">*</span></label>
                                <input type="text" name="customer_name" id="customer_name" class="form-input" value="{{ old('customer_name', auth()->user()?->name) }}" required placeholder="Contoh: Andi Muhammad Rifki">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="whatsapp">Nomor WhatsApp Aktif: <span style="color:red">*</span></label>
                                <input type="text" name="whatsapp" id="whatsapp" class="form-input" value="{{ old('whatsapp', auth()->user()?->profile?->no_whatsapp) }}" required placeholder="Contoh: 081234567890">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">
                                Alamat Email Aktif: <span style="color:red">*</span>
                            </label>
                            
                            {{-- Arahan Pengguna Mengenai Validitas Email --}}
                            <div style="background:#FAF8F5;border:1px solid #ECE7DF;border-radius:8px;padding:.6rem .85rem;margin-bottom:.5rem;font-size:.78rem;color:#57534E;line-height:1.45;">
                                <span style="color:var(--maroon);font-weight:700;">📧 Jalur Kode Aktivasi:</span>
                                Kode aktivasi gantungan kunci NFC dan rincian pesanan akan <strong>dikirimkan langsung ke alamat email ini</strong> ketika produk selesai dirakit &amp; siap diambil/dikirim. Pastikan email Anda aktif &amp; benar.
                            </div>

                            <div style="position:relative;">
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="email" 
                                    class="form-input" 
                                    value="{{ old('email', auth()->user()?->email) }}" 
                                    required 
                                    placeholder="Contoh: nama@gmail.com" 
                                    oninput="handleEmailInput(this.value)"
                                    autocomplete="email"
                                >
                                <span id="email_spinner" style="display:none;position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:.8rem;color:var(--muted);">⏳</span>
                            </div>

                            {{-- Kotak Saran Typo Domain --}}
                            <div id="email_typo_box" style="display:none;margin-top:.4rem;background:#FEF5E7;border:1px solid #FAD7A0;border-radius:6px;padding:.45rem .75rem;font-size:.78rem;color:#A86414;align-items:center;justify-content:space-between;">
                                <span>💡 Maksud Anda: <strong id="email_typo_suggestion" style="text-decoration:underline;cursor:pointer;" onclick="applyEmailSuggestion()"></strong>?</span>
                                <button type="button" onclick="applyEmailSuggestion()" style="background:#A86414;color:#fff;border:none;border-radius:4px;padding:3px 8px;font-size:.72rem;margin-left:6px;cursor:pointer;font-weight:600;">Gunakan ini</button>
                            </div>

                            {{-- Pesan Status Validasi Real-Time --}}
                            <div id="email_status_msg" style="margin-top:.35rem;font-size:.77rem;min-height:18px;">
                                @if ($errors->has('email'))
                                    <span style="color:#dc2626;font-weight:600;">⚠️ {{ $errors->first('email') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Metode Penyerahan: Hanya tampil untuk Pesan Online --}}
                        <div class="form-group" id="delivery_method_selection_group" style="{{ old('channel', 'online') === 'offline' ? 'display:none;' : '' }}">
                            <label class="form-label">Metode Penyerahan Produk:</label>
                            <div class="radio-pill-group">
                                <label class="radio-pill delivery-opt {{ old('delivery_method', 'kirim') === 'kirim' ? 'active' : '' }}" onclick="selectDelivery('kirim')">
                                    <input type="radio" name="delivery_method" id="delivery_kirim" value="kirim" {{ old('delivery_method', 'kirim') === 'kirim' ? 'checked' : '' }}>
                                    📦 Kirim via Kurir / Ekspedisi ke Alamat
                                </label>
                                <label class="radio-pill delivery-opt {{ old('delivery_method') === 'ambil' ? 'active' : '' }}" onclick="selectDelivery('ambil')">
                                    <input type="radio" name="delivery_method" id="delivery_ambil" value="ambil" {{ old('delivery_method') === 'ambil' ? 'checked' : '' }}>
                                    🏢 Ambil Langsung di Workshop FlexiTag Makassar (Bebas Biaya)
                                </label>
                            </div>
                        </div>

                        {{-- Notifikasi Khusus Pesan Langsung (Offline) --}}
                        <div id="offline_delivery_notice" style="{{ old('channel') === 'offline' ? '' : 'display:none;' }}background:#FAF8F5;border:1.5px dashed var(--gold);padding:1.1rem 1.25rem;border-radius:12px;margin-bottom:1.2rem;">
                            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.4rem;color:var(--maroon);font-weight:700;">
                                <span style="font-size:1.3rem;">🏬</span>
                                <span style="font-size:.95rem;">Penyerahan Langsung di Workshop / Booth FlexiTag</span>
                            </div>
                            <p style="margin:0 0 .5rem;font-size:.84rem;color:var(--ink);line-height:1.5;">
                                Karena Anda memesan langsung di workshop / booth kami, gantungan kunci NFC akan diserahkan langsung kepada Anda setelah proses cetak dan konfigurasi selesai. <strong>Bebas biaya pengiriman</strong>.
                            </p>
                            <small style="color:var(--muted);font-size:.78rem;display:block;">
                                📍 Lokasi: Jl. Perintis Kemerdekaan KM 10, Tamalanrea, Makassar (Buka Senin – Sabtu, 09.00 – 17.00 WITA).
                            </small>
                        </div>

                        <div class="form-group" id="address_box" style="{{ old('channel') === 'offline' || old('delivery_method', 'kirim') === 'ambil' ? 'display:none;' : '' }}">
                            <label class="form-label" for="address">Alamat Pengiriman Lengkap: <span style="color:red">*</span></label>
                            <textarea name="address" id="address" rows="3" class="form-textarea" placeholder="Jalan, Nomor Rumah, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten, Kode Pos.">{{ old('address') }}</textarea>
                        </div>

                        <div id="workshop_info" style="{{ old('channel') !== 'offline' && old('delivery_method') === 'ambil' ? '' : 'display:none;' }}background:#FCF5E3;border:1px solid #F0C75E;padding:.9rem 1.1rem;border-radius:10px;font-size:.82rem;color:#684f18;">
                            <b>📍 Alamat Workshop FlexiTag Makassar:</b><br>
                            Jl. Perintis Kemerdekaan KM 10, Tamalanrea, Kota Makassar, Sulawesi Selatan.<br>
                            <small>Waktu operasional: Senin – Sabtu, pukul 09.00 – 17.00 WITA.</small>
                        </div>
                    </div>

                </div>

                {{-- KOLOM KANAN: RINGKASAN HARGA & SUBMIT STICKY --}}
                <div>
                    <div class="summary-card">
                        <h3 style="margin-top:0;font-size:1.15rem;color:var(--maroon);border-bottom:1px solid var(--line);padding-bottom:.8rem;">
                            Ringkasan Pesanan
                        </h3>

                        <div style="display:flex;gap:1rem;align-items:center;margin:1rem 0;background:#FAF8F5;padding:.8rem;border-radius:10px;">
                            <img id="summary_preview_img" src="{{ $selectedDesign ? $selectedDesign->image_url : asset('storage/designs/ganci_toraja.png') }}" style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:1px solid var(--line);" alt="Preview">
                            <div>
                                <b id="summary_motif_text" style="display:block;font-size:.85rem;line-height:1.2;color:var(--maroon);">
                                    {{ $selectedDesign ? $selectedDesign->name : 'Edisi Motif Toraja Pa\'ssulan' }}
                                </b>
                                <small id="summary_shape_size" style="color:var(--muted);font-size:.74rem;">Bentuk Bulat • Standar</small>
                            </div>
                        </div>

                        <div class="summary-row">
                            <span>Harga Satuan:</span>
                            <span id="summary_unit_price">Rp{{ number_format($unitPrice, 0, ',', '.') }}</span>
                        </div>

                        <div class="summary-row">
                            <span>Kuantitas:</span>
                            <div style="display:flex;align-items:center;gap:.4rem;">
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" max="1000" style="width:70px;padding:.3rem .5rem;border:1px solid var(--line);border-radius:6px;font-weight:700;text-align:center;" oninput="updatePricing()">
                                <span>Unit</span>
                            </div>
                        </div>

                        <div id="wholesale_badge" style="display:none;background:#EAF5EC;border:1px solid #C8E6C9;color:#2E7D32;padding:.4rem .6rem;border-radius:8px;font-size:.75rem;margin:.6rem 0;font-weight:600;">
                            🎉 Diskon Paket Komunitas: Rp{{ number_format($wholesaleUnitPrice, 0, ',', '.') }}/unit diterapkan!
                        </div>

                        <div class="summary-row">
                            <span>Kanal Pesanan:</span>
                            <span id="summary_channel_text" style="font-weight:600;color:var(--maroon);">{{ old('channel') === 'offline' ? 'Offline (Langsung)' : 'Pesan Online' }}</span>
                        </div>

                        <div class="summary-row">
                            <span>Pengiriman:</span>
                            <span id="summary_delivery_text" style="font-weight:600;color:var(--ink);">{{ old('channel') === 'offline' || old('delivery_method') === 'ambil' ? 'Ambil di Workshop (Bebas Biaya)' : 'Kirim via Kurir' }}</span>
                        </div>

                        <div style="margin: 1.2rem 0 .8rem; padding-top: .8rem; border-top: 2px solid var(--line); display:flex; justify-content:space-between; align-items:baseline;">
                            <span style="font-weight:700;font-size:1rem;">Total Biaya:</span>
                            <span class="summary-total" id="summary_total_text">Rp{{ number_format($unitPrice, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit" class="btn btn-maroon btn-block btn-lg" style="margin-top:1rem;font-weight:700;font-size:.95rem;">
                            Lanjut ke Pembayaran →
                        </button>

                        <div style="margin-top:1rem;font-size:.74rem;color:var(--muted);line-height:1.4;text-align:center;">
                            🔒 Pembayaran aman melalui Midtrans (QRIS, E-Wallet, Virtual Account) atau Tunai di Kasir.<br>
                            Nomor pesanan & rincian pembayaran akan diberikan setelah formulir dikirim.
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>
</section>
@endsection

@section('extra_js')
<script>
    var baseUnitPrice = {{ $unitPrice }};
    var wholesaleUnitPrice = {{ $wholesaleUnitPrice }};
    var wholesaleThreshold = {{ $wholesaleThreshold }};

    function updatePricing() {
        var qtyInput = document.getElementById('quantity');
        var qty = parseInt(qtyInput.value) || 1;
        if (qty < 1) { qty = 1; qtyInput.value = 1; }

        var effectiveUnit = (qty >= wholesaleThreshold) ? wholesaleUnitPrice : baseUnitPrice;
        var total = effectiveUnit * qty;

        var wholesaleBadge = document.getElementById('wholesale_badge');
        if (qty >= wholesaleThreshold) {
            wholesaleBadge.style.display = 'block';
        } else {
            wholesaleBadge.style.display = 'none';
        }

        document.getElementById('summary_unit_price').innerText = 'Rp' + effectiveUnit.toLocaleString('id-ID');
        document.getElementById('summary_total_text').innerText = 'Rp' + total.toLocaleString('id-ID');
    }

    function selectChannel(val) {
        var radioInput = document.getElementById('channel_' + val);
        if (radioInput) {
            radioInput.checked = true;
        }

        document.querySelectorAll('.channel-opt').forEach(el => el.classList.remove('active'));
        if (event && event.currentTarget && event.currentTarget.classList.contains('channel-opt')) {
            event.currentTarget.classList.add('active');
        } else {
            var card = radioInput ? radioInput.closest('.channel-opt') : null;
            if (card) card.classList.add('active');
        }

        var deliverySelectionGroup = document.getElementById('delivery_method_selection_group');
        var offlineDeliveryNotice = document.getElementById('offline_delivery_notice');
        var addressBox = document.getElementById('address_box');
        var workshopInfo = document.getElementById('workshop_info');
        var step4Title = document.getElementById('step_4_title');
        var summaryChannel = document.getElementById('summary_channel_text');
        var summaryDelivery = document.getElementById('summary_delivery_text');

        if (val === 'offline') {
            if (summaryChannel) summaryChannel.innerText = 'Offline (Langsung)';
            if (step4Title) step4Title.innerText = 'Data Pemesan & Penyerahan';
            if (deliverySelectionGroup) deliverySelectionGroup.style.display = 'none';
            if (offlineDeliveryNotice) offlineDeliveryNotice.style.display = 'block';
            if (addressBox) addressBox.style.display = 'none';
            if (workshopInfo) workshopInfo.style.display = 'none';

            var ambilRadio = document.getElementById('delivery_ambil');
            if (ambilRadio) ambilRadio.checked = true;
            if (summaryDelivery) summaryDelivery.innerText = 'Ambil di Workshop (Bebas Biaya)';
        } else {
            if (summaryChannel) summaryChannel.innerText = 'Pesan Online';
            if (step4Title) step4Title.innerText = 'Data Pemesan & Pengiriman';
            if (deliverySelectionGroup) deliverySelectionGroup.style.display = 'block';
            if (offlineDeliveryNotice) offlineDeliveryNotice.style.display = 'none';

            var currentDelivery = document.querySelector('input[name="delivery_method"]:checked')?.value || 'kirim';
            selectDelivery(currentDelivery);
        }
    }

    function selectShape(val, title) {
        document.querySelectorAll('.shape-opt').forEach(el => el.classList.remove('active'));
        if (event && event.currentTarget) {
            event.currentTarget.classList.add('active');
            var input = event.currentTarget.querySelector('input');
            if (input) input.checked = true;
        }
        updateSummaryMeta();
    }

    function selectSize(val) {
        document.querySelectorAll('.size-opt').forEach(el => el.classList.remove('active'));
        if (event && event.currentTarget) {
            event.currentTarget.classList.add('active');
            var input = event.currentTarget.querySelector('input');
            if (input) input.checked = true;
        }
        updateSummaryMeta();
    }

    function selectMotif(name, imgUrl) {
        document.querySelectorAll('.color-opt').forEach(el => el.classList.remove('active'));
        if (event && event.currentTarget) {
            event.currentTarget.classList.add('active');
            var input = event.currentTarget.querySelector('input');
            if (input) input.checked = true;
        }
        document.getElementById('summary_motif_text').innerText = name;
        if (imgUrl) {
            document.getElementById('summary_preview_img').src = imgUrl;
        }
    }

    function selectDelivery(val) {
        document.querySelectorAll('.delivery-opt').forEach(el => el.classList.remove('active'));
        var activeOpt = document.querySelector(`.delivery-opt input[value="${val}"]`)?.closest('.delivery-opt');
        if (activeOpt) {
            activeOpt.classList.add('active');
        }
        var radioInput = document.getElementById('delivery_' + val);
        if (radioInput) {
            radioInput.checked = true;
        }

        var addrBox = document.getElementById('address_box');
        var workshopInfo = document.getElementById('workshop_info');
        var summaryText = document.getElementById('summary_delivery_text');
        var isOffline = document.querySelector('input[name="channel"]:checked')?.value === 'offline';

        if (val === 'ambil') {
            if (addrBox) addrBox.style.display = 'none';
            if (workshopInfo) workshopInfo.style.display = isOffline ? 'none' : 'block';
            if (summaryText) summaryText.innerText = 'Ambil di Workshop (Bebas Biaya)';
        } else {
            if (addrBox) addrBox.style.display = isOffline ? 'none' : 'block';
            if (workshopInfo) workshopInfo.style.display = 'none';
            if (summaryText) summaryText.innerText = 'Kirim via Kurir';
        }
    }

    function updateSummaryMeta() {
        var shapeChecked = document.querySelector('input[name="shape"]:checked');
        var sizeChecked = document.querySelector('input[name="size"]:checked');
        var shapeText = shapeChecked ? shapeChecked.value.toUpperCase() : 'BULAT';
        var sizeText = sizeChecked ? sizeChecked.value : 'Standar';
        document.getElementById('summary_shape_size').innerText = 'Bentuk ' + shapeText + ' • ' + sizeText;
    }

    // Real-Time Email Validation & Typo Detection
    var emailDebounceTimer = null;
    var currentEmailSuggestion = null;

    function handleEmailInput(val) {
        clearTimeout(emailDebounceTimer);
        var typoBox = document.getElementById('email_typo_box');
        var statusMsg = document.getElementById('email_status_msg');
        var spinner = document.getElementById('email_spinner');

        val = (val || '').trim();
        if (!val) {
            typoBox.style.display = 'none';
            statusMsg.innerHTML = '';
            return;
        }

        // Basic client-side regex check first
        var basicRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!basicRegex.test(val)) {
            typoBox.style.display = 'none';
            statusMsg.innerHTML = '<span style="color:#dc2626;font-weight:500;">⚠️ Format email belum lengkap (contoh: nama@gmail.com)</span>';
            return;
        }

        // Client-side quick typo check
        var parts = val.toLowerCase().split('@');
        if (parts.length === 2) {
            var domain = parts[1];
            var typos = {
                'gmai.com': 'gmail.com',
                'gamil.com': 'gmail.com',
                'gmial.com': 'gmail.com',
                'gmaill.com': 'gmail.com',
                'gmaik.com': 'gmail.com',
                'yaho.com': 'yahoo.com',
                'yahooo.com': 'yahoo.com',
                'yaho.co.id': 'yahoo.co.id',
                'hotmial.com': 'hotmail.com',
                'outlok.com': 'outlook.com',
                'iclloud.com': 'icloud.com'
            };
            if (typos[domain]) {
                currentEmailSuggestion = parts[0] + '@' + typos[domain];
                document.getElementById('email_typo_suggestion').innerText = currentEmailSuggestion;
                typoBox.style.display = 'flex';
                statusMsg.innerHTML = '<span style="color:#b45309;font-weight:500;">⚠️ Kemungkinan salah ketik domain email.</span>';
                return;
            }
        }

        typoBox.style.display = 'none';
        if (spinner) spinner.style.display = 'inline';

        emailDebounceTimer = setTimeout(function () {
            fetch('{{ route("order.check_email") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: val })
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (spinner) spinner.style.display = 'none';
                if (data.has_typo && data.suggestion) {
                    currentEmailSuggestion = data.suggestion;
                    document.getElementById('email_typo_suggestion').innerText = currentEmailSuggestion;
                    typoBox.style.display = 'flex';
                    statusMsg.innerHTML = '<span style="color:#b45309;font-weight:500;">⚠️ Kemungkinan salah ketik domain.</span>';
                } else if (data.valid) {
                    typoBox.style.display = 'none';
                    statusMsg.innerHTML = '<span style="color:#166534;font-weight:600;">✓ Alamat email valid &amp; aktif (siap menerima kode aktivasi).</span>';
                } else {
                    typoBox.style.display = 'none';
                    statusMsg.innerHTML = '<span style="color:#dc2626;font-weight:500;">⚠️ ' + (data.message || 'Domain email tidak aktif.') + '</span>';
                }
            })
            .catch(function () {
                if (spinner) spinner.style.display = 'none';
                statusMsg.innerHTML = '<span style="color:#166534;font-weight:500;">✓ Format email sesuai (nama@domain.com).</span>';
            });
        }, 400);
    }

    function applyEmailSuggestion() {
        if (!currentEmailSuggestion) return;
        var emailInput = document.getElementById('email');
        emailInput.value = currentEmailSuggestion;
        document.getElementById('email_typo_box').style.display = 'none';
        document.getElementById('email_status_msg').innerHTML = '<span style="color:#166534;font-weight:600;">✓ Email diperbaiki: ' + currentEmailSuggestion + ' (valid).</span>';
        currentEmailSuggestion = null;
    }

    // Init state on load
    document.addEventListener('DOMContentLoaded', function () {
        updatePricing();
        updateSummaryMeta();

        var emailInput = document.getElementById('email');
        if (emailInput && emailInput.value) {
            handleEmailInput(emailInput.value);
        }

        var initialChannel = document.querySelector('input[name="channel"]:checked')?.value || 'online';
        if (initialChannel === 'offline') {
            selectChannel('offline');
        } else {
            var initialDelivery = document.querySelector('input[name="delivery_method"]:checked')?.value || 'kirim';
            selectDelivery(initialDelivery);
        }
    });
</script>
@endsection
