@extends('layouts.user-auth')

@section('title', 'Daftar')

@section('content')
    <h1>Buat akun baru</h1>

    <form method="POST" action="{{ route('register.submit') }}" novalidate>
        @csrf

        <div class="field">
            <label for="name">Nama lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="100" required autofocus>
            @error('name') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="150" required>
            @error('email') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password">Kata sandi</label>
            <input type="password" id="password" name="password" autocomplete="new-password" required>
            <div class="hint">Minimal 8 karakter, mengandung huruf dan angka.</div>
            @error('password') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Ulangi kata sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
        </div>

        <button type="submit" class="btn btn-maroon btn-block">Daftar</button>
    </form>

    <p class="auth-alt">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection
