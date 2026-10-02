<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>
<?php $editing = $period !== null; ?>

<form action="<?= $editing ? site_url('manager/spmb/' . $period['id']) : site_url('manager/spmb') ?>" method="post" class="form-page">
<?= csrf_field() ?>

<div class="form-main">
    <section class="content-card">
        <div class="section-head">
            <div>
                <p class="eyebrow">PERIODE SPMB</p>
                <h2><?= $editing ? 'Edit Periode SPMB' : 'Tambah Periode SPMB' ?></h2>
                <p class="muted">Informasi tahun ajaran, periode pendaftaran, isi, dan akses pendaftaran.</p>
            </div>
        </div>

        <div class="settings-grid">
            <div class="field">
                <label>Tahun Ajaran *</label>
                <input type="text" name="academic_year" maxlength="20" required placeholder="Contoh: 2026/2027" value="<?= esc(old('academic_year', $period['academic_year'] ?? '')) ?>">
            </div>
            <div class="field">
                <label>Judul *</label>
                <input type="text" name="title" maxlength="255" required placeholder="Contoh: SPMB MIN 6 JEMBER 2026/2027" value="<?= esc(old('title', $period['title'] ?? '')) ?>">
            </div>

            <div class="field">
                <label>Tanggal Buka</label>
                <input type="date" name="start_date" value="<?= esc(old('start_date', $period['start_date'] ?? '')) ?>">
            </div>
            <div class="field">
                <label>Tanggal Tutup</label>
                <input type="date" name="end_date" value="<?= esc(old('end_date', $period['end_date'] ?? '')) ?>">
            </div>

            <div class="field span-2">
                <label>Ringkasan</label>
                <textarea name="summary" rows="4" maxlength="5000"><?= esc(old('summary', $period['summary'] ?? '')) ?></textarea>
            </div>

            <div class="field span-2">
                <label>Isi SPMB</label>
                <textarea name="content" rows="14" maxlength="60000" data-rich-editor><?= esc(old('content', $period['content'] ?? '')) ?></textarea>
                <small>Gunakan untuk deskripsi umum SPMB. Alur dan Program Unggulan dikelola pada bagian terstruktur di bawah.</small>
            </div>

            <div class="field span-2">
                <label>Link Pendaftaran</label>
                <input type="url" name="registration_url" maxlength="500" placeholder="https://..." value="<?= esc(old('registration_url', $period['registration_url'] ?? '')) ?>">
            </div>

            <div class="field">
                <label>Narahubung</label>
                <input type="text" name="contact_name" maxlength="180" value="<?= esc(old('contact_name', $period['contact_name'] ?? '')) ?>">
            </div>
            <div class="field">
                <label>Nomor Kontak</label>
                <input type="text" name="contact_phone" maxlength="50" value="<?= esc(old('contact_phone', $period['contact_phone'] ?? '')) ?>">
            </div>
        </div>
    </section>
</div>

<aside class="form-side">
    <section class="content-card">
        <h3>Publikasi</h3>
        <div class="settings-grid" style="grid-template-columns:1fr">
            <div class="field">
                <label>Status</label>
                <select name="status">
                    <?php foreach (['DRAFT','PUBLISHED','ARCHIVED'] as $status): ?>
                        <option value="<?= esc($status) ?>" <?= old('status', $period['status'] ?? 'DRAFT') === $status ? 'selected' : '' ?>><?= esc($status) ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <label class="checkbox-row">
                <input type="checkbox" name="is_current" value="1" <?= (int) old('is_current', $period['is_current'] ?? 0) === 1 ? 'checked' : '' ?>>
                <span><strong>Current</strong><small>Jika dicentang, periode Current sebelumnya otomatis dilepas.</small></span>
            </label>
        </div>
    </section>

    <section class="content-card">
        <h3>Media SPMB</h3>
        <div class="settings-grid" style="grid-template-columns:1fr">
            <div class="field">
                <label>QR Pendaftaran</label>
                <select name="qr_media_id">
                    <option value="">— Tanpa QR —</option>
                    <?php foreach ($images as $image): ?>
                        <option value="<?= (int) $image['id'] ?>" <?= (int) old('qr_media_id', $period['qr_media_id'] ?? 0) === (int) $image['id'] ? 'selected' : '' ?>>
                            #<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <small>QR harus berupa gambar dari Media Library.</small>
            </div>

            <div class="field">
                <label>Brosur PDF</label>
                <select name="brochure_media_id">
                    <option value="">— Tanpa brosur —</option>
                    <?php foreach ($documents as $document): ?>
                        <option value="<?= (int) $document['id'] ?>" <?= (int) old('brochure_media_id', $period['brochure_media_id'] ?? 0) === (int) $document['id'] ? 'selected' : '' ?>>
                            #<?= (int) $document['id'] ?> — <?= esc($document['original_name']) ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <small>Dokumen yang dipilih harus PDF.</small>
            </div>
        </div>
    </section>

    <section class="content-card">
        <div class="form-actions">
            <a class="btn ghost" href="<?= site_url('manager/spmb') ?>">Kembali</a>
            <button class="btn primary" type="submit">Simpan Periode</button>
        </div>
    </section>
