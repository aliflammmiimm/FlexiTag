<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($mode ?? 'create') === 'edit' ? 'Edit Produk' : 'Tambah Produk Baru' }} — {{ config('flexitag.brand') }}</title>
    
    {{-- Google Fonts - Strictly Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/flexitag-user-umkm-product.css') }}">
</head>
<body class="umkm-product-page-body">

<div class="umkm-product-canvas">

    {{-- TOPBAR HEADER --}}
    <header class="umkm-product-topbar">
        <a href="{{ route('user.profile.umkm') }}" class="btn-product-back" aria-label="Kembali ke Profil UMKM">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <h1 class="umkm-product-topbar-title">
            {{ ($mode ?? 'create') === 'edit' ? 'Edit Produk' : 'Tambah Produk Baru' }}
        </h1>
    </header>

    {{-- FORMULIR PRODUK --}}
    <form method="POST"
          action="{{ ($mode ?? 'create') === 'edit' ? route('user.profile.umkm.product.update', ['id' => $product['id']]) : route('user.profile.umkm.product.store') }}"
          enctype="multipart/form-data"
          id="productForm">
        @csrf

        <main class="umkm-product-main">

            {{-- Flash / Error Messages --}}
            @if ($errors->any())
                <div style="background: #FEF2F2; border: 1.5px solid #FECACA; color: #991B1B; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.8rem;">
                    <strong style="display:block; margin-bottom: 0.2rem;">Periksa kembali data produk:</strong>
                    <ul style="margin-left: 1.25rem; font-size: 0.76rem;">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 1. FOTO PRODUK UTAMA --}}
            <div class="prod-field-group">
                <label class="prod-field-label">
                    Foto Produk Utama <span class="prod-req-star">*</span>
                </label>

                <div class="prod-upload-card" id="uploadCard">
                    {{-- Decorative Toraja Buffalo Horn Arches --}}
                    <svg class="prod-arch-ornament-left" viewBox="0 0 32 32" fill="none">
                        <path d="M4 28 C4 14 14 4 28 4" stroke="#4A0A0E" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M8 28 C8 17 17 8 28 8" stroke="#4A0A0E" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <svg class="prod-arch-ornament-right" viewBox="0 0 32 32" fill="none">
                        <path d="M28 28 C28 14 18 4 4 4" stroke="#4A0A0E" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M24 28 C24 17 15 8 4 8" stroke="#4A0A0E" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>

                    {{-- Default Upload State --}}
                    <div id="uploadPlaceholder" style="{{ !empty($product['foto_url']) ? 'display: none;' : '' }}">
                        <div class="prod-camera-circle">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                                <line x1="19" y1="5" x2="19" y2="9"></line>
                                <line x1="17" y1="7" x2="21" y2="7"></line>
                            </svg>
                        </div>
                        <h3 class="prod-upload-heading">Unggah Foto Produk</h3>
                        <p class="prod-upload-subtext">Ukuran maksimal 5MB • Format JPG, PNG (Rasio lanskap 16:9 disarankan)</p>
                        <button type="button" class="btn-choose-gallery" onclick="document.getElementById('fotoInput').click()">
                            <span>🖼️</span>
                            <span>+ Pilih Foto dari Galeri</span>
                        </button>
                    </div>

                    {{-- Preview State (if image exists or chosen) --}}
                    <div id="uploadPreview" class="prod-preview-box" style="{{ empty($product['foto_url']) ? 'display: none;' : '' }}">
                        <img id="previewImg" src="{{ $product['foto_url'] ?? '' }}" alt="Preview Foto Produk">
                        <button type="button" class="btn-change-photo" onclick="document.getElementById('fotoInput').click()">
                            📷 Ganti Foto
                        </button>
                    </div>

                    <input type="file" id="fotoInput" name="foto" accept="image/png,image/jpeg,image/jpg,image/webp" style="display: none;" onchange="previewPhoto(this)">
                </div>
            </div>

            {{-- 2. NAMA PRODUK --}}
            <div class="prod-field-group">
                <div class="prod-field-header-row">
                    <label for="inputNama" class="prod-field-label">
                        Nama Produk <span class="prod-req-star">*</span>
                    </label>
                    <span class="prod-label-aside">Wajib diisi</span>
                </div>

                <div class="prod-input-icon-wrap">
                    <div class="prod-input-store-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <input type="text"
                           id="inputNama"
                           name="nama"
                           class="prod-input-text"
                           placeholder="Contoh: Coto Makassar Instan Premium"
                           value="{{ old('nama', $product['nama'] ?? '') }}"
                           required>
                </div>
            </div>

            {{-- 3. HARGA (RP) & SATUAN TAKARAN --}}
            <div class="prod-row-2col">
                <div class="prod-field-group">
                    <label for="inputHarga" class="prod-field-label">
                        Harga (Rp) <span class="prod-req-star">*</span>
                    </label>
                    <div class="prod-input-prefix-wrap">
                        <span class="prod-prefix-rp">Rp</span>
                        <input type="text"
                               inputmode="numeric"
                               id="inputHarga"
                               name="harga"
                               class="prod-input-price"
                               placeholder="35.000"
                               value="{{ old('harga', isset($product['harga']) ? number_format($product['harga'], 0, ',', '.') : '') }}"
                               oninput="formatCurrencyInput(this)"
                               required>
                    </div>
                </div>

                <div class="prod-field-group">
                    <label for="inputSatuan" class="prod-field-label">
                        Satuan Takaran <span class="prod-req-star">*</span>
                    </label>
                    <input type="text"
                           id="inputSatuan"
                           name="satuan"
                           class="prod-input-plain"
                           placeholder="/porsi, /pak"
                           value="{{ old('satuan', isset($product['satuan']) ? (str_starts_with($product['satuan'], '/') ? $product['satuan'] : '/' . $product['satuan']) : '') }}"
                           required>
                </div>
            </div>

            {{-- Quick Recommendations for Satuan --}}
            <div class="prod-recom-row">
                <span class="prod-recom-title">Rekomendasi:</span>
                <button type="button" class="btn-chip-recom" onclick="setSatuan('/porsi')">/porsi</button>
                <button type="button" class="btn-chip-recom" onclick="setSatuan('/pak')">/pak</button>
                <button type="button" class="btn-chip-recom" onclick="setSatuan('/kg')">/kg</button>
                <button type="button" class="btn-chip-recom" onclick="setSatuan('/toples')">/toples</button>
            </div>

            {{-- 4. DESKRIPSI PRODUK (OPSIONAL) --}}
            <div class="prod-field-group">
                <div class="prod-field-header-row">
                    <label for="inputDeskripsi" class="prod-field-label">
                        Deskripsi Produk (Opsional)
                    </label>
                    <span class="prod-label-aside" id="charCount">0/200</span>
                </div>

                <textarea id="inputDeskripsi"
                          name="deskripsi"
                          class="prod-textarea"
                          rows="3"
                          maxlength="200"
                          placeholder="Jelaskan keunggulan rasa, rempah khas Sulawesi, cara penyajian, atau ketahanan simpan..."
                          oninput="updateCharCount(this)">{{ old('deskripsi', $product['deskripsi'] ?? '') }}</textarea>
            </div>

            {{-- 5. LABEL PROMOSI --}}
            @php
                $currentLabel = old('label_promosi');
                if ($currentLabel === null && isset($product['label'])) {
                    if ($product['label'] === 'Unggulan') $currentLabel = 'unggulan';
                    elseif ($product['label'] === 'Paling Laris') $currentLabel = 'paling_laris';
                    else $currentLabel = 'tanpa_label';
                }
                $currentLabel = $currentLabel ?? 'tanpa_label';
            @endphp

            <div class="prod-field-group">
                <div class="prod-promo-header">
                    <div class="prod-promo-title-row">
                        <span>🏷️</span>
                        <span>Label Promosi</span>
                    </div>
                    <p class="prod-promo-subtext">Pilih penanda khusus untuk memikat pembeli saat tag disentuh gawai</p>
                </div>

                <div class="prod-pills-radio-wrap">
                    <button type="button"
                            class="pill-promo-btn {{ $currentLabel === 'tanpa_label' ? 'is-active' : '' }}"
                            id="pillTanpaLabel"
                            onclick="selectPromoLabel('tanpa_label')">
                        <span id="checkTanpaLabel">{{ $currentLabel === 'tanpa_label' ? '✓' : '' }}</span>
                        <span>Tanpa Label</span>
                    </button>

                    <button type="button"
                            class="pill-promo-btn {{ $currentLabel === 'unggulan' ? 'is-active' : '' }}"
                            id="pillUnggulan"
                            onclick="selectPromoLabel('unggulan')">
                        <span>⭐</span>
                        <span>Unggulan</span>
                    </button>

                    <button type="button"
                            class="pill-promo-btn {{ $currentLabel === 'paling_laris' ? 'is-active' : '' }}"
                            id="pillPalingLaris"
                            onclick="selectPromoLabel('paling_laris')">
                        <span>🔥</span>
                        <span>Paling Laris</span>
                    </button>
                </div>

                <input type="hidden" name="label_promosi" id="labelPromosiInput" value="{{ $currentLabel }}">
            </div>

            {{-- 6. SINKRONISASI OTOMATIS (INFO CARD) --}}
            <div class="prod-sync-info-card">
                <div class="prod-sync-circle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5.5 8.5C7 7 9.5 7 11 8.5"></path>
                        <path d="M3 6C6 3 11 3 14 6"></path>
                        <path d="M8 11a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"></path>
                        <path d="M12 12a5 5 0 0 1 5 5"></path>
                        <path d="M12 8a9 9 0 0 1 9 9"></path>
                    </svg>
                </div>
                <div class="prod-sync-details">
                    <div class="prod-sync-title-row">
                        <strong class="prod-sync-title">Sinkronisasi Otomatis</strong>
                        <span class="badge-nfc-ready">NFC READY</span>
                    </div>
                    <p class="prod-sync-desc">
                        Produk ini langsung tertaut dan muncul di etalase interaktif saat gantungan kunci atau kartu pintar <strong>{{ config('flexitag.brand') }}</strong> disentuh smartphone pembeli.
                    </p>
                </div>
            </div>

        </main>

        {{-- STICKY BOTTOM SAVE BAR --}}
        <footer class="prod-bottom-bar">
            <button type="submit" class="btn-save-product">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Simpan Produk</span>
            </button>
        </footer>

    </form>

