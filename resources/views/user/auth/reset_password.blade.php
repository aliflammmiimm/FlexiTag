@extends('layouts.user-auth')

@section('title', 'Verifikasi Token & Reset Kata Sandi')

@section('content')
    <h1>Atur Ulang Kata Sandi</h1>
    <p style="text-align: center; font-size: 0.8rem; color: var(--muted); margin-top: -0.5rem; margin-bottom: 1.25rem; line-height: 1.45;">
        Masukkan <strong>kode token 6 digit</strong> yang kami kirimkan ke email Anda, lalu buat kata sandi baru untuk akun FlexiTag Anda.
    </p>

    @if (session('debug_otp'))
        <div style="background: #FEF3C7; border: 1.5px solid #F59E0B; border-radius: 12px; padding: 12px 14px; margin-bottom: 1.25rem; font-size: 0.82rem; color: #92400E; text-align: left; line-height: 1.5; box-shadow: 0 2px 8px rgba(245,158,11,0.12);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                <strong style="color: #78350F; font-size: 0.85rem;">🔔 Mode Lokal (Pengujian)</strong>
                <span style="background: #F59E0B; color: #fff; padding: 2px 8px; border-radius: 999px; font-size: 0.68rem; font-weight: 700;">DEV / LOCAL</span>
            </div>
            Server lokal saat ini masih menggunakan setelan <code>MAIL_MAILER=log</code> (belum terkoneksi SMTP internet).
            <div style="margin-top: 8px; padding: 10px 12px; background: #FFFBEB; border-radius: 8px; border: 1px dashed #F59E0B; text-align: center;">
                <div style="font-size: 0.75rem; color: #92400E; margin-bottom: 2px;">Kode Token 6 Digit Anda:</div>
                <strong style="font-size: 1.4rem; font-family: 'Courier New', Courier, monospace; color: #B45309; letter-spacing: 6px;">{{ session('debug_otp') }}</strong>
                <div style="font-size: 0.72rem; color: #B45309; margin-top: 4px;">(Salin atau ketikkan kode di atas pada kolom di bawah)</div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" novalidate>
        @csrf

        <div class="field">
            <label for="email">Alamat Email Terdaftar</label>
            <input type="email" id="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" placeholder="nama@email.com" required>
            @error('email') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="token">
                Kode Token 6 Digit Angka
                <span style="font-weight: 400; font-size: 0.72rem; color: var(--muted); margin-left: 4px;">(Cek kotak masuk atau spam)</span>
            </label>
            <input 
                type="text" 
                id="token" 
                name="token" 
                value="{{ old('token', $token) }}" 
                maxlength="6" 
                inputmode="numeric" 
                pattern="[0-9]{6}" 
                autocomplete="one-time-code" 
                placeholder="123456" 
                style="text-align: center; font-size: 1.35rem; font-weight: 700; letter-spacing: 0.35em; font-family: 'Courier New', Courier, monospace; background: #FAF8F5; border: 1.5px dashed var(--gold);" 
                required 
                autofocus
            >
            @error('token') <div class="err">{{ $message }}</div> @enderror
            <div class="hint" style="text-align: center; font-size: 0.72rem; color: var(--muted); margin-top: 0.3rem;">
                Kode token berlaku selama 60 menit sejak email dikirimkan.
            </div>
        </div>

        <div class="field">
            <label for="password">Kata Sandi Baru</label>
            <input type="password" id="password" name="password" autocomplete="new-password" placeholder="Minimal 8 karakter (kombinasi huruf &amp; angka)" required>
            @error('password') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" placeholder="Ketik ulang kata sandi baru" required>
        </div>

        <button type="submit" class="btn btn-gold btn-block" style="font-weight: 700; padding: 0.75rem 1.2rem; font-size: 0.95rem; margin-top: 0.5rem;">
            Verifikasi &amp; Simpan Kata Sandi →
        </button>
    </form>

    <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--line); text-align: center; font-size: 0.82rem; color: var(--muted); display: flex; flex-direction: column; gap: 0.45rem;">
        <p style="margin: 0;">Belum menerima kode token? <a href="{{ route('password.request') }}" style="font-weight: 700; color: var(--maroon);">Kirim Ulang Kode</a></p>
        <p style="margin: 0;"><a href="{{ route('login') }}" style="font-weight: 600; color: var(--muted);">Batal &amp; Kembali ke Halaman Masuk</a></p>
    </div>
@endsection
