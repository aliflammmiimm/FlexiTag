@php
    $brand = config('flexitag.brand');
    $price = 'Rp'.number_format(config('flexitag.unit_price'), 0, ',', '.');
    $contact = config('flexitag.contact');

    // Format: [nama-ikon, judul, teks, chip]. Nama ikon ada di partials/icon.blade.php
    $problems = [
        ['file', 'Kartu Nama Mudah Hilang & Usang', 'Kartu kertas tidak bisa diperbarui saat nomor telepon, jabatan, atau portofolio berubah.', ['Nomor Telepon', 'Jabatan', 'Portofolio']],
        ['clip', 'Berbagi Kontak Masih Berbelit', 'Mengetik nomor dan mencari akun media sosial satu per satu memakan waktu.', ['Nomor Kontak', 'Media Sosial']],
        ['key', 'Barang Pribadi Hilang Sulit Kembali', 'Kunci, tas, atau laptop yang tertinggal tidak membawa informasi pemilik yang aman.', ['Kunci', 'Tas', 'Laptop']],
    ];

    $modes = [
        ['cap', 'Mode Kampus', 'Mahasiswa & Organisasi', 'Menampilkan nama, jurusan, kampus dan angkatan, serta tautan jejaring.', ['Instagram', 'LinkedIn', 'Tautan Organisasi', 'WhatsApp']],
        ['briefcase', 'Mode Kerja', 'Kontak Profesional', 'Menampilkan nama, profesi, ringkasan singkat, portofolio, dan kontak.', ['Unduh CV', 'Portofolio / GitHub / Behance', 'LinkedIn', 'Simpan Kontak vCard']],
        ['bag', 'Mode UMKM', 'Pelaku Usaha', 'Menampilkan logo, nama usaha, slogan, jam buka, dan katalog produk.', ['Peta Lokasi', 'Katalog Foto & Harga', 'Pesan via WhatsApp', 'Marketplace']],
        ['shield', 'Mode Barang Hilang', 'Privasi Terlindungi', 'Menampilkan pesan “Barang ini milik seseorang” dan formulir penemu, tanpa membuka nomor HP pemilik.', ['Nama Penemu', 'Lokasi Penemuan', 'Pesan', 'Nomor HP Tersembunyi']],
    ];

    $features = [
        ['phone', 'Multi-Mode Fleksibel', 'Beralih antar 4 mode profil kapan saja lewat dashboard.'],
        ['chart', 'Statistik Tap Lengkap', 'Pantau jumlah tap harian, mingguan, bulanan, dan klik per tautan.'],
        ['vcard', 'Simpan Kontak (vCard)', 'Pengunjung mengunduh kontak langsung ke ponsel dengan sekali tekan.'],
        ['shield', 'Mode Barang Hilang', 'Penemu bisa menghubungi pemilik lewat formulir tanpa melihat nomor HP.'],
        ['palette', 'Tema Lokal Sulawesi', 'Pilihan tema tampilan profil dan desain bermotif Bugis, Toraja, dan kampus.'],
        ['qr', 'QR Cadangan & Kontrol Privasi', 'QR di setiap produk untuk ponsel tanpa NFC, serta pengaturan tampilan nomor HP dan email.'],
    ];

    $steps = [
        ['01', 'edit', 'Pesan & Kirim Desain', 'Pilih bentuk, ukuran, warna akrilik, dan unggah desain custom.'],
        ['02', 'key', 'Dicetak & Dirakit', 'Akrilik dicetak, dipasang chip NFC NTAG213 yang dikunci (read-only).'],
        ['03', 'phone', 'Terima & Aktifkan', 'Tempelkan ke HP atau scan QR, lalu masukkan kode aktivasi sekali pakai.'],
        ['04', 'zap', 'Tap & Bagikan', 'Kelola isi profil di dashboard dan bagikan identitas dalam 1–2 detik.'],
    ];

    // 4 Produk Terlaris (Katalog Desain & Model Ganci)
    $catalogDesigns = ($designs ?? \App\Models\Design::active()->get())
        ->sortByDesc(fn ($d) => $d->units_sold ?? 0)
        ->values()
        ->take(4);

    $models = [
        ['circle', 'Model 01 — Bentuk Bulat', 'Pribadi & Kampus', 'Desain bulat dengan pilihan ukuran kecil, sedang, atau besar.', asset('storage/designs/ganci_bulat.png')],
        ['square', 'Model 02 — Bentuk Persegi', 'UMKM', 'Bentuk persegi untuk menampilkan logo usaha, nama brand, dan QR.', asset('storage/designs/ganci_umkm.png')],
        ['diamond', 'Model 04 — Edisi Motif Toraja', 'Tema Lokal', 'Gantungan kunci akrilik dengan ornamen khas budaya Toraja.', asset('storage/designs/ganci_toraja.png')],
        ['diamond', 'Model 05 — Edisi Motif Bugis', 'Tema Lokal', 'Gantungan kunci akrilik dengan sentuhan motif tradisional Bugis.', asset('storage/designs/ganci_bugis.png')],
    ];

    $faqs = [
        ['Apakah perlu aplikasi?', 'Tidak. Profil terbuka melalui browser tanpa aplikasi.'],
        ['Ponsel apa yang didukung?', 'Android & iPhone; gunakan NFC atau QR cadangan.'],
        ['Bagaimana bila ganti nomor?', 'Perbarui nomor dan isi profil melalui Dashboard tanpa mengganti gantungan.'],
        ['Bagaimana bila gantungan hilang & apakah data aman?', 'Mode Barang Hilang memungkinkan penemu mengirim formulir tanpa melihat nomor HP Anda. Anda juga dapat mengatur data apa yang tampil di profil publik.'],
        ['Berapa lama pengerjaan & cara pembayaran?', 'Estimasi 5–7 hari kerja. Konfirmasikan informasi pembayaran kepada admin setelah memesan.'],
    ];

    $personas = [
        ['R', 'Rina', 'Mahasiswa', 'Memakai Mode Kampus & Mode Kerja.', ['Mode Kampus', 'Mode Kerja']],
        ['S', 'Ibu Sari', 'Pemilik Kedai Kopi', 'Memakai Mode UMKM untuk profil usaha.', ['Mode UMKM']],
        ['H', 'HIMA Teknik Komputer', 'Organisasi Mahasiswa', 'Memesan paket komunitas untuk 30 anggota.', ['Paket Komunitas', '30 Anggota']],
    ];

    $trust = [
        ['phone', 'Tanpa Aplikasi'],
        ['qr', 'Android & iPhone (QR Cadangan)'],
        ['refresh', 'Bisa Diubah Kapan Saja'],
        ['zap', 'Terbuka < 2 Detik'],
    ];

    // Foto produk: letakkan berkas di public/images/keychain.jpg (opsional). Bila tidak ada, dipakai ilustrasi.
    $keychainPhoto = file_exists(public_path('images/keychain.jpg')) ? asset('images/keychain.jpg') : null;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brand }} — {{ config('flexitag.tagline') }}</title>
    <meta name="description" content="Gantungan kunci akrilik custom bertag NFC dengan profil digital yang bisa diubah kapan saja.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/flexitag-user.css') }}">
