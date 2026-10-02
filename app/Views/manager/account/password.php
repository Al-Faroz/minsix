<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="form-panel">
    <p class="eyebrow">AKUN</p>
    <h2>Ubah Password</h2>
    <p class="muted">Gunakan minimal 10 karakter. Password sementara dari command pembuatan user sebaiknya segera diganti.</p>

    <form action="<?= site_url('manager/account/password') ?>" method="post" class="stack-form narrow">
        <?= csrf_field() ?>
        <label>Password saat ini
            <input type="password" name="current_password" maxlength="255" autocomplete="current-password" required>
        </label>
        <label>Password baru
            <input type="password" name="password" minlength="10" maxlength="255" autocomplete="new-password" required>
        </label>
        <label>Ulangi password baru
            <input type="password" name="password_confirm" minlength="10" maxlength="255" autocomplete="new-password" required>
        </label>
        <div class="form-actions">
            <a class="btn ghost" href="<?= site_url('manager') ?>">Kembali</a>
            <button class="btn primary" type="submit">Simpan Password</button>
        </div>
    </form>
</section>

<?= $this->endSection() ?>
