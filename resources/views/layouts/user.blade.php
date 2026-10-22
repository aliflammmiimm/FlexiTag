<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('flexitag.brand') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/flexitag-user.css') }}">
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <a href="{{ route('user.dashboard') }}" class="brand">
            <strong>{{ config('flexitag.brand') }}</strong>
            <small>{{ config('flexitag.tagline') }}</small>
        </a>

        <nav class="side-nav" aria-label="Navigasi pengguna">
            <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">@include('partials.icon', ['name' => 'home']) Dashboard</a>
            <a href="{{ route('home') }}">@include('partials.icon', ['name' => 'tag']) Beranda & Katalog</a>
        </nav>

        <div class="side-foot">
            <p style="margin:0 0 .6rem;color:#f6e1e1;">{{ auth()->user()->name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-gold btn-block">Keluar</button>
            </form>
        </div>
    </aside>

    <div>
        <header class="topbar">
            <a href="{{ route('user.dashboard') }}" class="brand"><strong>{{ config('flexitag.brand') }}</strong></a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-gold">Keluar</button>
            </form>
        </header>

        <main class="app-main">
            @if (session('status'))
                <div class="alert alert-ok" role="status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-bad" role="alert">{{ $errors->first() }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
