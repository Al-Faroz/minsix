# 05 — FRONTEND & UI WEBSITE MIN 6 JEMBER

## 1. Arah Visual

Karakter:
- modern;
- minimalis;
- editorial;
- institusional;
- humanis;
- premium tanpa terlihat mewah berlebihan.

Referensi utama yang **dikunci**: **Rival — BootstrapMade**.

Yang wajib diadaptasi secara konsisten:
- hero split dan dark hero;
- hierarchy tipografi besar;
- header/nav ringkas;
- statistik horizontal;
- numbered services;
- editorial portfolio/media grid;
- whitespace luas;
- ritme dark/light;
- tombol pill/CTA;
- motion reveal ringan;
- responsive behavior yang terasa native, bukan sekadar mengecilkan desktop.

Implementasi MIN SIX **tidak menyalin asset/template Rival**. Yang ditiru adalah sistem visual, komposisi, hierarchy, spacing, dan interaction pattern.

Yang tidak ditiru mentah:
- identitas agency;
- visual teknologi;
- neon berlebihan;
- source/assets berlisensi.

---

## 2. Palet Awal

```text
Dark / Deep Green     #0D1713
Primary Green         #176B45
Emerald Accent        #24A56A
Soft Lime Accent      #A8D94F
Warm White            #F7F8F3
Charcoal              #18211D
Neutral Border        #E5E8E4
```

Warna dapat disempurnakan pada phase frontend.

---

## 3. Ritme Halaman

```text
Hero                  DARK
Stats                 DARK
About                 LIGHT
Programs              LIGHT
Achievements/Stories  DARK / CONTRAST
News/Agenda           LIGHT
Instagram             LIGHT
SPMB CTA              GREEN/DARK
Footer                DARK
```

---

## 4. Typography

PHASE 9 mengunci hierarchy ala BootstrapMade/Rival dengan stack:
- **Body:** Roboto;
- **Heading:** Raleway;
- **Navigation/label:** Poppins.

Font di-load dari Google Fonts dengan fallback system sans-serif. Heading desktop menggunakan `clamp()`, tight line-height, dan negative letter-spacing secukupnya agar proporsi mendekati Rival.

Jika pada audit browser ditemukan Rival memakai family berbeda, font dapat dikoreksi tanpa mengubah struktur layout karena seluruh typography sudah memakai CSS variable.

---

## 5. Navbar

Desktop:

```text
[LOGO]   Beranda Profil Program GTK Kabar Madrasah   [SPMB]
```

Kabar dan SPMB mengikuti feature settings.

Mobile:
- logo;
- hamburger;
- drawer/overlay sederhana;
- CTA SPMB jika aktif.

---

## 6. Hero

### Desktop
Split composition:
- text 50–60%;
- image 40–50%;
- statistik dapat berada di bawah hero.

Foto:
- aktivitas siswa;
- lingkungan madrasah terlihat;
- hindari pose formal sebagai satu-satunya hero;
- sisakan negative space.

### Mobile
- heading tetap dominan;
- image menyesuaikan komposisi;
- CTA mudah ditekan;
- statistik menjadi 2x2 atau pola responsive lain.

---

## 7. Homepage Structure

```text
01 Hero
02 Stats
03 Mengenal MIN 6 JEMBER
04 Keseharian yang Membentuk Karakter
05 Belajar & Berkembang
06 Prestasi
07 Cerita dari Madrasah
08 Kabar Terbaru *
09 Instagram *
10 Sambutan Kepala Madrasah
11 SPMB CTA *
12 Kontak
13 Footer
```

`*` mengikuti feature toggle.

---

## 8. Program Section

Hindari puluhan icon card.

Gunakan gaya bernomor:

```text
01 Akademik
02 Keagamaan
03 Karakter
04 Literasi / Bahasa
05 Kreativitas
06 Prestasi
```

Atau kelompok program aktual sesuai data.

---

## 9. Prestasi

Homepage:
- 4–6 prestasi terpilih/terbaru;
- visual besar;
- headline singkat;
- kategori/tahun;
- CTA.

---

## 10. Cerita dari Madrasah

Gaya editorial/masonry terkontrol.

Kategori visual:
- Pembelajaran;
- Keagamaan;
- Prestasi;
- Kegiatan;
- Ekstrakurikuler.

---

## 11. Kabar

Jika aktif:
- homepage maksimal 3 berita terbaru;
- judul kuat;
- tanggal;
- summary singkat;
- CTA.

Jika OFF:
- section tidak dirender;
- tidak meninggalkan ruang/heading kosong.

---

## 12. Instagram

Carousel:
- desktop 4 item;
- tablet 2–3;
- mobile 1–2;
- rasio dominan 1:1 / 4:5;
- lazy load;
- klik menuju permalink Instagram.

---

