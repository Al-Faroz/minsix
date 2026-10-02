<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>
<?php
$editing = $event !== null;
$start = old('start_at', $event['start_at'] ?? '');
$end = old('end_at', $event['end_at'] ?? '');
if ($start) $start = date('Y-m-d\TH:i', strtotime($start));
if ($end) $end = date('Y-m-d\TH:i', strtotime($end));
?>
<form action="<?= $editing ? site_url('manager/events/' . $event['id']) : site_url('manager/events') ?>" method="post" class="form-page">
<?= csrf_field() ?>
<div class="form-main"><section class="content-card">
<div class="section-head"><div><p class="eyebrow">AGENDA</p><h2><?= $editing ? 'Edit Agenda' : 'Tambah Agenda' ?></h2></div></div>
<div class="settings-grid">
<div class="field span-2"><label>Judul *</label><input type="text" name="title" maxlength="255" required value="<?= esc(old('title', $event['title'] ?? '')) ?>"></div>
<div class="field span-2"><label>Slug</label><input type="text" name="slug" maxlength="200" value="<?= esc(old('slug', $event['slug'] ?? '')) ?>"></div>
<div class="field"><label>Mulai *</label><input type="datetime-local" name="start_at" required value="<?= esc($start) ?>"></div>
<div class="field"><label>Selesai</label><input type="datetime-local" name="end_at" value="<?= esc($end) ?>"></div>
<div class="field span-2"><label>Lokasi</label><input type="text" name="location" maxlength="255" value="<?= esc(old('location', $event['location'] ?? '')) ?>"></div>
<div class="field span-2"><label>Ringkasan</label><textarea name="summary" rows="4" maxlength="3000"><?= esc(old('summary', $event['summary'] ?? '')) ?></textarea></div>
<div class="field span-2"><label>Deskripsi</label><textarea name="description" rows="10" maxlength="30000"><?= esc(old('description', $event['description'] ?? '')) ?></textarea></div>
</div></section></div>
<aside class="form-side"><section class="content-card"><h3>Publikasi</h3><div class="settings-grid" style="grid-template-columns:1fr">
<div class="field"><label>Status</label><select name="status"><option value="DRAFT" <?= old('status', $event['status'] ?? 'DRAFT') === 'DRAFT' ? 'selected' : '' ?>>DRAFT</option><option value="PUBLISHED" <?= old('status', $event['status'] ?? '') === 'PUBLISHED' ? 'selected' : '' ?>>PUBLISHED</option></select></div>
<div class="field"><label>Foto</label><select name="primary_media_id"><option value="">— Tanpa foto —</option><?php foreach ($images as $image): ?><option value="<?= (int) $image['id'] ?>" <?= (int) old('primary_media_id', $event['primary_media_id'] ?? 0) === (int) $image['id'] ? 'selected' : '' ?>>#<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?></option><?php endforeach ?></select></div>
</div></section><section class="content-card"><div class="form-actions"><a class="btn ghost" href="<?= site_url('manager/events') ?>">Kembali</a><button class="btn primary" type="submit">Simpan</button></div></section></aside>
</form>
<?= $this->endSection() ?>
