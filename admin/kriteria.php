<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/functions/helpers.php';
// Proteksi Mode Demo
if (IS_DEMO && ($_SERVER['REQUEST_METHOD'] === 'POST')) {
    flash('error', 'Mode Demo: Anda tidak diizinkan mengubah data.');
    redirect('kriteria.php');
}

// --- Hapus Kriteria ---
if (isset($_POST['delete_kriteria_id'])) {
    $id = (int) $_POST['id'];
    try {
        $stmt = db()->prepare("DELETE FROM kriteria WHERE id = ?");
        $stmt->execute([$id]);
        flash('success', 'Data kriteria berhasil dihapus.');
    } catch (PDOException $e) {
        flash('error', 'Gagal menghapus data: ' . $e->getMessage());
    }
    redirect('kriteria.php');
}

// --- Edit Kriteria ---
if (isset($_POST['edit_kriteria'])) {
    $id       = (int) $_POST['id'];
    $code     = trim($_POST['kode_kriteria']);
    $kriteria = trim($_POST['nama_kriteria']);
    $jenis    = $_POST['jenis_kriteria'];
    $bobot    = (int) $_POST['bobot_kriteria'];

    if ($code && $kriteria && in_array($jenis, ['cost', 'benefit'])) {
        try {
            $stmt = db()->prepare("UPDATE kriteria SET code = ?, kriteria = ?, jenis = ?, bobot = ? WHERE id = ?");
            $stmt->execute([$code, $kriteria, $jenis, $bobot, $id]);
            flash('success', 'Data kriteria berhasil diperbarui.');
        } catch (PDOException $e) {
            flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    } else {
        flash('error', 'Input tidak valid. Pastikan semua kolom terisi.');
    }
    redirect('kriteria.php');
}

$stmt = db()->query("SELECT * FROM kriteria ORDER BY id ASC");
$kriteriaList = $stmt->fetchAll();

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
            <li>Kriteria</li>
        </ol>
    </nav>
    <h1><i class="bi bi-list-check"></i> Data Kriteria</h1>
    <p>Daftar kriteria penilaian beserta bobot dan jenisnya.</p>
</div>

<?php if ($flashSuccess): ?>
    <script>Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= $flashSuccess ?>', timer: 2000, showConfirmButton: false });</script>
<?php endif; ?>
<?php if ($flashError): ?>
    <script>Swal.fire({ icon: 'error', title: 'Gagal', text: '<?= $flashError ?>' });</script>
<?php endif; ?>

<div class="card-modern">
    <i class="bi bi-list-check bg-icon"></i>
    <div class="card-head">
        <h5><i class="bi bi-table"></i> Tabel Kriteria</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th width="60px" class="text-center">No</th>
                        <th>Kode</th>
                        <th>Kriteria</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-center">Bobot</th>
                        <th width="120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kriteriaList)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <img src="https://illustrations.popsy.co/amber/list.svg" alt="Empty">
                                    <h4>Belum Ada Data Kriteria</h4>
                                    <p>Data kriteria masih kosong. Silakan tambahkan data terlebih dahulu.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($kriteriaList as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><strong><?= e($row['code']) ?></strong></td>
                                <td><?= e($row['kriteria']) ?></td>
                                <td class="text-center">
                                    <span class="badge-soft <?= e($row['jenis']) ?>"><?= ucwords(e($row['jenis'])) ?></span>
                                </td>
                                <td class="text-center"><strong><?= e((string)$row['bobot']) ?></strong></td>
                                <td class="text-center" style="white-space: nowrap;">
                                    <button class="btn-soft" style="padding: 4px 8px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>" title="Edit">
                                        <i class="bi bi-pencil-square" style="color: #f59e0b;"></i>
                                    </button>
                                    <button class="btn-soft" style="padding: 4px 8px;" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id'] ?>" title="Hapus">
                                        <i class="bi bi-trash" style="color: #ef4444;"></i>
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

<!-- PINDAHKAN SEMUA MODAL KE SINI (DI LUAR TABEL) -->
<?php foreach ($kriteriaList as $row): ?>
    <!-- MODAL EDIT -->
    <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: var(--radius); border: none;">
                <div class="modal-header" style="border-bottom: 1px solid var(--slate-200);">
                    <h5 class="modal-title" style="font-size: 1rem; font-weight: 600;">
                        <i class="bi bi-pencil-square text-warning"></i> Edit Kriteria
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label" style="font-size: .85rem; font-weight: 600;">Kode Kriteria</label>
                            <input type="text" class="form-control" name="kode_kriteria" value="<?= e($row['code']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size: .85rem; font-weight: 600;">Nama Kriteria</label>
                            <input type="text" class="form-control" name="nama_kriteria" value="<?= e($row['kriteria']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size: .85rem; font-weight: 600;">Jenis</label>
                            <select name="jenis_kriteria" class="form-select">
                                <option value="benefit" <?= $row['jenis'] == 'benefit' ? 'selected' : '' ?>>Benefit</option>
                                <option value="cost" <?= $row['jenis'] == 'cost' ? 'selected' : '' ?>>Cost</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size: .85rem; font-weight: 600;">Bobot (1-10)</label>
                            <select name="bobot_kriteria" class="form-select">
                                <?php for ($i = 1; $i <= 10; $i++) : ?>
                                    <option value="<?= $i ?>" <?= $row['bobot'] == $i ? 'selected' : '' ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--slate-200);">
                        <button type="button" class="btn-soft" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_kriteria" class="btn-brand">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL HAPUS -->
    <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius: var(--radius); border: none; text-align: center;">
                <div class="modal-body p-4">
                    <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: #ef4444;"></i>
                    <h5 class="mt-3 mb-2" style="font-weight: 600;">Hapus Kriteria?</h5>
                    <p style="color: var(--slate-500); font-size: .9rem;">
                        Data <strong><?= e($row['code']) ?> - <?= e($row['kriteria']) ?></strong> akan dihapus permanen.
                    </p>
                </div>
                <div class="modal-footer justify-content-center" style="border-top: none; padding-top: 0;">
                    <form action="" method="post" class="d-flex gap-2 w-100 px-3 pb-3">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="button" class="btn-soft w-50 justify-content-center" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="delete_kriteria_id" class="btn-brand w-50 justify-content-center" style="background: #ef4444;">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include 'template/footer.php'; ?>