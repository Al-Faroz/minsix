<?= $this->extend('manager/layouts/main') ?>
<?= $this->section('content') ?>

<section class="form-panel">
    <div class="section-head">
        <div>
            <p class="eyebrow">ADMIN ONLY</p>
            <h2>ON/OFF Fitur Publik</h2>
            <p class="muted">OFF hanya menyembunyikan fitur dari website publik. Data konten tidak dihapus dan tetap dapat dikelola di CMS.</p>
        </div>
    </div>

    <form action="<?= site_url('manager/features') ?>" method="post">
        <?= csrf_field() ?>

        <div class="feature-list">
            <?php foreach ($features as $feature): ?>
                <?php $cap = $capabilities[$feature['feature_key']] ?? ['nav' => false, 'home' => false, 'description' => '']; ?>
                <article class="feature-row">
                    <div class="feature-copy">
                        <strong><?= esc($feature['label']) ?></strong>
                        <span><?= esc($cap['description']) ?></span>
                    </div>

                    <label class="switch-field">
                        <input type="checkbox" name="features[<?= esc($feature['feature_key']) ?>][is_enabled]" value="1" <?= (int) $feature['is_enabled'] === 1 ? 'checked' : '' ?>>
                        <span class="switch"></span><small>Aktif</small>
                    </label>

                    <?php if ($cap['nav']): ?>
                        <label class="switch-field">
                            <input type="checkbox" name="features[<?= esc($feature['feature_key']) ?>][show_in_nav]" value="1" <?= (int) $feature['show_in_nav'] === 1 ? 'checked' : '' ?>>
                            <span class="switch"></span><small>Navbar</small>
                        </label>
                    <?php else: ?>
                        <div class="feature-na">Navbar<br><small>—</small></div>
                    <?php endif ?>

                    <?php if ($cap['home']): ?>
                        <label class="switch-field">
                            <input type="checkbox" name="features[<?= esc($feature['feature_key']) ?>][show_on_home]" value="1" <?= (int) $feature['show_on_home'] === 1 ? 'checked' : '' ?>>
                            <span class="switch"></span><small>Homepage</small>
                        </label>
                    <?php else: ?>
                        <div class="feature-na">Homepage<br><small>—</small></div>
                    <?php endif ?>
                </article>
            <?php endforeach ?>
        </div>

        <div class="feature-note">
            <strong>Aturan Kabar Madrasah</strong>
            <p>Jika induk <b>Kabar Madrasah</b> OFF, Berita/Agenda/Prestasi/Galeri tidak akan tampil di publik walaupun subfiturnya masih ON. Nilai subfitur disimpan untuk digunakan kembali ketika Kabar dinyalakan.</p>
        </div>

        <div class="form-actions"><button class="btn primary" type="submit">Simpan Pengaturan Fitur</button></div>
    </form>
</section>

<?= $this->endSection() ?>
