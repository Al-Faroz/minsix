<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<?php
$connected = (bool) ($apiState['connected'] ?? false);
$appReady = (bool) (($apiState['storage_ready'] ?? false) && ($apiState['app_id_ready'] ?? false) && ($apiState['app_secret_ready'] ?? false));
$daysRemaining = $apiState['token_days_remaining'] ?? null;
$accountLabel = trim((string) ($apiState['username'] ?? '')) !== ''
    ? '@' . trim((string) $apiState['username'])
    : (trim((string) ($apiState['user_id'] ?? '')) !== '' ? 'ID ' . $apiState['user_id'] : '—');
?>

<section class="content-card">
    <div class="section-head">
        <div>
            <p class="eyebrow">ADMIN ONLY</p>
            <h2>Instagram Login</h2>
            <p class="muted">Integrasi API memakai login resmi Instagram. Token dibuat dari proses login, disimpan terenkripsi, dan direfresh melalui lifecycle token.</p>
        </div>
    </div>

    <div class="instagram-state-grid">
        <div class="metric-card"><span>MODE</span><strong style="font-size:1.25rem"><?= esc($settings['instagram_source_mode'] ?? 'HYBRID') ?></strong><small>Source carousel</small></div>
        <div class="metric-card"><span>AKUN</span><strong style="font-size:1rem"><?= esc($accountLabel) ?></strong><small><?= $connected ? 'Terhubung' : 'Belum terhubung' ?></small></div>
        <div class="metric-card"><span>TOKEN</span><strong style="font-size:1.1rem"><?= $connected ? ($daysRemaining !== null ? (int) $daysRemaining . ' hari' : 'AKTIF') : (($apiState['token_expired'] ?? false) ? 'EXPIRED' : '—') ?></strong><small>Sisa masa berlaku</small></div>
        <div class="metric-card"><span>API CACHE</span><strong><?= (int) $apiCount ?></strong><small><?= $latestFetchedAt ? 'Fetch ' . esc(date('d-m-Y H:i', strtotime($latestFetchedAt))) : 'Belum pernah fetch' ?></small></div>
    </div>
</section>

<section class="content-card">
    <div class="section-head"><div><p class="eyebrow">PENGATURAN</p><h2>Source & Carousel</h2></div></div>
    <form action="<?= site_url('manager/instagram-settings') ?>" method="post" class="settings-grid">
        <?= csrf_field() ?>
        <div class="field">
            <label>Source Mode</label>
            <select name="instagram_source_mode">
                <option value="HYBRID" <?= old('instagram_source_mode', $settings['instagram_source_mode'] ?? 'HYBRID') === 'HYBRID' ? 'selected' : '' ?>>HYBRID — cache Instagram → fallback manual</option>
                <option value="MANUAL" <?= old('instagram_source_mode', $settings['instagram_source_mode'] ?? 'HYBRID') === 'MANUAL' ? 'selected' : '' ?>>MANUAL — hanya konten manual</option>
            </select>
        </div>
        <div class="field">
            <label>Jumlah item carousel</label>
            <select name="instagram_display_count">
                <?php foreach ([6, 7, 8] as $count): ?><option value="<?= $count ?>" <?= (int) old('instagram_display_count', $settings['instagram_display_count'] ?? 8) === $count ? 'selected' : '' ?>><?= $count ?> post</option><?php endforeach ?>
            </select>
        </div>
        <div class="form-actions span-2"><button class="btn primary" type="submit">Simpan Pengaturan</button></div>
    </form>
</section>

<section class="content-card">
    <div class="section-head">
        <div>
            <p class="eyebrow">META APP</p>
            <h2>Konfigurasi Instagram Login</h2>
            <p class="muted">Isi App ID dan App Secret dari Meta Developer. User ID dan Access Token tidak diinput manual; keduanya diperoleh setelah tombol Hubungkan Instagram.</p>
        </div>
    </div>

    <?php if (! ($apiState['encryption_ready'] ?? false)): ?>
        <div class="feature-note" style="border-color:#ead9a6;background:#fff9e8">
            <strong>Encryption key belum tersedia.</strong>
            <p>Jalankan <code>php spark minsix:key:generate</code>, lalu salin hasilnya ke <code>.env</code> sebagai <code>encryption.key</code>. App Secret dan token tidak akan disimpan tanpa encryption key.</p>
        </div>
    <?php endif ?>

    <?php if (($apiState['app_secret_error'] ?? false) || ($apiState['token_error'] ?? false)): ?>
        <div class="alert danger">Secret/token terenkripsi tidak dapat dibaca dengan encryption key saat ini. Periksa key; untuk token, lakukan Hubungkan Instagram ulang.</div>
    <?php endif ?>

    <form action="<?= site_url('manager/instagram-settings/app') ?>" method="post" class="settings-grid" autocomplete="off">
        <?= csrf_field() ?>
        <div class="field">
            <label>Meta App ID *</label>
            <input type="text" inputmode="numeric" name="app_id" maxlength="100" required value="<?= esc(old('app_id', $apiState['app_id'] ?? '')) ?>" placeholder="Contoh: 123456789012345">
        </div>
        <div class="field">
            <label>API Version <span class="muted">(opsional)</span></label>
            <input type="text" name="api_version" maxlength="50" value="<?= esc(old('api_version', $apiState['api_version'] ?? '')) ?>" placeholder="Contoh: v25.0">
            <small>Kosongkan untuk endpoint Instagram yang tidak memerlukan versi pada path.</small>
        </div>
        <div class="field span-2">
            <label>Meta App Secret</label>
            <input type="password" name="app_secret" maxlength="1000" value="" autocomplete="new-password" placeholder="<?= ($apiState['app_secret_ready'] ?? false) ? 'Secret sudah tersedia — kosongkan untuk mempertahankan' : 'Masukkan App Secret' ?>">
            <small>Disimpan terenkripsi dan tidak pernah ditampilkan kembali.</small>
        </div>
        <div class="field span-2">
            <label>OAuth Redirect URI</label>
            <input type="text" readonly value="<?= esc($callbackUrl) ?>" onclick="this.select()">
            <small>Daftarkan URL ini persis pada konfigurasi Instagram API di Meta Developer.</small>
        </div>
        <div class="form-actions span-2"><button class="btn primary" type="submit">Simpan Meta App</button></div>
    </form>
