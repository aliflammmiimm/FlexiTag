@extends('layouts.app')

@section('title', 'FlexiTag — Satu Kartu NFC, Ragam Identitas Berbasis Warisan Budaya')
@section('meta_description', 'FlexiTag menghubungkan kartu fisik NFC bermotif warisan budaya Sulawesi dengan identitas digital dinamis: Mode Kampus, Kerja, Katalog UMKM, dan Mode Barang Hilang.')

@section('content')
<!-- Top Sticky Navigation -->
<header class="ft-navbar">
    <div class="ft-container ft-navbar-container">
        <a href="{{ route('landing') }}" class="ft-nav-brand" aria-label="Beranda FlexiTag">
            @include('components.brand-mark')
        </a>

        <nav class="ft-nav-links" aria-label="Navigasi Utama">
            <a href="#fitur" class="ft-nav-link">Manfaat</a>
            <a href="#mode" class="ft-nav-link">4 Mode Profil</a>
            <a href="#alur" class="ft-nav-link">Alur Pakai</a>
            <a href="#motif" class="ft-nav-link">Motif Budaya</a>
        </nav>

        <div class="ft-nav-actions">
            <a href="{{ route('login') }}" class="ft-btn ft-btn-ghost">Login</a>
            <a href="#mode" class="ft-btn ft-btn-primary">Jelajahi Mode</a>
            <button type="button" id="ft-hamburger-btn" class="ft-hamburger" aria-label="Buka menu navigasi" aria-expanded="false">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Navigation Drawer -->
<div id="ft-drawer-backdrop" class="ft-drawer-backdrop"></div>
<aside id="ft-drawer" class="ft-drawer" aria-label="Menu Navigasi Mobile">
    <div class="ft-drawer-header">
        @include('components.brand-mark')
        <button type="button" id="ft-drawer-close-btn" class="ft-hamburger" aria-label="Tutup menu navigasi">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="ft-drawer-nav">
        <a href="#fitur" class="ft-nav-link">Manfaat</a>
        <a href="#mode" class="ft-nav-link">4 Mode Profil</a>
        <a href="#alur" class="ft-nav-link">Alur Pakai</a>
        <a href="#motif" class="ft-nav-link">Motif Budaya</a>
        <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="{{ route('login') }}" class="ft-btn ft-btn-secondary" style="width: 100%; justify-content: center;">Portal Admin / Login</a>
            <a href="#mode" class="ft-btn ft-btn-primary" style="width: 100%; justify-content: center;">Beli Kartu FlexiTag</a>
        </div>
    </div>
</aside>

