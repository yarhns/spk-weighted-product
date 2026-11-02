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
    
    $jml_kriteria   = count($kriteria);
    $jml_alternatif = count($alternatif);
    $top3           = array_slice($hasil['ranking'], 0, 3); // Ambil 3 terbaik
} catch (Throwable $ex) {
    $error = $ex->getMessage();
    $jml_kriteria = $jml_alternatif = 0;
    $hasil = null;
    $top3 = [];
}

include 'template/head.php';
include 'template/nav-side.php'; // Sidebar dulu
include 'template/nav-top.php';  // Topbar belakangan
?>

<?php if (!empty($error)): ?>
    <!-- Error message jika database bermasalah -->
    <div class="callout-modern danger" style="margin-top: 20px;">
        <i class="bi bi-exclamation-triangle"></i> <strong>Gagal memuat data:</strong> <?= e($error) ?>
    </div>
<?php else: ?>

        <!-- ============ HERO BANNER ============ -->
    <div class="hero-dashboard">
        <div class="hero-badge">
            <i class="bi bi-house-check"></i> SPK Weighted Product
        </div>
        <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin') ?> <?= IS_DEMO ? '🔍' : '👋' ?></h1>
        
        <p>
            <?php if (IS_DEMO): ?>
                Anda sedang menjelajahi aplikasi dalam <strong>Mode Demo</strong>. Silakan lihat-lihat data dan hasil perhitungan. Untuk mengubah data, silakan login sebagai Admin.
            <?php else: ?>
                Kelola data kriteria, alternatif, dan lihat hasil perangkingan pemilihan rumah tinggal menggunakan metode Weighted Product.
            <?php endif; ?>
        </p>
        
        <a href="hitung.php" class="btn-brand" style="background: #fff; color: var(--brand-700); margin-top: 10px;">
            <i class="bi bi-calculator"></i> Lihat Hasil Perhitungan
        </a>
    </div>

    <!-- ============ STAT CARDS ============ -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="icon"><i class="bi bi-list-check"></i></div>
            <div class="label">Kriteria Penilaian</div>
            <div class="value"><?= $jml_kriteria ?></div>
        </div>
        <div class="stat-card" style="--brand-600: #22c55e;"> <!-- Hijau -->
            <div class="icon" style="background: linear-gradient(135deg, #dcfce7, #fff);"><i class="bi bi-houses"></i></div>
            <div class="label">Alternatif Rumah</div>
            <div class="value"><?= $jml_alternatif ?></div>
        </div>
        <div class="stat-card" style="--brand-600: #f59e0b;"> <!-- Oranye -->
            <div class="icon" style="background: linear-gradient(135deg, #fef3c7, #fff);"><i class="bi bi-trophy"></i></div>
            <div class="label">Rekomendasi Terbaik</div>
            <div class="value" style="font-size: 1.1rem; margin-top: 6px; color: var(--brand-600);">
                <?= $hasil && isset($hasil['ranking'][0]) ? e($hasil['ranking'][0]['code']) : '-' ?>
            </div>
            <div style="font-size: .75rem; color: var(--slate-500); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <?= $hasil && isset($hasil['ranking'][0]) ? e($hasil['ranking'][0]['alternatif']) : 'Belum ada data' ?>
            </div>
        </div>
        <div class="stat-card" style="--brand-600: #0ea5e9;"> <!-- Biru -->
            <div class="icon" style="background: linear-gradient(135deg, #e0f2fe, #fff);"><i class="bi bi-file-earmark-pdf"></i></div>
            <div class="label">Laporan</div>
            <div style="margin-top: 10px;">
                <a href="hitung.php" class="btn-soft" style="padding: 6px 12px; font-size: .8rem;">
                    <i class="bi bi-download"></i> Export PDF
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ============ CHART HASIL ============ -->
        <div class="col-lg-8 mb-4">
            <div class="chart-card h-100 mb-0">
                <div class="card-head">
                    <h5><i class="bi bi-bar-chart-fill text-primary"></i> Grafik Nilai Preferensi (Vi)</h5>
                    <span class="badge-soft brand" style="font-size:.7rem;">Semua Alternatif</span>
                </div>
                <div class="card-body" style="padding: 20px;">
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="chartVi"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============ TOP 3 RANKING ============ -->
        <div class="col-lg-4 mb-4">
            <div class="chart-card h-100 mb-0">
                <div class="card-head">
                    <h5><i class="bi bi-award-fill text-warning"></i> Top 3 Rekomendasi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-modern">
                            <tbody>
                                <?php foreach ($top3 as $t): ?>
                                    <tr>
                                        <td style="width: 50px;">
                                            <span class="rank-badge <?= $t['peringkat'] === 1 ? 'gold' : '' ?>">
                                                <?= $t['peringkat'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: var(--slate-800);"><?= e($t['code']) ?></div>
                                            <div style="font-size: .75rem; color: var(--slate-500);"><?= e($t['alternatif']) ?></div>
                                        </td>
                                        <td class="text-end" style="font-weight: 600; color: var(--brand-600);">
                                            <?= fmt($t['vi']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top text-center">
                        <a href="hitung.php" class="text-decoration-none" style="font-size: .85rem; font-weight: 600; color: var(--brand-600);">
                            Lihat Semua Ranking <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php include 'template/footer.php'; ?>

<!-- Chart.js v4 (Modern) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('chartVi');
    if (!ctx) return;

    // Data dari PHP ke JS
    const labels = <?= json_encode(array_column($hasil['ranking'] ?? [], 'code')) ?>;
    const dataVi = <?= json_encode(array_column($hasil['ranking'] ?? [], 'vi')) ?>;

    // Buat gradient untuk batang grafik
    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, '#6366f1');
    gradient.addColorStop(1, '#a5b4fc');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai Vi',
                data: dataVi,
                backgroundColor: gradient,
                borderRadius: 6,
                hoverBackgroundColor: '#4f46e5'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return ' Vi: ' + context.parsed.y.toFixed(6);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { font: { size: 11 }, color: '#64748b' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#64748b' }
                }
            }
        }
    });
});
</script>