</aside>
</form>

<?php if ($editing): ?>
<section class="content-card spmb-repeat-section">
    <div class="section-head">
        <div>
            <p class="eyebrow">REPEATABLE LIST</p>
            <h2>Persyaratan</h2>
            <p class="muted">Daftar persyaratan ditampilkan sesuai urutan.</p>
        </div>
    </div>

    <form action="<?= site_url('manager/spmb/' . $period['id'] . '/requirements') ?>" method="post" class="repeat-add-form">
        <?= csrf_field() ?>
        <div class="field"><label>Persyaratan baru</label><textarea name="requirement_text" rows="2" maxlength="3000" required></textarea></div>
        <div class="field"><label>Urutan</label><input type="number" name="display_order" value="<?= count($requirements) * 10 + 10 ?>"></div>
        <button class="btn primary" type="submit">Tambah Persyaratan</button>
    </form>

    <?php if ($requirements === []): ?>
        <div class="empty-state">Belum ada persyaratan.</div>
    <?php else: ?>
        <div class="repeat-list">
            <?php foreach ($requirements as $item): ?>
                <article class="repeat-item">
                    <form action="<?= site_url('manager/spmb/' . $period['id'] . '/requirements/' . $item['id']) ?>" method="post" class="repeat-edit-form">
                        <?= csrf_field() ?>
                        <div class="field"><label>Isi</label><textarea name="requirement_text" rows="2" maxlength="3000" required><?= esc($item['requirement_text']) ?></textarea></div>
                        <div class="field"><label>Urutan</label><input type="number" name="display_order" value="<?= (int) $item['display_order'] ?>"></div>
                        <button class="btn ghost" type="submit">Simpan</button>
                    </form>
                    <form action="<?= site_url('manager/spmb/' . $period['id'] . '/requirements/' . $item['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus persyaratan ini?');">
                        <?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button>
                    </form>
                </article>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</section>

<section class="content-card spmb-repeat-section">
    <div class="section-head">
        <div>
            <p class="eyebrow">ALUR PENDAFTARAN</p>
            <h2>Tahapan Pendaftaran</h2>
            <p class="muted">Gunakan langkah singkat dan berurutan agar mudah dipahami calon wali murid.</p>
        </div>
    </div>

    <form action="<?= site_url('manager/spmb/' . $period['id'] . '/steps') ?>" method="post" class="faq-add-form">
        <?= csrf_field() ?>
        <div class="field"><label>Judul Tahap</label><input type="text" name="title" maxlength="255" required placeholder="Contoh: Isi Formulir"></div>
        <div class="field"><label>Keterangan</label><textarea name="description" rows="3" maxlength="3000"></textarea></div>
        <div class="field"><label>Urutan</label><input type="number" name="display_order" value="<?= count($steps) * 10 + 10 ?>"></div>
        <button class="btn primary" type="submit">Tambah Tahap</button>
    </form>

    <?php if ($steps === []): ?>
        <div class="empty-state">Belum ada alur pendaftaran.</div>
    <?php else: ?>
        <div class="repeat-list">
            <?php foreach ($steps as $item): ?>
                <article class="repeat-item faq-item">
                    <form action="<?= site_url('manager/spmb/' . $period['id'] . '/steps/' . $item['id']) ?>" method="post" class="faq-edit-form">
                        <?= csrf_field() ?>
                        <div class="field"><label>Judul</label><input type="text" name="title" maxlength="255" required value="<?= esc($item['title']) ?>"></div>
                        <div class="field"><label>Keterangan</label><textarea name="description" rows="3" maxlength="3000"><?= esc($item['description']) ?></textarea></div>
                        <div class="field"><label>Urutan</label><input type="number" name="display_order" value="<?= (int) $item['display_order'] ?>"></div>
                        <button class="btn ghost" type="submit">Simpan</button>
                    </form>
                    <form action="<?= site_url('manager/spmb/' . $period['id'] . '/steps/' . $item['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus tahap ini?');">
                        <?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button>
                    </form>
                </article>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</section>

