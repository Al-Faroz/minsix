<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>
<?php
$editing = $gtk !== null;
$categoryLabels = [
    'LEADERSHIP' => 'Pimpinan',
    'CLASS_TEACHER' => 'Guru Kelas',
    'SUBJECT_TEACHER' => 'Guru Mata Pelajaran',
    'STAFF' => 'Tenaga Kependidikan',
];
?>

<form action="<?= $editing ? site_url('manager/gtk/' . $gtk['id']) : site_url('manager/gtk') ?>" method="post" class="form-page">
    <?= csrf_field() ?>

    <div class="form-main">
        <section class="content-card">
            <div class="section-head"><div><p class="eyebrow">GTK</p><h2><?= $editing ? 'Edit GTK' : 'Tambah GTK' ?></h2></div></div>

            <div class="settings-grid">
                <div class="field"><label>Gelar Depan</label><input type="text" name="front_title" maxlength="50" value="<?= esc(old('front_title', $gtk['front_title'] ?? '')) ?>"></div>
                <div class="field"><label>Nama *</label><input type="text" name="name" maxlength="180" required value="<?= esc(old('name', $gtk['name'] ?? '')) ?>"></div>
                <div class="field span-2"><label>Gelar Belakang</label><input type="text" name="back_title" maxlength="120" value="<?= esc(old('back_title', $gtk['back_title'] ?? '')) ?>"></div>
                <div class="field span-2"><label>Bio Singkat</label><textarea name="short_bio" rows="5" maxlength="3000"><?= esc(old('short_bio', $gtk['short_bio'] ?? '')) ?></textarea></div>
            </div>
        </section>

        <section class="content-card">
            <div class="section-head"><div><p class="eyebrow">MULTI-ROLE</p><h2>Jabatan / Peran</h2><p class="muted">Centang semua jabatan yang berlaku untuk orang ini.</p></div><a class="btn ghost" href="<?= site_url('manager/gtk-roles') ?>">Kelola Jabatan</a></div>
            <div class="role-groups">
                <?php foreach ($categoryLabels as $key => $label): ?>
                    <div class="role-group">
                        <h4><?= esc($label) ?></h4>
                        <div class="checkbox-list">
                            <?php if (empty($roleGroups[$key])): ?>
                                <span class="empty-inline">Belum ada jabatan pada kategori ini.</span>
                            <?php else: ?>
                                <?php foreach ($roleGroups[$key] as $role): ?>
                                    <label class="checkbox-row">
                                        <input type="checkbox" name="role_ids[]" value="<?= (int) $role['id'] ?>" <?= in_array((int) $role['id'], array_map('intval', old('role_ids', $selectedRoles)), true) ? 'checked' : '' ?>>
                                        <span><strong><?= esc($role['role_name']) ?></strong><small><?= esc($role['role_key']) ?></small></span>
                                    </label>
                                <?php endforeach ?>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </section>
    </div>

    <aside class="form-side">
        <section class="content-card">
            <h3>Tampilan</h3>
            <div class="settings-grid" style="grid-template-columns:1fr">
                <div class="field"><label>Foto</label><select name="photo_media_id"><option value="">— Tanpa foto —</option><?php foreach ($images as $image): ?><option value="<?= (int) $image['id'] ?>" <?= (int) old('photo_media_id', $gtk['photo_media_id'] ?? 0) === (int) $image['id'] ? 'selected' : '' ?>>#<?= (int) $image['id'] ?> — <?= esc($image['original_name']) ?></option><?php endforeach ?></select></div>
                <div class="field"><label>Urutan</label><input type="number" name="display_order" value="<?= esc(old('display_order', $gtk['display_order'] ?? 0)) ?>"></div>
                <label class="checkbox-row"><input type="checkbox" name="is_active" value="1" <?= (int) old('is_active', $gtk['is_active'] ?? 1) === 1 ? 'checked' : '' ?>><span><strong>Aktif</strong><small>Tampilkan pada data GTK publik.</small></span></label>
            </div>
        </section>
        <section class="content-card"><div class="form-actions"><a class="btn ghost" href="<?= site_url('manager/gtk') ?>">Kembali</a><button class="btn primary" type="submit">Simpan</button></div></section>
    </aside>
</form>

<?= $this->endSection() ?>
