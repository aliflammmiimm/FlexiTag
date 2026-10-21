# SPESIFIKASI KEBUTUHAN PERANGKAT LUNAK (SRS)
## EKOSISTEM SMART MERCHANDISE & PROFIL DIGITAL DINAMIS: FLEXITAG

**Disusun Oleh:** Tim Pengembang MaccaTech  
**Versi Dokumen:** 1.1 (Revisi Penyelarasan Desain Sulawesi Heritage & 4 Mode Inti)  
**Tanggal Pembaruan:** 21 Oktober 2026  
**Lokasi Pengembangan:** Makassar, Sulawesi Selatan, Indonesia  
**Status Dokumen:** Final / Siap Produksi  

---

## DAFTAR ISI
1. [PENDAHULUAN](#bab-1-pendahuluan)
   - 1.1 Tujuan Dokumen
   - 1.2 Ruang Lingkup Produk
   - 1.3 Definisi, Akronim, dan Singkatan
   - 1.4 Referensi
   - 1.5 Ikhtisar Dokumen
2. [DESKRIPSI UMUM SISTEM](#bab-2-deskripsi-umum-sistem)
   - 2.1 Perspektif Produk
   - 2.2 Fungsi Utama Produk (4 Mode Profil Dinamis)
   - 2.3 Karakteristik Pengguna
   - 2.4 Batasan Umum Sistem
   - 2.5 Asumsi dan Ketergantungan
3. [SPESIFIKASI KEBUTUHAN ANTARMUKA EKSTERNAL](#bab-3-spesifikasi-kebutuhan-antarmuka-eksternal)
   - 3.1 Antarmuka Pengguna (UI/UX & Standar Visual)
   - 3.2 Antarmuka Perangkat Keras (Hardware Interface)
   - 3.3 Antarmuka Perangkat Lunak (Software Interface)
   - 3.4 Antarmuka Komunikasi
4. [SPESIFIKASI KEBUTUHAN FUNGSIONAL](#bab-4-spesifikasi-kebutuhan-fungsional)
   - 4.1 Daftar Use Case Utama
   - 4.2 Modul Autentikasi & Akun (FR-AUT)
   - 4.3 Modul Aktivasi & Manajemen Tag Fisik (FR-TAG)
   - 4.4 Modul Manajemen Profil & 4 Mode Dinamis (FR-PRF)
   - 4.5 Modul Tampilan Profil Publik (FR-PUB)
   - 4.6 Modul Sistem Relay Barang Hilang (FR-LST)
   - 4.7 Modul Pemesanan Custom & Pelacakan Publik (FR-ORD)
   - 4.8 Modul Analitik Interaksi Pengguna (FR-ANL)
   - 4.9 Modul Konsol Administrasi / Admin Panel (FR-ADM)
5. [SPESIFIKASI KEBUTUHAN NON-FUNGSIONAL](#bab-5-spesifikasi-kebutuhan-non-fungsional)
   - 5.1 Kebutuhan Kinerja (Performance - NFR-PRF)
   - 5.2 Kebutuhan Keamanan (Security - NFR-SEC)
   - 5.3 Kebutuhan Keandalan & Ketersediaan (Reliability - NFR-REL)
   - 5.4 Kebutuhan Kepatuhan & Privasi Data (Compliance - NFR-CMP)
6. [ARSITEKTUR & MODEL DATA (DATABASE SCHEMA)](#bab-6-arsitektur--model-data-database-schema)
   - 6.1 Gambaran Umum Relasi Entitas (ERD)
   - 6.2 Kamus Data & Struktur Tabel (15 Tabel Inti)
   - 6.3 Standar Struktur JSON pada Tabel `mode_data`
7. [ATURAN BISNIS (BUSINESS RULES)](#bab-7-aturan-bisnis-business-rules)

---

## BAB 1: PENDAHULUAN

### 1.1 Tujuan Dokumen
Dokumen Spesifikasi Kebutuhan Perangkat Lunak (*Software Requirements Specification* / SRS) ini bertujuan untuk mendefinisikan secara lengkap, terstruktur, dan terukur seluruh kebutuhan fungsional maupun non-fungsional dari platform web **FlexiTag** yang dikembangkan oleh **MaccaTech**. Dokumen ini menjadi acuan tunggal (*single source of truth*) bagi tim pengembang (*front-end*, *back-end*, dan *database engineer*), desainer UI/UX, tim penguji (*Quality Assurance*), serta pemangku kepentingan dalam memvalidasi implementasi sistem.

### 1.2 Ruang Lingkup Produk
**FlexiTag** adalah ekosistem *smart merchandise* berbasis kearifan lokal Sulawesi (gantungan kunci akrilik kustom, gelang karet, *lanyard* tenun, dan stiker pintar) yang terintegrasi dengan cip *Near Field Communication* (NFC) dan kode QR dinamis. 

Saat perangkat fisik FlexiTag disentuhkan (*tap*) ke ponsel pintar atau dipindai tanpa memerlukan aplikasi tambahan, sistem akan menampilkan halaman profil web publik yang isinya dapat berubah secara instan berdasarkan **4 Mode Profil Dinamis** yang dikendalikan oleh pemilik melalui dasbor mereka:
1. **Mode Kampus** (Jejaring akademik, organisasi mahasiswa, dan media sosial).
2. **Mode Kerja** (Portofolio profesional, tautan LinkedIn/GitHub, dan unduhan CV/vCard).
3. **Mode UMKM** (Etalase bisnis lokal, katalog produk hingga 20 item, lokasi toko GPS, dan pemesanan cepat WhatsApp).
4. **Mode Barang Hilang** (Pesan darurat penemuan barang dan sistem *relay* pesan terenkripsi ke email serta kotak masuk dasbor pemilik tanpa membocorkan nomor telepon pribadi).

Selain manajemen profil dan *multi-tag* (`1:N`), platform ini juga mencakup sistem pemesanan kustom 4 langkah, pelacakan status produksi 7 tahap secara *real-time*, analitik pemindaian, serta **Admin Console** terpusat untuk memproduksi *batch* kode aktivasi, memantau inventaris cip, dan mengelola operasional bisnis.

### 1.3 Definisi, Akronim, dan Singkatan
| Istilah / Singkatan | Definisi |
| :--- | :--- |
| **FlexiTag** | Nama produk *smart merchandise* NFC & platform profil digital dinamis. |
| **MaccaTech** | Entitas *startup* / tim pengembang sistem FlexiTag (diambil dari kata Bugis/Makassar *Macca* yang berarti cerdas). |
| **NFC** | *Near Field Communication*; protokol komunikasi nirkabel jarak pendek (1–4 cm) pada frekuensi 13.56 MHz. |
| **NTAG213 / NTAG215** | Seri cip mikrokontroler NFC standar NXP yang tertanam di dalam produk fisik FlexiTag. |
| **Tag Code** | Kode alfanumerik unik acak (8–12 karakter) yang menjadi ujung URL publik (`/u/{tag_code}`). |
| **Activation Code** | Kode rahasia unik yang disertakan pada kemasan produk fisik untuk mengklaim dan menautkan *tag* ke akun pemilik. |
| **Kill Switch** | Fitur keamanan darurat bagi pemilik atau admin untuk membekukan/menonaktifkan akses publik ke *tag* fisik secara instan. |
| **Relay Pesan** | Mekanisme pengiriman pesan satu arah dari penemu barang ke email dan dasbor pemilik tanpa mengekspos kontak pribadi pemilik. |
| **UU PDP** | Undang-Undang Pelindungan Data Pribadi Republik Indonesia (UU No. 27 Tahun 2022). |

### 1.4 Referensi
1. Standar IEEE Std 830-1998 / ISO/IEC/IEEE 29148 untuk *Software Requirements Specifications*.
2. Dokumentasi Resmi Laravel Framework 11.x & MySQL 8.0 Reference Manual.
3. Spesifikasi Teknis NXP Semiconductors NTAG213/215/216 NFC Forum Type 2 Tag.
4. Undang-Undang Republik Indonesia Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP).

### 1.5 Ikhtisar Dokumen
Dokumen ini dibagi menjadi 8 bab utama. Bab 1 dan Bab 2 menjelaskan konteks bisnis serta gambaran umum produk. Bab 3 menetapkan standar antarmuka termasuk identitas visual *Sulawesi Heritage*. Bab 4 dan Bab 5 merinci spesifikasi teknis fungsional dan non-fungsional. Bab 6 mendefinisikan struktur basis data (ERD dan skema JSON). Bab 7 memuat aturan bisnis operasional, dan Bab 8 menyediakan matriks pengujian sistem.

---

## BAB 2: DESKRIPSI UMUM SISTEM

### 2.1 Perspektif Produk
FlexiTag merupakan sistem mandiri berbasis web (*cloud-based web application*) yang menjembatani produk kerajinan fisik lokal dengan identitas digital dinamis. Berbeda dengan kartu nama NFC konvensional yang hanya menampilkan satu halaman statis (seperti Linktree biasa), FlexiTag memisahkan konteks kehidupan pengguna ke dalam **4 mode fungsional yang dapat dipertukarkan (*switchable*)** dalam satu keping perangkat keras yang sama, didukung oleh sistem manajemen satu akun untuk banyak perangkat (*one-to-many hardware binding*).

### 2.2 Fungsi Utama Produk (4 Mode Profil Dinamis)
1. **Manajemen Multi-Merchandise (`1:N`):** Satu akun pengguna dapat mengklaim, memberi nama kustom, dan mengelola beberapa produk fisik FlexiTag sekaligus beserta fitur *Kill Switch* per perangkat.
2. **Pertukaran 4 Mode Instan (*Real-Time Mode Switcher*):**
   - **Mode Kampus:** Menampilkan identitas mahasiswa/akademisi, program studi/universitas, tautan media sosial, dan portofolio kegiatan.
   - **Mode Kerja:** Menampilkan profil profesional, *headline* karir, tautan LinkedIn/GitHub/Behance, tombol unduh CV (PDF), serta tombol "Simpan Kontak (vCard `.vcf`)".
   - **Mode UMKM:** Mengubah tampilan profil menjadi katalog bisnis digital lengkap dengan logo usaha, deskripsi, tombol navigasi GPS ke lokasi toko, daftar hingga 20 produk (beserta foto, harga, satuan, dan label promosi), serta tombol pesanan langsung ke WhatsApp.
   - **Mode Barang Hilang:** Mengubah halaman publik menjadi pemberitahuan darurat saat barang tercecer, menampilkan pesan khusus dari pemilik, info imbalan (opsional), dan formulir *Relay Pesan Aman* bagi penemu barang.
3. **Konfigurator & Pemesanan Kustom Tanpa Akun:** Pengunjung publik dapat memesan produk FlexiTag kustom melalui alur 4 langkah, mengunggah desain, memanfaatkan kalkulator harga grosir/komunitas otomatis, serta melacak 7 tahapan status pesanan secara langsung.
4. **Analitik Interaksi & Konsol Administrasi:** Menyediakan visualisasi data pemindaian bagi pemilik serta panel kendali menyeluruh bagi Admin (pembuatan *batch* kode aktivasi CSV, manajemen pesanan, dan pusat laporan penyalahgunaan).

### 2.3 Karakteristik Pengguna
| Kategori Pengguna | Karakteristik & Peran dalam Sistem | Tingkat Keahlian Teknis |
| :--- | :--- | :--- |
| **Pengunjung / Pemindai Publik (*Scanner*)** | Orang yang memindai *tag* NFC/QR milik pengguna lain, atau calon pembeli yang mengakses *landing page*, form pemesanan, dan pelacakan pesanan. Tidak memerlukan akun. | Dasar (mampu menggunakan *browser* ponsel & fitur NFC/Kamera). |
| **Pemilik Tag (*Registered Owner*)** | Pelanggan (mahasiswa, pekerja profesional, pelaku UMKM) yang telah membeli produk fisik, mendaftarkan akun, mengaktifkan *tag*, dan mengelola 4 mode profil di dasbor. | Dasar hingga Menengah. |
| **Staf Operasional (*Staff*)** | Anggota tim internal MaccaTech yang bertugas memverifikasi pesanan, memperbarui tahapan produksi/pengiriman, dan memantau inventaris *tag*. | Menengah. |
| **Administrator Utama (*Super Admin*)** | Pengelola tertinggi sistem yang memiliki akses penuh ke seluruh fitur Admin Console, termasuk *generator batch* NFC, manajemen pengguna, keuangan, dan konfigurasi server. | Menengah hingga Mahir. |

### 2.4 Batasan Umum Sistem
1. **Keterbatasan Perangkat Keras Pemindai:** Fitur *tap* NFC hanya berjalan pada ponsel pintar yang memiliki sensor NFC aktif. Untuk ponsel tanpa NFC, akses tetap dijamin melalui pemindaian kode QR yang tercetak pada produk fisik atau pembagian tautan langsung.
2. **Kapasitas Memori Cip NFC:** Cip NTAG213 (144 bytes) dan NTAG215 (504 bytes) hanya digunakan untuk menyimpan satu URL statis pendek (`https://flexitag.id/u/{tag_code}`). Seluruh logika perubahan mode diproses di sisi *cloud server* (Laravel), bukan dengan menulis ulang (*overwrite*) memori cip fisik.
3. **Notifikasi Versi 1.0:** Pada rilis versi 1.0, sistem *relay* pesan barang hilang dan notifikasi pesanan dikirimkan melalui protokol **Email (SMTP)** dan **Kotak Masuk Dashboard** untuk efisiensi biaya operasional (tanpa ketergantungan pada API WhatsApp Gateway berbayar).
4. **Batas Unggahan Berkas:** Demi menjaga performa penyimpanan server, ukuran unggahan foto profil/produk dibatasi maksimal 2 MB–5 MB, berkas CV PDF maksimal 5 MB, dan berkas desain cetak maksimal 10 MB.

### 2.5 Asumsi dan Ketergantungan
- Pengguna dan pemindai diasumsikan memiliki koneksi internet aktif saat mengakses halaman profil publik karena data profil dimuat secara dinamis dari server.
- Penulisan (*encoding*) URL ke dalam cip NFC fisik dilakukan oleh tim operasional MaccaTech menggunakan perangkat *NFC Reader/Writer* (seperti ACR122U atau aplikasi NFC Tools Pro) berdasarkan berkas CSV yang diunduh dari menu *Generate Tag*.

---

## BAB 3: SPESIFIKASI KEBUTUHAN ANTARMUKA EKSTERNAL

### 3.1 Antarmuka Pengguna (UI/UX & Standar Visual)
Sistem menerapkan standar desain bertema **"Sulawesi Heritage"** yang memadukan estetika budaya lokal Sulawesi Selatan dengan tata letak aplikasi modern yang bersih (*clean SaaS UI*).

| Kode | Spesifikasi Kebutuhan Antarmuka Visual |
| :--- | :--- |
| **UI-01** | **Pendekatan Responsif (*Mobile-First & Desktop Admin*):** Seluruh halaman publik, autentikasi, aktivasi, dan dasbor pemilik dirancang dengan proporsi *mobile viewport* (rasio 9:19.5, lebar optimal `360px–430px`) yang tetap rapi saat dibuka di desktop. Sementara itu, halaman **Admin Console** dirancang khusus untuk layar *desktop widescreen* (`1280px–1920px`) dengan tata letak 2 kolom (*Left Sidebar* + *Full-Width Main Content*). |
| **UI-02** | **Navigasi Bawah Dasbor Pemilik (*Bottom Navigation Bar*):** Dasbor pemilik menggunakan bilah navigasi bawah yang terdiri atas 4 menu inti: **Home** (Kendali 4 Mode & Ringkasan), **Merch** (Manajemen Perangkat Fisik & *Kill Switch*), **Analytics** (Statistik Interaksi), dan **Settings** (Pengaturan Akun & Privasi). |
| **UI-03** | **Navigasi Samping Konsol Admin (*Admin Sidebar*):** Panel admin menggunakan *sidebar* kiri berwarna *Rich Maroon* dengan 6 menu tetap: **Dashboard**, **Generate Tag**, **Daftar Tag**, **Daftar Pesanan**, **Analitik & Tap**, dan **Pengaturan**, ditambah **Ikon Lonceng Notifikasi** di *header* kanan atas yang membuka *Slide-Over Drawer* untuk Pusat Pesan & Laporan. |
| **UI-04** | **Palet Warna Resmi (*Sulawesi Heritage Palette*):**<br>- **Warna Utama (*Primary*):** *Deep Rich Maroon* (`#8B0000`) — melambangkan keanggunan tenun dan ketegasan.<br>- **Warna Aksen (*Accent/Highlight*):** *Warm Gold* (`#D4AF37`) — melambangkan kemewahan benang emas *Saqbe* dan ukiran *Pa'barre Allo*.<br>- **Warna Latar Utama (*Background*):** *Warm Cream* (`#FAF9F6`) dipadukan dengan kartu *Pure White* (`#FFFFFF`).<br>- **Warna Teks Utama (*Typography*):** *Dark Charcoal* (`#1A1A1A`) untuk keterbacaan tinggi.<br>- **Warna Status Semantik:** Hijau Aktif (`#16A34A`), Kuning Peringatan/Pending (`#D97706`), Biru Proses (`#2563EB`), dan Merah Darurat/Blokir (`#DC2626`). |
| **UI-05** | **Standar Tipografi Tunggal:** Seluruh antarmuka sistem (baik halaman pengguna maupun Admin Console) wajib menggunakan keluarga fon **Poppins** (*Regular 400, Medium 500, SemiBold 600, Bold 700*) secara konsisten tanpa mencampurkan fon *serif* agar tampilan modern dan mudah dibaca di berbagai resolusi layar. |

### 3.2 Antarmuka Perangkat Keras (Hardware Interface)
1. **Cip NFC Fisik:** Kompatibel dengan standar ISO/IEC 14443 Type A (NXP NTAG213/215) yang mendukung format *NDEF (NFC Data Exchange Format)* bertipe *URI Record*.
2. **Perangkat Pemindai:** Seluruh ponsel cerdas Android (OS 5.0+) dan iOS (iPhone 7 ke atas dengan iOS 14+ untuk pembacaan NFC latar belakang secara otomatis) tanpa instalasi aplikasi khusus.

### 3.3 Antarmuka Perangkat Lunak (Software Interface)
1. **Framework & Bahasa Pemrograman:** Laravel (PHP 8.2+), Blade Templating Engine dengan dukungan komponen reaktif (Alpine.js / Livewire), serta Tailwind CSS.
2. **Sistem Manajemen Basis Data:** MySQL 8.0+ atau MariaDB 10.6+ dengan dukungan tipe data `JSON` asli (*native JSON column*).
3. **Layanan Pihak Ketiga:**
   - **Google OAuth 2.0 (Laravel Socialite):** Untuk autentikasi satu klik pemilik tag.
   - **Layanan SMTP (Mailtrap / Google Workspace / Amazon SES):** Untuk pengiriman verifikasi email, reset kata sandi, dan penerusan pesan darurat Mode Barang Hilang.
   - **Deep Linking Eksternal:** Integrasi URI ke WhatsApp (`https://wa.me/`), Google Maps (`https://maps.google.com/?q=`), serta unduhan kontak standar MIME `text/vcard`.

### 3.4 Antarmuka Komunikasi
Seluruh komunikasi antara peramban klien (*client browser*) dan server wajib melalui protokol **HTTPS (TLS 1.2 / TLS 1.3)**. Pengiriman formulir dilindungi oleh token **CSRF (*Cross-Site Request Forgery*)** bawaan Laravel.

---

## BAB 4: SPESIFIKASI KEBUTUHAN FUNGSIONAL

### 4.1 Daftar Use Case Utama
| Kode UC | Nama Use Case | Aktor Utama | Deskripsi Singkat |
| :--- | :--- | :--- | :--- |
| **UC-01** | Memindai Tag & Melihat Profil Publik | Pengunjung Publik | Mengakses URL `/u/{tag_code}` via NFC/QR dan melihat halaman sesuai mode yang sedang diaktifkan pemilik. |
| **UC-02** | Registrasi & Login Akun Pemilik | Pemilik Tag | Mendaftar atau masuk ke dasbor menggunakan email/kata sandi atau akun Google OAuth. |
| **UC-03** | Mengaktifkan Produk FlexiTag Baru | Pemilik Tag | Mengklaim kepemilikan *tag* baru dengan memasukkan `activation_code` pada halaman aktivasi atau menu *Merch*. |
| **UC-04** | Mengganti Mode Profil Aktif (*Mode Switcher*) | Pemilik Tag | Mengubah mode tampil secara instan antara **Kampus, Kerja, UMKM, atau Barang Hilang** dari dasbor *Home*. |
| **UC-05** | Mengelola Konten Mode Kampus & Kerja | Pemilik Tag | Mengubah bio, foto, tautan sosial/portofolio, mengunggah CV PDF, dan mengatur kartu kontak vCard. |
| **UC-06** | Mengelola Profil & Katalog Mode UMKM | Pemilik Tag | Mengatur identitas usaha, koordinat GPS toko, nomor WA pesanan, serta menambah/mengedit hingga 20 produk katalog. |
| **UC-07** | Mengelola Mode Barang Hilang & Kotak Masuk | Pemilik Tag | Mengatur pesan darurat penemuan dan membaca pesan yang dikirim oleh penemu barang di dasbor/email. |
| **UC-08** | Mengelola Perangkat Fisik & *Kill Switch* | Pemilik Tag | Melihat daftar *merchandise* yang dimiliki, menambah *tag* baru, atau mengaktifkan sakelar *Kill Switch* jika *tag* hilang. |
| **UC-09** | Melihat Analitik Interaksi (*Analytics*) | Pemilik Tag | Memantau jumlah *tap* total, grafik tren harian, dan persentase distribusi penggunaan ke-4 mode. |
| **UC-10** | Mengatur Privasi & Keamanan Akun | Pemilik Tag | Mengubah kata sandi serta menyembunyikan/menampilkan nomor telepon, email, dan lokasi dari publik. |
| **UC-11** | Mengirim Pesan Penemuan Barang / Laporan | Pengunjung Publik | Mengirim pesan ke pemilik saat *tag* berada dalam Mode Barang Hilang, atau melaporkan penyalahgunaan profil (*abuse*). |
| **UC-12** | Memesan FlexiTag Custom (4 Langkah) | Pengunjung Publik | Memilih spesifikasi akrilik, mengunggah desain kustom, mengisi data pengiriman, dan mengunggah bukti pembayaran. |
| **UC-13** | Melacak Status Pesanan (*Track Order*) | Pengunjung Publik | Memeriksa linimasa 7 tahapan produksi pesanan menggunakan Nomor Pesanan dan Nomor WhatsApp. |
| **UC-14** | Mengelola Operasional & Batch Tag (Admin) | Admin / Staf | Membuat *batch* kode NFC baru (ekspor CSV), mengelola inventaris *tag*, memperbarui status pesanan, dan menindak laporan. |

---

### 4.2 Modul Autentikasi & Akun (FR-AUT)
| Kode | Kebutuhan Fungsional | Prioritas |
| :--- | :--- | :--- |
| **FR-AUT-01** | Sistem harus menyediakan halaman autentikasi terpadu (Tab **Masuk** dan **Daftar Akun**) bagi pemilik *tag* dengan validasi email unik dan kata sandi minimal 8 karakter. | Tinggi |
| **FR-AUT-02** | Sistem harus menyediakan opsi masuk dan daftar cepat menggunakan **Google OAuth 2.0** ("Lanjutkan dengan Akun Google"). | Tinggi |
| **FR-AUT-03** | Sistem harus menyediakan fitur **Lupa Kata Sandi** yang mengirimkan tautan pemulihan berbatas waktu (60 menit) ke email pengguna. | Tinggi |
| **FR-AUT-04** | Sistem harus memisahkan gerbang masuk pengelola melalui halaman khusus **Login Admin Console** (`/admin/login`) yang hanya mengizinkan pengguna dengan peran `admin` atau `staff` yang telah didaftarkan oleh Super Admin. | Tinggi |
| **FR-AUT-05** | Sistem harus menyediakan fungsi **Keluar Akun (*Log Out*)** yang menghapus sesi aktif dan melakukan *regeneration* token keamanan. | Tinggi |

### 4.3 Modul Aktivasi & Manajemen Tag Fisik (FR-TAG)
| Kode | Kebutuhan Fungsional | Prioritas |
| :--- | :--- | :--- |
| **FR-TAG-01** | Saat pengguna memindai *tag* NFC yang berstatus `belum_aktif`, sistem harus mengarahkan pengguna ke halaman **Aktivasi Tag** untuk memasukkan Kode Aktivasi (`activation_code`) yang tertera pada kemasan fisik. | Tinggi |
| **FR-TAG-02** | Sistem harus memverifikasi kecocokan `activation_code` dengan nilai *hash* di basis data. Jika valid, sistem mengikat `tag` tersebut ke `user_id` pengguna yang sedang login dan mengubah statusnya menjadi `aktif`. | Tinggi |
| **FR-TAG-03** | Sistem harus mendukung relasi kepemilikan **Satu Akun untuk Banyak Tag (`1:N`)**. Pada menu **Merch**, pemilik dapat melihat daftar seluruh produk fisik yang dimilikinya (lengkap dengan nama kustom dan status aktif). | Tinggi |
| **FR-TAG-04** | Pada menu **Merch**, pemilik harus dapat menekan tombol **"+ Tambah Produk FlexiTag Baru"** untuk menautkan *tag* tambahan ke akun yang sama. | Tinggi |
| **FR-TAG-05** | Setiap kartu perangkat pada menu **Merch** harus memiliki sakelar **Kunci Tag Sementara (*Kill Switch*)**. Jika diaktifkan oleh pemilik (`disabled_by = 'owner'`), status *tag* berubah menjadi `nonaktif` dan halaman publik otomatis terkunci hingga sakelar dibuka kembali. | Tinggi |

### 4.4 Modul Manajemen Profil & 4 Mode Dinamis (FR-PRF)
| Kode | Kebutuhan Fungsional | Prioritas |
| :--- | :--- | :--- |
| **FR-PRF-01** | Pada halaman utama dasbor pemilik (**Home**), sistem harus menampilkan komponen **Pemilih 4 Mode Profil (*Mode Switcher*)** yang terdiri atas: **Mode Kampus**, **Mode Kerja**, **Mode UMKM**, dan **Mode Barang Hilang**. | Tinggi |
| **FR-PRF-02** | Ketika pemilik memilih salah satu dari ke-4 mode tersebut, sistem

### 4.5 Modul Profil Publik Dinamis (FR-PUB)

Halaman ini diakses secara publik melalui rute `/u/{tag_code}` atau `/@{username}` tanpa memerlukan login maupun instalasi aplikasi tambahan.

| Kode | Kebutuhan Fungsional | Prioritas |
| :--- | :--- | :--- |
| **FR-PUB-01** | Sistem wajib merender halaman profil publik secara dinamis menyesuaikan nilai `active_mode` pada profil pemilik (`kampus`, `kerja`, `umkm`, atau `hilang`). | Tinggi |
| **FR-PUB-02** | Jika `status` tag adalah `belum_aktif`, sistem mengalihkan pengunjung ke halaman informasi aktivasi tag. | Tinggi |
| **FR-PUB-03** | Jika `status` tag adalah `nonaktif` (baik karena *Kill Switch* pemilik maupun blokir admin), sistem menampilkan halaman pemberitahuan bahwa tag sedang dinonaktifkan sementara dan menyembunyikan seluruh data pribadi pemilik. | Tinggi |
| **FR-PUB-04** | Sistem wajib mencatat setiap akses halaman publik yang valid ke dalam tabel `taps` (meliputi `tag_id`, waktu akses, sumber akses `nfc`/`qr`/`link`, tipe perangkat, dan estimasi wilayah). | Tinggi |
| **FR-PUB-05** | Sistem wajib mematuhi pengaturan pada tabel `privacy_settings` (menyembunyikan nomor telepon, alamat email, atau lokasi jika opsi terkait bernilai `false`). | Tinggi |
| **FR-PUB-06** | Jika opsi `indexable` pada pengaturan privasi bernilai `false`, sistem wajib menyisipkan meta tag `<meta name="robots" content="noindex, nofollow">` pada header HTML halaman publik. | Sedang |
| **FR-PUB-07** | **Tampilan Mode Kampus:** Menampilkan foto profil, nama tampilan, *headline* (institusi/jurusan/angkatan), bio singkat, serta daftar tautan sosial dan akademik yang diaktifkan. | Tinggi |
| **FR-PUB-08** | **Tampilan Mode Kerja:** Menampilkan foto profil profesional, nama, jabatan/spesialisasi, bio profesional, tombol **Unduh CV (PDF)**, tombol **Simpan Kontak (vCard `.vcf`)**, dan daftar tautan portofolio/LinkedIn. | Tinggi |
| **FR-PUB-09** | **Tampilan Mode UMKM:** Menampilkan logo usaha, nama usaha, deskripsi singkat, alamat toko beserta tautan navigasi rute GPS, tombol aksi cepat pemesanan WhatsApp, serta daftar katalog produk/menu aktif (foto, nama produk, harga, satuan, dan label promosi seperti *Unggulan* atau *Paling Laris*). | Tinggi |
| **FR-PUB-10** | **Tampilan Mode Barang Hilang:** Menyembunyikan seluruh tautan sosial/katalog reguler dan menampilkan banner peringatan barang hilang, pesan khusus dari pemilik, informasi imbalan (opsional), serta tombol utama **"Hubungi Pemilik"** yang membuka formulir pengiriman pesan aman. | Tinggi |
| **FR-PUB-11** | Setiap kali pengunjung mengeklik tautan eksternal pada Mode Kampus atau Mode Kerja, sistem wajib mencatat aktivitas tersebut ke dalam tabel `link_clicks` sebelum mengarahkan pengunjung ke URL tujuan. | Sedang |
| **FR-PUB-12** | Halaman profil publik wajib menyediakan tautan kecil di bagian *footer* bertuliskan **"Laporkan Penyalahgunaan Profil"** untuk mengirim laporan *abuse* ke panel admin. | Sedang |

---

### 4.6 Modul Barang Hilang & Sistem Relay Pesan (FR-LST)

Modul ini mengatur komunikasi satu arah yang aman antara penemu barang dan pemilik tag tanpa mengekspos nomor telepon atau email pribadi pemilik di layar publik.

| Kode | Kebutuhan Fungsional | Prioritas |
| :--- | :--- | :--- |
| **FR-LST-01** | Saat pengunjung menekan tombol **"Hubungi Pemilik"** pada Mode Barang Hilang, sistem menampilkan formulir pesan yang memuat input: Nama Penemu (opsional), Lokasi Penemuan (wajib), Isi Pesan (wajib), dan Kontak Penemu/WhatsApp (opsional). | Tinggi |
| **FR-LST-02** | Sistem wajib menyimpan pesan yang dikirim penemu ke dalam tabel `messages` dengan tipe `lost_item` dan menautkannya ke `user_id` pemilik tag. | Tinggi |
| **FR-LST-03** | Apabila konfigurasi sistem relay aktif, sistem secara otomatis mengirimkan email notifikasi (via SMTP) ke alamat email terdaftar milik pemilik tag dan menampilkan pesan tersebut pada kotak masuk di Dashboard Pemilik. | Tinggi |
| **FR-LST-04** | Sistem wajib menerapkan pembatasan frekuensi pengiriman (*rate limiting* maksimal 3 pesan per 15 menit dari alamat IP yang sama) serta validasi anti-spam pada formulir penemu barang. | Tinggi |

---

### 4.7 Modul Analitik & Statistik Pengguna (FR-ANL)

Modul ini menyajikan data performa interaksi NFC bagi pemilik tag pada menu **Analytics** di navigasi bawah.

| Kode | Kebutuhan Fungsional | Prioritas |
| :--- | :--- | :--- |
| **FR-ANL-01** | Sistem wajib menampilkan kartu ringkasan metrik utama milik pengguna: **Total Tap Keseluruhan**, **Total Tap Minggu Ini**, dan **Waktu Terakhir Di-tap**. | Tinggi |
| **FR-ANL-02** | Sistem wajib menampilkan grafik batang/garis **Tren Harian** yang memvisualisasikan jumlah pemindaian tag dari hari Senin hingga Minggu (atau rentang 7–30 hari terakhir). | Tinggi |
| **FR-ANL-03** | Sistem wajib menampilkan diagram lingkaran (*donut/pie chart*) **Distribusi Mode** yang menunjukkan persentase pemindaian berdasarkan keempat mode profil (`kampus`, `kerja`, `umkm`, `hilang`). | Sedang |
| **FR-ANL-04** | Sistem menampilkan rincian jumlah klik pada masing-masing tautan profil (`link_clicks`) dan distribusi perangkat pemindai (Android vs iOS). | Sedang |

---

### 4.8 Modul Pemesanan Custom & Pelacakan Publik (FR-ORD)

Modul ini memfasilitasi pemesanan *smart merchandise* kustom oleh pelanggan baru serta pelacakan status produksi tanpa perlu login.

| Kode | Kebutuhan Fungsional | Prioritas |
| :--- | :--- | :--- |
| **FR-ORD-01** | Sistem menyediakan halaman **Konfigurator & Form Pemesanan 4 Langkah**: (1) Pilih Produk & Kuantitas, (2) Kustomisasi Desain, (3) Data Diri & Pengiriman, (4) Pembayaran. | Tinggi |
| **FR-ORD-02** | Pada tahap kustomisasi, pelanggan dapat memilih bentuk akrilik (Bulat, Persegi, Kapsul), memilih motif dari galeri katalog (`pribadi`, `organisasi`, `umkm`, `motif_lokal`), atau mengunggah file desain sendiri (format PNG/SVG, maks. 10 MB). | Tinggi |
| **FR-ORD-03** | Sistem wajib menghitung total harga secara otomatis berdasarkan kuantitas pesanan dan menerapkan potongan harga grosir/komunitas jika jumlah pesanan mencapai ambang batas tertentu (misal $\ge$ 10 unit). | Tinggi |
| **FR-ORD-04** | Pelanggan wajib mengisi data pemesan (Nama Lengkap, Nomor WhatsApp aktif, Email, Metode Pengiriman `ambil` atau `kirim` beserta alamat lengkap). | Tinggi |
| **FR-ORD-05** | Pada tahap pembayaran, sistem menampilkan informasi rekening pembayaran manual/QRIS dan menyediakan kolom unggah bukti pembayaran (format JPG/PNG/PDF, maks. 5 MB). | Tinggi |
| **FR-ORD-06** | Setelah pesanan dikirim, sistem menghasilkan nomor pesanan unik (contoh: `FLX-2026-089`), menyimpan data ke tabel `orders` dengan status awal `menunggu_pembayaran`, dan menampilkan halaman konfirmasi. | Tinggi |
| **FR-ORD-07** | Sistem menyediakan halaman publik **Lacak Pesanan** di mana pelanggan dapat memasukkan **Nomor Pesanan** dan **Nomor WhatsApp** untuk melihat status terkini. | Tinggi |
| **FR-ORD-08** | Halaman Lacak Pesanan wajib menampilkan linimasa (*timeline*) vertikal 7 tahapan status pesanan secara *real-time* berdasarkan riwayat pada tabel `order_status_logs`: (1) *Menunggu Pembayaran*, (2) *Pembayaran Dikonfirmasi*, (3) *Desain Disetujui*, (4) *Dicetak*, (5) *Dirakit dan Diuji*, (6) *Siap Diambil / Dikirim*, (7) *Selesai* (serta status khusus *Dibatalkan*). | Tinggi |

---

### 4.9 Modul Admin Console (FR-ADM)

Modul ini diakses oleh pengguna dengan peran `admin` atau `staff` melalui antarmuka desktop berlajur dua (*Left Sidebar Rich Maroon* dengan 6 menu utama + *Full-Width Main Content*):

| Kode | Kebutuhan Fungsional | Prioritas |
| :--- | :--- | :--- |
| **FR-ADM-01** | **Menu 1 - Dashboard:** Menampilkan 4 kartu metrik ringkasan operasional (*Total Pendapatan*, *Total Pesanan*, *Tag Aktif*, *Perlu Dikirim*), grafik batang *Tren Penjualan & Aktivasi Mingguan*, daftar persentase *Distribusi Mode Pelanggan*, serta tabel ringkas *5 Pesanan Terbaru*. | Tinggi |
| **FR-ADM-02** | **Menu 2 - Generate Tag:** Menyediakan formulir pembuatan *batch* kode NFC baru (pilih jenis produk, edisi budaya, jumlah *batch* 10/50/100/250 tag, dan *prefix* kode aktivasi). | Tinggi |
| **FR-ADM-03** | Saat admin menekan tombol **Generate & Download CSV**, sistem wajib menghasilkan `tag_code` (alfanumerik acak non-sekuensial 8–12 karakter untuk URL publik) dan `activation_code` unik. Nilai `activation_code` disimpan dalam bentuk *hash* di database, sedangkan versi *plain-text* hanya diunduh satu kali ke dalam file CSV untuk keperluan penyolderan/pencetakan fisik, serta dicatat pada tabel riwayat *batch*. | Tinggi |
| **FR-ADM-04** | **Menu 3 - Daftar Tag:** Menampilkan kartu ringkasan stok chip (*Total Tag Diproduksi*, *Tag Terhubung/Aktif*, *Tag Belum Aktif/Stok*) dan tabel inventaris lebar penuh yang memuat kolom: Kode Aktivasi, Batch & Edisi, Status Tag, Pemilik Terdaftar, Mode Saat Ini, serta tombol aksi untuk mengelola atau memblokir/menonaktifkan tag. Dilengkapi fitur pencarian, filter tab status, dan ekspor CSV. | Tinggi |
| **FR-ADM-05** | **Menu 4 - Daftar Pesanan:** Menampilkan metrik antrean pemenuhan dan tabel daftar pesanan pelanggan. Untuk menjaga kerapian visual tabel, 7 tahapan status pesanan dari database dipetakan ke dalam *badge* ringkas (*Menunggu Bayar*, *Diproses*, *Siap/Dikirim*, *Selesai*, *Dibatalkan*), namun admin dapat memperbarui ke-7 tahapan status detail tersebut melalui modal/halaman **Detail Pesanan** yang otomatis mencatat log ke `order_status_logs`. | Tinggi |
| **FR-ADM-06** | **Menu 5 - Analitik & Tap:** Menampilkan metrik global (*Total Tap Keseluruhan*, *Rata-rata Tap Harian*, *Panggilan Relay Darurat*), grafik garis *Frekuensi Scan NFC Harian (30 Hari Terakhir)*, diagram donat *Mode Paling Sering Diakses*, grafik horizontal *Wilayah Interaksi Terbanyak*, serta tombol unduh laporan. | Tinggi |
| **FR-ADM-07** | **Menu 6 - Pengaturan:** Menampilkan pengaturan profil pengelola yang sedang login secara dinamis (`name` dan `role`), pengaturan nomor WhatsApp CS (dengan validasi format kode negara `+628...`), serta sakelar konfigurasi platform: *Sistem Relay Pesan Darurat (Mode Barang Hilang)* via Email/Dashboard, *Notifikasi Pesanan Baru via Email*, dan *Mode Pemeliharaan (Maintenance)*. | Tinggi |
| **FR-ADM-08** | **Sub-modul Pengaturan / Administrasi Lanjutan:** Di dalam area Pengaturan (atau melalui tab terintegrasi), Admin memiliki akses untuk mengelola daftar akun pengguna (`users`), memperbarui galeri template motif (`designs`), dan mencatat pembukuan kas sederhana pemasukan/pengeluaran (`financial_entries`). | Sedang |
| **FR-ADM-09** | **Pusat Pesan & Laporan (Header Slide-Over Drawer):** Ikon lonceng notifikasi di sudut kanan atas header Admin dilengkapi indikator titik merah (*unread badge*). Saat diklik, membuka *Slide-Over Drawer* di sisi kanan layar tanpa berpindah halaman untuk menampilkan: (1) *Laporan Abuse* (`abuse_report`) beserta aksi cepat blokir tag, dan (2) *Pesan Kontak* (`contact`) dari pengunjung *landing page*. | Tinggi |
| **FR-ADM-10** | Setiap tindakan sensitif yang dilakukan oleh admin (seperti generate batch, perubahan status pesanan, atau pemblokiran tag/pengguna) wajib dicatat secara otomatis ke dalam tabel `audit_logs`. | Sedang |

---

## 5. Kebutuhan Non-Fungsional (Non-Functional Requirements)

### 5.1 Kebutuhan Antarmuka & Estetika Desain (UI/UX)

| Kode | Spesifikasi Kebutuhan Antarmuka |
| :--- | :--- |
| **UI-01** | **Pendekatan Responsif:** Halaman publik, halaman aktivasi, dan Dashboard Pemilik dirancang dengan pendekatan *Mobile-First* (dioptimalkan untuk *viewport* potret ponsel rasio 9:19.5), sedangkan Admin Console dirancang khusus untuk layar *Desktop Widescreen* (1280px ke atas) berlajur dua tanpa widget samping yang mempersempit tabel. |
| **UI-02** | **Tipografi Tunggal Konsisten:** Seluruh antarmuka sistem (baik judul, subjudul, isi tabel, label tombol, maupun menu *sidebar*) wajib menggunakan keluarga fon **Poppins** (*Regular, Medium, SemiBold, Bold*) secara menyeluruh tanpa mencampurkan fon *serif*. |
| **UI-03** | **Palet Warna Resmi (*Sulawesi Heritage*):**<br>- **Warna Utama (Primary / Sidebar):** *Deep Rich Maroon* (`#8B0000`)<br>- **Warna Aksen & Sorotan Aktif (Accent):** *Warm Gold* (`#D4AF37`)<br>- **Warna Latar Belakang Utama (Background):** *Warm Cream* (`#FAF9F6`)<br>- **Warna Kartu & Kontainer (Surface):** *Pure White* (`#FFFFFF`)<br>- **Warna Teks Utama (Typography):** *Dark Charcoal* (`#1A1A1A`) |
| **UI-04** | **Navigasi Pemilik Efisien:** Bilah navigasi bawah (*Bottom Navigation Bar*) pada Dashboard Pemilik dibatasi tepat 4 menu utama yang tidak tumpang tindih: **Home** (kendali 4 mode & edit profil aktif), **Merch** (manajemen perangkat fisik & *Kill Switch*), **Analytics** (statistik interaksi), dan **Settings** (keamanan akun & bantuan). |

### 5.2 Kebutuhan Keamanan (Security - NFR-SEC)

*   **NFR-SEC-01 (Enkripsi Kata Sandi & Kode Aktivasi):** Seluruh kata sandi pengguna dan `activation_code` tag NFC wajib disimpan menggunakan algoritma *hashing* satu arah `bcrypt` atau `Argon2id`.
*   **NFR-SEC-02 (Komunikasi Terenkripsi):** Seluruh lalu lintas data antara peramban klien dan server wajib menggunakan protokol HTTPS (TLS 1.2/1.3) agar pembacaan fitur Web NFC dan pengiriman formulir berjalan aman.
*   **NFR-SEC-03 (Otorisasi Berbasis Peran / RBAC):** Rute `/admin/*` wajib dilindungi oleh *middleware* otorisasi yang hanya mengizinkan pengguna dengan `role` bernilai `admin` atau `staff`. Halaman Login Admin tidak menyediakan pendaftaran mandiri.
*   **NFR-SEC-04 (Anti-Enumerasi URL Publik):** Kolom `tag_code` yang digunakan pada URL publik (`/u/{tag_code}`) wajib menggunakan string alfanumerik acak kriptografis (tidak boleh menggunakan angka berurutan seperti `001, 002`) untuk mencegah serangan penebakan URL (*brute-force enumeration*).
*   **NFR-SEC-05 (Proteksi Kerentanan Web):** Sistem wajib mengimplementasikan token CSRF pada seluruh formulir `POST/PUT/DELETE`, sanitasi input XSS, penggunaan *Prepared Statements* (Eloquent ORM) untuk mencegah SQL Injection, serta validasi *MIME type* dan ukuran file pada setiap fitur unggah gambar/PDF.
*   **NFR-SEC-06 (Kepatuhan Privasi Data):** Sesuai prinsip perlindungan data pribadi (UU PDP), nomor telepon dan alamat email pemilik pada tabel `privacy_settings` secara bawaan (*default*) diatur tersembunyi (`false`) saat akun pertama kali dibuat, dan nomor penerima pada Mode Barang Hilang tidak pernah ditampilkan di kode sumber HTML publik.

### 5.3 Kebutuhan Kinerja & Keandalan (Performance - NFR-PRF)

*   **NFR-PRF-01 (Waktu Muat Profil Publik):** Halaman profil publik (`/u/{tag_code}`) wajib memiliki waktu respons server (*Time to First Byte*) di bawah **500 ms** dan waktu muat penuh di bawah **2 detik** pada jaringan seluler 4G agar pengalaman *tap* NFC terasa instan.
*   **NFR-PRF-02 (Optimasi Aset Media):** Foto profil dan foto katalog produk UMKM yang diunggah pengguna wajib dikompresi dan dikonversi ukurannya secara proporsional di sisi server agar tidak membebani kuota data pengunjung.
*   **NFR-PRF-03 (Pengindeksan Basis Data):** Kolom-kolom yang menjadi kunci pencarian frekuensi tinggi—yaitu `users.email`, `tags.tag_code`, `tags.activation_code`, `profiles.username`, `taps.tag_id`, `taps.tapped_at`, dan `orders.order_no`—wajib diberikan `INDEX` atau `UNIQUE INDEX` pada level RDBMS.

---

## 6. Perancangan Basis Data (Skema RDBMS & ERD)

Sistem menggunakan basis data relasional (MySQL 8.0+ / MariaDB) yang terdiri dari **15 tabel terintegrasi**. Data spesifik keempat mode profil disimpan secara efisien menggunakan struktur kolom bertipe `JSON` pada tabel `mode_data`.

### 6.1 Spesifikasi Struktur Tabel

#### 1. Tabel `users`
Menyimpan data akun pemilik tag, staf operasional, dan super admin.
*   `id` — `BIGINT UNSIGNED` (PK, Auto Increment)
*   `name` — `VARCHAR(100)` (Not Null)
*   `email` — `VARCHAR(150)` (Unique, Not Null)
*   `password` — `VARCHAR(255)` (Nullable, untuk pengguna login Google OAuth)
*   `google_id` — `VARCHAR(100)` (Nullable, Index)
*   `role` — `ENUM('user', 'staff', 'admin')` (Default: `'user'`)
*   `email_verified_at` — `DATETIME` (Nullable)
*   `is_active` — `BOOLEAN` (Default: `true`)
*   `created_at`, `updated_at` — `TIMESTAMP`

#### 2. Tabel `tags`
Menyimpan inventaris keping NFC fisik dan status kepemilikannya.
*   `id` — `BIGINT UNSIGNED` (PK, Auto Increment)
*   `tag_code` — `VARCHAR(12)` (Unique Index, digunakan pada URL `/u/{tag_code}`)
*   `activation_code` — `VARCHAR(255)` (Unique, disimpan dalam bentuk *hash*)
*   `user_id` — `BIGINT UNSIGNED` (FK ke `users.id`, Nullable, `ON DELETE SET NULL`)
*   `status` — `ENUM('belum_aktif', 'aktif', 'nonaktif')` (Default: `'belum_aktif'`)
*   `disabled_by` — `ENUM('owner', 'admin')` (Nullable, penanda apakah dikunci lewat *Kill Switch* pemilik atau diblokir admin)
*   `batch_id` — `VARCHAR(30)` (Nullable, Index, contoh: `#B-882`)
*   `produced_at` — `DATETIME` (Nullable)
*   `activated_at` — `DATETIME` (Nullable)

#### 3. Tabel `profiles`
Menyimpan identitas dasar pengguna dan mode profil yang sedang aktif (Relasi 1:1 dengan `users`).
*   `user_id` — `BIGINT UNSIGNED` (PK & FK ke `users.id`, `ON DELETE CASCADE`)
*   `display_name` — `VARCHAR(100)` (Not Null)
*   `headline` — `VARCHAR(150)` (Nullable)
*   `bio` — `TEXT` (Nullable, maks. 500 karakter)
*   `photo_path` — `VARCHAR(255)` (Nullable)
*   `theme` — `VARCHAR(30)` (Default: `'sulawesi_heritage'`)
*   `active_mode` — `ENUM('kampus', 'kerja', 'umkm', 'hilang')` (Default: `'kampus'`)
*   `username` — `VARCHAR(20)` (Unique Index, Nullable)

#### 4. Tabel `privacy_settings`
Menyimpan konfigurasi visibilitas informasi sensitif pemilik (Relasi 1:1 dengan `users`).
*   `user_id` — `BIGINT UNSIGNED` (PK & FK ke `users.id`, `ON DELETE CASCADE`)
*   `show_phone` — `BOOLEAN` (Default: `false`)
*   `show_email` — `BOOLEAN` (Default: `false`)
*   `show_location` — `BOOLEAN` (Default: `false`)
*   `indexable` — `BOOLEAN` (Default: `false`)

#### 5. Tabel `links`
Menyimpan daftar tautan media sosial dan portofolio pengguna.
*   `id` — `BIGINT UNSIGNED` (PK, Auto Increment)
*   `user_id` — `BIGINT UNSIGNED` (FK ke `users.id`, `ON DELETE CASCADE`)
*   `type` — `VARCHAR(30)` (Contoh: `instagram`, `linkedin`, `github`, `whatsapp`, `web`)
*   `label` — `VARCHAR(100)` (Not Null)
*   `url` — `VARCHAR(255)` (Not Null)
*   `sort_order` — `INT` (Default: `0`)
*   `is_visible` — `BOOLEAN` (Default: `true`)

#### 6. Tabel `mode_data`
Menyimpan muatan data spesifik untuk masing-masing dari 4 mode profil menggunakan tipe kolom `JSON`.
*   `id` — `BIGINT UNSIGNED` (PK, Auto Increment)
*   `user_id` — `BIGINT UNSIGNED` (FK ke `users.id`, `ON DELETE CASCADE`)
*   `mode` — `ENUM('kampus', 'kerja', 'umkm', 'hilang')` (Not Null)
*   `data` — `JSON` (Not Null)

> **Contoh Standar Struktur JSON pada `mode_data.data`:**
> *   **Mode `kampus`:** `{"university": "Universitas Negeri Makassar", "faculty": "Teknik", "major": "Teknik Informatika", "cohort": "2023", "organizations": ["HIMA TI", "Google Developer Student Clubs"]}`
> *   **Mode `kerja`:** `{"company": "MaccaTech Studio", "role": "Full-Stack Developer", "cv_file_path": "uploads/cv/andi_cv.pdf", "skills": ["Laravel", "TailwindCSS", "MySQL"], "vcard_phone": "+628123456789"}`
> *   **Mode `umkm`:** `{"business_name": "Bandeng Tanpa Duri Sari Laut", "logo_path": "uploads/umkm/logo.png", "description": "Sedia Bandeng Cabut Duri Segar & Otak-Otak Bakar khas Pangkep", "address": "Jl. Poros Makassar - Pangkep KM 42", "gps_url": "https://maps.google.com/?q=...", "whatsapp_order": "6281242981023", "products": [{"id": 1, "name": "Bandeng Tanpa Duri", "price": 35000, "unit": "/porsi", "badge": "unggulan", "image": "prod1.jpg", "is_active": true}]}`
> *   **Mode `hilang`:** `{"alert_title": "Barang Ini Milik Andi Muhammad", "finder_message": "Jika Anda menemukan gantungan kunci/barang ini, mohon hubungi saya melalui tombol di bawah.", "reward_info": "Tersedia imbalan apresiasi bagi penemu.", "enable_email_relay": true}`

#### 7. Tabel `taps`
Mencatat log historis setiap kali tag NFC/QR/Link publik diakses.
*   `id` — `BIGINT UNSIGNED` (PK, Auto Increment)
*   `tag_id` — `BIGINT UNSIGNED` (FK ke `tags.id`, Index, `ON DELETE CASCADE`)
*   `tapped_at` — `DATETIME` (Index, Not Null)
*   `source` — `ENUM('nfc', 'qr', 'link')` (Default: `'nfc'`)
*   `device_type` — `VARCHAR(50)` (Nullable, contoh: `'Android'`, `'iOS'`, `'Desktop'`)
*   `region` — `VARCHAR(100)` (Nullable, contoh: `'Makassar'`, `'Pangkep'`, `'Gowa'`, `'Toraja'`)

#### 8. Tabel `link_clicks`
Mencatat analitik jumlah klik pada masing-masing item tautan profil.
*   `id` — `BIGINT UNSIGNED` (PK, Auto Increment)
*   `link_id` — `BIGINT UNSIGNED` (FK ke `links.id`, `ON DELETE CASCADE`)
*   `clicked_at` — `DATETIME` (Not Null)

#### 9. Tabel `orders`
Menyimpan data pesanan *custom merchandise* dari pelanggan.
*   `id` — `BIGINT UNSIGNED` (PK, Auto Increment)
*   `order_no` — `VARCHAR(20)` (Unique Index, contoh: `FLX-2026-089`)
*   `customer_name` — `VARCHAR(100)` (Not Null)
*   `whatsapp` — `VARCHAR(30)` (Not Null)
*   `email` — `VARCHAR(150)` (Not Null)
*   `delivery_method` — `ENUM('ambil', 'kirim')` (Not Null)
*   `address` — `TEXT` (Nullable)
*   `shape` — `VARCHAR(50)` (Not Null)
*   `size` — `VARCHAR(50)` (Not Null)
*   `color` — `VARCHAR(50)` (Not Null)
*   `notes` — `TEXT` (Nullable)
*   `quantity` — `INT UNSIGNED` (Not Null)
*   `unit_price` — `DECIMAL(12,2)` (Not Null)
*   `total` — `DECIMAL(12,2)` (Not Null)
*   `status` — `ENUM('menunggu_pembayaran', 'pembayaran_dikonfirmasi', 'desain_disetujui', 'dicetak', 'dirakit_dan_diuji', 'siap_diambil_dikirim', 'selesai', 'dibatalkan')` (Default: `'menunggu_pembayaran'`)
*   `design_file_path` — `VARCHAR(255)` (Nullable)
*   `payment_proof_path` — `VARCHAR(255)` (Nullable)
*   `created_at`, `updated_at` — `TIMESTAMP`

#### 10. Tabel `order_status_logs`
Menyimpan riwayat perubahan 7 tahapan status pesanan untuk ditampilkan pada fitur Lacak Pesanan publik.
*   `id` — `BIGINT UNSIGNED` (PK, Auto Increment)
*   `order_id` — `BIGINT UNSIGNED` (FK ke `orders.id`, `ON DELETE CASCADE`)
*   `status` — `VARCHAR(50)` (Not Null)
*   `changed_at` — `DATETIME` (Not Null)
*   `note` — `TEXT` (Nullable)
*   `admin_id` — `BIGINT UNSIGNED` (FK ke `users.id`)

#### 11. Tabel `messages`
Menyimpan pesan dari penemu barang hilang, pesan kontak *landing page*, dan laporan penyalahgunaan (*abuse report*).
*   `id` — `BIGINT UNSIGNED` (PK, Auto Increment)
*   `type` — `ENUM('lost_item', 'contact', 'abuse_report')` (Not Null)
*   `user_id` — `BIGINT UNSIGNED` (FK ke `users.id`, Nullable — terisi ID pemilik tag jika `type = 'lost_item'`)
*   `sender_name` — `VARCHAR(100)` (Nullable)
*   `location` — `VARCHAR(150)` (Nullable)
*   `message` — `TEXT` (Not Null)
*   `contact` — `VARCHAR(100)` (Nullable)
*   `read_at` — `DATETIME` (Nullable, menjadi acuan indikator titik merah pada lonceng notifikasi)
*   `created_at` — `DATETIME` (Not Null)

#### 12. Tabel `designs`
Menyimpan katalog template motif lokal dan desain akrilik.
*   `id` — `BIGINT UNSIGNED` (PK, Auto Increment)
*   `name` — `VARCHAR(100)` (Not Null)
*   `category` — `ENUM('pribadi', 'organisasi', 'umkm', 'motif_lokal')` (Not Null)
*   `image_path` — `VARCHAR(255)` (Not Null)
*   `is_active` — `BOOLEAN` (Default: `true`)

#### 13. Tabel `financial_entries`
Menyimpan pencatatan kas pemasukan dan pengeluaran operasional.
*   `id` — `BIGINT UNSIGNED` (PK, Auto Increment)
*   `type` — `ENUM('pemasukan', 'pengeluaran')` (Not Null)
*   `amount` — `DECIMAL(12,2)` (Not Null)
*   `category` — `VARCHAR(100)` (Not Null)
*   `entry_date` — `DATE` (Not Null)
*   `note` — `TEXT` (Nullable)

#### 14. Tabel `settings`
Menyimpan pasangan konfigurasi global sistem (*key-value*).
*   `key` — `VARCHAR(50)` (PK, contoh: `relay_email_enabled`, `order_email_notif`, `maintenance_mode`, `cs_whatsapp`)
*   `value` — `TEXT` (Not Null)

#### 15. Tabel `audit_logs`
Mencatat jejak aktivitas administratif untuk keperluan keamanan dan audit internal.
*   `id` — `BIGINT UNSIGNED` (PK, Auto Increment)
*   `user_id` — `BIGINT UNSIGNED` (FK ke `users.id`)
*   `action` — `VARCHAR(100)` (Not Null)
*   `target` — `VARCHAR(100)` (Not Null)
*   `created_at` — `DATETIME` (Not Null)

---

### 6.2 Kode DBML (Siap Pakai untuk dbdiagram.io)

```dbml
Table users {
  id bigint [pk, increment]
  name varchar(100) [not null]
  email varchar(150) [unique, not null]
  password varchar(255)
  google_id varchar(100)
  role enum('user','staff','admin') [default: 'user']
  email_verified_at datetime
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table tags {
  id bigint [pk, increment]
  tag_code varchar(12) [unique, not null]
  activation_code varchar(255) [unique, not null]
  user_id bigint [ref: > users.id]
  status enum('belum_aktif','aktif','nonaktif') [default: 'belum_aktif']
  disabled_by enum('owner','admin')
  batch_id varchar(30)
  produced_at datetime
  activated_at datetime
}

Table profiles {
  user_id bigint [pk, ref: - users.id]
  display_name varchar(100) [not null]
  headline varchar(150)
  bio text
  photo_path varchar(255)
  theme varchar(30) [default: 'sulawesi_heritage']
  active_mode enum('kampus','kerja','umkm','hilang') [default: 'kampus']
  username varchar(20) [unique]
}

Table privacy_settings {
  user_id bigint [pk, ref: - users.id]
  show_phone boolean [default: false]
  show_email boolean [default: false]
  show_location boolean [default: false]
  indexable boolean [default: false]
}

Table links {
  id bigint [pk, increment]
  user_id bigint [ref: > users.id]
  type varchar(30) [not null]
  label varchar(100) [not null]
  url varchar(255) [not null]
  sort_order int [default: 0]
  is_visible boolean [default: true]
}

Table mode_data {
  id bigint [pk, increment]
  user_id bigint [ref: > users.id]
  mode enum('kampus','kerja','umkm','hilang') [not null]
  data json [not null]
}

Table taps {
  id bigint [pk, increment]
  tag_id bigint [ref: > tags.id]
  tapped_at datetime [not null]
  source enum('nfc','qr','link') [default: 'nfc']
  device_type varchar(50)
  region varchar(100)
}

Table link_clicks {
  id bigint [pk, increment]
  link_id bigint [ref: > links.id]
  clicked_at datetime [not null]
}

Table orders {
  id bigint [pk, increment]
  order_no varchar(20) [unique, not null]
  customer_name varchar(100) [not null]
  whatsapp varchar(30) [not null]
  email varchar(150) [not null]
  delivery_method enum('ambil','kirim') [not null]
  address text
  shape varchar(50) [not null]
  size varchar(50) [not null]
  color varchar(50) [not null]
  notes text
  quantity int [not null]
  unit_price decimal(12,2) [not null]
  total decimal(12,2) [not null]
  status enum('menunggu_pembayaran','pembayaran_dikonfirmasi','desain_disetujui','dicetak','dirakit_dan_diuji','siap_diambil_dikirim','selesai','dibatalkan') [default: 'menunggu_pembayaran']
  design_file_path varchar(255)
  payment_proof_path varchar(255)
  created_at timestamp
  updated_at timestamp
}

Table order_status_logs {
  id bigint [pk, increment]
  order_id bigint [ref: > orders.id]
  status varchar(50) [not null]
  changed_at datetime [not null]
  note text
  admin_id bigint [ref: > users.id]
}

Table messages {
  id bigint [pk, increment]
  type enum('lost_item','contact','abuse_report') [not null]
  user_id bigint [ref: > users.id]
  sender_name varchar(100)
  location varchar(150)
  message text [not null]
  contact varchar(100)
  read_at datetime
  created_at datetime [not null]
}

Table designs {
  id bigint [pk, increment]
  name varchar(100) [not null]
  category enum('pribadi','organisasi','umkm','motif_lokal') [not null]
  image_path varchar(255) [not null]
  is_active boolean [default: true]
}

Table financial_entries {
  id bigint [pk, increment]
  type enum('pemasukan','pengeluaran') [not null]
  amount decimal(12,2) [not null]
  category varchar(100) [not null]
  entry_date date [not null]
  note text
}

Table settings {
  key varchar(50) [pk]
  value text [not null]
}

Table audit_logs {
  id bigint [pk, increment]
  user_id bigint [ref: > users.id]
  action varchar(100) [not null]
  target varchar(100) [not null]
  created_at datetime [not null]
}

7. Aturan Bisnis (Business Rules)
1. BR-01 (Kepemilikan & Pengikatan Tag): Satu activation_code hanya berlaku untuk satu kali proses klaim aktivasi. Setelah sebuah tag terikat ke satu user_id, tag tersebut tidak dapat diklaim oleh akun lain kecuali dilepas atau di-reset secara eksplisit oleh Admin.
2. BR-02 (Satu Akun Banyak Merchandise): Satu pengguna (user_id) diperbolehkan mengaktifkan dan mengelola lebih dari satu perangkat fisik FlexiTag (1:N) melalui menu Merch.
3. BR-03 (Sinkronisasi Mode Tunggal): Pergantian active_mode pada Dashboard Pemilik berlaku secara instan (tanpa perlu memprogram ulang chip NFC fisik) dan menentukan tampilan yang disajikan saat salah satu tag aktif milik pengguna tersebut dipindai.
4. BR-04 (Prioritas Kill Switch & Blokir Admin): Apabila sebuah tag dinonaktifkan oleh pemilik (disabled_by = 'owner' melalui sakelar Kill Switch) atau diblokir oleh pengelola (disabled_by = 'admin' akibat laporan abuse), maka tag tersebut tidak dapat menampilkan profil publik sama sekali. Tag yang diblokir oleh admin hanya dapat diaktifkan kembali oleh admin.
5. BR-05 (Batas Katalog UMKM): Untuk menjaga kecepatan muat halaman profil publik pada perangkat seluler, setiap pengguna dibatasi maksimal menampilkan 20 produk aktif di dalam katalog Mode UMKM (mode_data).
6. BR-06 (Harga Komunitas / Grosir): Pemesanan gantungan kunci kustom dengan kuantitas $\ge$ 10 unit secara otomatis mendapatkan tarif diskon harga komunitas sesuai parameter harga yang ditetapkan di sistem.