</section>

<section class="content-card">
    <div class="section-head">
        <div>
            <p class="eyebrow">CONNECTION & TOKEN</p>
            <h2>Akun Instagram</h2>
            <p class="muted">Scope integrasi: <code>instagram_business_basic</code>. Cache lama tidak dihapus ketika token bermasalah atau akun diputus.</p>
        </div>
    </div>

    <div class="credential-list">
        <div><code>Encryption Key</code><span class="status-chip <?= ($apiState['encryption_ready'] ?? false) ? 'published' : 'draft' ?>"><?= ($apiState['encryption_ready'] ?? false) ? 'READY' : 'BELUM' ?></span></div>
        <div><code>Integration Storage</code><span class="status-chip <?= ($apiState['storage_ready'] ?? false) ? 'published' : 'draft' ?>"><?= ($apiState['storage_ready'] ?? false) ? 'READY' : 'JALANKAN SQL' ?></span></div>
        <div><code>Meta App</code><span class="status-chip <?= $appReady ? 'published' : 'draft' ?>"><?= $appReady ? 'READY' : 'BELUM' ?></span></div>
        <div><code>Instagram Login</code><span class="status-chip <?= $connected ? 'published' : 'draft' ?>"><?= $connected ? esc($accountLabel) : (($apiState['token_expired'] ?? false) ? 'REAUTH' : 'BELUM') ?></span></div>
        <div><code>Token Expires</code><span><?= ! empty($apiState['token_expires_at']) ? esc(date('d-m-Y H:i', strtotime($apiState['token_expires_at']))) : '—' ?></span></div>
        <div><code>Last Refresh</code><span><?= ! empty($apiState['last_refreshed_at']) ? esc(date('d-m-Y H:i', strtotime($apiState['last_refreshed_at']))) : '—' ?></span></div>
        <div><code>Scope</code><span><?= esc($apiState['granted_scopes'] ?: 'instagram_business_basic') ?></span></div>
    </div>

    <div class="integration-actions">
        <?php if (! $connected): ?>
            <form action="<?= site_url('manager/instagram-settings/connect') ?>" method="post">
                <?= csrf_field() ?>
                <button class="btn primary" type="submit" <?= (! $appReady || ! ($apiState['encryption_ready'] ?? false)) ? 'disabled' : '' ?>>Hubungkan Instagram</button>
            </form>
        <?php else: ?>
            <form action="<?= site_url('manager/instagram-settings/test') ?>" method="post">
                <?= csrf_field() ?>
                <button class="btn ghost" type="submit">Tes Koneksi</button>
            </form>
            <form action="<?= site_url('manager/instagram-settings/token/refresh') ?>" method="post">
                <?= csrf_field() ?>
                <button class="btn ghost" type="submit">Refresh Token</button>
            </form>
            <form action="<?= site_url('manager/instagram-settings/sync') ?>" method="post" onsubmit="return confirm('Sinkronkan cache Instagram sekarang? Cache lama tidak akan dihapus bila API gagal.');">
                <?= csrf_field() ?>
                <button class="btn primary" type="submit">Sinkronkan Sekarang</button>
            </form>
            <form action="<?= site_url('manager/instagram-settings/disconnect') ?>" method="post" onsubmit="return confirm('Putuskan akun Instagram? Token lokal akan dihapus, tetapi cache media lama tetap dipertahankan.');">
                <?= csrf_field() ?>
                <button class="btn ghost" type="submit">Putuskan Akun</button>
            </form>
        <?php endif ?>
    </div>

    <?php if (($apiState['refresh_due'] ?? false) && $connected): ?>
        <div class="feature-note" style="margin-top:18px"><strong>Token mendekati masa refresh.</strong><p>Scheduled <code>instagram:sync</code> akan mencoba refresh otomatis sebelum mengambil media.</p></div>
    <?php endif ?>

    <p class="muted" style="margin-top:16px">Production: jalankan <code>php spark instagram:sync</code> berkala (misalnya setiap 1–6 jam). Command ini juga menjalankan pemeriksaan lifecycle token sebelum sinkronisasi media.</p>
</section>

<?= $this->endSection() ?>
