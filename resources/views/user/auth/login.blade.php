@extends('layouts.user-auth')

@section('title', 'Masuk ke Dashboard')

@section('content')
    <h1>Masuk ke Dashboard Anda</h1>
    <p style="text-align: center; font-size: 0.8rem; color: var(--muted); margin-top: -0.5rem; margin-bottom: 1.25rem; line-height: 1.45;">
        Sudah pernah aktivasi gantungan kunci FlexiTag? Masuk untuk mengelola mode aktif dan melihat statistik tap.
    </p>

    <form method="POST" action="{{ route('login.submit') }}" novalidate>
        @csrf

        <div class="field">
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com" required autofocus>
            @error('email') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password">Kata Sandi</label>
            <input type="password" id="password" name="password" autocomplete="current-password" placeholder="••••••••" required>
            @error('password') <div class="err">{{ $message }}</div> @enderror
        </div>

        <label class="check">
            <input type="checkbox" name="remember" value="1"> Ingat saya di perangkat ini
        </label>

        <button type="submit" class="btn btn-gold btn-block" style="font-weight: 700; padding: 0.75rem 1.2rem; font-size: 0.95rem;">
            Masuk ke Dashboard →
        </button>
    </form>

    <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--line); text-align: center; font-size: 0.82rem; color: var(--muted); display: flex; flex-direction: column; gap: 0.45rem;">
        <p style="margin: 0;">Belum aktivasi tag baru? <a href="{{ route('user.activation.show') }}" style="font-weight: 700; color: var(--maroon);">Aktivasi Tag Sekarang</a></p>
        <p style="margin: 0;">Belum punya akun? <a href="{{ route('register') }}" style="font-weight: 600;">Daftar Akun</a></p>
    </div>
@endsection