</div>

<script>
    // 1. Preview Foto saat dipilih dari Galeri
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('uploadPlaceholder').style.display = 'none';
                document.getElementById('uploadPreview').style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // 2. Format Input Harga Ribuan (Contoh: "35000" -> "35.000")
    function formatCurrencyInput(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            input.value = new Intl.NumberFormat('id-ID').format(parseInt(value, 10));
        } else {
            input.value = '';
        }
    }

    // 3. Quick Chips Satuan Takaran
    function setSatuan(val) {
        document.getElementById('inputSatuan').value = val;
    }

    // 4. Character Counter untuk Deskripsi
    function updateCharCount(textarea) {
        const count = textarea.value.length;
        document.getElementById('charCount').textContent = `${count}/200`;
    }

    // Initialize character counter on page load
    document.addEventListener('DOMContentLoaded', function() {
        const desc = document.getElementById('inputDeskripsi');
        if (desc) {
            updateCharCount(desc);
        }
    });

    // 5. Radio Buttons Label Promosi
    function selectPromoLabel(choice) {
        const input = document.getElementById('labelPromosiInput');
        input.value = choice;

        const pillTanpa = document.getElementById('pillTanpaLabel');
        const pillUnggulan = document.getElementById('pillUnggulan');
        const pillLaris = document.getElementById('pillPalingLaris');
        const checkTanpa = document.getElementById('checkTanpaLabel');

        pillTanpa.classList.remove('is-active');
        pillUnggulan.classList.remove('is-active');
        pillLaris.classList.remove('is-active');
        checkTanpa.textContent = '';

        if (choice === 'tanpa_label') {
            pillTanpa.classList.add('is-active');
            checkTanpa.textContent = '✓ ';
        } else if (choice === 'unggulan') {
            pillUnggulan.classList.add('is-active');
        } else if (choice === 'paling_laris') {
            pillLaris.classList.add('is-active');
        }
    }
</script>

</body>
</html>
