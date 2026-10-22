# FlexiTag Design System

## 1. Tujuan desain

FlexiTag adalah platform identitas digital berbasis NFC untuk tiga kebutuhan utama: profil personal, profil UMKM, dan mode kehilangan/darurat. UI harus terasa tepercaya ketika pengguna mengelola identitas dan data pribadi, hangat ketika menampilkan produk budaya, dan tegas ketika menangani kondisi darurat.

Arah visualnya adalah **heritage technology**: kehangatan budaya dan material tradisional dipadukan dengan struktur digital yang rapi. Jangan membuat antarmuka terlihat seperti dashboard SaaS generik. Maroon menjadi identitas utama, gold menjadi aksen bernilai, cream menjadi kanvas yang tenang, dan white menjadi permukaan kerja.

## 2. Token warna

| Token | Hex | Peran |
|---|---|---|
| `--color-maroon` | `#8B0000` | Brand utama, navigasi aktif, tombol utama, status penting |
| `--color-maroon-dark` | `#650000` | Hover/pressed state, heading pada permukaan terang, overlay maroon |
| `--color-gold` | `#D4AF37` | Aksen, garis fokus, badge unggulan, ikon penting, highlight budaya |
| `--color-gold-soft` | `#F2E6B6` | Background badge dan highlight ringan |
| `--color-cream` | `#FAF9F6` | Background aplikasi dan halaman publik |
| `--color-white` | `#FFFFFF` | Card, modal, input, panel dashboard |
| `--color-charcoal` | `#1A1A1A` | Text utama |
| `--color-muted` | `#665F5A` | Text sekunder dan helper text |
| `--color-border` | `#E7E0D8` | Border halus dan separator |
| `--color-success` | `#2F6B45` | Berhasil, aktif, terkirim |
| `--color-warning` | `#9A6700` | Perhatian, pending, diproses |
| `--color-danger` | `#B3261E` | Error, diblokir, gagal |
| `--color-info` | `#315D7A` | Informasi netral |

### Aturan kontras

Text utama di atas cream/white menggunakan `#1A1A1A`. Text putih di atas maroon menggunakan `#FFFFFF`. Gold digunakan sebagai aksen visual, bukan sebagai warna body text berukuran kecil. Untuk label kecil di atas gold gunakan charcoal atau maroon gelap.

## 3. Typography

Gunakan dua family dengan peran yang jelas:

- **Display dan heading:** `Plus Jakarta Sans`, fallback `Inter`, `system-ui`, sans-serif. Weight 700–800. Bentuknya modern dan tegas untuk menyeimbangkan nuansa heritage.
- **Body dan UI:** `Inter`, fallback `system-ui`, sans-serif. Weight 400–600. Prioritaskan keterbacaan pada form, tabel, dan status.

Jangan menggunakan all caps untuk seluruh label UI. Gunakan sentence case agar antarmuka terasa ramah dan mudah dipindai.

| Token | Ukuran | Line height | Weight | Penggunaan |
|---|---:|---:|---:|---|
| `display-xl` | 48px | 1.08 | 800 | Hero heading desktop |
| `display-lg` | 40px | 1.12 | 800 | Page heading |
| `heading-1` | 32px | 1.2 | 800 | Heading section utama |
| `heading-2` | 24px | 1.25 | 700 | Card dan subsection |
| `heading-3` | 18px | 1.35 | 700 | Group heading |
| `body-lg` | 18px | 1.6 | 400 | Intro dan lead paragraph |
| `body` | 16px | 1.55 | 400 | Body default |
| `body-sm` | 14px | 1.5 | 400 | Helper text dan metadata |
| `label` | 13px | 1.3 | 600 | Label form dan badge |
| `caption` | 12px | 1.35 | 600 | Informasi tambahan |

Pada layar kecil, `display-xl` turun menjadi 36px dan `display-lg` menjadi 30px. Panjang baris ideal maksimum 75–80 karakter.

## 4. Prinsip visual

### Maroon sebagai jangkar

Maroon adalah warna tindakan dan kepercayaan. Gunakan untuk primary CTA, sidebar, header publik, active navigation, dan elemen identitas utama. Jangan menjadikan semua permukaan maroon karena akan menghilangkan hierarki.

### Gold sebagai jejak nilai

Gold menandakan sesuatu yang perlu diperhatikan: tag aktif, item unggulan, produk budaya, focus ring, atau metrik penting. Gunakan dengan hemat. Gold bukan pengganti warna error atau success.

### Cream sebagai ruang bernapas

Cream adalah latar default agar white card memiliki kedalaman. Hindari background putih penuh untuk seluruh dashboard karena akan membuat panel sulit dibedakan.

### Material budaya tanpa ornamen berlebihan

