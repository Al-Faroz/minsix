# Dokumen Acuan — Website MIN 6 JEMBER

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
- FINAL-A: Branding & Visual Cleanup — logo/favicons final, placeholder M6 dan label PHASE di UI dibersihkan
- FINAL-B: CRUD Hardening — hasil write/delete diverifikasi; delete Media menghapus DB sebelum file fisik
- FINAL-C: User Management — Admin-only CRUD akun tanpa delete; role/status/reset password + last-active-admin protection
- FINAL-D1: Rich Text + Maps — editor ringan internal, sanitasi server-side, rendering aman, dan Google Maps Profil
- FINAL-D2: Structured SPMB — Alur Pendaftaran + Program Unggulan terstruktur
- FINAL-D3: Dashboard Completion — Konten Terbaru lintas Berita/Prestasi/Agenda/Galeri/Program/SPMB

- PHASE 10A: Production Hardening Source — security headers/CSP, dynamic robots, branded errors, dan deployment runbook selesai
- PHASE 10B: Branding Uppercase — seluruh branding aplikasi + database dinormalisasi menjadi MIN 6 JEMBER
- PHASE 10 Runtime: menunggu deploy pada domain/hosting final dan smoke test production
- FINAL-E: Homepage Program Cards + CMS Access — kartu Program memakai gradient per item dan tombol Login CMS tersedia di footer publik

- PHASE 10C1: Integration Credential Core — storage generik + encryption service + generator key selesai
- PHASE 10C2: Instagram API Manager — form credential, test connection, sync, clear token selesai
- PHASE 10C3: Integration Hardening — fallback aman sebelum SQL upgrade + dokumentasi deployment selesai

- PHASE 10C4: Route/Upgrade Fix — route credential API lengkap dan SQL upgrade idempotent
