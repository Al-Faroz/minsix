<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="content-card">
    <div class="section-head">
        <div>
            <p class="eyebrow">ADMIN ONLY</p>
            <h2>Instagram Hybrid</h2>
            <p class="muted">Credential tidak disimpan di database dan tidak pernah ditampilkan oleh CMS. Token harus diatur melalui <code>.env</code>.</p>
        </div>
    </div>

    <div class="instagram-state-grid">
        <div class="metric-card"><span>MODE</span><strong style="font-size:1.25rem"><?= esc($settings['instagram_source_mode'] ?? 'HYBRID') ?></strong><small>Source carousel</small></div>
        <div class="metric-card"><span>API CACHE</span><strong><?= (int) $apiCount ?></strong><small>Record tersimpan</small></div>
        <div class="metric-card"><span>LAST FETCH</span><strong style="font-size:1rem"><?= $latestFetchedAt ? esc(date('d-m-Y H:i', strtotime($latestFetchedAt))) : '—' ?></strong><small>Cache terakhir</small></div>
        <div class="metric-card"><span>API ENV</span><strong style="font-size:1.25rem"><?= $apiReady ? 'READY' : 'BELUM' ?></strong><small>Credential readiness</small></div>
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
                <option value="MANUAL" <?= old('instagram_source_mode', $settings['instagram_source_mode'] ?? 'HYBRID') === 'MANUAL' ? 'selected' : '' ?>>MANUAL — hanya fallback manual</option>
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
    <div class="section-head"><div><p class="eyebrow">ENVIRONMENT</p><h2>Status Credential</h2><p class="muted">Nilai credential tidak pernah ditampilkan; CMS hanya menunjukkan apakah variabel tersedia.</p></div></div>
    <div class="credential-list">
        <?php foreach ([
            'instagram.apiBaseUrl' => $apiState['base_url'],
            'instagram.apiVersion' => $apiState['api_version'],
            'instagram.userId' => $apiState['user_id'],
            'instagram.accessToken' => $apiState['access_token'],
        ] as $name => $ready): ?>
            <div><code><?= esc($name) ?></code><span class="status-chip <?= $ready ? 'published' : 'draft' ?>"><?= $ready ? 'SET' : 'BELUM' ?></span></div>
        <?php endforeach ?>
    </div>
    <div class="form-actions" style="margin-top:18px">
        <form action="<?= site_url('manager/instagram-settings/sync') ?>" method="post" onsubmit="return confirm('Sinkronkan cache Instagram sekarang? Cache lama tidak akan dihapus bila API gagal.');">
            <?= csrf_field() ?>
            <button class="btn primary" type="submit" <?= ! $apiReady ? 'disabled' : '' ?>>Sinkronkan Sekarang</button>
        </form>
    </div>
    <p class="muted" style="margin-top:16px">Cron/CLI: <code>php spark instagram:sync</code>. Jadwal yang disarankan dokumen acuan adalah setiap 1–6 jam.</p>
</section>

<?= $this->endSection() ?>