Jika membutuhkan ornamen, gunakan garis geometris tipis yang terinspirasi pola tenun secara abstrak, bukan ilustrasi dekoratif yang memenuhi layar. Ornamen harus berada di batas atau background opacity rendah dan tidak mengganggu konten.

### Satu momen khas per halaman

Setiap halaman boleh memiliki satu focal point: misalnya NFC tag visual pada halaman profil, status kehilangan pada halaman darurat, atau ringkasan batch pada halaman admin. Komponen lain tetap tenang dan fungsional.

## 5. Layout dan grid

Gunakan grid 12 kolom pada desktop dengan container maksimal 1200px. Padding horizontal desktop 32–48px, tablet 24px, mobile 16px. Jarak dasar menggunakan unit 4px dengan spacing umum 8, 12, 16, 24, 32, 48, dan 64px.

Dashboard admin memakai struktur berikut:

```text
┌──────────────────────────────────────────────────────────┐
│ Brand / breadcrumb                         profile / menu │
├───────────────┬──────────────────────────────────────────┤
│               │ Page heading              primary action  │
│  navigation   ├──────────────────────────────────────────┤
│  maroon rail  │ summary / filters / search               │
│               ├──────────────────────────────────────────┤
│               │ primary content table or cards           │
└───────────────┴──────────────────────────────────────────┘
```

Halaman publik profil/tag memakai layout berpusat dengan satu kolom utama dan maksimal lebar 680px. Identitas pemilik atau usaha muncul lebih dahulu, kemudian link/action, lalu informasi tambahan. Jangan menampilkan chrome admin pada halaman publik.

```text
       ┌──────────────────────────────┐
       │ brand mark / tag status       │
       │ avatar or logo                │
       │ name + short description     │
       │ primary contact / action     │
       │ links and useful information │
       └──────────────────────────────┘
```

Konten dashboard rata kiri. Halaman profil publik boleh menggunakan alignment tengah untuk identitas utama, tetapi detail, deskripsi, dan link tetap memiliki alignment yang konsisten dan mudah dipindai.

## 6. Navigation

Sidebar desktop berwarna maroon dengan brand lockup berwarna white dan aksen gold. Item aktif memakai permukaan maroon-dark atau white transparan yang jelas, ditambah indikator gold di sisi kiri. Pada mobile, sidebar berubah menjadi top bar dengan drawer atau bottom navigation untuk aksi inti.

Label navigasi menggunakan istilah pengguna: `Dashboard`, `Tag saya`, `Profil personal`, `Profil UMKM`, `Mode hilang`, `Pesanan`, `Pengaturan`. Hindari label teknis seperti `NFC Entity` atau `Relay Configuration`.

## 7. Buttons

Primary button: maroon `#8B0000` dengan text white. Hover memakai maroon-dark. Dipakai untuk aksi utama seperti `Simpan perubahan`, `Aktifkan tag`, dan `Buat batch`.

Secondary button: white dengan border maroon dan text maroon. Dipakai untuk aksi alternatif yang tetap penting.

Tertiary button: tanpa background, text maroon, dengan underline atau perubahan weight saat hover. Dipakai untuk navigasi ringan.

Danger button: gunakan danger red hanya untuk tindakan destruktif atau status berbahaya, misalnya `Blokir tag`. Jangan menggunakan maroon sebagai satu-satunya sinyal bahaya karena maroon adalah warna brand.

Semua button memiliki minimum height 44px, radius 8px, padding horizontal 16–20px, dan focus ring gold 3px dengan offset 2px. Text button menggunakan sentence case dan menjelaskan hasil aksi.

## 8. Cards, tables, and surfaces

Card putih memiliki border `1px solid var(--color-border)`, radius 12px, dan shadow sangat ringan `0 4px 18px rgba(26, 26, 26, 0.06)`. Jangan menumpuk banyak card identik; gunakan grouping dan whitespace untuk menunjukkan hierarchy.

Card dengan informasi identitas dapat memiliki top accent line 3px maroon atau gold, tetapi gunakan maksimal satu accent per card.

Table digunakan untuk data admin seperti batch, tag, pesanan, dan analytics. Header table menggunakan cream yang sedikit lebih gelap atau maroon dengan text white untuk table yang sangat penting. Row hover menggunakan gold-soft. Status ditampilkan sebagai badge teks, bukan hanya warna.

Mobile table diubah menjadi stacked rows atau card list. Jangan memaksa tabel desktop menjadi horizontal overflow tanpa label yang jelas.

## 9. Form dan input

Label selalu berada di atas input dan menjelaskan data yang diminta. Gunakan helper text untuk format, contoh, atau konsekuensi privasi. Input memiliki tinggi minimum 44px, border `#CFC6BC`, radius 8px, background white, dan padding 12px 14px.

Focus state: border maroon dan focus ring gold. Error state: border danger dan pesan error spesifik di bawah field. Jangan hanya mengandalkan warna; sertakan ikon atau teks.

