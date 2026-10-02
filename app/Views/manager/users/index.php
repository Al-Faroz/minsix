<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<div class="toolbar">
    <div>
        <p class="eyebrow">PENGATURAN</p>
        <h2 style="margin:.1rem 0">Pengguna</h2>
        <p class="muted" style="margin:.35rem 0 0">Kelola akun Admin dan Operator CMS. Akun lama dinonaktifkan, bukan dihapus.</p>
    </div>
    <div class="toolbar-actions"><a class="btn primary" href="<?= site_url('manager/users/new') ?>">+ Tambah Pengguna</a></div>
</div>

<?php if ($users === []): ?>
    <div class="empty-state">Belum ada pengguna CMS.</div>
<?php else: ?>
<div class="table-wrap">
<table class="cms-table">
    <thead>
        <tr>
            <th>Pengguna</th>
            <th>Role</th>
            <th>Status</th>
            <th>Login Terakhir</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <?php $isSelf = (int) $user['id'] === (int) $currentUserId; ?>
        <tr>
            <td>
                <div class="table-title">
                    <strong><?= esc($user['name']) ?><?= $isSelf ? ' (Anda)' : '' ?></strong>
                    <small><?= esc($user['username']) ?></small>
                </div>
            </td>
            <td><span class="status-chip category"><?= esc($user['role']) ?></span></td>
            <td>
                <span class="status-chip <?= (int) $user['is_active'] === 1 ? 'published' : 'draft' ?>">
                    <?= (int) $user['is_active'] === 1 ? 'AKTIF' : 'NONAKTIF' ?>
                </span>
            </td>
            <td>
                <?= $user['last_login_at'] ? esc(date('d-m-Y H:i', strtotime($user['last_login_at']))) : '<span class="muted">Belum pernah</span>' ?>
            </td>
            <td>
                <div class="table-actions">
                    <a class="text-link" href="<?= site_url('manager/users/' . $user['id'] . '/edit') ?>">Edit</a>
                    <?php if (! $isSelf): ?>
                    <form action="<?= site_url('manager/users/' . $user['id'] . '/toggle') ?>" method="post" onsubmit="return confirm('<?= (int) $user['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?> akun <?= esc(addslashes($user['username'])) ?>?');">
                        <?= csrf_field() ?>
                        <button class="<?= (int) $user['is_active'] === 1 ? 'text-danger' : 'text-link' ?>" type="submit">
                            <?= (int) $user['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?>
                        </button>
                    </form>
                    <?php endif ?>
                </div>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
</div>
<?php endif ?>

<section class="content-card" style="margin-top:18px">
    <p class="eyebrow">KEAMANAN AKUN</p>
    <h3 style="margin:.2rem 0 .55rem">Aturan Pengguna</h3>
    <p class="muted" style="margin:0">Sistem harus selalu memiliki minimal satu Admin aktif. Admin yang sedang login tidak dapat menonaktifkan atau menurunkan role dirinya sendiri. Password tidak pernah ditampilkan kembali setelah disimpan.</p>
</section>

<?= $this->endSection() ?>
