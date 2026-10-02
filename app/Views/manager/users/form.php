<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>
<?php $editing = $user !== null; ?>
<?php $isSelf = $editing && (int) $user['id'] === (int) $currentUserId; ?>

<div class="form-page">
    <div class="form-main">
        <section class="content-card">
            <div class="section-head">
                <div>
                    <p class="eyebrow">PENGGUNA</p>
                    <h2><?= $editing ? 'Edit Pengguna' : 'Tambah Pengguna' ?></h2>
                    <p class="muted"><?= $editing ? 'Perbarui identitas, role, dan status akun CMS.' : 'Buat akun baru untuk Admin atau Operator.' ?></p>
                </div>
            </div>

            <form action="<?= $editing ? site_url('manager/users/' . $user['id']) : site_url('manager/users') ?>" method="post" class="settings-grid">
                <?= csrf_field() ?>

                <div class="field">
                    <label>Nama</label>
                    <input type="text" name="name" maxlength="150" required value="<?= esc(old('name', $user['name'] ?? '')) ?>">
                </div>

                <div class="field">
                    <label>Username</label>
                    <input type="text" name="username" minlength="3" maxlength="100" pattern="[A-Za-z0-9._-]+" required autocomplete="off" value="<?= esc(old('username', $user['username'] ?? '')) ?>">
                    <small>Gunakan huruf, angka, titik, underscore, atau tanda minus.</small>
                </div>

                <div class="field">
                    <label>Role</label>
                    <select name="role" required <?= $isSelf ? 'aria-describedby="self-role-note"' : '' ?>>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= esc($role) ?>" <?= old('role', $user['role'] ?? 'OPERATOR') === $role ? 'selected' : '' ?>><?= esc($role) ?></option>
                        <?php endforeach ?>
                    </select>
                    <?php if ($isSelf): ?><small id="self-role-note">Role akun yang sedang digunakan tidak dapat diturunkan dari ADMIN.</small><?php endif ?>
                </div>

                <?php if ($editing): ?>
                <div class="field">
                    <label>Status</label>
                    <select name="is_active">
                        <option value="1" <?= (string) old('is_active', $user['is_active']) === '1' ? 'selected' : '' ?>>AKTIF</option>
                        <option value="0" <?= (string) old('is_active', $user['is_active']) === '0' ? 'selected' : '' ?> <?= $isSelf ? 'disabled' : '' ?>>NONAKTIF</option>
                    </select>
                    <?php if ($isSelf): ?><small>Akun yang sedang digunakan harus tetap aktif.</small><?php endif ?>
                </div>
                <?php else: ?>
                <div class="field">
                    <label>Password Awal</label>
                    <input type="password" name="password" minlength="10" maxlength="255" required autocomplete="new-password">
                    <small>Minimal 10 karakter. Berikan kepada pengguna melalui saluran yang aman.</small>
                </div>
                <div class="field">
                    <label>Ulangi Password Awal</label>
                    <input type="password" name="password_confirm" minlength="10" maxlength="255" required autocomplete="new-password">
                </div>
                <?php endif ?>

                <div class="form-actions span-2">
                    <a class="btn ghost" href="<?= site_url('manager/users') ?>">Kembali</a>
                    <button class="btn primary" type="submit"><?= $editing ? 'Simpan Perubahan' : 'Buat Pengguna' ?></button>
                </div>
            </form>
        </section>

        <?php if ($editing): ?>
        <section class="content-card">
            <p class="eyebrow">PASSWORD</p>
            <h3>Reset Password</h3>
            <p class="muted">Admin dapat menetapkan password baru tanpa mengetahui password lama. Password baru tidak dicatat ke audit log.</p>

            <form action="<?= site_url('manager/users/' . $user['id'] . '/reset-password') ?>" method="post" class="settings-grid">
                <?= csrf_field() ?>
                <div class="field">
                    <label>Password Baru</label>
                    <input type="password" name="password" minlength="10" maxlength="255" required autocomplete="new-password">
                </div>
                <div class="field">
                    <label>Ulangi Password Baru</label>
                    <input type="password" name="password_confirm" minlength="10" maxlength="255" required autocomplete="new-password">
                </div>
                <div class="form-actions span-2">
                    <button class="btn dark" type="submit" onclick="return confirm('Reset password akun <?= esc(addslashes($user['username'])) ?>?')">Reset Password</button>
                </div>
            </form>
        </section>
        <?php endif ?>
    </div>

    <aside class="form-side">
        <section class="content-card">
            <p class="eyebrow">AKSES</p>
            <h3>ADMIN</h3>
            <p class="muted">Sistem + konten: pengguna, pengaturan, fitur, SEO, Instagram API, dan seluruh konten.</p>
        </section>
        <section class="content-card">
            <p class="eyebrow">AKSES</p>
            <h3>OPERATOR</h3>
            <p class="muted">Konten saja: Beranda, Profil, Program, GTK, Kabar, SPMB, Media, dan Instagram Manual.</p>
        </section>
    </aside>
</div>

<?= $this->endSection() ?>
