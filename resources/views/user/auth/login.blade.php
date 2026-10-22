@extends('layouts.user-auth')

@section('title', 'Masuk')

@section('content')
    <h1>Masuk ke akun Anda</h1>

    <form method="POST" action="{{ route('login.submit') }}" novalidate>
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            @error('email') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password">Kata sandi</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>
            @error('password') <div class="err">{{ $message }}</div> @enderror
        </div>

        <label class="check">
            <input type="checkbox" name="remember" value="1"> Ingat saya di perangkat ini
        </label>

        <button type="submit" class="btn btn-maroon btn-block">Masuk</button>
    </form>

    <p class="auth-alt">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
@endsection
