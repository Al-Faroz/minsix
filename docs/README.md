# Dokumen Acuan — Website MIN 6 Jember

Dokumen ini menjadi pegangan implementasi proyek `minsix`.

Urutan baca:
1. `01_MASTERPLAN_WEBSITE_MIN6.md`
2. `02_DATABASE_WEBSITE_MIN6.md`
3. `03_AUTH_RBAC_MENU_WEBSITE_MIN6.md`
4. `04_CMS_CONTENT_WEBSITE_MIN6.md`
5. `05_FRONTEND_UI_WEBSITE_MIN6.md`
6. `06_MEDIA_INSTAGRAM_SEO_WEBSITE_MIN6.md`
7. `07_DEPLOYMENT_SECURITY_WEBSITE_MIN6.md`

## Keputusan Utama
- Framework: CodeIgniter 4
- Folder lokal: `G:\xampp\htdocs\minsix`
- Repository: `Al-Faroz/minsix`
- Database: SQL dump
- Migration: tidak digunakan
- Seeder: tidak digunakan
- Role: `ADMIN`, `OPERATOR`
- Admin: sistem + konten
- Operator: konten saja
- Homepage menjadi prioritas utama
- Kabar/Berita dapat ON/OFF
- GTK halaman terpisah
- SPMB halaman khusus
- Instagram `@min6jember` dengan pola hybrid
- Gaya publik: modern-humanis


## Status Implementasi
- PHASE 0: Dokumen Acuan — selesai
- PHASE 1: Auth + Manager Skeleton — PASS
- PHASE 2: Settings + Feature Toggle + Media — PASS
- PHASE 3: Profil + Program + GTK — PASS
- PHASE 4: Kabar Madrasah (Berita + Agenda + Prestasi + Galeri) — audit source selesai, menunggu cek fungsional lokal
- PHASE 5: SPMB — selesai di kode, menunggu cek lokal
