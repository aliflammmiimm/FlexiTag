@extends('layouts.user-auth')

@section('title', 'Lupa Kata Sandi')

@section('content')
    <h1>Lupa Kata Sandi</h1>
    <p style="text-align: center; font-size: 0.8rem; color: var(--muted); margin-top: -0.5rem; margin-bottom: 1.25rem; line-height: 1.45;">
        Masukkan alamat email yang terdaftar pada akun FlexiTag Anda. Kami akan mengirimkan <strong>kode verifikasi 6 digit</strong> ke email Anda untuk mengatur ulang kata sandi.
    </p>

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="field">
            <label for="email">Alamat Email Terdaftar</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com" required autofocus>
            @error('email') <div class="err">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-gold btn-block" style="font-weight: 700; padding: 0.75rem 1.2rem; font-size: 0.95rem; margin-top: 0.5rem;">
            Kirim Kode Verifikasi →
        </button>
    </form>

    <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--line); text-align: center; font-size: 0.82rem; color: var(--muted); display: flex; flex-direction: column; gap: 0.45rem;">
        <p style="margin: 0;">Sudah punya kode 6 digit? <a href="{{ route('password.reset') }}" style="font-weight: 700; color: var(--maroon);">Input Token di Sini</a></p>
        <p style="margin: 0;">Ingat kata sandi Anda? <a href="{{ route('login') }}" style="font-weight: 600; color: var(--maroon);">Kembali ke Halaman Masuk</a></p>
    </div>
@endsection