<main id="main-content">
    <!-- Hero Section -->
    <section class="ft-hero">
        <div class="ft-container">
            <div class="ft-hero-grid">
                <div>
                    <span class="ft-badge ft-badge-gold" style="margin-bottom: 1rem;">Teknologi NFC & Warisan Tradisi Sulawesi</span>
                    <h1 class="ft-hero-title">
                        Satu Tap Kartu, <br>
                        <span class="highlight">Empat Dimensi Identitas</span> Digital Anda.
                    </h1>
                    <p class="ft-hero-subtitle">
                        FlexiTag memadukan keanggunan motif etnik Sulawesi dengan kartu pintar NFC. Ganti peran identitas Anda secara instan kapan saja: dari kartu mahasiswa, profil profesional, katalog UMKM, hingga mode pelacak barang hilang.
                    </p>
                    <div class="ft-hero-actions">
                        <a href="#mode" class="ft-btn ft-btn-primary">Lihat Simulasi 4 Mode</a>
                        <a href="#alur" class="ft-btn ft-btn-secondary">Cara Kerja Kartu</a>
                    </div>
                </div>

                <!-- NFC Card Mockup Preview -->
                <div class="ft-nfc-mockup">
                    <div class="ft-nfc-card">
                        <div class="ft-nfc-chip"></div>
                        <div class="ft-nfc-waves">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                                <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                                <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                                <line x1="12" y1="20" x2="12.01" y2="20"></line>
                            </svg>
                        </div>
                        <div style="margin-top: 3.5rem;">
                            <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #D4AF37; margin-bottom: 0.25rem;">NFC Smart Identity</p>
                            <h3 style="font-size: 1.35rem; font-weight: 700; color: #FAF9F6; margin-bottom: 0.5rem; font-family: var(--ft-font-heading);">FLEXITAG PRO</h3>
                            <p style="font-size: 0.85rem; color: #A0988E;">Koleksi Motif Pa'tedong Toraja</p>
                        </div>
                        <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: flex-end; border-top: 1px solid rgba(212,175,55,0.25); padding-top: 1rem;">
                            <span style="font-size: 0.75rem; color: #C5BCB1;">MaccaTech Studio</span>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #F5E6B3;">TAP TO CONNECT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Manfaat Utama -->
    <section id="fitur" style="padding: 5rem 0; background-color: #ffffff; border-top: 1px solid var(--ft-border);">
        <div class="ft-container">
            <div style="text-align: center; max-width: 640px; margin: 0 auto 3rem;">
                <span class="ft-badge ft-badge-maroon" style="margin-bottom: 0.75rem;">Keunggulan Fleksibel</span>
                <h2 style="font-size: 2.25rem; font-weight: 800; color: var(--ft-charcoal); margin-bottom: 0.75rem;">Mengapa Memilih FlexiTag?</h2>
                <p style="color: var(--ft-muted); font-size: 1rem;">Solusi identitas modern tanpa perlu mencetak kartu berulang kali saat informasi Anda berubah.</p>
            </div>

            <div class="ft-features-grid">
                <div class="ft-feature-card">
                    <div class="ft-feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--ft-charcoal); margin-bottom: 0.5rem;">4 Mode Dinamis</h3>
                    <p style="font-size: 0.875rem; color: var(--ft-muted); line-height: 1.5;">Ganti mode dari dashboard kapan saja. Kartu fisik tetap sama, tapi halaman yang terbuka menyesuaikan kebutuhan Anda.</p>
                </div>

                <div class="ft-feature-card">
                    <div class="ft-feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--ft-charcoal); margin-bottom: 0.5rem;">Eksklusif & Bernilai Seni</h3>
                    <p style="font-size: 0.875rem; color: var(--ft-muted); line-height: 1.5;">Desain fisik mengusung ukiran Pa'tedong Toraja, Sa'pi' Mandar, dan Lontara Bugis yang penuh makna kehormatan.</p>
                </div>

                <div class="ft-feature-card">
                    <div class="ft-feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--ft-charcoal); margin-bottom: 0.5rem;">Kontrol Privasi Penuh</h3>
                    <p style="font-size: 0.875rem; color: var(--ft-muted); line-height: 1.5;">Tentukan informasi yang tampil. Nomor telepon dan email tersembunyi kecuali Anda memilih untuk menampilkannya.</p>
                </div>

                <div class="ft-feature-card">
                    <div class="ft-feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                    </div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--ft-charcoal); margin-bottom: 0.5rem;">Relay Aman Barang Hilang</h3>
                    <p style="font-size: 0.875rem; color: var(--ft-muted); line-height: 1.5;">Saat barang tertinggal, penemu dapat mengirim pesan langsung ke pemilik tanpa nomor kontak Anda terekspos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Mode Switcher Showcase -->
    <section id="mode" class="ft-modes-section">
        <div class="ft-container">
            <div style="text-align: center; max-width: 680px; margin: 0 auto 1.5rem;">
                <span class="ft-badge ft-badge-gold">Simulasi Interaktif</span>
                <h2 style="font-size: 2.25rem; font-weight: 800; color: var(--ft-charcoal); margin-top: 0.5rem;">Pilih Peran Sesuai Situasi Anda</h2>
                <p style="color: var(--ft-muted); font-size: 1rem;">Satu ketukan di kartu fisik Anda dapat menampilkan salah satu dari 4 tampilan profil berikut:</p>
            </div>

            <!-- Tab Buttons -->
            <div class="ft-mode-tabs" role="tablist" aria-label="Tab Simulasi Mode">
                <button type="button" class="ft-mode-tab is-active" data-mode="kampus" role="tab" aria-selected="true">🎓 Mode Kampus</button>
                <button type="button" class="ft-mode-tab" data-mode="kerja" role="tab" aria-selected="false">💼 Mode Kerja</button>
                <button type="button" class="ft-mode-tab" data-mode="umkm" role="tab" aria-selected="false">🛍️ Mode UMKM</button>
                <button type="button" class="ft-mode-tab" data-mode="hilang" role="tab" aria-selected="false">🚨 Mode Barang Hilang</button>
            </div>

            <!-- Tab Content Pane: Kampus -->
            <div id="ft-pane-kampus" class="ft-mode-content-pane is-active" role="tabpanel">
                <div class="ft-card ft-card-accent-maroon">
                    <div class="ft-card-body">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                            <span class="ft-badge ft-badge-maroon">Mahasiswa Aktif</span>
                            <span style="font-size: 0.8rem; color: var(--ft-muted);">NIM Terverifikasi</span>
                        </div>
                        <h4 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.25rem;">Andi M. Fikri</h4>
                        <p style="font-size: 0.9rem; color: var(--ft-maroon); font-weight: 600; margin-bottom: 1rem;">Teknik Informatika — Universitas Hasanuddin</p>
                        <div class="ft-info-grid">
                            <div class="ft-info-item">
                                <span class="ft-info-label">Angkatan</span>
                                <span class="ft-info-val">2023 / Semester 5</span>
                            </div>
                            <div class="ft-info-item">
                                <span class="ft-info-label">Fokus Riset</span>
                                <span class="ft-info-val">IoT & Smart Identity</span>
                            </div>
                        </div>
                        <div class="ft-links-container">
                            <div class="ft-link-btn" style="pointer-events: none;">
                                <div class="ft-link-content">
                                    <span>🌐 Repositori GitHub Tugas Akhir</span>
                                </div>
                                <span>&rarr;</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content Pane: Kerja -->
            <div id="ft-pane-kerja" class="ft-mode-content-pane" role="tabpanel">
                <div class="ft-card ft-card-accent-gold">
                    <div class="ft-card-body">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                            <span class="ft-badge ft-badge-gold">Profil Profesional</span>
                            <span style="font-size: 0.8rem; color: var(--ft-muted);">CV Ringkas</span>
                        </div>
                        <h4 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.25rem;">Siti Nurhaliza, S.T.</h4>
                        <p style="font-size: 0.9rem; color: var(--ft-charcoal); font-weight: 600; margin-bottom: 1rem;">Senior Embedded Systems Engineer</p>
                        <div class="ft-info-grid">
                            <div class="ft-info-item">
                                <span class="ft-info-label">Keahlian</span>
                                <span class="ft-info-val">C++, FreeRTOS, PCB Design</span>
                            </div>
                            <div class="ft-info-item">
                                <span class="ft-info-label">Status</span>
                                <span class="ft-info-val">Terbuka untuk Konsultasi</span>
                            </div>
                        </div>
                        <div class="ft-links-container">
                            <div class="ft-link-btn" style="pointer-events: none;">
                                <div class="ft-link-content">
                                    <span>💼 Unduh Portofolio LinkedIn / PDF</span>
                                </div>
                                <span>&rarr;</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content Pane: UMKM -->
            <div id="ft-pane-umkm" class="ft-mode-content-pane" role="tabpanel">
                <div class="ft-card ft-card-accent-maroon">
                    <div class="ft-card-body">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                            <span class="ft-badge ft-badge-success">Etalase UMKM</span>
                            <span style="font-size: 0.8rem; color: var(--ft-muted);">Katalog Instan</span>
                        </div>
                        <h4 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.25rem;">Kopi Arabika Toraja Sesean</h4>
                        <p style="font-size: 0.9rem; color: var(--ft-muted); margin-bottom: 1rem;">Biji kopi petik merah premium khas pegunungan Toraja Utara.</p>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                            <div style="border: 1px solid var(--ft-border); border-radius: 8px; padding: 0.75rem; background: #fff;">
                                <div style="font-size: 0.85rem; font-weight: 700;">Arabika 250gr</div>
                                <div style="font-size: 0.8rem; color: var(--ft-maroon); font-weight: 700;">Rp 75.000</div>
                            </div>
                            <div style="border: 1px solid var(--ft-border); border-radius: 8px; padding: 0.75rem; background: #fff;">
                                <div style="font-size: 0.85rem; font-weight: 700;">Drip Bag 5 Pack</div>
                                <div style="font-size: 0.8rem; color: var(--ft-maroon); font-weight: 700;">Rp 45.000</div>
                            </div>
                        </div>

                        <div class="ft-links-container">
                            <div class="ft-link-btn" style="pointer-events: none; background-color: #25D366; color: #fff; border-color: #25D366;">
                                <div class="ft-link-content">
                                    <span>💬 Hubungi Pemilik via WhatsApp</span>
                                </div>
                                <span>&rarr;</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content Pane: Hilang -->
            <div id="ft-pane-hilang" class="ft-mode-content-pane" role="tabpanel">
                <div class="ft-card" style="border-color: #fecaca;">
                    <div class="ft-card-body">
                        <div class="ft-lost-banner" style="margin-bottom: 1rem;">
                            <div class="ft-lost-title">
                                <span>⚠️ MODE BARANG HILANG AKTIF</span>
                            </div>
                            <p class="ft-lost-note">
                                Pemilik melaporkan bahwa barang ini sedang tertinggal atau hilang. Jika Anda menemukannya, silakan kirim pesan melalui form aman berikut.
                            </p>
                        </div>
                        <div style="background-color: var(--ft-cream); border: 1px dashed var(--ft-border); border-radius: 8px; padding: 1rem; text-align: center; margin-bottom: 1rem;">
                            <p style="font-size: 0.875rem; font-weight: 600; color: var(--ft-charcoal); margin-bottom: 0.5rem;">Formulir Kontak Penemu</p>
                            <span class="ft-btn ft-btn-primary" style="font-size: 0.85rem; pointer-events: none;">Kirim Pesan Lokasi Temuan</span>
                        </div>
                        <div class="ft-security-notice">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            <span>Pesan diteruskan secara aman tanpa mengekspos kontak pemilik kepada publik.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Alur Penggunaan -->
    <section id="alur" style="padding: 5rem 0; background-color: #ffffff;">
        <div class="ft-container">
            <div style="text-align: center; max-width: 640px; margin: 0 auto 3rem;">
                <span class="ft-badge ft-badge-maroon" style="margin-bottom: 0.75rem;">Sangat Mudah</span>
                <h2 style="font-size: 2.25rem; font-weight: 800; color: var(--ft-charcoal); margin-bottom: 0.75rem;">Bagaimana Alur Penggunaannya?</h2>
                <p style="color: var(--ft-muted); font-size: 1rem;">Hanya tiga langkah mudah untuk mengaktifkan dan mengelola identitas digital pintar Anda.</p>
            </div>

            <div class="ft-steps-grid">
                <div class="ft-step-item">
                    <div class="ft-step-number">1</div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--ft-charcoal); margin-bottom: 0.5rem;">Pilih & Miliki Kartu</h3>
                    <p style="font-size: 0.9rem; color: var(--ft-muted); line-height: 1.6;">Pilih kartu fisik FlexiTag dengan motif ukiran warisan Sulawesi favorit Anda yang telah dilengkapi chip NFC NTAG213.</p>
                </div>

                <div class="ft-step-item">
                    <div class="ft-step-number">2</div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--ft-charcoal); margin-bottom: 0.5rem;">Aktivasi Sekali Klik</h3>
                    <p style="font-size: 0.9rem; color: var(--ft-muted); line-height: 1.6;">Scan QR aktivasi pada kemasan, tautkan dengan akun Anda, dan lengkapi profil untuk keempat mode yang tersedia.</p>
                </div>

                <div class="ft-step-item">
                    <div class="ft-step-number">3</div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--ft-charcoal); margin-bottom: 0.5rem;">Tap & Bagikan Identitas</h3>
                    <p style="font-size: 0.9rem; color: var(--ft-muted); line-height: 1.6;">Cukup tempelkan kartu ke bagian belakang smartphone rekan bicara Anda. Profil akan langsung terbuka tanpa aplikasi tambahan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Koleksi Motif Warisan Sulawesi -->
    <section id="motif" style="padding: 5rem 0; background-color: #f7f4ef; border-top: 1px solid var(--ft-border);">
        <div class="ft-container">
            <div style="text-align: center; max-width: 680px; margin: 0 auto 2.5rem;">
                <span class="ft-badge ft-badge-gold">Koleksi Desain Budaya</span>
                <h2 style="font-size: 2.25rem; font-weight: 800; color: var(--ft-charcoal); margin-top: 0.5rem;">Karya Seni Nusantara Dalam Genggaman</h2>
                <p style="color: var(--ft-muted); font-size: 1rem;">Setiap kartu fisik FlexiTag dicetak dengan ketelitian tinggi, menghadirkan estetika ornamen tradisional khas Sulawesi Selatan dan Barat.</p>
            </div>

            <div class="ft-designs-grid">
                @if(isset($designs) && $designs->count() > 0)
                    @foreach($designs as $design)
                        <div class="ft-design-card">
                            <div class="ft-design-preview" style="background: linear-gradient(135deg, #2b1113, #150809);">
                                @if($design->image_path)
                                    <img src="{{ asset('storage/' . $design->image_path) }}" alt="{{ $design->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div class="ft-design-motif-symbol">⚜️</div>
                                @endif
                            </div>
                            <div class="ft-design-info">
                                <span class="ft-badge ft-badge-maroon" style="margin-bottom: 0.5rem;">Katalog Resmi</span>
                                <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--ft-charcoal); margin-bottom: 0.25rem;">{{ $design->name }}</h4>
                                <p style="font-size: 0.8rem; color: var(--ft-muted);">{{ Str::limit($design->description ?? 'Ornamen khas Sulawesi karya pengrajin lokal.', 85) }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback Visual Sesuai DESIGN.md Bila Tabel Desain Belum Berisi Data -->
                    @foreach($fallbackMotifs as $motif)
                        <div class="ft-design-card">
                            <div class="ft-design-preview" style="background: {{ $motif['bg_gradient'] }};">
                                <div class="ft-design-motif-symbol">{{ $motif['symbol'] }}</div>
                            </div>
                            <div class="ft-design-info">
                                <span class="ft-badge ft-badge-gold" style="margin-bottom: 0.5rem;">Khas {{ $motif['origin'] }}</span>
                                <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--ft-charcoal); margin-bottom: 0.25rem;">{{ $motif['name'] }}</h4>
                                <p style="font-size: 0.8rem; color: var(--ft-muted); line-height: 1.4;">{{ $motif['meaning'] }}</p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- Call to Action Banner -->
    <section style="padding: 5rem 0; background: linear-gradient(135deg, var(--ft-maroon-dark) 0%, var(--ft-maroon) 100%); color: #ffffff; text-align: center; border-top: 1px solid var(--ft-gold);">
        <div class="ft-container" style="max-width: 680px;">
            <h2 style="font-size: 2.25rem; font-weight: 800; color: #FAF9F6; margin-bottom: 1rem; font-family: var(--ft-font-heading);">Siap Mengupgrade Kartu Nama Anda?</h2>
            <p style="color: #E7E0D8; font-size: 1.05rem; line-height: 1.6; margin-bottom: 2rem;">Tinggalkan kartu nama kertas yang cepat usang. Beralih ke FlexiTag, simpan kontak rekan bisnis dengan satu sentuhan ringan di smartphone mereka.</p>
            <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                <a href="#mode" class="ft-btn ft-btn-gold" style="font-size: 1rem; padding: 0.85rem 2rem;">Pesan Kartu FlexiTag</a>
                <a href="{{ route('login') }}" class="ft-btn ft-btn-ghost" style="color: #FAF9F6; border-color: rgba(255,255,255,0.4);">Masuk ke Dashboard</a>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="ft-footer">
    <div class="ft-container">
        <div class="ft-footer-grid">
            <div>
                <div style="margin-bottom: 1rem;">
                    @include('components.brand-mark')
                </div>
                <p style="color: #a0a0a0; font-size: 0.875rem; line-height: 1.6; max-width: 320px;">
                    FlexiTag adalah platform identitas pintar berbasis NFC dengan sentuhan seni warisan Sulawesi yang dikembangkan oleh MaccaTech Studio.
                </p>
            </div>
            <div>
                <h4 style="color: var(--ft-gold-light); font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem;">Fitur Unggulan</h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem;">
                    <li><a href="#mode">Mode Kampus</a></li>
                    <li><a href="#mode">Mode Profesional</a></li>
                    <li><a href="#mode">Katalog UMKM</a></li>
                    <li><a href="#mode">Relay Barang Hilang</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: var(--ft-gold-light); font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem;">Akses & Bantuan</h4>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem;">
                    <li><a href="{{ route('login') }}">Masuk Akun Pengguna</a></li>
                    <li><a href="{{ route('admin.login') }}">Console Administrasi</a></li>
                    <li><a href="#alur">Panduan Aktivasi</a></li>
                </ul>
            </div>
        </div>
        <div class="ft-footer-bottom">
            <span>&copy; {{ date('Y') }} MaccaTech FlexiTag. Seluruh hak cipta dilindungi undang-undang.</span>
            <span>Estetika Warisan Sulawesi & Teknologi Digital Terpadu.</span>
        </div>
    </div>
</footer>
@endsection
