# 03 — AUTH, RBAC & MENU WEBSITE MIN 6 JEMBER

## 1. Prinsip

Sistem hanya memiliki dua role:

```text
ADMIN
OPERATOR
```

Aturan inti:

> **ADMIN = SISTEM + KONTEN**

> **OPERATOR = KONTEN SAJA**

Tidak menggunakan RBAC kompleks berbasis permission table pada versi awal karena hanya ada dua role dengan batas yang sangat jelas.

---

## 2. Login Manager

Route yang disarankan:

```text
/manager
/manager/login
/manager/logout
```

Jika belum login:
- `/manager/*` diarahkan ke login.

Jika sudah login:
- `/manager/login` diarahkan ke dashboard.

Session menggunakan mekanisme CI4 yang aman sesuai environment.

---

## 3. ADMIN

Admin memiliki seluruh akses.

### Sistem
- kelola user;
- tambah Operator;
- edit user;
- aktif/nonaktif user;
- reset password;
- pengaturan website;
- identitas website;
- feature ON/OFF;
- show/hide navbar;
- show/hide homepage section;
- konfigurasi Instagram/API;
- SEO global;
- konfigurasi sistem yang disediakan CMS;
- maintenance/backup jika modul disediakan.

### Konten
- Beranda;
- Profil;
- Program;
- GTK;
- Berita;
- Agenda;
- Prestasi;
- Galeri;
- SPMB;
- Media;
- Instagram fallback/manual.

---

## 4. OPERATOR

Operator hanya mengubah konten.

Operator boleh:
- mengedit konten Hero;
- mengedit statistik;
- mengedit teks homepage;
- mengedit Profil;
- mengelola Program;
- mengelola GTK;
- menambah/edit/hapus/publish Berita;
- menambah/edit/hapus/publish Agenda;
- menambah/edit/hapus/publish Prestasi;
- mengelola Galeri;
- mengelola SPMB;
- upload dan memilih Media;
- mengelola Instagram fallback/manual;
- mengedit SEO **per konten** bila field tersedia.

Operator tidak boleh:
- membuat user;
- mengubah role;
- menonaktifkan user;
- mengubah site settings global;
- mengubah ON/OFF fitur;
- mengubah visibility navbar;
- mengubah konfigurasi Instagram API;
- mengubah token/credential;
- mengubah SEO global;
- mengubah konfigurasi sistem;
- mengubah struktur menu;
- mengubah layout/template;
- melakukan maintenance sistem.

---

## 5. Matriks Akses

| Modul | Admin | Operator |
|---|---:|---:|
| Dashboard | Full | View |
| Beranda — isi | CRUD | CRUD |
| Profil — isi | CRUD | CRUD |
| Program | CRUD | CRUD |
| GTK | CRUD | CRUD |
| Berita | CRUD | CRUD |
| Agenda | CRUD | CRUD |
| Prestasi | CRUD | CRUD |
| Galeri | CRUD | CRUD |
| SPMB — isi | CRUD | CRUD |
| Media | CRUD | CRUD |
| Instagram fallback | CRUD | CRUD |
| Publish/Draft | Ya | Ya |
| Site Settings | Ya | Tidak |
| Feature ON/OFF | Ya | Tidak |
| Navbar visibility | Ya | Tidak |
| Instagram API config | Ya | Tidak |
| SEO global | Ya | Tidak |
| User management | Ya | Tidak |
| System/Maintenance | Ya | Tidak |

---

## 6. Sidebar Manager

### Admin

```text
DASHBOARD

KONTEN
├── Beranda
├── Profil
├── Program
├── GTK
├── Kabar Madrasah
│   ├── Berita
│   ├── Agenda
│   ├── Prestasi
│   └── Galeri
├── SPMB
└── Media

INTEGRASI
└── Instagram Content

PENGATURAN
├── Website
├── Fitur
├── Instagram
├── SEO
└── Pengguna
```

### Operator

```text
DASHBOARD

KONTEN
├── Beranda
├── Profil
├── Program
├── GTK
├── Kabar Madrasah
│   ├── Berita
│   ├── Agenda
│   ├── Prestasi
│   └── Galeri
├── SPMB
└── Media

INTEGRASI
└── Instagram Content
```

Menu Pengaturan tidak ditampilkan.

---

## 7. Feature OFF dan Operator

Jika Admin mematikan fitur Berita:
- Operator **tetap dapat mengelola Berita** di CMS;
- data lama tetap tersimpan;
- website publik tidak menampilkan Berita;
- Operator tidak dapat menyalakan fitur kembali.

Aturan sama berlaku pada Agenda, Prestasi, Galeri, Instagram, dan SPMB bila memakai feature toggle.

---

## 8. Publish Workflow

Tidak ada approval berlapis.

Status:
```text
DRAFT
PUBLISHED
```

Admin dan Operator dapat mengubah status konten.

---

## 9. Filter CI4

Baseline filter:

```text
auth
admin
```

### `auth`
Memastikan pengguna login.

### `admin`
Memastikan role `ADMIN`.

Contoh:

```text
/manager/*                  -> auth
/manager/settings/*         -> auth + admin
/manager/features/*         -> auth + admin
/manager/users/*            -> auth + admin
```

Konfigurasi API Instagram tertentu tetap admin-only.

---

## 10. Authorization di Backend

Jangan hanya menyembunyikan menu.

Setiap controller/action sensitif harus memvalidasi role dari server.

Operator yang tidak melihat tombol fitur tetap tidak boleh mengakses endpoint Admin secara langsung.

---

## 11. Password

- gunakan `password_hash()` dan `password_verify()`;
- jangan log password;
- jangan simpan password plaintext;
- reset password dilakukan Admin;
- user yang dinonaktifkan tidak dapat login.

---

## 12. Login Protection

Minimal:
- CSRF;
- rate limit/throttling login;
- generic error message;
- session regeneration setelah login;
- logout menghapus session login;
- secure cookie pada production HTTPS.

---

## 13. Audit

Log minimal:
- login berhasil;
- perubahan user;
- feature toggle;
- site settings;
- delete konten;
- update SPMB current;
- perubahan konfigurasi Instagram.

---

## 14. Public Navigation Rules

Navbar:

```text
Beranda
Profil
Program
GTK
Kabar Madrasah *
SPMB *
```

Jika Kabar OFF, menu tidak tampil.

Jika SPMB OFF, CTA/menu SPMB tidak tampil.

Frontend selalu membaca konfigurasi Admin.

---

## 15. Keputusan Final

1. Hanya ADMIN dan OPERATOR.
2. Tidak membuat permission matrix dinamis di database.
3. Operator = konten saja.
4. Feature/configuration = Admin saja.
5. Publish tidak membutuhkan approval Admin.
6. Security enforcement wajib di backend, bukan hanya UI.
