-- MIN SIX | DATA DEMO SPMB 2027/2028
-- Tujuan: mengisi CMS dengan konten simulasi untuk uji tampilan dan alur SPMB.
-- PENTING:
-- 1. Semua teks pada periode ini adalah CONTOH, bukan pengumuman/kebijakan resmi.
-- 2. Status sengaja DRAFT dan is_current=0 agar TIDAK tampil di website publik.
-- 3. Tanggal hanya perkiraan untuk kebutuhan mockup; verifikasi sebelum dipublikasikan.
-- 4. Script idempotent: hanya menghapus record demo dengan judul penanda yang sama.
-- 5. Tidak mengubah/menghapus periode SPMB lain.

SET NAMES utf8mb4;
START TRANSACTION;

DELETE FROM spmb_periods
WHERE academic_year = '2027/2028'
  AND title = '[DATA DEMO] SPMB MIN 6 JEMBER 2027/2028';

INSERT INTO spmb_periods (
    academic_year,
    title,
    summary,
    content,
    start_date,
    end_date,
    registration_url,
    qr_media_id,
    brochure_media_id,
    contact_name,
    contact_phone,
    status,
    is_current,
    created_by,
    updated_by,
    created_at,
    updated_at
) VALUES (
    '2027/2028',
    '[DATA DEMO] SPMB MIN 6 JEMBER 2027/2028',
    'SIMULASI — Contoh informasi Sistem Penerimaan Murid Baru Tahun Pelajaran 2027/2028. Seluruh isi, jadwal, persyaratan, dan alur pada halaman ini perlu diverifikasi dan diganti sebelum diumumkan secara resmi.',
    '<h2>Selamat Datang di Informasi SPMB MIN 6 JEMBER</h2><p><strong>HALAMAN SIMULASI / BUKAN PENGUMUMAN RESMI.</strong> Konten ini disediakan sebagai data contoh untuk menguji tampilan website dan pengelolaan SPMB di CMS.</p><p>Informasi jadwal, persyaratan, tahapan, kontak, dan tautan pendaftaran pada data ini belum ditetapkan. Silakan sesuaikan dengan keputusan dan informasi resmi madrasah sebelum mengubah status menjadi PUBLISHED.</p><p>Website MIN 6 JEMBER berfungsi sebagai pusat informasi. Pendaftaran pada implementasi ini diarahkan ke formulir eksternal yang dikelola panitia.</p>',
    '2027-06-01',
    '2027-07-15',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'DRAFT',
    0,
    NULL,
    NULL,
    NOW(),
    NOW()
);

SET @spmb_demo_id = LAST_INSERT_ID();

INSERT INTO spmb_requirements (spmb_period_id, requirement_text, display_order) VALUES
(@spmb_demo_id, 'Contoh: Mengisi formulir pendaftaran melalui tautan resmi yang akan diumumkan panitia.', 1),
(@spmb_demo_id, 'Contoh: Menyiapkan salinan Kartu Keluarga calon murid.', 2),
(@spmb_demo_id, 'Contoh: Menyiapkan dokumen identitas/akta kelahiran calon murid.', 3),
(@spmb_demo_id, 'Contoh: Menyiapkan pas foto calon murid sesuai ketentuan panitia.', 4),
(@spmb_demo_id, 'Contoh: Dokumen pendukung lain apabila dipersyaratkan dalam pengumuman resmi.', 5);

INSERT INTO spmb_steps (spmb_period_id, title, description, display_order) VALUES
(@spmb_demo_id, 'Pelajari Informasi', 'Baca jadwal, persyaratan, dan ketentuan resmi setelah diumumkan oleh panitia. Teks ini masih simulasi.', 1),
(@spmb_demo_id, 'Siapkan Dokumen', 'Lengkapi dokumen sesuai daftar persyaratan resmi. Jangan menggunakan daftar contoh ini sebagai ketentuan final.', 2),
(@spmb_demo_id, 'Isi Formulir Online', 'Gunakan tautan formulir eksternal yang nantinya diberikan oleh panitia. Tautan belum diisi pada data demo.', 3),
(@spmb_demo_id, 'Pantau Informasi Lanjutan', 'Periksa kanal informasi resmi madrasah untuk pembaruan proses penerimaan.', 4);

INSERT INTO spmb_highlights (spmb_period_id, title, description, display_order) VALUES
(@spmb_demo_id, 'Pembiasaan Karakter', 'Contoh materi pengenalan tentang pembentukan karakter dan kebiasaan positif di lingkungan belajar.', 1),
(@spmb_demo_id, 'Pembelajaran Aktif', 'Contoh materi pengenalan tentang proses belajar yang mendorong rasa ingin tahu dan kreativitas peserta didik.', 2),
(@spmb_demo_id, 'Pengembangan Potensi', 'Contoh materi pengenalan tentang ruang pengembangan minat dan bakat peserta didik.', 3);

INSERT INTO spmb_faq (spmb_period_id, question, answer, display_order) VALUES
(@spmb_demo_id, 'Kapan pendaftaran dibuka?', 'Tanggal pada halaman demo hanya perkiraan untuk pengujian. Jadwal resmi akan ditetapkan dan diumumkan oleh panitia.', 1),
(@spmb_demo_id, 'Bagaimana cara mendaftar?', 'Pada implementasi website ini, tombol pendaftaran akan mengarah ke formulir eksternal setelah tautan resmi dimasukkan ke CMS.', 2),
(@spmb_demo_id, 'Apakah dokumen dapat dikirim secara online?', 'Mekanisme pengiriman dokumen mengikuti petunjuk pada formulir dan pengumuman resmi panitia.', 3),
(@spmb_demo_id, 'Apakah ada biaya pendaftaran?', 'Informasi biaya tidak ditentukan dalam data simulasi ini. Silakan merujuk pada pengumuman resmi madrasah.', 4),
(@spmb_demo_id, 'Ke mana harus bertanya?', 'Kontak panitia belum diisi pada data demo. Informasi narahubung resmi perlu ditambahkan melalui CMS.', 5);

COMMIT;

-- Verifikasi:
-- SELECT id, academic_year, title, status, is_current FROM spmb_periods
-- WHERE title = '[DATA DEMO] SPMB MIN 6 JEMBER 2027/2028';
-- Pastikan status=DRAFT dan is_current=0.
