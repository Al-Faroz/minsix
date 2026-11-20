# Pemasangan aset demo MIN 6 Jember

Setelah mengambil perubahan terbaru dari GitHub, jalankan satu kali dari root proyek:

```bash
php spark minsix:assets:install
```

Perintah ini akan:
- mengekstrak paket logo dan favicon ke `assets/brand/`;
- memasang 25 foto GTK serta 11 media kegiatan/SPMB ke `public/uploads/demo/`;
- mendaftarkan atau memperbarui metadata media berdasarkan `relative_path` sehingga aman dijalankan ulang;
- menautkan foto GTK ke data GTK yang namanya cocok dan belum memiliki foto;
- mengisi data simulasi SPMB tahun pelajaran 2027/2028.

Data SPMB dibuat dengan status `DRAFT` dan `is_current=0`, sehingga tidak dipublikasikan ke halaman umum. Data periode SPMB lain tidak diubah.

Syarat: PHP CLI dengan ekstensi `zip`, `fileinfo`, dan koneksi database CMS yang sudah aktif.

Catatan: foto GTK dalam paket adalah aset awal hasil ekstraksi dokumen. Verifikasi kesesuaian foto sebelum digunakan sebagai dokumentasi publik.
