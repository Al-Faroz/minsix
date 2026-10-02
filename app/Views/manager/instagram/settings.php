<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="content-card">
    <div class="section-head">
        <div>
            <p class="eyebrow">ADMIN ONLY</p>
            <h2>Instagram Hybrid</h2>
            <p class="muted">API dapat dikonfigurasi dari CMS. Access Token disimpan terenkripsi dan tidak pernah ditampilkan kembali.</p>
        </div>
    </div>

    <div class="instagram-state-grid">
        <div class="metric-card"><span>MODE</span><strong style="font-size:1.25rem"><?= esc($settings['instagram_source_mode'] ?? 'HYBRID') ?></strong><small>Source carousel</small></div>
        <div class="metric-card"><span>API CACHE</span><strong><?= (int) $apiCount ?></strong><small>Record tersimpan</small></div>
        <div class="metric-card"><span>LAST FETCH</span><strong style="font-size:1rem"><?= $latestFetchedAt ? esc(date('d-m-Y H:i', strtotime($latestFetchedAt))) : '—' ?></strong><small>Cache terakhir</small></div>
        <div class="metric-card"><span>API STATUS</span><strong style="font-size:1.25rem"><?= $apiReady ? 'READY' : 'BELUM' ?></strong><small>Credential readiness</small></div>
    </div>
</section>

<section class="content-card">
    <div class="section-head"><div><p class="eyebrow">PENGATURAN</p><h2>Source & Carousel</h2></div></div>
    <form action="<?= site_url('manager/instagram-settings') ?>" method="post" class="settings-grid">
        <?= csrf_field() ?>
        <div class="field">
            <label>Source Mode</label>
            <select name="instagram_source_mode">
                <option value="HYBRID" <?= old('instagram_source_mode', $settings['instagram_source_mode'] ?? 'HYBRID') === 'HYBRID' ? 'selected' : '' ?>>HYBRID — API → cache → fallback manual</option>
                <option value="MANUAL" <?= old('instagram_source_mode', $settings['instagram_source_mode'] ?? 'HYBRID') === 'MANUAL' ? 'selected' : '' ?>>MANUAL — hanya konten manual</option>
            </select>
        </div>
        <div class="field">
            <label>Jumlah item carousel</label>
            <select name="instagram_display_count">
                <?php foreach ([6,7,8] as $count): ?><option value="<?= $count ?>" <?= (int) old('instagram_display_count', $settings['instagram_display_count'] ?? 8) === $count ? 'selected' : '' ?>><?= $count ?> item</option><?php endforeach ?>
            </select>
        </div>
        <div class="form-actions span-2"><button class="btn primary" type="submit">Simpan Pengaturan</button></div>
    </form>
</section>

