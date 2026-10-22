@extends('layouts.app')

@section('title', ($profile->display_name ?? 'Profil') . ' — FlexiTag')
@section('meta_description', Str::limit($profile->headline ?? $profile->bio ?? 'Identitas digital pintar berbasis NFC.', 150))

@section('content')
<div class="ft-profile-wrapper">
    <!-- Profile Card Container -->
    <div class="ft-profile-card">
        <!-- Top Cover -->
        <div class="ft-profile-cover"></div>

        <div class="ft-profile-header">
            <!-- Avatar -->
            <div class="ft-profile-avatar-container">
                @if($profile->photo_path)
                    <img src="{{ asset('storage/' . $profile->photo_path) }}" alt="{{ $profile->display_name }}" class="ft-profile-avatar">
                @else
                    <div class="ft-profile-avatar-placeholder">
                        {{ strtoupper(substr($profile->display_name ?? 'F', 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- Active Mode Badge Indicator -->
            <div>
                @if($activeMode === 'kampus')
                    <span class="ft-mode-pill ft-mode-kampus">🎓 Mode Kampus & Akademik</span>
                @elseif($activeMode === 'kerja')
                    <span class="ft-mode-pill ft-mode-kerja">💼 Mode Portofolio Kerja</span>
                @elseif($activeMode === 'umkm')
                    <span class="ft-mode-pill ft-mode-umkm">🛍️ Mode Katalog UMKM</span>
                @elseif($activeMode === 'hilang')
                    <span class="ft-mode-pill ft-mode-hilang">🚨 Mode Barang Hilang</span>
                @endif
            </div>

            <!-- Name, Headline, Bio -->
            <h1 class="ft-profile-name">{{ $profile->display_name }}</h1>
            @if(!empty($profile->headline))
                <p class="ft-profile-headline">{{ $profile->headline }}</p>
            @endif

            @if(!empty($profile->bio))
                <p class="ft-profile-bio">{{ $profile->bio }}</p>
            @endif

            <!-- Contact Badges (Privacy Filtered) -->
            @if(($privacy->show_email ?? false) && !empty($user->email))
                <div style="margin-bottom: 0.75rem;">
                    <span style="font-size: 0.825rem; color: var(--ft-muted);">✉️ {{ $user->email }}</span>
                </div>
            @endif

            @if(($privacy->show_location ?? false) && !empty($modeData['lokasi'] ?? null))
                <div style="margin-bottom: 1rem;">
                    <span style="font-size: 0.825rem; color: var(--ft-muted);">📍 {{ $modeData['lokasi'] }}</span>
                </div>
            @endif

            <!-- MODE SPECIFIC RENDERERS (Safely Processed from JSON) -->

            <!-- 1. Mode Kampus -->
            @if($activeMode === 'kampus')
                <div class="ft-info-grid">
                    @if(!empty($modeData['kampus'] ?? null))
                        <div class="ft-info-item">
                            <span class="ft-info-label">Universitas / Institut</span>
                            <span class="ft-info-val">{{ $modeData['kampus'] }}</span>
                        </div>
                    @endif
                    @if(!empty($modeData['fakultas'] ?? null))
                        <div class="ft-info-item">
                            <span class="ft-info-label">Fakultas</span>
                            <span class="ft-info-val">{{ $modeData['fakultas'] }}</span>
                        </div>
                    @endif
                    @if(!empty($modeData['jurusan'] ?? null))
                        <div class="ft-info-item">
                            <span class="ft-info-label">Program Studi</span>
                            <span class="ft-info-val">{{ $modeData['jurusan'] }}</span>
                        </div>
                    @endif
                    @if(!empty($modeData['angkatan'] ?? null))
                        <div class="ft-info-item">
                            <span class="ft-info-label">Angkatan</span>
                            <span class="ft-info-val">{{ $modeData['angkatan'] }}</span>
                        </div>
                    @endif
                    @if(!empty($modeData['nim'] ?? null))
                        <div class="ft-info-item">
                            <span class="ft-info-label">Nomor Induk Mahasiswa</span>
                            <span class="ft-info-val">{{ $modeData['nim'] }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- 2. Mode Kerja -->
            @if($activeMode === 'kerja')
                <div class="ft-info-grid">
                    @if(!empty($modeData['perusahaan'] ?? null))
                        <div class="ft-info-item">
                            <span class="ft-info-label">Instansi / Perusahaan</span>
                            <span class="ft-info-val">{{ $modeData['perusahaan'] }}</span>
                        </div>
                    @endif
                    @if(!empty($modeData['posisi'] ?? null))
                        <div class="ft-info-item">
                            <span class="ft-info-label">Jabatan / Posisi</span>
                            <span class="ft-info-val">{{ $modeData['posisi'] }}</span>
                        </div>
                    @endif
                    @if(!empty($modeData['spesialisasi'] ?? null))
                        <div class="ft-info-item">
                            <span class="ft-info-label">Keahlian Utama</span>
                            <span class="ft-info-val">{{ $modeData['spesialisasi'] }}</span>
                        </div>
                    @endif
                    @if(!empty($modeData['pengalaman_tahun'] ?? null))
                        <div class="ft-info-item">
                            <span class="ft-info-label">Pengalaman Kerja</span>
                            <span class="ft-info-val">{{ $modeData['pengalaman_tahun'] }} Tahun</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- 3. Mode UMKM -->
            @if($activeMode === 'umkm')
                @if(!empty($modeData['nama_usaha'] ?? null) || !empty($modeData['kategori'] ?? null))
                    <div class="ft-info-grid">
                        @if(!empty($modeData['nama_usaha'] ?? null))
                            <div class="ft-info-item">
                                <span class="ft-info-label">Unit Usaha</span>
                                <span class="ft-info-val">{{ $modeData['nama_usaha'] }}</span>
                            </div>
                        @endif
                        @if(!empty($modeData['kategori'] ?? null))
                            <div class="ft-info-item">
                                <span class="ft-info-label">Bidang Produk</span>
                                <span class="ft-info-val">{{ $modeData['kategori'] }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Produk Katalog (Hingga 20 Item Sesuai SRS) -->
                @if(!empty($modeData['katalog']) && is_array($modeData['katalog']))
                    <div style="text-align: left; margin-top: 1.5rem;">
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--ft-charcoal); margin-bottom: 0.75rem;">Etalase Produk</h3>
                        <div class="ft-catalog-grid">
                            @foreach(array_slice($modeData['katalog'], 0, 20) as $item)
                                <div class="ft-product-card">
                                    @if(!empty($item['gambar']))
                                        <img src="{{ $item['gambar'] }}" alt="{{ $item['nama'] ?? 'Produk' }}" class="ft-product-img">
                                    @endif
                                    <div class="ft-product-body">
                                        <div>
                                            <h4 class="ft-product-title">{{ $item['nama'] ?? 'Item Produk' }}</h4>
                                            @if(!empty($item['deskripsi']))
                                                <p class="ft-product-desc">{{ $item['deskripsi'] }}</p>
                                            @endif
                                        </div>
                                        @if(!empty($item['harga']))
                                            <div class="ft-product-price">Rp {{ number_format((float)$item['harga'], 0, ',', '.') }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <!-- 4. Mode Hilang -->
            @if($activeMode === 'hilang')
                <div class="ft-lost-banner">
                    <div class="ft-lost-title">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span>BARANG INI SEDANG TERTINGGAL ATAU HILANG</span>
                    </div>
                    <p class="ft-lost-note">
                        {{ $modeData['catatan_darurat'] ?? 'Halo, barang yang ditempel kartu FlexiTag ini adalah milik saya dan sedang tertinggal. Mohon bantu kirimkan pesan lokasi atau kontak Anda melalui tombol di bawah agar saya dapat mengambilnya kembali. Terima kasih atas kejujuran dan kebaikan hati Anda.' }}
                    </p>
                </div>

                <!-- Alert Feedback Status Pesan -->
                @if(session('lost_success'))
                    <div class="ft-alert ft-alert-success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <div>{{ session('lost_success') }}</div>
                    </div>
                @endif

                @if($errors->has('lost_rate_limit'))
                    <div class="ft-alert ft-alert-error">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <div>{{ $errors->first('lost_rate_limit') }}</div>
                    </div>
                @endif

                @if($tagCode)
                    <button type="button" id="ft-open-lost-form-btn" class="ft-btn ft-btn-danger" style="width: 100%; justify-content: center; margin-bottom: 1rem; font-size: 1rem; padding: 0.85rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        Hubungi Pemilik Barang Ini
                    </button>
                @endif

                <div class="ft-security-notice">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span>Pesan diteruskan secara aman tanpa mengekspos kontak pemilik kepada publik.</span>
                </div>
            @endif

            <!-- Links Container (Jika Bukan Mode Hilang atau jika ada link darurat) -->
            @if($activeMode !== 'hilang' && isset($links) && count($links) > 0)
                <div class="ft-links-container" style="margin-top: 1.5rem;">
                    @foreach($links as $link)
                        <a href="{{ route('public.link.click', $link->id) }}" target="_blank" rel="noopener noreferrer" class="ft-link-btn">
                            <div class="ft-link-content">
                                @if($link->type === 'whatsapp')
                                    <span>💬</span>
                                @elseif($link->type === 'instagram')
                                    <span>📸</span>
                                @elseif($link->type === 'linkedin')
                                    <span>💼</span>
                                @elseif($link->type === 'github')
                                    <span>💻</span>
                                @else
                                    <span>🌐</span>
                                @endif
                                <span>{{ $link->label }}</span>
                            </div>
                            <span style="font-size: 1.1rem; color: var(--ft-muted);">&rarr;</span>
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Action Buttons: Share & Copy -->
            <div style="display: flex; gap: 0.75rem; margin-top: 2rem; border-top: 1px solid var(--ft-border); padding-top: 1.25rem;">
                <button type="button" id="ft-copy-link-btn" class="ft-btn ft-btn-secondary" style="flex: 1; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    Salin Profil
                </button>
                <a href="{{ route('landing') }}" class="ft-btn ft-btn-ghost" style="justify-content: center;" title="Tentang FlexiTag">
                    Tentang FlexiTag
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dialog Form Pesan Penemu (Mode Barang Hilang) -->
@if($activeMode === 'hilang' && $tagCode)
    <div id="ft-lost-modal-backdrop" class="ft-drawer-backdrop"></div>
    <div id="ft-lost-modal" class="ft-drawer" style="top: auto; right: 0; left: 0; bottom: 0; width: 100%; border-radius: 20px 20px 0 0; max-height: 90vh; overflow-y: auto;" role="dialog" aria-modal="true" aria-labelledby="lost-dialog-title">
        <div class="ft-drawer-header">
            <h3 id="lost-dialog-title" style="font-size: 1.2rem; font-weight: 700; color: #991b1b; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                Kirim Pesan ke Pemilik
            </h3>
            <button type="button" id="ft-close-lost-modal-btn" class="ft-hamburger" aria-label="Tutup form">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <form action="{{ route('public.lost-message', $tagCode) }}" method="POST" style="margin-top: 1rem;">
            @csrf

            <div class="ft-form-group">
                <label for="sender_name" class="ft-label">Nama Anda (Penemu) <span class="required">*</span></label>
                <input type="text" id="sender_name" name="sender_name" class="ft-input" placeholder="Contoh: Budi Santoso" value="{{ old('sender_name') }}" required maxlength="100">
                @error('sender_name')
                    <span class="ft-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="ft-form-group">
                <label for="location" class="ft-label">Lokasi Temuan <span class="required">*</span></label>
                <input type="text" id="location" name="location" class="ft-input" placeholder="Contoh: Meja Perpustakaan Lt. 2 / Kafe Kopi Kenangan" value="{{ old('location') }}" required maxlength="150">
                @error('location')
                    <span class="ft-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="ft-form-group">
                <label for="contact" class="ft-label">Kontak Anda (WhatsApp / No. HP) <span class="required">*</span></label>
                <input type="text" id="contact" name="contact" class="ft-input" placeholder="Contoh: 08123456789" value="{{ old('contact') }}" required maxlength="100">
                <span class="ft-help-text">Kontak Anda hanya akan dikirimkan secara privat ke akun pemilik barang.</span>
                @error('contact')
                    <span class="ft-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="ft-form-group">
                <label for="message" class="ft-label">Pesan Tambahan <span class="required">*</span></label>
                <textarea id="message" name="message" class="ft-textarea" placeholder="Deskripsikan kondisi barang atau petunjuk pengambilan..." required maxlength="1000">{{ old('message') }}</textarea>
                @error('message')
                    <span class="ft-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="ft-security-notice" style="margin-bottom: 1.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Pesan diteruskan secara aman tanpa mengekspos kontak pemilik kepada publik.</span>
            </div>

            <button type="submit" class="ft-btn ft-btn-primary" style="width: 100%; justify-content: center; padding: 0.85rem; font-size: 1rem;">
                Kirim Notifikasi ke Pemilik
            </button>
        </form>
    </div>
@endif
@endsection
