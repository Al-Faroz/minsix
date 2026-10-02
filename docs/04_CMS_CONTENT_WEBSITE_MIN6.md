# 04 — CMS CONTENT WEBSITE MIN 6 JEMBER

## 1. Prinsip CMS

CMS dibuat khusus untuk MIN 6 JEMBER.

Prinsip:
- sederhana;
- form jelas;
- tidak teknis;
- tanpa page builder;
- layout website tidak dapat diacak-acak Operator;
- konten terstruktur;
- image-first;
- mobile manager tetap usable, desktop menjadi prioritas utama.

---

## 2. Dashboard

Dashboard tidak perlu grafik dekoratif.

Tampilkan:
- jumlah berita;
- jumlah prestasi;
- agenda mendatang;
- jumlah GTK aktif;
- konten terbaru;
- quick action.

Contoh:

```text
Selamat Datang

18 Berita
42 Prestasi
3 Agenda Mendatang
36 GTK

KONTEN TERBARU
...

AKSI CEPAT
[+ Berita]
[+ Prestasi]
[+ Agenda]
[Update SPMB]
```

Feature yang OFF dapat diberi label internal, bukan dihilangkan dari CMS.

---

## 3. Beranda

CMS Beranda tidak berupa page builder.

### Hero
- eyebrow;
- heading;
- subheading;
- foto hero;
- primary CTA label/URL;
- secondary CTA label/URL.

### Statistik
- siswa;
- GTK;
- rombel;
- prestasi;
- label masing-masing.

Statistik boleh dikosongkan jika data belum valid.

### Tentang
- judul;
- ringkasan;
- foto;
- CTA.

### Pembiasaan
Baseline:
- sambut pagi;
- upacara;
- salat Dhuha;
- Asmaul Husna;
- surat pendek;
- doa;
- Zuhur berjamaah.

### Sambutan Kepala
- headline;
- kutipan;
- isi pendek;
- foto;
- link profil.

---

## 4. Profil

Section:
- Tentang;
- Visi;
- Sejarah;
- Timeline;
- Sambutan Kepala;
- Identitas;
- Lokasi.

Operator mengedit konten, tetapi tidak mengubah struktur section.

---

## 5. Program

Field:

```text
Nama
Slug
Kategori
Ringkasan
Isi
Foto utama
Urutan
Status
```

Kategori awal:
- Pembelajaran
- Pembiasaan/karakter
- Keagamaan
- Yanbu'a
- Ekstrakurikuler
- Pengembangan Prestasi

---

## 6. GTK

### Master GTK

```text
Nama
Gelar depan
Gelar belakang
Foto
Bio singkat
Urutan tampil
Aktif/Tidak
```

### Role/Jabatan
Satu GTK dapat memiliki banyak role.

Kategori:
- Pimpinan
- Guru Kelas
- Guru Mapel
- Tenaga Kependidikan

UI harus mencegah duplikasi orang yang sama hanya karena beda jabatan.

---

## 7. Berita

Field:

```text
Judul
Slug
Tanggal publish
Ringkasan
Isi
Foto utama
Meta title (opsional)
Meta description (opsional)
OG image (opsional)
Status DRAFT/PUBLISHED
```

Slug otomatis dari judul tetapi dapat diedit.

Konten memakai rich text editor sederhana.

---

## 8. Agenda

Field:

```text
Judul
Slug
Tanggal/jam mulai
Tanggal/jam selesai
Lokasi
Ringkasan
Deskripsi
Foto
Status
```

Homepage mengambil agenda mendatang terdekat.

---

## 9. Prestasi

Field:

```text
Judul
Slug
Nama peserta/tim
Bidang
Juara/Penghargaan
Tingkat
Penyelenggara
Tanggal
Ringkasan
Isi
Foto
Status
```

---

## 10. Galeri

### Album

```text
Judul
Slug
Tanggal
Deskripsi
Cover
Status
```

