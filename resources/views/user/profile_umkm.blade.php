<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Profil Mode UMKM — FlexiTag</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/flexitag-user-umkm.css') }}">
</head>
<body class="umkm-edit-body">

<div class="umkm-app-container">

    {{-- ========================================================================= --}}
    {{-- TOP APP BAR                                                               --}}
    {{-- ========================================================================= --}}
    <header class="umkm-topbar">
        <a href="{{ route('user.dashboard') }}" class="topbar-back-btn" aria-label="Kembali ke Dashboard">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>

        <div class="topbar-center-heading">
            <h1 class="topbar-center-title">Edit Profil</h1>
            <span class="topbar-center-sub">• KATALOG KULINER • {{ $tagCode }}</span>
        </div>

        <span class="topbar-mode-badge">
            <span>🏪</span>
            <span>UMKM</span>
        </span>
    </header>

    {{-- ========================================================================= --}}
    {{-- FORM UTAMA                                                                --}}
    {{-- ========================================================================= --}}
    <form method="POST" action="{{ route('user.profile.umkm.update') }}" enctype="multipart/form-data" id="umkmMainForm">
        @csrf
        <input type="hidden" name="katalog_json" id="katalogJsonInput" value="{{ json_encode($umkm['katalog_produk'] ?? []) }}">
        <input type="file" name="logo" id="logoFileInput" accept="image/*" style="display:none;" onchange="handleLogoChange(this)">

        <main class="umkm-main-content">

            {{-- Flash status notification --}}
            @if (session('status'))
                <div class="status-toast-bar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="status-toast-bar" style="background:#FEE2E2; border-color:#FECACA; color:#991B1B;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- 1. UBAH LOGO USAHA / TOKO --}}
            <section class="logo-upload-card">
                <div class="logo-avatar-wrap" onclick="document.getElementById('logoFileInput').click()" title="Klik untuk mengganti logo usaha">
                    @if (!empty($umkm['logo_url']))
                        <img src="{{ $umkm['logo_url'] }}" alt="Logo Usaha" id="logoPreviewImg" class="logo-avatar-img">
                    @else
                        <div class="logo-avatar-placeholder" id="logoPlaceholderBox">
                            <span>🏪</span>
                        </div>
                        <img src="" alt="Logo Usaha" id="logoPreviewImg" class="logo-avatar-img" style="display:none;">
                    @endif
                    <button type="button" class="logo-camera-btn" aria-label="Unggah Logo">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </button>
                </div>
                <h2 class="logo-card-title">Ubah Logo Usaha / Toko</h2>
                <p class="logo-card-sub">Maksimal 5 MB • Format JPG atau PNG <strong>(Rasio 1:1)</strong></p>
            </section>

            {{-- 2. SINKRONISASI TAG UMKM NOTICE --}}
            <section class="sync-callout-card">
                <div class="sync-icon-box">
                    <span>🗃️</span>
                </div>
                <div class="sync-text-wrap">
                    <strong class="sync-title">Sinkronisasi Tag UMKM</strong>
                    <p class="sync-desc">
                        Perubahan langsung terhubung ke profil publik saat kartu tap fisik atau gantungan kunci NFC disentuh ke ponsel pembeli.
                    </p>
                </div>
            </section>

            {{-- 3. INFORMASI POKOK --}}
            <div class="section-header-row">
                <h2 class="section-serif-title">Informasi Pokok</h2>
                <span class="section-gold-label">IDENTITAS PUBLIK</span>
            </div>

            <section class="umkm-form-card">
                {{-- Field 1: Nama Usaha / Toko --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="nama_usaha" class="field-label">
                            Nama Usaha / Toko <span class="field-label-req">*</span>
                        </label>
                        <span class="field-badge-public">Publik</span>
                    </div>
                    <div class="input-with-end-icon">
                        <input type="text"
                               id="nama_usaha"
                               name="nama_usaha"
                               class="umkm-input"
                               value="{{ old('nama_usaha', $umkm['nama_usaha']) }}"
                               placeholder="Contoh: Bandeng Tanpa Duri Pangkep 'Sari Laut'"
                               required>
                        <div class="input-end-icon" title="Akun Resmi Terverifikasi">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#E5B32A" stroke="#4A0A0E" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2l3.09 3.26 4.45.65 1.34 4.3 3.12 3.23-1.34 4.3.65 4.45-4.3 1.34-3.26 3.09-3.26-3.09-4.3-1.34.65-4.45-1.34-4.3 3.12-3.23 1.34-4.3 4.45-.65L12 2z"></path>
                                <polyline points="9 12 11 14 15 10" fill="none" stroke="#FFFFFF" stroke-width="2.5"></polyline>
                            </svg>
                        </div>
                    </div>
                    <span class="field-subtext">Nama resmi usaha yang akan muncul paling atas di katalog tap.</span>
                </div>

                {{-- Field 2: Deskripsi Singkat Usaha --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="deskripsi" class="field-label">
                            Deskripsi Singkat Usaha <span class="field-label-req">*</span>
                        </label>
                        <span class="field-char-counter">
                            <span id="descCount">{{ mb_strlen($umkm['deskripsi'] ?? '') }}</span>/120
                        </span>
                    </div>
                    <textarea id="deskripsi"
                              name="deskripsi"
                              class="umkm-textarea"
                              rows="3"
                              maxlength="120"
                              oninput="updateCharCount(this)"
                              placeholder="Contoh: Sedia Bandeng Cabut Duri Segar, Otak-Otak Bakar &amp; Abon Bandeng Premium khas Pangkep."
                              required>{{ old('deskripsi', $umkm['deskripsi']) }}</textarea>
                    <span class="field-subtext">Ringkasan produk unggulan atau keistimewaan rasa kuliner Anda.</span>
                </div>

                {{-- Field 3: Alamat Toko & Cabang --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="alamat_toko" class="field-label">
                            Alamat Toko &amp; Cabang <span class="field-label-req">*</span>
                        </label>
                        <a href="https://maps.google.com" target="_blank" rel="noopener" class="field-link-gps">
                            <span>📍 Rute GPS</span>
                        </a>
                    </div>
                    <div class="input-with-end-icon input-with-store-icon">
                        <span class="input-store-prefix">🏪</span>
                        <input type="text"
                               id="alamat_toko"
                               name="alamat_toko"
                               class="umkm-input"
                               value="{{ old('alamat_toko', $umkm['alamat_toko']) }}"
                               placeholder="Contoh: Jl. Poros Makassar – Pangkep KM 42 / Cabang Pusat Sentra Ikan Segar"
                               required>
                    </div>
                    <span class="field-subtext">Penerima tap dapat langsung membuka navigasi rute ke lokasi ini.</span>
                </div>

                {{-- Field 4: Nomor WhatsApp Pemesanan --}}
                <div class="form-field-group">
                    <div class="field-label-row">
                        <label for="whatsapp_order" class="field-label">
                            Nomor WhatsApp Pemesanan <span class="field-label-req">*</span>
                        </label>
                        <span class="field-badge-fastorder">Fast Order</span>
                    </div>
                    <div class="wa-input-group">
                        <div class="wa-prefix-box">
                            <span>🇮🇩</span>
                            <span>+62</span>
                        </div>
                        <input type="tel"
                               id="whatsapp_order"
                               name="whatsapp_order"
                               class="wa-number-input"
                               value="{{ old('whatsapp_order', $umkm['whatsapp_order']) }}"
                               placeholder="81242981023"
                               required>
                        <div class="wa-end-chat-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <span class="field-subtext">Nomor WhatsApp aktif untuk menerima pesanan langsung dari pelanggan.</span>
                </div>
            </section>

            {{-- 4. KATALOG PRODUK & MENU --}}
            <div class="section-header-row">
                <h2 class="section-serif-title">Katalog Produk Menu</h2>
                <span class="section-pill-badge" id="productCountBadge">{{ count($umkm['katalog_produk'] ?? []) }} Produk Aktif</span>
            </div>

            {{-- Button Tambah Produk Baru --}}
            <a href="{{ route('user.profile.umkm.product.create') }}" class="btn-add-product-dashed" style="text-decoration:none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <span>+ Tambah Produk Baru</span>
            </a>

            {{-- List Produk --}}
            <div class="product-list-wrap" id="productListContainer">
                {{-- Dynamically rendered via JavaScript below --}}
            </div>

        </main>

        {{-- ========================================================================= --}}
        {{-- STICKY BOTTOM SAVE BAR                                                    --}}
        {{-- ========================================================================= --}}
        <footer class="umkm-bottom-bar">
            <button type="submit" class="btn-save-umkm">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Simpan Profil UMKM</span>
            </button>
            <div class="bottom-sync-subtext">
                <span>⚙️</span>
                <span>Tersedia instan untuk pembeli via NFC Android &amp; iPhone</span>
            </div>
        </footer>
    </form>

</div>

{{-- ========================================================================= --}}
{{-- MODAL TAMBAH / EDIT PRODUK                                                --}}
{{-- ========================================================================= --}}
<div class="umkm-modal-overlay" id="productModal" onclick="if(event.target === this) closeProductModal()">
    <div class="umkm-modal-dialog">
        <div class="modal-header-row">
            <h3 class="modal-title" id="productModalTitle">🍲 Tambah Produk Baru</h3>
            <button type="button" class="modal-close-icon" onclick="closeProductModal()">✕</button>
        </div>

        <div class="modal-form-grid">
            <input type="hidden" id="modalProdId">

            <div class="form-field-group">
                <label for="modalProdNama" class="field-label">Nama Produk / Menu *</label>
                <input type="text" id="modalProdNama" class="umkm-input" placeholder="contoh: Bandeng Bakar Rica">
            </div>

            <div class="modal-row-2col">
                <div class="form-field-group">
                    <label for="modalProdHarga" class="field-label">Harga (Rp) *</label>
                    <input type="number" id="modalProdHarga" class="umkm-input" placeholder="35000">
                </div>
                <div class="form-field-group">
                    <label for="modalProdSatuan" class="field-label">Satuan</label>
                    <input type="text" id="modalProdSatuan" class="umkm-input" placeholder="porsi / pak / 150g">
                </div>
            </div>

            <div class="form-field-group">
                <label for="modalProdLabel" class="field-label">Label Promosi</label>
                <select id="modalProdLabel" class="umkm-input umkm-select">
                    <option value="">(Tanpa Label)</option>
                    <option value="Unggulan|⭐|badge-gold">⭐ Unggulan</option>
                    <option value="Paling Laris|🔥|badge-red">🔥 Paling Laris</option>
                    <option value="Tahan 3 Bulan|⏱️|badge-gray">⏱️ Tahan 3 Bulan</option>
                    <option value="Menu Baru|✨|badge-gold">✨ Menu Baru</option>
                </select>
            </div>

            <div class="form-field-group">
                <label for="modalProdFoto" class="field-label">URL Foto Produk</label>
                <input type="text" id="modalProdFoto" class="umkm-input" placeholder="{{ asset('images/umkm/bandeng_tanpa_duri.jpg') }}">
            </div>
        </div>

        <div class="modal-footer-btns">
            <button type="button" class="btn-modal-cancel" onclick="closeProductModal()">Batal</button>
            <button type="button" class="btn-modal-submit" onclick="saveProductFromModal()">Simpan Produk</button>
        </div>
    </div>
</div>

<script>
    // State management for Products
    let products = {!! json_encode($umkm['katalog_produk'] ?? []) !!};

    function renderProducts() {
        const container = document.getElementById('productListContainer');
        const badge = document.getElementById('productCountBadge');
        const jsonInput = document.getElementById('katalogJsonInput');

        if (!container) return;

        container.innerHTML = '';
        badge.textContent = `${products.length} Produk Aktif`;
        jsonInput.value = JSON.stringify(products);

        if (products.length === 0) {
            container.innerHTML = `
                <div style="background:#FFFFFF; border:1.5px dashed #D6CEBE; border-radius:14px; padding:1.75rem 1rem; text-align:center; color:#78716C; font-size:0.82rem; display:flex; flex-direction:column; align-items:center; gap:0.45rem;">
                    <span style="font-size:1.8rem;">📦</span>
                    <strong style="color:#4A0A0E; font-size:0.9rem;">Belum ada produk aktif</strong>
                    <span>Klik tombol <b>+ Tambah Produk Baru</b> di atas untuk mulai memasukkan menu kuliner atau produk usaha Anda.</span>
                </div>
            `;
            return;
        }

        products.forEach((prod, index) => {
            const formattedPrice = new Intl.NumberFormat('id-ID').format(prod.harga || 0);
            const badgeHtml = prod.label ? `
                <span class="product-badge-pill ${prod.badge_class || 'badge-gold'}">
                    <span>${prod.badge_icon || '⭐'}</span>
                    <span>${prod.label}</span>
                </span>
            ` : '';

            const fallbackImg = "{{ asset('images/umkm/bandeng_tanpa_duri.jpg') }}";
            const imgSrc = prod.foto_url || fallbackImg;

            const card = document.createElement('div');
            card.className = 'product-item-card';
            card.innerHTML = `
                <div class="product-card-left">
                    <img src="${imgSrc}" alt="${prod.nama}" class="product-thumb-img" onerror="this.src='${fallbackImg}'">
                    <div class="product-details">
                        <span class="product-title-row" title="${prod.nama}">${prod.nama}</span>
                        <div class="product-price-row">
                            <span class="product-price-val">Rp ${formattedPrice}</span>
                            <span class="product-price-unit">/ ${prod.satuan || 'porsi'}</span>
                        </div>
                        ${badgeHtml}
                    </div>
                </div>

                <div class="product-card-actions">
                    <a href="${prod.id ? '/dashboard/mode/umkm/produk/' + prod.id + '/edit' : 'javascript:void(0)'}" 
                       class="btn-prod-action btn-prod-edit" 
                       title="Edit Menu" 
                       style="text-decoration:none; display:flex; align-items:center; justify-content:center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                    </a>
                    <button type="button" class="btn-prod-action btn-prod-delete" onclick="deleteProduct(${index})" title="Hapus Menu">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </div>
            `;
            container.appendChild(card);
        });
    }

    // Modal Operations
    let editingIndex = -1;

    function openAddProductModal() {
        editingIndex = -1;
        document.getElementById('productModalTitle').textContent = '🍲 Tambah Produk Baru';
        document.getElementById('modalProdNama').value = '';
        document.getElementById('modalProdHarga').value = '';
        document.getElementById('modalProdSatuan').value = 'porsi';
        document.getElementById('modalProdLabel').value = 'Unggulan|⭐|badge-gold';
        document.getElementById('modalProdFoto').value = "{{ asset('images/umkm/bandeng_tanpa_duri.jpg') }}";
        document.getElementById('productModal').classList.add('is-active');
    }

    function editProduct(index) {
        editingIndex = index;
        const prod = products[index];
        document.getElementById('productModalTitle').textContent = '✏️ Edit Produk Menu';
        document.getElementById('modalProdNama').value = prod.nama || '';
        document.getElementById('modalProdHarga').value = prod.harga || '';
        document.getElementById('modalProdSatuan').value = prod.satuan || 'porsi';

        const labelSelect = document.getElementById('modalProdLabel');
        if (prod.label) {
            labelSelect.value = `${prod.label}|${prod.badge_icon || '⭐'}|${prod.badge_class || 'badge-gold'}`;
        } else {
            labelSelect.value = '';
        }

        document.getElementById('modalProdFoto').value = prod.foto_url || '';
        document.getElementById('productModal').classList.add('is-active');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.remove('is-active');
    }

    function saveProductFromModal() {
        const nama = document.getElementById('modalProdNama').value.trim();
        const harga = parseInt(document.getElementById('modalProdHarga').value, 10);
        const satuan = document.getElementById('modalProdSatuan').value.trim() || 'porsi';
        const labelVal = document.getElementById('modalProdLabel').value;
        const foto_url = document.getElementById('modalProdFoto').value.trim() || "{{ asset('images/umkm/bandeng_tanpa_duri.jpg') }}";

        if (!nama) {
            alert('Mohon masukkan nama produk.');
            return;
        }
        if (isNaN(harga) || harga <= 0) {
            alert('Mohon masukkan harga yang valid.');
            return;
        }

        let label = '';
        let badge_icon = '';
        let badge_class = 'badge-gold';
        if (labelVal) {
            const parts = labelVal.split('|');
            label = parts[0] || '';
            badge_icon = parts[1] || '⭐';
            badge_class = parts[2] || 'badge-gold';
        }

        const productData = {
            id: editingIndex >= 0 && products[editingIndex].id ? products[editingIndex].id : Date.now(),
            nama: nama,
            harga: harga,
            satuan: satuan,
            label: label,
            badge_icon: badge_icon,
            badge_class: badge_class,
            foto_url: foto_url,
            is_ready: true
        };

        if (editingIndex >= 0) {
            products[editingIndex] = productData;
        } else {
            products.push(productData);
        }

        closeProductModal();
        renderProducts();
    }

    function deleteProduct(index) {
        if (confirm(`Yakin ingin menghapus menu "${products[index].nama}" dari katalog tap?`)) {
            products.splice(index, 1);
            renderProducts();
        }
    }

    // Logo image preview
    function handleLogoChange(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logoPreviewImg');
                const placeholder = document.getElementById('logoPlaceholderBox');
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Dynamic character counter
    function updateCharCount(textarea) {
        const counter = document.getElementById('descCount');
        if (counter) {
            counter.textContent = textarea.value.length;
        }
    }

    // Initial render
    document.addEventListener('DOMContentLoaded', () => {
        renderProducts();
    });
</script>

</body>
</html>
