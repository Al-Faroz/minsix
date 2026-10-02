<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>
<?php $labels = ['LEADERSHIP'=>'Pimpinan','CLASS_TEACHER'=>'Guru Kelas','SUBJECT_TEACHER'=>'Guru Mata Pelajaran','STAFF'=>'Tenaga Kependidikan']; ?>

<section class="content-card">
    <div class="section-head">
        <div><p class="eyebrow">MASTER JABATAN</p><h2>Jabatan GTK</h2><p class="muted">Buat jabatan spesifik seperlunya, misalnya “Guru Kelas VI-A” atau “Guru Mata Pelajaran Bahasa Inggris”.</p></div>
        <a class="btn ghost" href="<?= site_url('manager/gtk') ?>">Kembali ke GTK</a>
    </div>

    <form action="<?= site_url('manager/gtk-roles') ?>" method="post" class="role-editor">
        <?= csrf_field() ?>
        <div class="field"><label>Nama Jabatan</label><input type="text" name="role_name" maxlength="150" required placeholder="Contoh: Guru Kelas VI-A"></div>
        <div class="field"><label>Kategori</label><select name="category" required><?php foreach ($categories as $category): ?><option value="<?= esc($category) ?>"><?= esc($labels[$category] ?? $category) ?></option><?php endforeach ?></select></div>
        <button class="btn primary" type="submit">Tambah</button>
    </form>
</section>

<section class="content-card" style="padding:0;overflow:hidden">
    <?php foreach ($roles as $role): ?>
        <div class="role-row">
            <div><strong><?= esc($role['role_name']) ?></strong><div class="content-card__meta"><?= esc($role['role_key']) ?></div></div>
            <span class="status-chip category"><?= esc($labels[$role['category']] ?? $role['category']) ?></span>
            <div class="role-row__actions">
                <details>
                    <summary class="text-link">Edit</summary>
                    <form action="<?= site_url('manager/gtk-roles/' . $role['id']) ?>" method="post" class="mini-form" style="min-width:280px">
                        <?= csrf_field() ?>
                        <label>Nama<input type="text" name="role_name" value="<?= esc($role['role_name']) ?>" required maxlength="150"></label>
                        <label>Kategori<select name="category"><?php foreach ($categories as $category): ?><option value="<?= esc($category) ?>" <?= $role['category'] === $category ? 'selected' : '' ?>><?= esc($labels[$category] ?? $category) ?></option><?php endforeach ?></select></label>
                        <label>Urutan<input type="number" name="display_order" value="<?= (int) $role['display_order'] ?>"></label>
                        <label class="checkbox-row"><input type="checkbox" name="is_active" value="1" <?= (int) $role['is_active'] === 1 ? 'checked' : '' ?>><span>Aktif</span></label>
                        <button class="btn ghost" type="submit">Simpan</button>
                    </form>
                </details>
                <form action="<?= site_url('manager/gtk-roles/' . $role['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus jabatan ini?');"><?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button></form>
            </div>
        </div>
    <?php endforeach ?>
</section>

<?= $this->endSection() ?>