### Foto
- multi upload/pilih dari media;
- caption opsional;
- urutan dapat diubah.

Frontend memakai lightbox.

---

## 11. Kabar Madrasah

`Kabar Madrasah` adalah group presentation untuk:
- Berita;
- Agenda;
- Prestasi;
- Galeri.

Feature visibility diatur Admin.

Operator dapat tetap membuat konten meskipun publik sedang OFF.

---

## 12. SPMB

### Period

```text
Tahun Ajaran
Judul
Ringkasan
Tanggal buka
Tanggal tutup
Isi
Link pendaftaran
QR
Brosur
Narahubung
Nomor kontak
Status
Current
```

### Persyaratan
Repeatable ordered list.

### FAQ

```text
Pertanyaan
Jawaban
Urutan
```

Hanya satu periode current pada satu waktu.

---

## 13. Media Manager

Kemampuan:
- upload gambar;
- upload PDF;
- preview;
- alt text;
- caption;
- pilih media dari modal;
- reuse media;
- pencarian nama;
- filter tipe.

Jangan upload file yang sama setiap kali satu foto dipakai ulang.

---

## 14. Instagram Content

Untuk fallback/manual:

```text
Thumbnail/Gambar lokal
Caption
Permalink Instagram
Tanggal
Urutan
Visible
Fallback
```

Operator dapat mengelola fallback.

Admin mengelola:
- koneksi/token;
- source mode;
- refresh policy;
- konfigurasi teknis.

---

## 15. Draft dan Published

Semua konten editorial memakai:

```text
DRAFT
PUBLISHED
```

Operator boleh publish langsung.

---

## 16. Delete UX

Sebelum delete:
- konfirmasi;
- jelaskan nama data;
- jika berelasi, jangan hapus diam-diam.

Bulk delete hanya pada modul yang memang membutuhkannya.

---

## 17. DataTable CMS

Untuk tabel panjang:
- pagination;
- search;
- sort seperlunya;
- mobile horizontal scroll;
- aksi mudah dijangkau;
- jumlah kolom tidak berlebihan.

Modul yang dapat memakai DataTable:
- GTK;
- Berita;
- Agenda;
- Prestasi;
- Galeri;
- Media;
- Users.

Frontend publik tidak menggunakan DataTable.

---

## 18. Form UX

Standar:
- label jelas;
- helper text singkat;
- validation error dekat field;
- required diberi tanda;
- preview foto;
- tombol Simpan/Kembali konsisten;
- form panjang dikelompokkan per section.

Bahasa UI menggunakan Bahasa Indonesia umum.

---

## 19. Konten Baseline dari PDF

### Visi
**Berakhlaqul Karimah dan Berprestasi**

### Kepala Madrasah
**Eko Iswanto, S.Pd., M.Pd.**

### Pembiasaan
- sambut siswa pagi;
- upacara Senin;
- salat Dhuha;
- Asmaul Husna;
- surat pendek;
- doa sebelum belajar;
- Zuhur berjamaah.

### Pembelajaran
- pembelajaran di kelas;
- metode mengaji Yanbu'a;
- pembiasaan menulis/buku kotak.

### Ekstrakurikuler
- Yanbu'a;
- Pramuka;
- bola voli;
- Bahasa Inggris;
- pidato Bahasa Indonesia;
- pidato Bahasa Arab;
- puisi;
- pencak silat;
- mewarnai;
- tahfidz;
- tilawah;
- badminton;
- catur;
- tenis meja;
- menyanyi;
- hadrah;
- tari;
- kaligrafi;
- akademik IPA dan Matematika.

Daftar ini baseline konten, bukan pembatas fitur.

---

## 20. Batas CMS

CMS tidak menyediakan:
- custom HTML bebas untuk layout;
- CSS editor;
- drag/drop page builder;
- custom JavaScript;
- route editor;
- permission editor;
- database manager.

Tujuannya menjaga desain dan keamanan tetap konsisten.