<section class="content-card spmb-repeat-section">
    <div class="section-head">
        <div>
            <p class="eyebrow">PROGRAM UNGGULAN</p>
            <h2>Highlight SPMB</h2>
            <p class="muted">Tampilkan keunggulan yang memang ingin dikenalkan pada periode SPMB ini.</p>
        </div>
    </div>

    <form action="<?= site_url('manager/spmb/' . $period['id'] . '/highlights') ?>" method="post" class="faq-add-form">
        <?= csrf_field() ?>
        <div class="field"><label>Nama Program</label><input type="text" name="title" maxlength="255" required placeholder="Contoh: Yanbu'a"></div>
        <div class="field"><label>Keterangan</label><textarea name="description" rows="3" maxlength="3000"></textarea></div>
        <div class="field"><label>Urutan</label><input type="number" name="display_order" value="<?= count($highlights) * 10 + 10 ?>"></div>
        <button class="btn primary" type="submit">Tambah Program</button>
    </form>

    <?php if ($highlights === []): ?>
        <div class="empty-state">Belum ada program unggulan SPMB.</div>
    <?php else: ?>
        <div class="repeat-list">
            <?php foreach ($highlights as $item): ?>
                <article class="repeat-item faq-item">
                    <form action="<?= site_url('manager/spmb/' . $period['id'] . '/highlights/' . $item['id']) ?>" method="post" class="faq-edit-form">
                        <?= csrf_field() ?>
                        <div class="field"><label>Nama Program</label><input type="text" name="title" maxlength="255" required value="<?= esc($item['title']) ?>"></div>
                        <div class="field"><label>Keterangan</label><textarea name="description" rows="3" maxlength="3000"><?= esc($item['description']) ?></textarea></div>
                        <div class="field"><label>Urutan</label><input type="number" name="display_order" value="<?= (int) $item['display_order'] ?>"></div>
                        <button class="btn ghost" type="submit">Simpan</button>
                    </form>
                    <form action="<?= site_url('manager/spmb/' . $period['id'] . '/highlights/' . $item['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus program unggulan ini?');">
                        <?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button>
                    </form>
                </article>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</section>

<section class="content-card spmb-repeat-section">
    <div class="section-head">
        <div>
            <p class="eyebrow">FAQ</p>
            <h2>Pertanyaan yang Sering Diajukan</h2>
        </div>
    </div>

    <form action="<?= site_url('manager/spmb/' . $period['id'] . '/faq') ?>" method="post" class="faq-add-form">
        <?= csrf_field() ?>
        <div class="field"><label>Pertanyaan</label><input type="text" name="question" maxlength="255" required></div>
        <div class="field"><label>Jawaban</label><textarea name="answer" rows="3" maxlength="5000" required></textarea></div>
        <div class="field"><label>Urutan</label><input type="number" name="display_order" value="<?= count($faq) * 10 + 10 ?>"></div>
        <button class="btn primary" type="submit">Tambah FAQ</button>
    </form>

    <?php if ($faq === []): ?>
        <div class="empty-state">Belum ada FAQ.</div>
    <?php else: ?>
        <div class="repeat-list">
            <?php foreach ($faq as $item): ?>
                <article class="repeat-item faq-item">
                    <form action="<?= site_url('manager/spmb/' . $period['id'] . '/faq/' . $item['id']) ?>" method="post" class="faq-edit-form">
                        <?= csrf_field() ?>
                        <div class="field"><label>Pertanyaan</label><input type="text" name="question" maxlength="255" required value="<?= esc($item['question']) ?>"></div>
                        <div class="field"><label>Jawaban</label><textarea name="answer" rows="3" maxlength="5000" required><?= esc($item['answer']) ?></textarea></div>
                        <div class="field"><label>Urutan</label><input type="number" name="display_order" value="<?= (int) $item['display_order'] ?>"></div>
                        <button class="btn ghost" type="submit">Simpan</button>
                    </form>
                    <form action="<?= site_url('manager/spmb/' . $period['id'] . '/faq/' . $item['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus FAQ ini?');">
                        <?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button>
                    </form>
                </article>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</section>
<?php endif ?>

<?= $this->endSection() ?>
