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
- PHASE 4: Kabar Madrasah — audit source selesai, menunggu cek fungsional lokal
- PHASE 5: SPMB — audit source selesai, menunggu cek fungsional lokal
- PHASE 6: Homepage / Frontend — implementasi source selesai, menunggu cek lokal & responsive audit akhir
- PHASE 7: Instagram Hybrid — audit source selesai, menunggu konfigurasi credential dan cek lokal
- PHASE 8: SEO + Performance + Security — audit source selesai, menunggu cek lokal/production
- PHASE 9A: Rival Visual Conformity — implementasi source selesai
- PHASE 9B: Responsive Structural Audit — source fixes selesai
- PHASE 9C: Functional Source Audit — PASS; menunggu runtime/browser audit lokal
- PHASE 9D: Palette + Typography Revision — gradient #032100 → #614A27 dan font public/CMS diperkecil