</head>
<body>

<header class="site-header">
    <div class="container">
        <a href="{{ route('home') }}" class="brand">
            <strong>{{ $brand }}</strong>
            <small>{{ config('flexitag.tagline') }}</small>
        </a>

        <button class="menu-btn" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="menu" id="menuBtn">☰</button>

        <div class="menu" id="menu">
            <nav class="nav" aria-label="Navigasi utama">
                <a href="#produk">Produk</a>
                <a href="#cara-kerja">Cara Kerja</a>
                <a href="{{ route('order.track') }}">Lacak Pesanan</a>
                <a href="#faq">FAQ</a>
                <a href="#kontak">Kontak</a>
            </nav>

            <div class="header-actions">
                @auth
                    @if (in_array(auth()->user()->role, ['admin', 'staff'], true))
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-gold">Dashboard Admin</a>
                    @elseif (auth()->user()->tags()->where('status', 'aktif')->exists())
                        <a href="{{ route('user.dashboard') }}" class="btn btn-gold">Buka Dashboard</a>
                    @else
                        <a href="{{ route('user.activation.show', ['step' => 2]) }}" class="btn btn-gold">Aktivasi Tag</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="padding: .5rem .9rem;">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                    <a href="{{ route('user.activation.show') }}" class="btn btn-gold">Aktivasi Tag</a>
                    <a href="{{ route('order.create') }}" class="btn btn-maroon">Pesan Sekarang</a>
                @endauth
            </div>
        </div>
    </div>