Untuk data sensitif seperti nomor publik, lokasi GPS, dan relay aman, gunakan copy yang menjelaskan siapa yang dapat melihat data tersebut.

## 10. Status dan feedback

Gunakan kombinasi warna, icon, dan teks:

| Status | Warna | Contoh copy |
|---|---|---|
| Berhasil | success | `Perubahan tersimpan` |
| Pending | warning | `Menunggu diproses` |
| Informasi | info | `Tag belum diaktifkan` |
| Gagal | danger | `Pesan belum terkirim` |
| Darurat | maroon-dark + danger accent | `Mode hilang aktif` |

Toast hanya untuk feedback singkat. Untuk error form atau kondisi yang membutuhkan tindakan, gunakan inline alert yang tetap terlihat.

## 11. Mode hilang dan kondisi darurat

Mode hilang memiliki visual yang berbeda namun tetap berada di dalam brand. Gunakan maroon yang lebih pekat sebagai header/status strip, aksen gold untuk instruksi utama, dan danger red hanya untuk tindakan berisiko. Pesan harus langsung dan tidak dramatis.

Contoh copy: `Tag ini sedang berada dalam mode hilang.` Kemudian tampilkan tindakan aman seperti `Hubungi pemilik melalui relay` dan form pesan penemu. Jangan menampilkan nomor pribadi jika pengaturan privasi melarangnya.

## 12. Empty, loading, dan error states

Empty state harus menjelaskan kondisi dan langkah berikutnya. Contoh: `Belum ada tag di akun ini. Buat atau aktifkan tag pertama Anda.` Sertakan satu CTA yang relevan.

Loading state memakai skeleton berwarna cream dan border lembut, bukan spinner besar untuk seluruh halaman. Error state menjelaskan objek yang gagal dan aksi pemulihan, misalnya `Data pesanan belum dapat dimuat. Coba lagi.`

## 13. Responsive behavior

Breakpoint rekomendasi: 640px mobile-large, 768px tablet, 1024px desktop-small, 1280px desktop. Pada mobile, kurangi dekorasi, pertahankan hierarki, dan jadikan primary action mudah dijangkau. Jangan mengecilkan text sampai sulit dibaca.

## 14. Accessibility

Target minimum WCAG 2.1 AA. Semua kontrol keyboard-accessible dan focus state terlihat. Jangan memakai gold sebagai satu-satunya indikator. Gunakan `aria-label` untuk icon-only button. Pertahankan urutan heading yang logis. Pastikan modal memiliki focus trap dan dapat ditutup dengan Escape. Hormati `prefers-reduced-motion`.

## 15. Motion

Motion hanya digunakan untuk menjelaskan perubahan state: drawer terbuka, toast muncul, status tag berubah, atau panel detail terbuka. Durasi umum 160–220ms dengan easing lembut. Hindari animasi dekoratif berulang, parallax, dan efek bounce yang tidak membantu pemahaman.

## 16. Iconography dan imagery

Gunakan icon line yang sederhana dengan stroke konsisten 1.75–2px. Icon harus memperkuat label, bukan menggantikannya. Untuk imagery produk, gunakan crop yang rapi dengan background cream dan aksen gold. Hindari stok foto korporat generik.

Jika menampilkan pattern budaya, gunakan sebagai watermark dengan opacity rendah dan jangan mengklaim motif spesifik tanpa sumber yang valid. Visual produk `pateddong`, `saqbe`, `sulapa eppa`, dan `pabarre allo` harus diberi label yang jelas.

## 17. CSS token starter

```css
:root {
  --color-maroon: #8B0000;
  --color-maroon-dark: #650000;
  --color-gold: #D4AF37;
  --color-gold-soft: #F2E6B6;
  --color-cream: #FAF9F6;
  --color-white: #FFFFFF;
  --color-charcoal: #1A1A1A;
  --color-muted: #665F5A;
  --color-border: #E7E0D8;
  --color-success: #2F6B45;
  --color-warning: #9A6700;
  --color-danger: #B3261E;
  --color-info: #315D7A;

  --font-display: 'Plus Jakarta Sans', Inter, system-ui, sans-serif;
  --font-body: Inter, system-ui, sans-serif;

  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --shadow-card: 0 4px 18px rgba(26, 26, 26, 0.06);
  --focus-ring: 0 0 0 3px var(--color-gold);
}
```

## 18. Review checklist

Sebelum halaman dianggap selesai, pastikan halaman memakai token warna, memiliki hierarchy maroon-gold-cream yang jelas, tidak menggunakan gold untuk text kecil, memiliki focus state, dapat dipakai di mobile, memiliki loading/empty/error state, menggunakan copy sentence case, dan tidak menambahkan dekorasi yang tidak membantu tugas pengguna.