## 13. Sambutan Kepala

Format editorial:
- portrait/landscape foto berkualitas;
- kutipan pendek;
- 2–3 paragraf ringkas;
- CTA ke Profil.

---

## 14. SPMB CTA

Jika aktif:

```text
Mari tumbuh bersama MIN 6 JEMBER.
[Informasi SPMB]
```

Jika OFF, section tidak dirender.

---

## 15. Responsive Standard

Breakpoint Bootstrap 5 sebagai baseline.

### Desktop
- whitespace luas;
- grid editorial;
- heading besar;
- max-width konsisten.

### Tablet
- jumlah kolom dikurangi;
- tidak memaksakan desktop 4-column.

### Mobile
- satu kolom dominan;
- font responsive;
- tombol nyaman ditekan;
- foto tidak memotong wajah/subjek;
- tidak ada horizontal overflow kecuali carousel disengaja.

Mobile bukan desktop yang sekadar dikecilkan.

---

## 16. Accessibility Baseline

- semantic heading;
- alt text;
- contrast cukup;
- focus state;
- keyboard-friendly;
- aria label untuk icon-only control;
- form label nyata;
- status tidak hanya bergantung warna.

---

## 17. Motion

Gunakan ringan:
- fade;
- translate;
- reveal;
- hover image scale kecil.

Hindari parallax berat, autoplay video hero, dan animasi terus-menerus.

---

## 18. Daftar Foto yang Diperlukan

| Kebutuhan | Jumlah ideal | Orientasi | Rasio |
|---|---:|---|---|
| Hero siswa + lingkungan | 4–6 | Landscape | 16:9 / 3:2 |
| Tampak madrasah | 3 | Landscape/Wide | 16:9 |
| Lapangan/halaman | 2–3 | Wide | 16:9 / 21:9 |
| Sambut siswa pagi | 3 | Landscape | 3:2 |
| Upacara | 3 | Wide | 16:9 |
| Salat Dhuha | 2–3 | Landscape | 3:2 |
| Asmaul Husna/doa | 2 | Landscape | 3:2 |
| Salat berjamaah | 2 | Wide | 16:9 |
| Yanbu'a | 3–4 | Landscape | 3:2 |
| Pembelajaran kelas | 4–6 | Landscape | 3:2 |
| Interaksi guru-siswa | 3 | Landscape | 3:2 |
| Detail menulis/karya | 3 | Landscape/detail | 3:2 |
| Pramuka | 3 | Landscape | 3:2 |
| Olahraga | 4–6 | Landscape | 3:2 |
| Seni/Hadrah/Tari | 4–6 | Landscape | 3:2 |
| Tahfidz/Tilawah | 3 | Landscape | 3:2 |
| Kompetisi akademik | 2–3 | Landscape | 3:2 |
| Siswa + piala/sertifikat | 8–12 | Portrait | 4:5 |
| Tim pemenang | 4–6 | Landscape | 3:2 |
| Kepala Madrasah resmi | 1–2 | Portrait | 4:5 |
| Kepala di lingkungan sekolah | 2 | Landscape | 3:2 |
| PKM | 1/orang | Portrait | 4:5 |
| Guru | 1/orang | Portrait | 4:5 |
| Tendik | 1/orang | Portrait | 4:5 |
| GTK bersama | 2 | Wide | 16:9 |
| Ruang kelas | 2 | Landscape | 3:2 |
| Perpustakaan | 2 | Landscape | 3:2 |
| Tempat ibadah | 2 | Landscape | 3:2 |
| Fasilitas lain | 1–2/fasilitas | Landscape | 3:2 |
| SPMB | 4–6 | Landscape | 3:2 |

---

## 19. Standar Foto GTK

Disarankan foto ulang seragam:
- 4:5;
- kamera sejajar mata;
- setengah badan;
- background konsisten;
- cahaya lembut;
- framing konsisten.

---

## 20. Foto Anak

Untuk publikasi anak:
- gunakan dokumentasi yang memang boleh dipublikasikan;
- ikuti kebijakan izin madrasah;
- hindari informasi pribadi sensitif;
- hero lebih baik natural/candid terkontrol.

---

## 21. Frontend Performance

- lazy load gambar di bawah fold;
- responsive images bila tersedia;
- WebP;
- CSS/JS minimum;
- defer script non-kritis;
- hindari embed berat;
- cache output yang sesuai;
- Instagram memakai cache lokal.

---

## 22. UI Konsistensi

Gunakan token/variabel untuk:
- spacing;
- radius;
- border;
- typography;
- container width;
- colors.

---

## 23. Footer

Isi:
- logo;
- nama madrasah;
- alamat;
- telepon/WhatsApp;
- email;
- Instagram;
- quick links;
- copyright.

Google Maps dapat ditempatkan pada section Contact atau berupa link arah.
