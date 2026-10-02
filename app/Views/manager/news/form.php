<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>
<?php $editing = $newsItem !== null; $published = old('published_at', $newsItem['published_at'] ?? ''); if ($published) $published = date('Y-m-d\TH:i', strtotime($published)); ?>

<form action="<?= $editing ? site_url('manager/news/' . $newsItem['id']) : site_url('manager/news') ?>" method="post" class="form-page">
<?= csrf_field() ?>
<div class="form-main">
<section class="content-card">
<div class="section-head"><div><p class="eyebrow">BERITA</p><h2><?= $editing ? 'Edit Berita' : 'Tambah Berita' ?></h2></div></div>
<div class="settings-grid">
<div class="field span-2"><label>Judul *</label><input type="text" name="title" maxlength="255" required value="<?= esc(old('title', $newsItem['title'] ?? '')) ?>"></div>
<div class="field span-2"><label>Slug</label><input type="text" name="slug" maxlength="200" value="<?= esc(old('slug', $newsItem['slug'] ?? '')) ?>"><small>Kosongkan untuk dibuat otomatis.</small></div>
<div class="field span-2"><label>Ringkasan</label><textarea name="summary" rows="4" maxlength="3000"><?= esc(old('summary', $newsItem['summary'] ?? '')) ?></textarea></div>
<div class="field span-2"><label>Isi Berita *</label><textarea name="content" rows="15" maxlength="60000" required data-rich-editor><?= esc(old('content', $newsItem['content'] ?? '')) ?></textarea><small>Gunakan heading, bold/italic, daftar, kutipan, dan link seperlunya.</small></div>
</div>
</section>
<section class="content-card">
<p class="eyebrow">SEO PER KONTEN</p>
<div class="settings-grid">
<div class="field span-2"><label>Meta Title</label><input type="text" name="meta_title" maxlength="255" value="<?= esc(old('meta_title', $newsItem['meta_title'] ?? '')) ?>"><small>Kosongkan untuk memakai Judul.</small></div>
<div class="field span-2"><label>Meta Description</label><textarea name="meta_description" rows="3" maxlength="320"><?= esc(old('meta_description', $newsItem['meta_description'] ?? '')) ?></textarea><small>Kosongkan untuk memakai Ringkasan.</small></div>
<div class="field span-2"><label>OG Image</label><select name="og_media_id"><option value="">— Gunakan foto utama/default —</option><?php foreach ($images as $image): ?><option value="<?= (int) $image['id'] ?>" <?= (int) old('og_media_id', $newsItem['og_media_id'] ?? 0) === (int) $image['id'] ? 'selected' : '' ?>>#<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?></option><?php endforeach ?></select></div>
</div>
</section>
</div>
<aside class="form-side">
<section class="content-card"><h3>Publikasi</h3><div class="settings-grid" style="grid-template-columns:1fr">
<div class="field"><label>Status</label><select name="status"><option value="DRAFT" <?= old('status', $newsItem['status'] ?? 'DRAFT') === 'DRAFT' ? 'selected' : '' ?>>DRAFT</option><option value="PUBLISHED" <?= old('status', $newsItem['status'] ?? '') === 'PUBLISHED' ? 'selected' : '' ?>>PUBLISHED</option></select></div>
<div class="field"><label>Tanggal Terbit</label><input type="datetime-local" name="published_at" value="<?= esc($published) ?>"><small>Jika publish dan kosong, sistem memakai waktu saat disimpan.</small></div>
<div class="field"><label>Foto Utama</label><select name="primary_media_id"><option value="">— Tanpa foto —</option><?php foreach ($images as $image): ?><option value="<?= (int) $image['id'] ?>" <?= (int) old('primary_media_id', $newsItem['primary_media_id'] ?? 0) === (int) $image['id'] ? 'selected' : '' ?>>#<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?></option><?php endforeach ?></select></div>
</div></section>
<section class="content-card"><div class="form-actions"><a class="btn ghost" href="<?= site_url('manager/news') ?>">Kembali</a><button class="btn primary" type="submit">Simpan</button></div></section>
</aside>
</form>
<?= $this->endSection() ?>
