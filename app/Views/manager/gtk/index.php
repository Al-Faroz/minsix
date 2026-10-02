<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<div class="toolbar">
    <div>
        <p class="eyebrow">MASTER KONTEN</p>
        <h2 style="margin:.1rem 0">Guru & Tenaga Kependidikan</h2>
        <p class="muted" style="margin:.35rem 0 0">Satu orang hanya satu master data dan dapat memiliki beberapa jabatan.</p>
    </div>
    <div class="toolbar-actions">
        <a class="btn ghost" href="<?= site_url('manager/gtk-roles') ?>">Kelola Jabatan</a>
        <a class="btn primary" href="<?= site_url('manager/gtk/new') ?>">+ Tambah GTK</a>
    </div>
</div>

<?php if ($gtk === []): ?>
    <div class="empty-state">Belum ada GTK. Tambahkan data pertama.</div>
<?php else: ?>
<div class="table-wrap">
<table class="cms-table">
<thead><tr><th>GTK</th><th>Jabatan</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach ($gtk as $person): ?>
<tr>
    <td>
        <div class="person-cell">
            <?php if ($person['photo_path']): ?><img class="preview-thumb" src="<?= base_url($person['photo_path']) ?>" alt=""><?php else: ?><div class="preview-thumb"></div><?php endif ?>
            <div class="table-title">
                <strong><?= esc(trim(($person['front_title'] ? $person['front_title'] . ' ' : '') . $person['name'] . ($person['back_title'] ? ', ' . $person['back_title'] : ''))) ?></strong>
                <small><?= esc($person['short_bio'] ?: 'Belum ada bio singkat') ?></small>
            </div>
        </div>
    </td>
    <td><div class="role-badges"><?php if ($person['roles']): ?><?php foreach (explode(' • ', $person['roles']) as $role): ?><span class="role-badge"><?= esc($role) ?></span><?php endforeach ?><?php else: ?><span class="empty-inline">Belum ada jabatan</span><?php endif ?></div></td>
    <td><?= (int) $person['display_order'] ?></td>
    <td><span class="status-chip <?= (int) $person['is_active'] === 1 ? 'active' : 'inactive' ?>"><?= (int) $person['is_active'] === 1 ? 'AKTIF' : 'NONAKTIF' ?></span></td>
    <td><div class="table-actions"><a class="text-link" href="<?= site_url('manager/gtk/' . $person['id'] . '/edit') ?>">Edit</a><form action="<?= site_url('manager/gtk/' . $person['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus data GTK ini?');"><?= csrf_field() ?><button class="text-danger" type="submit">Hapus</button></form></div></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<?php endif ?>

<?= $this->endSection() ?>
