# 01 — MASTERPLAN WEBSITE MIN 6 JEMBER

## 1. Status Dokumen
- **Nama proyek:** Website MIN 6 Jember / MIN SIX
- **Framework:** CodeIgniter 4
- **Folder lokal:** `G:\xampp\htdocs\minsix`
- **Repository:** `https://github.com/Al-Faroz/minsix`
- **Database:** MySQL/MariaDB
- **Sumber schema database:** SQL dump
- **Migration:** Tidak digunakan
- **Seeder:** Tidak digunakan
- **Status dokumen:** Acuan implementasi
- **Prinsip utama:** **Frontend premium, CMS sederhana**

Dokumen ini menjadi acuan induk. Jika implementasi berbeda dengan dokumen ini, keputusan harus diperbarui di dokumen terlebih dahulu atau dicatat sebagai revisi resmi.

---

## 2. Tujuan Proyek

Membangun website resmi MIN 6 Jember yang:
1. modern, minimalis, responsif, dan mudah digunakan;
2. berfungsi sebagai representasi resmi dan identitas digital madrasah;
3. mengutamakan **homepage/landing page** sebagai pusat pengalaman pengunjung;
4. memiliki CMS sederhana untuk pengelolaan konten tanpa page builder;
5. mudah dirawat oleh Admin dan Operator non-programmer;
6. ringan dan cocok untuk shared hosting;
7. memiliki dukungan berita/kabar yang dapat diaktifkan atau dinonaktifkan;
8. mendukung informasi SPMB yang dapat diperbarui tiap tahun;
9. menampilkan Instagram resmi `@min6jember` dengan pola hybrid;
10. memiliki pengelolaan media, SEO dasar, keamanan, caching, dan optimasi gambar.

---

## 3. Karakter Website

Website **bukan** portal administrasi sekolah dan **bukan** CMS generik seperti WordPress.

Karakter yang dikunci:
- modern;
- minimalis;
- editorial;
- institusional;
- humanis;
- fotografi asli sebagai elemen visual utama;
- typography besar dan tegas;
- whitespace cukup;
- animasi ringan;
- mobile-friendly.

Referensi visual utama sementara adalah **Rival — BootstrapMade**, tetapi hanya sebagai referensi sistem desain, ritme section, hero, typography, statistik, dan portfolio/editorial grid. Source code, asset, dan elemen berlisensi tidak boleh disalin tanpa memperhatikan lisensinya.

---

## 4. Sumber Konten dan Prioritas Data

Urutan sumber kebenaran konten:
1. **PDF/dokumen internal terbaru MIN 6 Jember**
2. **Data internal madrasah**
3. **Sumber resmi pemerintah (Kemenag/Kemendikdasmen)**
4. **Website resmi/arsip MIN 6 Jember**
5. **Sumber internet lain sebagai pelengkap**

Jika terjadi konflik, data internal/PDF terbaru menjadi acuan utama.

### Baseline konten yang sudah dikunci
- Visi: **Berakhlaqul Karimah dan Berprestasi**
- Kepala Madrasah: **Eko Iswanto, S.Pd., M.Pd.**
- Pembiasaan utama:
  - sambut siswa setiap pagi;
  - upacara bendera Senin;
  - salat Dhuha;
  - pembacaan Asmaul Husna;
  - membaca surat-surat pendek;
  - doa sebelum belajar;
  - salat Zuhur berjamaah.
- Pembelajaran Al-Qur'an menggunakan metode **Yanbu'a**.
- Ekstrakurikuler mencakup bidang keagamaan, akademik/bahasa, olahraga, seni/kreativitas, serta Pramuka.
- Instagram resmi: **@min6jember**.

Data statistik seperti jumlah siswa, jumlah GTK, jumlah rombel, dan jumlah prestasi harus divalidasi dari data internal terbaru sebelum ditampilkan sebagai angka publik.

---

## 5. Arsitektur Besar

```text
PUBLIC WEBSITE
       │
       ├── Homepage / Landing Page
       ├── Profil
       ├── Program
       ├── GTK
       ├── Kabar Madrasah (opsional)
       ├── SPMB (opsional)
       └── Detail konten

CMS / MANAGER
       │
       ├── Dashboard
       ├── Konten Beranda
       ├── Profil
       ├── Program
       ├── GTK
       ├── Kabar Madrasah
       ├── SPMB
       ├── Media
       ├── Instagram Content
       └── Pengaturan (Admin)
```

Tidak menggunakan SPA. Frontend utama bersifat server-rendered oleh CI4.

---

## 6. Sitemap Publik

Navbar dibuat seminimal mungkin:

```text
Beranda
Profil
Program
GTK
Kabar Madrasah *
SPMB *
```

`*` dapat disembunyikan berdasarkan konfigurasi Admin.

Kontak tidak membutuhkan halaman khusus; tampil pada homepage/footer.

### URL utama

```text
/
/profil
/program
/gtk
/kabar
/spmb
```

Detail konten:

```text
/kabar/berita/{slug}
/kabar/prestasi/{slug}
/kabar/agenda/{slug}
/kabar/galeri/{slug}
```

Route publik mengikuti status modul. Jika modul dinonaktifkan, data tidak dihapus tetapi route publik modul tidak tersedia.

---

## 7. Homepage — Prioritas Utama

Urutan awal:
1. **Hero**
2. **Statistik MIN 6 Jember**
3. **Mengenal MIN 6 Jember**
4. **Keseharian yang Membentuk Karakter**
5. **Program / Belajar & Berkembang**
6. **Prestasi Peserta Didik**
7. **Cerita dari Madrasah / kegiatan visual**
8. **Kabar Terbaru** — opsional
9. **Instagram @min6jember** — opsional
10. **Sambutan Kepala Madrasah**
11. **SPMB CTA** — opsional
12. **Kontak**
13. **Footer**

