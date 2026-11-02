<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/functions/helpers.php';

// Proteksi Mode Demo
if (IS_DEMO && ($_SERVER['REQUEST_METHOD'] === 'POST')) {
    flash('error', 'Mode Demo: Anda tidak diizinkan mengubah data.');
    redirect('bobot.php');
}

// --- LOGIKA UPDATE BOBOT ---
if (isset($_POST['edit_bobot'])) {
    $id_bobot = (int) $_POST['id_bobot'];
    $c = [];
    for ($i = 1; $i <= 8; $i++) {
        $c[$i] = (int) ($_POST['c' . $i] ?? 0);
    }
    try {
        $sql = "UPDATE bobot SET c1 = ?, c2 = ?, c3 = ?, c4 = ?, c5 = ?, c6 = ?, c7 = ?, c8 = ? WHERE id_bobot = ?";
        $stmt = db()->prepare($sql);
        $stmt->execute([$c[1], $c[2], $c[3], $c[4], $c[5], $c[6], $c[7], $c[8], $id_bobot]);
        flash('success', 'Nilai bobot berhasil diperbarui.');
    } catch (PDOException $e) {
        flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
    }
    redirect('bobot.php');
}

$kriteriaList = db()->query("SELECT * FROM kriteria ORDER BY id ASC")->fetchAll();
$sql = "SELECT a.alternatif, a.code, b.* FROM bobot b JOIN alternatif a ON b.id_alter = a.id_alter ORDER BY a.id_alter ASC";
$bobotList = db()->query($sql)->fetchAll();

$flashSuccess = flash('success');
$flashError   = flash('error');

include 'template/head.php';
include 'template/nav-side.php';
include 'template/nav-top.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="page-header">
    <nav>
        <ol class="breadcrumb-modern">
            <li><a href="index.php"><i class="bi bi-house"></i> Home</a></li>
            <li>/</li>
            <li>Nilai Bobot</li>
        </ol>
    </nav>
    <h1><i class="bi bi-sliders"></i> Nilai Bobot Alternatif</h1>
    <p>Matriks keputusan nilai C1-C8 untuk setiap alternatif rumah.</p>
</div>

<?php if ($flashSuccess): ?>
    <script>Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= $flashSuccess ?>', timer: 2000, showConfirmButton: false });</script>
<?php endif; ?>
<?php if ($flashError): ?>
    <script>Swal.fire({ icon: 'error', title: 'Gagal', text: '<?= $flashError ?>' });</script>
<?php endif; ?>

<div class="card-modern">
    <i class="bi bi-sliders bg-icon"></i>
    <div class="card-head">
        <h5><i class="bi bi-table"></i> Matriks Nilai Bobot</h5>
        <a href="alternatif.php" class="btn-soft" style="font-size: .8rem;">
            <i class="bi bi-plus-circle"></i> Tambah Alternatif Baru
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th width="60px" class="text-center">No</th>
                        <th>Alternatif</th>
                        <?php foreach ($kriteriaList as $k): ?>
                            <th class="text-center" title="<?= e($k['kriteria']) ?> (<?= e($k['jenis']) ?>)">
                                <?= e($k['code']) ?>
                            </th>
                        <?php endforeach; ?>
                        <th width="80px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bobotList)): ?>
                        <tr>
                            <td colspan="<?= 3 + count($kriteriaList) ?>">
                                <div class="empty-state">
                                    <img src="https://illustrations.popsy.co/amber/data-analysis.svg" alt="Empty">
                                    <h4>Belum Ada Data Bobot</h4>
                                    <p>Data bobot akan muncul otomatis setelah Anda menambahkan data Alternatif.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($bobotList as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td>
                                    <strong><?= e($row['code']) ?></strong><br>
                                    <span style="font-size: .8rem; color: var(--slate-500);"><?= e($row['alternatif']) ?></span>
                                </td>
                                <?php for ($i = 1; $i <= 8; $i++): ?>
                                    <td class="text-center"><?= e((string)$row['c' . $i]) ?></td>
                                <?php endfor; ?>
                                <td class="text-center">
                                    <button class="btn-soft" style="padding: 4px 8px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_bobot'] ?>" title="Edit Nilai">
                                        <i class="bi bi-pencil-square" style="color: #f59e0b;"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- PINDAHKAN SEMUA MODAL KE SINI -->
<?php foreach ($bobotList as $row): ?>
    <div class="modal fade" id="editModal<?= $row['id_bobot'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: var(--radius); border: none;">
                <div class="modal-header" style="border-bottom: 1px solid var(--slate-200);">
                    <h5 class="modal-title" style="font-size: 1rem; font-weight: 600;">
                        <i class="bi bi-pencil-square text-warning"></i> Edit Nilai: <?= e($row['code']) ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id_bobot" value="<?= $row['id_bobot'] ?>">
                        <div class="callout-modern" style="margin-bottom: 20px;">
                            <strong>Alternatif:</strong> <?= e($row['alternatif']) ?>
                        </div>
                        <div class="row g-3">
                            <?php $c = 1; foreach ($kriteriaList as $k): $colName = 'c' . $c; ?>
                                <div class="col-md-6 col-lg-3">
                                    <div class="mb-2">
                                        <label class="form-label" style="font-size: .8rem; font-weight: 600;">
                                            <?= e($k['code']) ?> - <?= e($k['kriteria']) ?>
                                            <span class="badge-soft <?= e($k['jenis']) ?>" style="font-size: .65rem; margin-left: 4px;">
                                                <?= e($k['jenis']) ?>
                                            </span>
                                        </label>
                                        <input type="number" class="form-control form-control-sm" name="c<?= $c ?>" value="<?= e((string)$row[$colName]) ?>" required>
                                    </div>
                                </div>
                            <?php $c++; endforeach; ?>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--slate-200);">
                        <button type="button" class="btn-soft" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_bobot" class="btn-brand">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include 'template/footer.php'; ?>