<section class="content-card">
    <div class="section-head">
        <div>
            <p class="eyebrow">KONFIGURASI API</p>
            <h2>Credential Instagram</h2>
            <p class="muted">Base URL dibatasi ke endpoint resmi Meta. Token baru dienkripsi sebelum disimpan ke database.</p>
        </div>
    </div>

    <?php if (! $apiState['encryption_ready']): ?>
        <div class="feature-note" style="border-color:#ead9a6;background:#fff9e8">
            <strong>Encryption key belum tersedia.</strong>
            <p>Jalankan <code>php spark minsix:key:generate</code>, lalu salin hasilnya ke <code>.env</code> sebagai <code>encryption.key</code>. Non-secret setting tetap dapat disimpan, tetapi Access Token baru belum dapat disimpan aman.</p>
        </div>
    <?php endif ?>

    <?php if ($apiState['token_error']): ?>
        <div class="alert danger">Token database terdeteksi tetapi tidak dapat didekripsi dengan encryption key saat ini. Periksa key atau simpan token baru.</div>
    <?php endif ?>

    <form action="<?= site_url('manager/instagram-settings/api') ?>" method="post" class="settings-grid" autocomplete="off">
        <?= csrf_field() ?>
        <div class="field">
            <label>API Base URL</label>
            <input type="url" name="api_base_url" maxlength="255" required value="<?= esc(old('api_base_url', $apiState['base_url'] ?: 'https://graph.instagram.com')) ?>">
            <small>Hanya <code>https://graph.instagram.com</code> atau <code>https://graph.facebook.com</code>.</small>
        </div>
        <div class="field">
            <label>API Version <span class="muted">(opsional)</span></label>
            <input type="text" name="api_version" maxlength="50" value="<?= esc(old('api_version', $apiState['api_version'])) ?>" placeholder="Contoh: vXX.X">
            <small>Kosongkan bila endpoint yang digunakan tidak memerlukan versi pada URL.</small>
        </div>
        <div class="field">
            <label>Instagram User ID</label>
            <input type="text" name="instagram_user_id" maxlength="100" required value="<?= esc(old('instagram_user_id', $apiState['user_id'])) ?>" placeholder="ID akun dari Meta">
        </div>
        <div class="field">
            <label>Access Token</label>
            <input type="password" name="instagram_access_token" maxlength="10000" value="" autocomplete="new-password" placeholder="<?= $apiState['access_token_ready'] ? 'Token sudah tersedia — kosongkan untuk mempertahankan' : 'Tempel Access Token baru' ?>">
            <small>Token tersimpan tidak pernah ditampilkan kembali. Kosongkan field ini untuk mempertahankan token yang sudah ada.</small>
        </div>
        <div class="form-actions span-2"><button class="btn primary" type="submit">Simpan Konfigurasi API</button></div>
    </form>

    <div class="credential-list" style="margin-top:20px">
        <div><code>Encryption Key</code><span class="status-chip <?= $apiState['encryption_ready'] ? 'published' : 'draft' ?>"><?= $apiState['encryption_ready'] ? 'READY' : 'BELUM' ?></span></div>
        <div><code>API Base URL</code><span class="status-chip <?= $apiState['base_url_ready'] ? 'published' : 'draft' ?>"><?= $apiState['base_url_ready'] ? 'SET' : 'BELUM' ?></span></div>
        <div><code>User ID</code><span class="status-chip <?= $apiState['user_id_ready'] ? 'published' : 'draft' ?>"><?= $apiState['user_id_ready'] ? 'SET' : 'BELUM' ?></span></div>
        <div><code>Access Token</code><span class="status-chip <?= $apiState['access_token_ready'] ? 'published' : 'draft' ?>"><?= $apiState['access_token_ready'] ? 'SET · ' . esc($apiState['token_source']) : 'BELUM' ?></span></div>
    </div>

    <div class="integration-actions">
        <form action="<?= site_url('manager/instagram-settings/test') ?>" method="post">
            <?= csrf_field() ?>
            <button class="btn ghost" type="submit" <?= ! $apiReady ? 'disabled' : '' ?>>Tes Koneksi</button>
        </form>
        <form action="<?= site_url('manager/instagram-settings/sync') ?>" method="post" onsubmit="return confirm('Sinkronkan cache Instagram sekarang? Cache lama tidak akan dihapus bila API gagal.');">
            <?= csrf_field() ?>
            <button class="btn primary" type="submit" <?= ! $apiReady ? 'disabled' : '' ?>>Sinkronkan Sekarang</button>
        </form>
        <?php if ($apiState['has_database_token']): ?>
        <form action="<?= site_url('manager/instagram-settings/token/clear') ?>" method="post" onsubmit="return confirm('Hapus Access Token terenkripsi dari database? Token ENV, bila ada, tetap menjadi fallback.');">
            <?= csrf_field() ?>
            <button class="btn ghost" type="submit">Hapus Token DB</button>
        </form>
        <?php endif ?>
    </div>

    <p class="muted" style="margin-top:16px">Cron/CLI tetap tersedia melalui <code>php spark instagram:sync</code>. Untuk production, jadwalkan setiap 1–6 jam bila API digunakan.</p>
</section>

<?= $this->endSection() ?>
