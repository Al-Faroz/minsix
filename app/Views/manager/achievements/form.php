<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>
<?php $editing = $achievement !== null; ?>
<form action="<?= $editing ? site_url('manager/achievements/' . $achievement['id']) : site_url('manager/achievements') ?>" method="post" class="form-page">
<?= csrf_field() ?>
<div class="form-main"><section class="content-card">
<div class="section-head"><div><p class="eyebrow">PRESTASI</p><h2><?= $editing ? 'Edit Prestasi' : 'Tambah Prestasi' ?></h2></div></div>
<div class="settings-grid">
<div class="field span-2"><label>Judul *</label><input type="text" name="title" maxlength="255" required value="<?= esc(old('title', $achievement['title'] ?? '')) ?>"></div>
<div class="field span-2"><label>Slug</label><input type="text" name="slug" maxlength="200" value="<?= esc(old('slug', $achievement['slug'] ?? '')) ?>"></div>
<div class="field"><label>Nama Peserta / Tim</label><input type="text" name="participant_name" maxlength="255" value="<?= esc(old('participant_name', $achievement['participant_name'] ?? '')) ?>"></div>
<div class="field"><label>Bidang</label><input type="text" name="field_name" maxlength="150" placeholder="Contoh: Matematika / Pencak Silat" value="<?= esc(old('field_name', $achievement['field_name'] ?? '')) ?>"></div>
<div class="field"><label>Juara / Penghargaan</label><input type="text" name="award" maxlength="150" placeholder="Contoh: Juara I" value="<?= esc(old('award', $achievement['award'] ?? '')) ?>"></div>
<div class="field"><label>Tingkat</label><input type="text" name="level" maxlength="100" placeholder="Kabupaten / Provinsi / Nasional" value="<?= esc(old('level', $achievement['level'] ?? '')) ?>"></div>
<div class="field"><label>Penyelenggara</label><input type="text" name="organizer" maxlength="255" value="<?= esc(old('organizer', $achievement['organizer'] ?? '')) ?>"></div>
<div class="field"><label>Tanggal Prestasi</label><input type="date" name="achievement_date" value="<?= esc(old('achievement_date', $achievement['achievement_date'] ?? '')) ?>"></div>
<div class="field span-2"><label>Ringkasan</label><textarea name="summary" rows="4" maxlength="3000"><?= esc(old('summary', $achievement['summary'] ?? '')) ?></textarea></div>
<div class="field span-2"><label>Cerita / Keterangan</label><textarea name="content" rows="10" maxlength="30000"><?= esc(old('content', $achievement['content'] ?? '')) ?></textarea></div>
</div></section></div>
<aside class="form-side"><section class="content-card"><h3>Publikasi</h3><div class="settings-grid" style="grid-template-columns:1fr">
<div class="field"><label>Status</label><select name="status"><option value="DRAFT" <?= old('status', $achievement['status'] ?? 'DRAFT') === 'DRAFT' ? 'selected' : '' ?>>DRAFT</option><option value="PUBLISHED" <?= old('status', $achievement['status'] ?? '') === 'PUBLISHED' ? 'selected' : '' ?>>PUBLISHED</option></select></div>
<div class="field"><label>Foto Utama</label><select name="primary_media_id"><option value="">— Tanpa foto —</option><?php foreach ($images as $image): ?><option value="<?= (int) $image['id'] ?>" <?= (int) old('primary_media_id', $achievement['primary_media_id'] ?? 0) === (int) $image['id'] ? 'selected' : '' ?>>#<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?></option><?php endforeach ?></select></div>
</div></section><section class="content-card"><div class="form-actions"><a class="btn ghost" href="<?= site_url('manager/achievements') ?>">Kembali</a><button class="btn primary" type="submit">Simpan</button></div></section></aside>
</form>
<?= $this->endSection() ?>