</header>

<main>
    {{-- Hero --}}
    <section class="hero">
        <div class="container grid">
            <div>
                <span class="pill">Gantungan kunci akrilik custom bertag NFC</span>
                <h1>{{ config('flexitag.tagline') }}</h1>
                <p class="lead">Gantungan kunci akrilik custom dengan chip NFC (NTAG213) yang terhubung ke platform profil digital dinamis. Tempelkan ke ponsel untuk membuka kontak, tautan media sosial, portofolio, atau katalog usaha tanpa aplikasi.</p>

                <div class="hero-actions">
                    <a href="{{ route('order.create') }}" class="btn btn-gold btn-lg">Pesan Sekarang ({{ $price }})</a>
                    <a href="#cara-kerja" class="btn btn-outline btn-lg">Lihat Cara Kerja</a>
                </div>

                <div class="trust">
                    @foreach ($trust as [$ico, $label])
                        <div><span class="ico ico-maroon">@include('partials.icon', ['name' => $ico])</span>{{ $label }}</div>
                    @endforeach
                </div>
            </div>

            <div class="hero-panel">
                <div class="orn"></div>
                <div class="mock">
                    <div class="phone" aria-hidden="true">
                        <div class="phone-screen">
                            <div class="brand-mini">{{ $brand }}</div>
                            <span class="tag">PROFIL PUBLIK</span>
                            <div class="avatar">@include('partials.icon', ['name' => 'user'])</div>
                            <div class="nm">Nama Anda</div>
                            <div class="sub">Nama • Jurusan / Profesi</div>
                            <div class="sub2">Profil digital dinamis</div>
                            <div class="phone-link wa">WhatsApp</div>
                            <div class="phone-link">Instagram</div>
                            <div class="phone-link">LinkedIn</div>
                            <div class="phone-link">Portofolio</div>
                        </div>
                    </div>

                    <div class="mock-side">
                        <div class="keychain" role="img" aria-label="Gantungan kunci akrilik NFC custom">
                            @if ($keychainPhoto)
                                <img src="{{ $keychainPhoto }}" alt="Gantungan kunci akrilik NFC custom">
                            @else
                                <svg viewBox="0 0 240 200" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                                    <defs>
                                        <radialGradient id="kcBg" cx="50%" cy="40%" r="80%"><stop offset="0" stop-color="#e6ded2"/><stop offset="1" stop-color="#c9bfb0"/></radialGradient>
                                        <radialGradient id="kcDisc" cx="50%" cy="45%" r="55%"><stop offset="0" stop-color="#b8782a"/><stop offset="1" stop-color="#6e3a10"/></radialGradient>
                                    </defs>
                                    <rect width="240" height="200" fill="url(#kcBg)"/>
                                    <circle cx="120" cy="30" r="14" fill="none" stroke="#9a9a9a" stroke-width="4"/>
                                    <circle cx="120" cy="112" r="68" fill="rgba(255,255,255,.55)" stroke="#fff" stroke-width="3"/>
                                    <circle cx="120" cy="112" r="56" fill="url(#kcDisc)"/>
                                    <circle cx="120" cy="112" r="40" fill="none" stroke="#e0b04a" stroke-width="2" stroke-dasharray="3 5"/>
                                    <path d="M120 82 L142 112 L120 142 L98 112 Z" fill="none" stroke="#e0b04a" stroke-width="2.5"/>
                                    <circle cx="120" cy="112" r="7" fill="#e0b04a"/>
                                </svg>
                            @endif
                        </div>
                        <div class="code-card">
                            <small>PANDUAN AKTIVASI</small>
                            <b>KD-0000-XX</b>
                            <span>Kode aktivasi sekali pakai</span>
                            <span style="margin-top:.4rem">Tempel NFC atau scan QR untuk memulai.</span>
                        </div>
                    </div>
                </div>
                <div class="orn bottom"></div>
            </div>
        </div>
    </section>

    {{-- Masalah & solusi --}}
    <section class="block band">
        <div class="container">
            <div class="eyebrow"><span>✦ Identitas yang lebih praktis ✦</span></div>
            <h2 class="section-title">Berbagi identitas seharusnya lebih mudah.</h2>
            <div class="grid grid-3">
                @foreach ($problems as [$ico, $title, $text, $chips])
                    <div class="card-maroon">
                        <div class="orn"></div>
                        <span class="ico ico-gold">@include('partials.icon', ['name' => $ico])</span>
                        <h3>{{ $title }}</h3>
                        <p class="txt">{{ $text }}</p>
                        <div class="chips">@foreach ($chips as $c)<span class="chip">{{ $c }}</span>@endforeach</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Empat mode --}}
    <section class="block" id="mode">
        <div class="container">
            <div class="eyebrow"><span>✦ Profil sesuai situasimu ✦</span></div>
            <h2 class="section-title">Satu Gantungan, 4 Mode Profil Dinamis</h2>
            <p class="section-sub">Ubah fungsi profil digitalmu kapan saja melalui Dashboard sesuai kebutuhan.</p>
            <div class="grid grid-2">
                @foreach ($modes as [$ico, $title, $cat, $text, $chips])
                    <div class="card">
                        <div style="display:flex;gap:.9rem;align-items:center;margin-bottom:.7rem;">
                            <span class="ico ico-maroon">@include('partials.icon', ['name' => $ico])</span>
                            <div><h3 style="margin:0">{{ $title }}</h3><small style="color:var(--gold-deep);font-weight:500">{{ $cat }}</small></div>
                        </div>
                        <p class="txt">{{ $text }}</p>
                        <div class="chips">@foreach ($chips as $c)<span class="chip">{{ $c }}</span>@endforeach</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Fitur --}}
    <section class="block band">
        <div class="container">
            <div class="eyebrow"><span>✦ Fitur resmi {{ $brand }} ✦</span></div>
            <h2 class="section-title">Satu platform, banyak cara terhubung.</h2>
            <div class="grid grid-3">
                @foreach ($features as [$ico, $title, $text])
                    <div class="card">
                        <span class="ico ico-maroon" style="margin-bottom:.8rem">@include('partials.icon', ['name' => $ico])</span>
                        <h3>{{ $title }}</h3>
                        <p class="txt" style="margin:0">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Cara kerja --}}
    <section class="block" id="cara-kerja">
        <div class="container">
            <div class="eyebrow"><span>✦ Dari desain ke satu tap ✦</span></div>
            <h2 class="section-title">Cara Kerja & Alur Aktivasi</h2>
            <div class="grid grid-4 keep2">
                @foreach ($steps as [$no, $ico, $title, $text])
                    <div class="card-maroon">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span class="ico ico-gold">@include('partials.icon', ['name' => $ico])</span>
                            <span class="step-no">{{ $no }}</span>
                        </div>
                        <h3>{{ $title }}</h3>
                        <p class="txt" style="margin:0">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Katalog --}}
    <section class="block band" id="produk">
        <div class="container">
            <div class="eyebrow"><span>✦ 4 Produk Terlaris Bertag NFC ✦</span></div>
            <h2 class="section-title">Katalog Produk Paling Laku & Desain Favorit</h2>
            <p class="section-sub">Pilihan model gantungan kunci akrilik NFC yang paling banyak dipesan untuk profil pribadi, kebutuhan kampus, dan branding UMKM.</p>
            <div class="grid grid-4">
                @if ($catalogDesigns->isNotEmpty())
                    @foreach ($catalogDesigns as $index => $design)
                        <div class="card model">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem;">
                                <span class="chip" style="margin:0">{{ $design->category_badge['label'] }}</span>
                                <span style="font-size:.68rem;font-weight:700;color:var(--gold-deep);background:var(--gold-soft);border:1px solid var(--line);border-radius:999px;padding:.15rem .55rem;">
                                    ★ Top #{{ $index + 1 }}
                                </span>
                            </div>
                            <div class="shot">
                                <img src="{{ $design->image_url }}" alt="{{ $design->name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/keychain.jpg') }}';">
                            </div>
                            <h3>{{ $design->name }}</h3>
                            <p class="txt">
                                @if ($design->category === 'motif_lokal')
                                    Gantungan kunci akrilik bernuansa ornamen khas Sulawesi dengan chip NFC NTAG213.
                                @elseif ($design->category === 'umkm')
                                    Bentuk akrilik branding usaha dengan QR dinamis, logo bisnis, dan katalog digital.
                                @elseif ($design->category === 'organisasi')
                                    Desain identitas komunitas, panitia kampus, atau angkatan mahasiswa.
                                @else
                                    Desain akrilik modern untuk profil digital, media sosial, dan kontak vCard.
                                @endif
                            </p>
                            <div class="price">{{ $price }} / unit</div>
                            <ul class="ticks">
                                <li>Chip NFC NTAG213 + QR Cadangan</li>
                                <li>Kartu Panduan & Kode Aktivasi</li>
                                <li>Ukuran Kecil / Sedang / Besar</li>
                            </ul>
                            <a href="{{ route('order.create', ['design_id' => $design->id]) }}" class="btn btn-maroon btn-block">Pilih Desain Ini</a>
                        </div>
                    @endforeach
                @else
                    @foreach ($models as $index => [$ico, $title, $cat, $text, $img])
                        <div class="card model">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem;">
                                <span class="chip" style="margin:0">{{ $cat }}</span>
                                <span style="font-size:.68rem;font-weight:700;color:var(--gold-deep);background:var(--gold-soft);border:1px solid var(--line);border-radius:999px;padding:.15rem .55rem;">
                                    ★ Top #{{ $index + 1 }}
                                </span>
                            </div>
                            <div class="shot">
                                <img src="{{ $img }}" alt="{{ $title }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/keychain.jpg') }}';">
                            </div>
                            <h3>{{ $title }}</h3>
                            <p class="txt">{{ $text }}</p>
                            <div class="price">{{ $price }} / unit</div>
                            <ul class="ticks">
                                <li>Chip NFC NTAG213 + QR Cadangan</li>
                                <li>Kartu Panduan & Kode Aktivasi</li>
                                <li>Ukuran Kecil / Sedang / Besar</li>
                            </ul>
                            <a href="{{ route('order.create', ['shape' => $ico]) }}" class="btn btn-maroon btn-block">Pilih Model Ini</a>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="card" style="margin-top:2rem;">
                <div class="eyebrow"><span>✦ Studio custom & paket harga ✦</span></div>
                <h2>Wujudkan desain gantungan versimu.</h2>
                <div class="grid grid-2" style="margin-top:1rem;">
                    <div>
                        <b style="color:var(--maroon);font-size:.8rem;letter-spacing:.05em">KUSTOMISASI GANTUNGAN</b>
                        <p style="margin:.6rem 0 .2rem;font-weight:600;font-size:.88rem">Bentuk</p>
                        <div class="chips" style="margin-top:0"><span class="chip">Bulat</span><span class="chip">Persegi</span><span class="chip">Bebas</span></div>
                        <p style="margin:.8rem 0 .2rem;font-weight:600;font-size:.88rem">Ukuran</p>
                        <div class="chips" style="margin-top:0"><span class="chip">Kecil</span><span class="chip">Sedang</span><span class="chip">Besar</span></div>
                        <p style="margin:.8rem 0 .2rem;font-weight:600;font-size:.88rem">Warna akrilik & jenis kait</p>
                        <p class="txt" style="margin:0">Dipilih saat memesan. Unggah desain PNG/SVG minimal 1000px dan tambahkan teks.</p>
                    </div>
                    <div>
                        <div class="pack"><div><b>Paket Satuan</b><br><small style="color:var(--muted)">Gantungan kunci akrilik NFC</small></div><div class="price" style="margin:0">{{ $price }}</div></div>
                        <div class="pack"><div><b>Paket Komunitas</b><br><small style="color:var(--muted)">Minimal 10 unit</small></div><div><b>Harga khusus</b></div></div>
                        <div class="pack soon"><b>Paket Premium</b><span>Segera hadir</span></div>
                        <div class="chips" style="margin:.4rem 0 1rem;"><span class="chip">Revisi gratis</span><span class="chip">Estimasi 5–7 hari kerja</span></div>
                        <a href="{{ route('order.create') }}" class="btn btn-gold btn-block">Buat Desain Custom Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Persona & FAQ --}}
    <section class="block" id="faq">
        <div class="container">
            <div class="eyebrow"><span>✦ Penggunaan dan informasi ✦</span></div>
            <h2 class="section-title">Untuk setiap kebutuhan, tetap satu {{ $brand }}.</h2>
            <div class="grid grid-2" style="grid-template-columns:.9fr 1.1fr;align-items:start;">
                <div>
                    <h3>Persona Pengguna</h3>
                    <span class="chip">Ilustrasi persona — bukan kutipan testimoni</span>
                    <div style="margin-top:1rem;display:grid;gap:1rem;">
                        @foreach ($personas as [$init, $name, $role, $text, $chips])
                            <div class="card">
                                <div style="display:flex;gap:.8rem;align-items:center;">
                                    <span class="ico ico-maroon" style="border-radius:50%;font-weight:700;font-size:1rem">{{ $init }}</span>
                                    <div><b>{{ $name }}</b><br><small style="color:var(--muted)">{{ $role }}</small></div>
                                </div>
                                <p class="txt" style="margin:.8rem 0 0">{{ $text }}</p>
                                <div class="chips">@foreach ($chips as $c)<span class="chip">{{ $c }}</span>@endforeach</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h3>Pertanyaan yang Sering Diajukan</h3>
                    @foreach ($faqs as [$q, $a])
                        <details>
                            <summary>{{ $q }}</summary>
                            <p>{{ $a }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Ajakan akhir --}}
    <section class="block" style="padding-top:0">
        <div class="container">
            <div class="cta">
                <div class="eyebrow center"><span>{{ config('flexitag.tagline') }}</span></div>
                <h2 class="section-title" style="margin-bottom:.6rem">Siap membagikan identitas digitalmu?</h2>
                <p class="section-sub">Pesan gantungan custom atau masuk untuk mengelola profil Anda.</p>
                <div class="hero-actions">
                    <a href="{{ route('order.create') }}" class="btn btn-gold btn-lg">Pesan Sekarang</a>
                    @auth
                        @if (in_array(auth()->user()->role, ['admin', 'staff'], true))
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-lg">Dashboard Admin</a>
                        @elseif (auth()->user()->tags()->where('status', 'aktif')->exists())
                            <a href="{{ route('user.dashboard') }}" class="btn btn-outline btn-lg">Buka Dashboard</a>
                        @else
                            <a href="{{ route('user.activation.show', ['step' => 2]) }}" class="btn btn-outline btn-lg">Aktivasi Tag</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline btn-lg">Masuk</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- Seksi Formulir Hubungi Kami (FR-ADM-09) --}}
    <section class="block band" id="kontak-kami" style="padding-top: 3.5rem; padding-bottom: 4rem;">
        <div class="container" style="max-width: 860px;">
            <div class="eyebrow center"><span>HUBUNGI KAMI</span></div>
            <h2 class="section-title center" style="text-align: center; margin-bottom: .6rem;">Ada Pertanyaan atau Minat Pesanan Khusus?</h2>
            <p class="section-sub center" style="text-align: center; margin-inline: auto; margin-bottom: 2rem;">
                Kirimkan pesan Anda langsung ke tim FlexiTag. Kami siap membantu konsultasi desain, kemitraan UMKM, maupun pemesanan custom komunitas.
            </p>

            @if (session('contact_success'))
                <div style="background: #eaf5ec; border: 1.5px solid #2e7d32; border-radius: 12px; padding: 14px 18px; margin-bottom: 1.5rem; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 20px;">✓</span>
                        <div>
                            <strong style="color: #2e7d32; display: block; font-size: 13.5px;">Pesan Anda Berhasil Terkirim!</strong>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #1e5a23;">{{ session('contact_success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div style="background: #fee2e2; border: 1.5px solid #991b1b; border-radius: 12px; padding: 14px 18px; margin-bottom: 1.5rem; text-align: left;">
                    <strong style="color: #991b1b; display: block; font-size: 13px;">Gagal Mengirim Pesan:</strong>
                    <ul style="margin: 4px 0 0; padding-left: 18px; font-size: 12px; color: #7f1d1d;">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card" style="padding: 2rem 2.2rem; border-radius: 16px;">
                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="grid grid-2" style="margin-bottom: 0;">
                        <div class="form-group">
                            <label class="form-label" for="contactSenderName">Nama Lengkap <span class="req">*</span></label>
                            <input type="text" name="sender_name" id="contactSenderName" class="form-control" placeholder="Contoh: Andi Pratama" value="{{ old('sender_name') }}" required maxlength="100">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contactSenderContact">No. WhatsApp / Email <span class="req">*</span></label>
                            <input type="text" name="contact" id="contactSenderContact" class="form-control" placeholder="Contoh: 0812-3456-7890 / email@anda.com" value="{{ old('contact') }}" required maxlength="100">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contactLocation">Kota / Instansi / Komunitas (Opsional)</label>
                        <input type="text" name="location" id="contactLocation" class="form-control" placeholder="Contoh: Makassar • HIMA Teknik Komputer" value="{{ old('location') }}" maxlength="150">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contactMessage">Isi Pesan atau Pertanyaan <span class="req">*</span></label>
                        <textarea name="message" id="contactMessage" class="form-control" rows="4" placeholder="Tuliskan pertanyaan, konsultasi motif budaya, atau rincian pesanan custom Anda di sini..." required maxlength="1500">{{ old('message') }}</textarea>
                    </div>

                    <div style="text-align: right; margin-top: 1.2rem;">
                        <button type="submit" class="btn btn-gold btn-lg" style="min-width: 200px;">
                            <span>Kirim Pesan Sekarang</span>
                            <span style="font-size: 1.1em;">➔</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<footer class="site-footer" id="kontak">
    <div class="orn" style="margin-bottom:2rem"></div>
    <div class="container">
        <div class="grid grid-4 f-grid">
            <div>
                <div class="f-brand">{{ $brand }}</div>
                <p style="color:var(--gold);margin:.3rem 0">{{ config('flexitag.tagline') }}</p>
                <p>Gantungan kunci akrilik custom bertag NFC dan platform profil digital.</p>
            </div>
            <div>
                <h4>MENU</h4>
                <ul>
                    <li><a href="#produk">Produk</a></li>
                    <li><a href="{{ route('order.create') }}">Pesan Ganci</a></li>
                    <li><a href="{{ route('order.track') }}">Lacak Pesanan</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#kontak">Kontak</a></li>
                </ul>
            </div>
            <div>
                <h4>BANTUAN & AKUN</h4>
                <ul>
                    @auth
                        @if (in_array(auth()->user()->role, ['admin', 'staff'], true))
                            <li><a href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                        @elseif (auth()->user()->tags()->where('status', 'aktif')->exists())
                            <li><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                        @else
                            <li><a href="{{ route('user.activation.show', ['step' => 2]) }}">Aktivasi Tag</a></li>
                        @endif
                    @else
                        <li><a href="{{ route('login') }}">Masuk</a></li>
                        <li><a href="{{ route('register') }}">Daftar</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h4>KONTAK KAMI</h4>
                <ul>
                    @if (! empty($contact['whatsapp']))
                        <li><a href="https://wa.me/{{ preg_replace('/\D+/', '', $contact['whatsapp']) }}" target="_blank" rel="noopener">WhatsApp Admin</a></li>
                    @endif
                    @if (! empty($contact['instagram']))
                        <li><a href="https://instagram.com/{{ ltrim($contact['instagram'], '@') }}" target="_blank" rel="noopener">Instagram</a></li>
                    @endif
                    @if (! empty($contact['email']))
                        <li><a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></li>
                    @endif
                    <li>Lokasi: {{ $contact['location'] }}</li>
                </ul>
            </div>
        </div>
        <div class="copy">© {{ date('Y') }} {{ $brand }}. Hak Cipta Dilindungi.</div>
    </div>
</footer>

<script>
    (function () {
        var btn = document.getElementById('menuBtn'), menu = document.getElementById('menu');
        if (!btn || !menu) return;
        btn.addEventListener('click', function () {
            var open = menu.classList.toggle('open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        menu.addEventListener('click', function (e) {
            if (e.target.tagName === 'A') { menu.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); }
        });
    })();
</script>
</body>
</html>
