<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Masuk CMS MIN 6 Jember') ?></title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/brand/favicon-32x32.png') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/manager/css/manager.css') ?>">
</head>
<body class="login-page">
<main class="login-shell">
    <section class="login-brand">
        <img class="login-brand-logo" src="<?= base_url('assets/brand/min6-logo.png') ?>" alt="Lambang MIN 6 Jember" width="82" height="82">
        <p class="eyebrow">CMS WEBSITE</p>
        <h1>MIN 6 Jember</h1>
        <p>Kelola konten website madrasah dengan tampilan sederhana dan terarah.</p>
    </section>

    <section class="login-card">
        <p class="eyebrow">MANAGER</p>
        <h2>Masuk ke CMS</h2>
        <p class="muted">Gunakan akun Admin atau Operator yang telah terdaftar.</p>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif ?>
        <?php $errors = session()->getFlashdata('errors') ?? []; ?>
        <?php if ($errors !== []): ?>
            <div class="alert danger">
                <?php foreach ($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach ?>
            </div>
        <?php endif ?>

        <form action="<?= site_url('manager/login') ?>" method="post" class="stack-form">
            <?= csrf_field() ?>
            <label>Username
                <input type="text" name="username" value="<?= esc(old('username')) ?>" maxlength="100" autocomplete="username" required autofocus>
            </label>
            <label>Password
                <input type="password" name="password" minlength="8" maxlength="255" autocomplete="current-password" required>
            </label>
            <button class="btn primary" type="submit">Masuk</button>
        </form>

        <p class="login-help">Akses CMS hanya untuk pengelola resmi MIN 6 Jember.</p>
        <a class="text-link" href="<?= site_url('/') ?>">← Kembali ke website</a>
    </section>
</main>
</body>
</html>
