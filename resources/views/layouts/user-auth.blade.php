<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('flexitag.brand') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/flexitag-user.css') }}">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="orn"></div>
        <a href="{{ route('home') }}" class="brand">
            <strong>{{ config('flexitag.brand') }}</strong>
            <small>{{ config('flexitag.tagline') }}</small>
        </a>

        @if (session('status'))
            <div class="alert alert-ok" role="status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </div>
</div>
</body>
</html>
