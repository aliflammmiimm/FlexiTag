<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'FlexiTag — Identitas Digital NFC Berbasis Warisan Budaya Sulawesi')</title>
    <meta name="description" content="@yield('meta_description', 'FlexiTag menghubungkan kartu fisik NFC bermotif warisan Sulawesi dengan identitas digital dinamis 4-in-1: Kampus, Portofolio Kerja, Katalog UMKM, dan Mode Barang Hilang.')">

    @if(isset($indexable) && !$indexable)
        <meta name="robots" content="noindex, nofollow">
    @endif

    <!-- Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Local Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="ft-body">
    <a href="#main-content" class="ft-skip-link">Lewati ke konten utama</a>

    @yield('content')

    <!-- Toast Notification Container -->
    <div id="ft-toast" class="ft-toast" style="display: none;" role="status" aria-live="polite"></div>

    @stack('scripts')
</body>
</html>
