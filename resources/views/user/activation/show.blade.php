@extends('layouts.user')

@section('title', 'Aktivasi Tag')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/flexitag-activation.css') }}">
@endpush

@section('content')
    @php
        $activated = session('activated');            // diisi setelah aktivasi berhasil
        $fail = session('activation_state');          // ['state' => wrong|used|own|disabled|throttled]
        $state = $activated ? 'success' : ($fail['state'] ?? ($errors->has('activation_code') ? 'format' : 'idle'));

        $messages = [
            'wrong' => ['Kode aktivasi tidak ditemukan.', 'Periksa kembali kode pada kartu panduan, lalu coba lagi.'],
            'used' => ['Kode ini sudah digunakan.', 'Tag dengan kode tersebut sudah terhubung ke akun lain. Hubungi admin bila Anda merasa ini keliru.'],
            'own' => ['Tag ini sudah aktif di akun Anda.', 'Anda tidak perlu mengaktifkannya lagi. Lihat tag Anda di dashboard.'],
            'disabled' => ['Tag ini sedang dinonaktifkan.', 'Tag nonaktif tidak dapat diaktifkan lewat alur biasa. Hubungi admin untuk bantuan.'],
            'throttled' => ['Terlalu banyak percobaan.', 'Coba lagi dalam '.($fail['minutes'] ?? 10).' menit.'],
        ];

        // Langkah: 1 Akun, 2 Kode Aktivasi, 3 Profil, 4 Selesai
        $current = $state === 'success' ? 3 : 2;
        $steps = [1 => 'Akun', 2 => 'Kode Aktivasi', 3 => 'Profil', 4 => 'Selesai'];

        $contact = config('flexitag.contact');
        $help = ! empty($contact['whatsapp'])
            ? 'https://wa.me/'.preg_replace('/\D+/', '', $contact['whatsapp'])
            : (! empty($contact['email']) ? 'mailto:'.$contact['email'] : null);

        $photo = file_exists(public_path('images/keychain.jpg')) ? asset('images/keychain.jpg') : null;
    @endphp

    <a href="{{ route('user.dashboard') }}" class="back-link">← Kembali ke Dashboard</a>

    <div class="act-head">
        <h1>Aktivasi Tag NFC</h1>
        <span class="status-chip">
            <i></i><i></i>
            Status Tag: {{ $activeTagCount > 0 ? $activeTagCount.' tag aktif' : 'Belum Diaktivasi' }}
        </span>
    </div>

    <ol class="stepper" aria-label="Langkah aktivasi">
        @foreach ($steps as $n => $label)
            <li class="{{ $n < $current ? 'done' : ($n === $current ? 'now' : '') }}" @if ($n === $current) aria-current="step" @endif>
                <span class="dot">{{ $n < $current ? '✓' : $n }}</span>
                <span class="lbl">{{ $label }}</span>
            </li>
        @endforeach
    </ol>

    <div class="act-grid">
        {{-- Kolom kiri: produk & perlindungan --}}
        <div class="act-side">
            <div class="chip-line">@include('partials.icon', ['name' => 'zap']) Chip NFC NTAG213 • Kode aktivasi sekali pakai</div>

            <div class="card prod">
                <div class="prod-photo" role="img" aria-label="Gantungan kunci akrilik NFC custom">
                    @if ($photo)
                        <img src="{{ $photo }}" alt="Gantungan kunci akrilik NFC custom">
                    @else
                        <svg viewBox="0 0 240 200" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                            <defs>
                                <radialGradient id="aBg" cx="50%" cy="40%" r="80%"><stop offset="0" stop-color="#e6ded2"/><stop offset="1" stop-color="#c9bfb0"/></radialGradient>
                                <radialGradient id="aDisc" cx="50%" cy="45%" r="55%"><stop offset="0" stop-color="#b8782a"/><stop offset="1" stop-color="#6e3a10"/></radialGradient>
                            </defs>
                            <rect width="240" height="200" fill="url(#aBg)"/>
                            <circle cx="120" cy="30" r="14" fill="none" stroke="#9a9a9a" stroke-width="4"/>
                            <circle cx="120" cy="112" r="68" fill="rgba(255,255,255,.55)" stroke="#fff" stroke-width="3"/>
                            <circle cx="120" cy="112" r="56" fill="url(#aDisc)"/>
                            <circle cx="120" cy="112" r="40" fill="none" stroke="#e0b04a" stroke-width="2" stroke-dasharray="3 5"/>
                            <path d="M120 82 L142 112 L120 142 L98 112 Z" fill="none" stroke="#e0b04a" stroke-width="2.5"/>
                            <circle cx="120" cy="112" r="7" fill="#e0b04a"/>
                        </svg>
                    @endif
                </div>
                <div class="prod-cap">
                    <b>Gantungan Kunci Akrilik Custom</b>
                    <small>Tag NFC FlexiTag</small>
                </div>
            </div>

        </div>

        {{-- Kolom kanan: form / hasil --}}
        <div class="act-main">
            @if ($state === 'success')
                <div class="card act-card success" role="status">
                    <div class="ok-mark">✓</div>
                    <h2>Tag berhasil diaktifkan!</h2>
                    <p class="txt">Gantungan Anda kini terhubung ke akun <b>{{ auth()->user()->name }}</b>.</p>
                    <dl class="kv">
                        <div><dt>Kode tag</dt><dd><code>{{ $activated['tag_code'] }}</code></dd></div>
                        <div><dt>Status</dt><dd><span class="badge badge-ok">Aktif</span></dd></div>
                        <div><dt>Diaktifkan</dt><dd>{{ $activated['activated_at'] }}</dd></div>
                    </dl>
                    <div class="act-actions">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-gold btn-lg">Ke Dashboard</a>
                        @if (Route::has('user.profile'))
                            <a href="{{ route('user.profile') }}" class="btn btn-outline btn-lg">Atur Profil Tag</a>
                        @endif
                    </div>
                    <p class="hint" style="margin-top:1rem">Punya tag lain? <a href="{{ route('user.activation.show') }}">Aktifkan tag berikutnya</a></p>
                </div>
            @else
                <div class="card act-card">
                    <h2>Masukkan Kode Aktivasi</h2>
                    <div class="gold-bar"></div>
                    <p class="txt">Masukkan kode aktivasi sekali pakai yang tertera pada kartu panduan di dalam kemasan gantungan kunci Anda.</p>

                    @if (isset($messages[$state]))
                        <div class="alert {{ $state === 'own' ? 'alert-ok' : 'alert-bad' }}" role="alert">
                            <b>{{ $messages[$state][0] }}</b><br>{{ $messages[$state][1] }}
                        </div>
                    @endif
                    {{-- Error format ditampilkan oleh layout (alert di atas halaman). --}}

                    <form method="POST" action="{{ route('user.activation.store') }}" id="activationForm" novalidate>
                        @csrf
                        <div class="code-label">
                            <label for="activation_code">Kode Aktivasi</label>
                            <span class="once">SEKALI PAKAI</span>
                        </div>
                        <input type="text" id="activation_code" name="activation_code" class="code-input"
                               placeholder="CONTOH: KD-0000-XX" maxlength="40" autocomplete="off"
                               autocapitalize="characters" spellcheck="false" required autofocus
                               aria-describedby="codeHelp">
                        <div class="code-help" id="codeHelp">
                            <span>Format kode: KD-0000-XX</span>
                            <a href="#panduan" id="openGuide">Lihat kartu panduan</a>
                        </div>

                        <button type="submit" class="btn btn-gold btn-block btn-lg act-submit" id="submitBtn">
                            <span class="label">Aktifkan Gantungan Sekarang →</span>
                            <span class="busy" hidden><i class="spin"></i> Memproses…</span>
                        </button>
                    </form>

                    <ul class="notes">
                        <li>✓ Kode hanya berlaku 1x &amp; terikat pada ID tag ini</li>
                        <li>• Percobaan salah berulang akan dibatasi sementara</li>
                    </ul>
                </div>

                <details class="card guide" id="panduan">
                    <summary>Di mana menemukan kode aktivasi?</summary>
                    <ol>
                        <li>Buka kemasan gantungan kunci FlexiTag Anda.</li>
                        <li>Cari <b>kartu panduan</b> di dalam kemasan.</li>
                        <li>Salin kode yang tertera pada bagian <b>Panduan Aktivasi</b> (contoh: <code>KD-0000-XX</code>).</li>
                        <li>Masukkan kode tersebut pada kolom di atas.</li>
                    </ol>
                    <p class="hint">Jangan membagikan kode ini kepada siapa pun. Kode hanya dapat dipakai satu kali.</p>
                </details>
            @endif

            <div class="protect">
                <span class="ico ico-maroon">@include('partials.icon', ['name' => 'shield'])</span>
                <div>
                    <b>Kepemilikan Terlindungi</b>
                    <small>Mencegah orang lain mengklaim profil gantungan Anda.</small>
                </div>
                @if ($help)
                    <a href="{{ $help }}" target="_blank" rel="noopener">Bantuan</a>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('activationForm');
            if (!form) return;
            var input = document.getElementById('activation_code');
            var btn = document.getElementById('submitBtn');

            // Rapikan input: huruf besar, hanya huruf/angka/tanda hubung.
            input.addEventListener('input', function () {
                var pos = input.selectionStart;
                input.value = input.value.toUpperCase().replace(/[^A-Z0-9-]/g, '');
                input.setSelectionRange(pos, pos);
            });

            // Status "memproses": cegah klik ganda.
            form.addEventListener('submit', function (e) {
                if (!input.value.trim()) { return; }       // biarkan server menampilkan pesan wajib diisi
                if (btn.disabled) { e.preventDefault(); return; }
                btn.disabled = true;
                btn.setAttribute('aria-busy', 'true');
                btn.querySelector('.label').hidden = true;
                btn.querySelector('.busy').hidden = false;
            });
            window.addEventListener('pageshow', function () {   // tombol Back browser
                btn.disabled = false; btn.removeAttribute('aria-busy');
                btn.querySelector('.label').hidden = false;
                btn.querySelector('.busy').hidden = true;
            });

            document.getElementById('openGuide').addEventListener('click', function (e) {
                e.preventDefault();
                var g = document.getElementById('panduan');
                g.open = true; g.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        })();
    </script>
@endpush