Layout homepage dikunci oleh programmer. CMS hanya mengubah kontennya.

---

## 8. Konsep Hero

Arah copy sementara:

```text
MADRASAH IBTIDAIYAH NEGERI 6 JEMBER

Berakhlakul Karimah.
Tumbuh dalam Prestasi.

Lingkungan belajar untuk menumbuhkan ilmu, karakter,
kreativitas, dan nilai-nilai keislaman sejak usia dasar.
```

CTA:

```text
[Jelajahi Madrasah]
Kenali MIN 6 Jember →
```

Hero menggunakan foto asli MIN 6 Jember, bukan ilustrasi stok sebagai elemen utama.

---

## 9. Halaman Profil

Satu halaman:
1. Tentang MIN 6 Jember
2. Visi
3. Sejarah
4. Timeline perkembangan madrasah
5. Sambutan Kepala Madrasah
6. Identitas Madrasah
7. Lokasi

Hindari terlalu banyak submenu profil.

---

## 10. Halaman Program

Satu halaman dengan section:
- Pembelajaran
- Pembiasaan dan Karakter
- Keagamaan
- Yanbu'a
- Ekstrakurikuler
- Pengembangan Prestasi

Program dapat mempunyai detail jika konten benar-benar memerlukan halaman detail, tetapi default-nya satu halaman.

---

## 11. Halaman GTK

Kategori:
- Pimpinan
- Guru Kelas
- Guru Mata Pelajaran
- Tenaga Kependidikan

Prinsip data:
> **Satu orang = satu master GTK.**

Satu GTK dapat memiliki lebih dari satu role/jabatan.

---

## 12. Kabar Madrasah

Kabar Madrasah mencakup:
- Berita
- Agenda
- Prestasi
- Galeri

Fitur ini memiliki sistem ON/OFF.

### Aturan ON/OFF
- OFF **tidak menghapus data**.
- CMS tetap dapat mengelola data walaupun modul tidak tampil di publik.
- Hanya Admin yang dapat mengubah ON/OFF.
- Operator tidak boleh mengubah konfigurasi visibility.
- Homepage dan navbar mengikuti konfigurasi fitur.

Parent `Kabar Madrasah` dapat dinonaktifkan secara keseluruhan. Submodul juga dapat memiliki kontrol visibility masing-masing.

---

## 13. SPMB

SPMB memiliki halaman khusus yang diperbarui per tahun.

Konten:
- tahun ajaran;
- periode pendaftaran;
- deskripsi;
- syarat pendaftaran;
- alur;
- program unggulan;
- link pendaftaran;
- QR;
- narahubung;
- FAQ;
- brosur PDF;
- media/foto.

URL publik tetap `/spmb`.

---

## 14. Instagram

Akun resmi:

```text
@min6jember
https://www.instagram.com/min6jember
```

Model integrasi: **Hybrid**

```text
Instagram/API
      ↓
Local Cache
      ↓
Carousel Homepage
      ↓
Fallback Manual jika source gagal
```

Homepage tidak boleh melakukan request berat ke Instagram pada setiap page load.

---

## 15. Bahasa Konten

Gaya bahasa publik: **Modern-humanis**.

Ciri:
- resmi tetapi hangat;
- mudah dibaca;
- tidak birokratis;
- tidak menggunakan klaim marketing berlebihan;
- narasi singkat dan kuat;
- data formal tetap menggunakan bahasa resmi.

---

## 16. Role Sistem

Hanya ada dua role:

### ADMIN
Mengelola **sistem + konten**.

### OPERATOR
Mengelola **konten saja**.

---

## 17. Database

Database menggunakan **SQL dump**.

File canonical:

```text
/database/minsix.sql
```

Tidak menggunakan Migration dan Seeder.

Perubahan schema harus:
1. dijalankan pada database pengembangan;
2. diperbarui pada SQL dump canonical;
3. dicatat bila berdampak besar.

Repository publik tidak boleh menyimpan password database, token Instagram, API key, credential, atau password akun produksi.

---

## 18. Stack

- CodeIgniter 4
- PHP sesuai requirement CI4 versi yang digunakan
- MySQL/MariaDB
- Bootstrap 5
- Vanilla JavaScript
- Swiper.js jika diperlukan
- Lightbox ringan
- Rich text editor sederhana
- CI4 Image Manipulation bila sesuai

---

## 19. Tahapan Implementasi

```text
PHASE 0 — Dokumen Acuan
PHASE 1 — Skeleton CI4 + Auth + Manager Layout
PHASE 2 — Settings + Features + Media
PHASE 3 — Profil + Program + GTK
PHASE 4 — Kabar: Berita / Agenda / Prestasi / Galeri
PHASE 5 — SPMB
PHASE 6 — Homepage / Frontend
PHASE 7 — Instagram Hybrid
PHASE 8 — SEO + Performance + Security
PHASE 9 — Responsive & Functional Audit
PHASE 10 — Production Deployment
```

---

## 20. Keputusan yang Dikunci

1. CI4 sebagai environment utama.
2. Database via SQL dump, tanpa Migration dan Seeder.
3. Role hanya Admin dan Operator.
4. Operator hanya mengubah konten.
5. Homepage menjadi fokus utama.
6. Jumlah halaman publik dibuat minimal.
7. Kabar/Berita memiliki ON/OFF.
8. Data tetap tersimpan ketika modul OFF.
9. GTK berada pada halaman terpisah.
10. SPMB memiliki halaman khusus dan dapat diperbarui tahunan.
11. Instagram memakai pola hybrid.
12. Gaya bahasa modern-humanis.
13. Layout tidak dikendalikan oleh page builder.
14. Rival digunakan sebagai referensi desain, bukan clone.
