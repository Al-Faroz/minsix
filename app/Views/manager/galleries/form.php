<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>
<?php $editing = $gallery !== null; ?>

<form action="<?= $editing ? site_url('manager/galleries/' . $gallery['id']) : site_url('manager/galleries') ?>" method="post" class="form-page">
<?= csrf_field() ?>

<div class="form-main">
    <section class="content-card">
        <div class="section-head">
            <div>
                <p class="eyebrow">GALERI</p>
                <h2><?= $editing ? 'Edit Album' : 'Tambah Album' ?></h2>
            </div>
        </div>

        <div class="settings-grid">
            <div class="field span-2">
                <label>Judul Album *</label>
                <input type="text" name="title" maxlength="255" required value="<?= esc(old('title', $gallery['title'] ?? '')) ?>">
            </div>

            <div class="field">
                <label>Slug</label>
                <input type="text" name="slug" maxlength="200" value="<?= esc(old('slug', $gallery['slug'] ?? '')) ?>">
                <small>Kosongkan untuk dibuat otomatis.</small>
            </div>

            <div class="field">
                <label>Tanggal Kegiatan</label>
                <input type="date" name="gallery_date" value="<?= esc(old('gallery_date', $gallery['gallery_date'] ?? '')) ?>">
            </div>

            <div class="field span-2">
                <label>Deskripsi</label>
                <textarea name="description" rows="7" maxlength="10000"><?= esc(old('description', $gallery['description'] ?? '')) ?></textarea>
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
                    <option value="DRAFT" <?= old('status', $gallery['status'] ?? 'DRAFT') === 'DRAFT' ? 'selected' : '' ?>>DRAFT</option>
                    <option value="PUBLISHED" <?= old('status', $gallery['status'] ?? '') === 'PUBLISHED' ? 'selected' : '' ?>>PUBLISHED</option>
                </select>
            </div>

            <div class="field">
                <label>Cover Album</label>
                <select name="cover_media_id">
                    <option value="">— Tanpa cover —</option>
                    <?php foreach ($images as $image): ?>
                        <option value="<?= (int) $image['id'] ?>" <?= (int) old('cover_media_id', $gallery['cover_media_id'] ?? 0) === (int) $image['id'] ? 'selected' : '' ?>>
                            #<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <small>Cover boleh sama dengan salah satu foto album.</small>
            </div>
        </div>
    </section>

    <section class="content-card">
        <div class="form-actions">
            <a class="btn ghost" href="<?= site_url('manager/galleries') ?>">Kembali</a>
            <button class="btn primary" type="submit">Simpan Album</button>
        </div>
    </section>
</aside>
</form>

<?php if ($editing): ?>
<section class="content-card" style="margin-top:18px">
    <div class="section-head">
        <div>
            <p class="eyebrow">FOTO ALBUM</p>
            <h2>Tambahkan Foto</h2>
            <p class="muted">Pilih satu atau beberapa gambar dari Media Library. Tekan Ctrl/Command untuk memilih lebih dari satu.</p>
        </div>
        <a class="btn ghost" href="<?= site_url('manager/media') ?>">Buka Media</a>
    </div>

    <form action="<?= site_url('manager/galleries/' . $gallery['id'] . '/items') ?>" method="post">
        <?= csrf_field() ?>
        <div class="field">
            <label>Foto dari Media</label>
            <select name="media_ids[]" class="multi-select" multiple required>
                <?php foreach ($images as $image): ?>
                    <option value="<?= (int) $image['id'] ?>">#<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="form-actions" style="margin-top:12px">
            <button class="btn primary" type="submit">Tambahkan ke Album</button>
        </div>
    </form>
</section>

<section class="content-card" style="margin-top:18px">
    <div class="section-head">
        <div>
            <p class="eyebrow">ISI ALBUM</p>
            <h2><?= count($items) ?> Foto</h2>
        </div>
    </div>

    <?php if ($items === []): ?>
        <div class="empty-state">Album belum memiliki foto.</div>
    <?php else: ?>
        <div class="gallery-items">
            <?php foreach ($items as $item): ?>
                <article class="gallery-item">
                    <div class="gallery-item__image">
                        <img src="<?= base_url($item['relative_path']) ?>" alt="<?= esc($item['alt_text'] ?: $item['original_name']) ?>" loading="lazy">
                    </div>

                    <div class="gallery-item__body">
                        <form action="<?= site_url('manager/galleries/' . $gallery['id'] . '/items/' . $item['id']) ?>" method="post" class="mini-form">
                            <?= csrf_field() ?>
                            <label>Caption
                                <input type="text" name="caption" maxlength="255" value="<?= esc($item['caption']) ?>">
                            </label>
                            <label>Urutan
                                <input type="number" name="display_order" value="<?= (int) $item['display_order'] ?>">
                            </label>
                            <button class="btn ghost" type="submit">Simpan</button>
                        </form>

                        <div class="media-actions">
                            <span class="content-card__meta">Media #<?= (int) $item['media_id'] ?></span>
                            <form action="<?= site_url('manager/galleries/' . $gallery['id'] . '/items/' . $item['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Lepas foto dari album? File media tetap tersimpan.');">
                                <?= csrf_field() ?>
                                <button class="text-danger" type="submit">Lepas</button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</section>
<?php endif ?>

<?= $this->endSection() ?>
