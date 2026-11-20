-- MIN 6 JEMBER — DEMO DATA SPMB 2027/2028
-- Import SETELAH database/minsix.sql.
-- Data dibuat DRAFT dan bukan Current agar tidak mengubah informasi publik.
-- Idempotent: periode contoh hanya dibuat jika academic_year belum tersedia.
-- Tidak menghapus atau menimpa periode SPMB milik operator.

SET NAMES utf8mb4;
START TRANSACTION;

SET @demo_academic_year := '2027/2028';
SET @demo_period_id := (
    SELECT id FROM spmb_periods
    WHERE academic_year = @demo_academic_year
    ORDER BY id ASC LIMIT 1
);

INSERT INTO spmb_periods (
    academic_year, title, summary, content, start_date, end_date,
    registration_url, qr_media_id, brochure_media_id, contact_name,
    contact_phone, status, is_current, created_at, updated_at
)
SELECT
    @demo_academic_year,
    'Penerimaan Murid Baru MIN 6 Jember Tahun Pelajaran 2027/2028',
    'Informasi contoh untuk menyiapkan halaman SPMB tahun pelajaran 2027/2028. Silakan verifikasi dan sesuaikan seluruh ketentuan sebelum diterbitkan.',
    '<p>Selamat datang di halaman Sistem Penerimaan Murid Baru (SPMB) MIN 6 Jember Tahun Pelajaran 2027/2028.</p><p>Konten ini merupakan <strong>data contoh/draft</strong> untuk membantu operator menyiapkan halaman penerimaan murid baru. Jadwal, persyaratan, kuota, jalur penerimaan, dan kontak resmi wajib dikonfirmasi oleh pihak madrasah sebelum publikasi.</p><p>Pendaftaran online akan diarahkan ke formulir eksternal resmi setelah tautannya ditetapkan. Website ini tidak mengumpulkan data pribadi calon murid.</p>',
    '2027-05-03',
    '2027-06-30',
    NULL,
    NULL,
    NULL,
    'Panitia SPMB MIN 6 Jember (perlu konfirmasi)',
    NULL,
    'DRAFT',
    0,
    NOW(),
    NOW()
WHERE @demo_period_id IS NULL;

SET @demo_period_id := COALESCE(
    @demo_period_id,
    (SELECT id FROM spmb_periods WHERE academic_year = @demo_academic_year ORDER BY id ASC LIMIT 1)
);

-- Detail hanya ditambahkan bila periode contoh baru saja dibuat dan belum memiliki item.
INSERT INTO spmb_requirements (spmb_period_id, requirement_text, display_order)
SELECT @demo_period_id, sample.requirement_text, sample.display_order
FROM (
    SELECT 'Usia calon murid mengikuti ketentuan resmi yang ditetapkan untuk tahun pelajaran 2027/2028 (wajib diverifikasi).' AS requirement_text, 10 AS display_order
    UNION ALL SELECT 'Mengisi formulir pendaftaran online melalui tautan resmi panitia setelah diumumkan.', 20
    UNION ALL SELECT 'Menyiapkan dokumen identitas calon murid dan orang tua/wali sesuai pengumuman resmi.', 30
    UNION ALL SELECT 'Menyerahkan atau mengunggah dokumen sesuai petunjuk panitia SPMB.', 40
) AS sample
WHERE @demo_period_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM spmb_requirements WHERE spmb_period_id = @demo_period_id);

INSERT INTO spmb_steps (spmb_period_id, title, description, display_order)
SELECT @demo_period_id, sample.title, sample.description, sample.display_order
FROM (
    SELECT 'Informasi dan konsultasi' AS title, 'Calon orang tua/wali membaca pengumuman dan menghubungi panitia untuk memperoleh informasi resmi.', 10 AS display_order
    UNION ALL SELECT 'Pengisian formulir', 'Mengisi formulir pendaftaran eksternal setelah tautan resmi tersedia.', 20
    UNION ALL SELECT 'Verifikasi berkas', 'Panitia memeriksa kelengkapan dokumen sesuai ketentuan yang telah diumumkan.', 30
    UNION ALL SELECT 'Pengumuman hasil', 'Hasil disampaikan melalui kanal resmi madrasah sesuai jadwal yang ditetapkan.', 40
) AS sample
WHERE @demo_period_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM spmb_steps WHERE spmb_period_id = @demo_period_id);

INSERT INTO spmb_highlights (spmb_period_id, title, description, display_order)
SELECT @demo_period_id, sample.title, sample.description, sample.display_order
FROM (
    SELECT 'Pembelajaran berkarakter' AS title, 'Penguatan akhlak, pembiasaan ibadah, dan budaya positif di lingkungan madrasah.', 10 AS display_order
    UNION ALL SELECT 'Pembelajaran Al-Qur’an', 'Program pembelajaran Al-Qur’an dengan metode Yanbu’a sesuai program madrasah.', 20
    UNION ALL SELECT 'Pengembangan potensi', 'Ruang tumbuh bagi kemampuan akademik, kreativitas, olahraga, seni, dan keagamaan.', 30
) AS sample
WHERE @demo_period_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM spmb_highlights WHERE spmb_period_id = @demo_period_id);

INSERT INTO spmb_faq (spmb_period_id, question, answer, display_order)
SELECT @demo_period_id, sample.question, sample.answer, sample.display_order
FROM (
    SELECT 'Kapan pendaftaran dibuka?' AS question, 'Tanggal pada data ini masih berupa rancangan. Silakan menunggu pengumuman resmi MIN 6 Jember.', 10 AS display_order
    UNION ALL SELECT 'Di mana mengisi formulir pendaftaran?', 'Tautan formulir eksternal akan ditampilkan setelah panitia menetapkan dan memverifikasi alamat resminya.', 20
    UNION ALL SELECT 'Apakah pendaftaran dilakukan melalui website ini?', 'Website MIN 6 Jember menyediakan informasi. Pengisian data pendaftar dilakukan melalui formulir eksternal resmi.', 30
    UNION ALL SELECT 'Bagaimana jika membutuhkan informasi lebih lanjut?', 'Hubungi madrasah melalui nomor kontak resmi yang telah diverifikasi dan diumumkan.', 40
) AS sample
WHERE @demo_period_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM spmb_faq WHERE spmb_period_id = @demo_period_id);

COMMIT;

SELECT
    id,
    academic_year,
    title,
    status,
    is_current,
    start_date,
    end_date
FROM spmb_periods
WHERE academic_year = @demo_academic_year
ORDER BY id ASC
LIMIT 1;
