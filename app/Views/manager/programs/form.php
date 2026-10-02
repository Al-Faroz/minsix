<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>
<?php $editing = $program !== null; ?>

<form action="<?= $editing ? site_url('manager/programs/' . $program['id']) : site_url('manager/programs') ?>" method="post" class="form-page">
    <?= csrf_field() ?>

    <div class="form-main">
        <section class="content-card">
            <div class="section-head"><div><p class="eyebrow">PROGRAM</p><h2><?= $editing ? 'Edit Program' : 'Tambah Program' ?></h2></div></div>
            <div class="settings-grid">
                <div class="field span-2"><label>Nama Program</label><input type="text" name="name" maxlength="200" required value="<?= esc(old('name', $program['name'] ?? '')) ?>"></div>
                <div class="field"><label>Slug</label><input type="text" name="slug" maxlength="180" value="<?= esc(old('slug', $program['slug'] ?? '')) ?>"><small>Kosongkan untuk dibuat otomatis.</small></div>
                <div class="field"><label>Kategori</label><select name="category" required><option value="">— Pilih —</option><?php foreach ($categories as $category): ?><option value="<?= esc($category) ?>" <?= old('category', $program['category'] ?? '') === $category ? 'selected' : '' ?>><?= esc($category) ?></option><?php endforeach ?></select></div>
                <div class="field span-2"><label>Ringkasan</label><textarea name="summary" rows="4" maxlength="3000"><?= esc(old('summary', $program['summary'] ?? '')) ?></textarea></div>
                <div class="field span-2"><label>Isi Program</label><textarea name="content" rows="12" maxlength="30000"><?= esc(old('content', $program['content'] ?? '')) ?></textarea><small>Rich-content editor akan dipasang ketika format editorial frontend dikunci. Saat ini data disimpan aman sebagai teks.</small></div>
            </div>
        </section>
    </div>

    <aside class="form-side">
        <section class="content-card">
            <h3>Publikasi</h3>
            <div class="settings-grid" style="grid-template-columns:1fr">
                <div class="field"><label>Status</label><select name="status"><option value="DRAFT" <?= old('status', $program['status'] ?? 'DRAFT') === 'DRAFT' ? 'selected' : '' ?>>DRAFT</option><option value="PUBLISHED" <?= old('status', $program['status'] ?? '') === 'PUBLISHED' ? 'selected' : '' ?>>PUBLISHED</option></select></div>
                <div class="field"><label>Urutan</label><input type="number" name="display_order" value="<?= esc(old('display_order', $program['display_order'] ?? 0)) ?>"></div>
                <div class="field"><label>Foto Utama</label><select name="primary_media_id"><option value="">— Tanpa foto —</option><?php foreach ($images as $image): ?><option value="<?= (int) $image['id'] ?>" <?= (int) old('primary_media_id', $program['primary_media_id'] ?? 0) === (int) $image['id'] ? 'selected' : '' ?>>#<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?></option><?php endforeach ?></select></div>
            </div>
        </section>
        <section class="content-card">
            <div class="form-actions">
                <a class="btn ghost" href="<?= site_url('manager/programs') ?>">Kembali</a>
                <button class="btn primary" type="submit">Simpan</button>
            </div>
        </section>
    </aside>
</form>

<?= $this->endSection() ?>
