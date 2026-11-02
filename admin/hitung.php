<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/functions/helpers.php';
require __DIR__ . '/functions/wp.php';

// ---- Ambil data & hitung ----
try {
    $kriteria   = wp_get_kriteria();
    $alternatif = wp_get_alternatif();
    $hasil      = wp_hitung($kriteria, $alternatif);
} catch (Throwable $ex) {
    $error      = $ex->getMessage();
    $kriteria   = [];
    $alternatif = [];
    $hasil      = null;
}

include 'template/head.php';
include 'template/nav-side.php';
include 'template/nav-top.php';

?>

<div class="page-header">
    <nav>
        <ol class="breadcrumb-modern">
            <li><a href="index.php"><i class="bi bi-house"></i> Home</a></li>
            <li>/</li>
            <li>Perhitungan</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-calculator"></i> Perhitungan Weighted Product</h1>
            <p class="mb-0">Hasil perhitungan metode WP berdasarkan kriteria dan alternatif yang tersedia.</p>
        </div>
        <a href="export_pdf.php" target="_blank" class="btn-brand" style="flex-shrink: 0;">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
    </div>
</div>


<?php if (!empty($error)): ?>
    <div class="callout-modern danger">
        <i class="bi bi-exclamation-triangle"></i>
        <strong>Gagal menghitung:</strong> <?= e($error) ?>
    </div>
<?php else: ?>

    <!-- ============ Matriks Keputusan ============ -->
    <div class="card-modern">
        <div class="card-head">
            <h5><i class="bi bi-table"></i> Matriks Keputusan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Alternatif</th>
                            <?php foreach ($kriteria as $k): ?>
                                <th title="<?= e($k['kriteria']) ?>"><?= e($k['code']) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alternatif as $a): ?>
                            <tr>
                                <td><strong><?= e($a['code']) ?></strong></td>
                                <?php foreach ($kriteria as $k): ?>
                                    <td class="text-center"><?= e((string) $a['nilai'][$k['code']]) ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============ Tahap 1 ============ -->
    <div class="card-modern">
        <div class="card-head">
            <h5><i class="bi bi-1-circle"></i> Tahap 1 — Normalisasi Bobot (Wj)</h5>
        </div>
        <div class="card-body">
            <div class="callout-modern">
                <strong>Σ Wj = <?= fmt($hasil['total_bobot'], 0) ?></strong>
            </div>
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Keterangan</th>
                            <?php foreach ($kriteria as $k): ?>
                                <th><?= e($k['code']) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Jenis</strong></td>
                            <?php foreach ($kriteria as $k): ?>
                                <td class="text-center">
                                    <span class="badge-soft <?= e($k['jenis']) ?>">
                                        <?= ucwords(e($k['jenis'])) ?>
                                    </span>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Bobot</strong></td>
                            <?php foreach ($kriteria as $k): ?>
                                <td class="text-center"><?= e((string) $k['bobot']) ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Wj</strong></td>
                            <?php foreach ($kriteria as $k): ?>
                                <td class="text-center"><?= fmt($hasil['wj'][$k['code']]) ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Wj Bertanda</strong></td>
                            <?php foreach ($kriteria as $k): ?>
                                <?php $neg = $hasil['wj_bertanda'][$k['code']] < 0; ?>
                                <td class="text-center" style="color:<?= $neg ? '#b91c1c' : '#15803d' ?>;font-weight:600;">
                                    <?= fmt($hasil['wj_bertanda'][$k['code']]) ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============ Tahap 2 ============ -->
    <div class="card-modern">
        <div class="card-head">
            <h5><i class="bi bi-2-circle"></i> Tahap 2 — Vektor S (Si)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr><th>Alternatif</th><th class="text-center">Nilai Si</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alternatif as $a): ?>
                            <tr>
                                <td><strong><?= e($a['code']) ?></strong> — <?= e($a['alternatif']) ?></td>
                                <td class="text-center"><?= fmt($hasil['si'][$a['code']]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============ Tahap 3 ============ -->
    <div class="card-modern">
        <div class="card-head">
            <h5><i class="bi bi-3-circle"></i> Tahap 3 — Vektor V (Vi) & Ranking</h5>
        </div>
        <div class="card-body">
            <div class="callout-modern success">
                <strong>Σ Si = <?= fmt($hasil['total_si']) ?></strong>
            </div>

            <h5 class="mb-3"><i class="bi bi-trophy"></i> Hasil Perangkingan</h5>
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th style="width:80px;">Ranking</th>
                            <th style="width:100px;">Kode</th>
                            <th>Nama Alternatif</th>
                            <th class="text-center" style="width:140px;">Vi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hasil['ranking'] as $r): ?>
                            <tr class="<?= $r['peringkat'] === 1 ? 'rank-1' : '' ?>">
                                <td>
                                    <span class="rank-badge <?= $r['peringkat'] === 1 ? 'gold' : '' ?>">
                                        <?= $r['peringkat'] ?>
                                    </span>
                                </td>
                                <td><?= e($r['code']) ?></td>
                                <td><?= e($r['alternatif']) ?></td>
                                <td class="text-center"><?= fmt($r['vi']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total Vi</strong></td>
                            <td class="text-center"><strong><?= fmt(array_sum($hasil['vi'])) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php $top = $hasil['ranking'][0] ?? null; ?>
            <?php if ($top): ?>
                <div class="callout-modern success mt-3 mb-0">
                    <i class="bi bi-medal"></i>
                    Berdasarkan perhitungan WP, alternatif terbaik adalah
                    <strong><?= e($top['alternatif']) ?></strong> (<?= e($top['code']) ?>)
                    dengan nilai Vi = <strong><?= fmt($top['vi']) ?></strong>.
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php endif; ?>

<?php include 'template/footer.php'; ?>