@php
    $brand = config('flexitag.brand', 'FlexiTag');
    $tagline = config('flexitag.tagline', 'Satu Tap, Seribu Identitas');
    $contact = config('flexitag.contact', []);
    $price = 'Rp'.number_format(config('flexitag.unit_price', 75000), 0, ',', '.');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pemesanan Custom') — {{ $brand }}</title>
    <meta name="description" content="@yield('meta_description', 'Pemesanan gantungan kunci akrilik custom bertag NFC FlexiTag.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/flexitag-user.css') }}">
    @yield('extra_css')
</head>
<body>

<header class="site-header">
    <div class="container">
        <a href="{{ route('home') }}" class="brand">
            <strong>{{ $brand }}</strong>
            <small>{{ $tagline }}</small>
        </a>

        <button class="menu-btn" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="menu" id="menuBtn">☰</button>

        <div class="menu" id="menu">
            <nav class="nav" aria-label="Navigasi utama">
                <a href="{{ route('home') }}#produk">Katalog Produk</a>
                <a href="{{ route('order.create') }}" class="{{ request()->routeIs('order.create') ? 'font-bold' : '' }}">Pesan Ganci Custom</a>
                <a href="{{ route('order.track') }}" class="{{ request()->routeIs('order.track') ? 'font-bold' : '' }}">Lacak Pesanan</a>
                <a href="{{ route('home') }}#faq">FAQ</a>
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
                    <a href="{{ route('order.create') }}" class="btn btn-maroon">Pesan Sekarang</a>
                @endauth
            </div>
        </div>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="site-footer" id="kontak">
    <div class="orn" style="margin-bottom:2rem"></div>
    <div class="container">
        <div class="grid grid-4 f-grid">
            <div>
                <div class="f-brand">{{ $brand }}</div>
                <p style="color:var(--gold);margin:.3rem 0">{{ $tagline }}</p>
                <p>Gantungan kunci akrilik custom bertag NFC dan platform profil digital.</p>
            </div>
            <div>
                <h4>MENU</h4>
                <ul>
                    <li><a href="{{ route('home') }}#produk">Katalog Produk</a></li>
                    <li><a href="{{ route('order.create') }}">Formulir Pemesanan</a></li>
                    <li><a href="{{ route('order.track') }}">Lacak Pesanan</a></li>
                    <li><a href="{{ route('home') }}#faq">FAQ</a></li>
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
                    <li>Lokasi: {{ $contact['location'] ?? 'Makassar, Sulawesi Selatan' }}</li>
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
@yield('extra_js')
</body>
</html>
