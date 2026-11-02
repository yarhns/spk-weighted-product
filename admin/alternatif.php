<?php
require __DIR__ . '/auth.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/functions/helpers.php';
// Proteksi Mode Demo
if (IS_DEMO && ($_SERVER['REQUEST_METHOD'] === 'POST')) {
    flash('error', 'Mode Demo: Anda tidak diizinkan mengubah data.');
    redirect('alternatif.php');
}

// --- LOGIKA CRUD (SAMA SEPERTI SEBELUMNYA) ---
if (isset($_POST['delete_id'])) {
    $id = (int) $_POST['id'];
    try {
        db()->beginTransaction();
        $stmt = db()->prepare("DELETE FROM bobot WHERE id_alter = ?");
        $stmt->execute([$id]);
        $stmt = db()->prepare("DELETE FROM alternatif WHERE id_alter = ?");
        $stmt->execute([$id]);
        db()->commit();
        flash('success', 'Data alternatif dan nilai bobotnya berhasil dihapus.');
    } catch (PDOException $e) {
        db()->rollBack();
        flash('error', 'Gagal menghapus data: ' . $e->getMessage());
    }
    redirect('alternatif.php');
}

if (isset($_POST['add_alternatif'])) {
    $code = trim($_POST['kode_alternatif']);
    $alternatif = trim($_POST['nama_alternatif']);
    if ($code && $alternatif) {
        try {
            $stmt = db()->prepare("INSERT INTO alternatif (code, alternatif) VALUES (?, ?)");
            $stmt->execute([$code, $alternatif]);
            $id_alter = db()->lastInsertId();
            $stmtBobot = db()->prepare("INSERT INTO bobot (id_alter, c1, c2, c3, c4, c5, c6, c7, c8) VALUES (?, 0, 0, 0, 0, 0, 0, 0, 0)");
            $stmtBobot->execute([$id_alter]);
            flash('success', 'Data alternatif berhasil ditambahkan. Silakan isi nilai bobotnya di menu "Nilai Bobot".');
        } catch (PDOException $e) {
            flash('error', 'Gagal menambah data: ' . $e->getMessage());
        }
    } else {
        flash('error', 'Input tidak valid. Pastikan semua kolom terisi.');
    }
    redirect('alternatif.php');
}

if (isset($_POST['edit_alternatif'])) {
    $id = (int) $_POST['id'];
    $code = trim($_POST['kode_alternatif']);
    $alternatif = trim($_POST['nama_alternatif']);
    if ($code && $alternatif) {
        try {
            $stmt = db()->prepare("UPDATE alternatif SET code = ?, alternatif = ? WHERE id_alter = ?");
            $stmt->execute([$code, $alternatif, $id]);
            flash('success', 'Data alternatif berhasil diperbarui.');
        } catch (PDOException $e) {
            flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    } else {
        flash('error', 'Input tidak valid. Pastikan semua kolom terisi.');
    }
    redirect('alternatif.php');
}

$stmt = db()->query("SELECT * FROM alternatif ORDER BY id_alter ASC");
$alternatifList = $stmt->fetchAll();
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
            <li>Alternatif</li>
        </ol>
    </nav>
    <h1><i class="bi bi-houses"></i> Data Alternatif</h1>
    <p>Daftar rumah tinggal yang menjadi alternatif dalam perhitungan WP.</p>
</div>

<?php if ($flashSuccess): ?>
    <script>Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= $flashSuccess ?>', timer: 3000, showConfirmButton: false });</script>
<?php endif; ?>
<?php if ($flashError): ?>
    <script>Swal.fire({ icon: 'error', title: 'Gagal', text: '<?= $flashError ?>' });</script>
<?php endif; ?>

<div class="card-modern">
    <i class="bi bi-houses bg-icon"></i>
    <div class="card-head">
        <h5><i class="bi bi-table"></i> Tabel Alternatif</h5>
        <button type="button" class="btn-brand" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-lg"></i> Tambah Data
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th width="60px" class="text-center">No</th>
                        <th>Kode</th>
                        <th>Nama Alternatif</th>
                        <th width="120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($alternatifList)): ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <img src="https://illustrations.popsy.co/amber/house.svg" alt="Empty">
                                    <h4>Belum Ada Data Alternatif</h4>
                                    <p>Silakan tambahkan data rumah yang akan dijadikan alternatif pemilihan.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($alternatifList as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><strong><?= e($row['code']) ?></strong></td>
                                <td><?= e($row['alternatif']) ?></td>
                                <td class="text-center" style="white-space: nowrap;">
                                    <button class="btn-soft" style="padding: 4px 8px;" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_alter'] ?>" title="Edit">
                                        <i class="bi bi-pencil-square" style="color: #f59e0b;"></i>
                                    </button>
                                    <button class="btn-soft" style="padding: 4px 8px;" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id_alter'] ?>" title="Hapus">
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

<!-- PINDAHKAN SEMUA MODAL KE SINI -->
<?php foreach ($alternatifList as $row): ?>
    <!-- MODAL EDIT -->
    <div class="modal fade" id="editModal<?= $row['id_alter'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: var(--radius); border: none;">
                <div class="modal-header" style="border-bottom: 1px solid var(--slate-200);">
                    <h5 class="modal-title" style="font-size: 1rem; font-weight: 600;">
                        <i class="bi bi-pencil-square text-warning"></i> Edit Alternatif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $row['id_alter'] ?>">
                        <div class="mb-3">
                            <label class="form-label" style="font-size: .85rem; font-weight: 600;">Kode Alternatif</label>
                            <input type="text" class="form-control" name="kode_alternatif" value="<?= e($row['code']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size: .85rem; font-weight: 600;">Nama Alternatif</label>
                            <input type="text" class="form-control" name="nama_alternatif" value="<?= e($row['alternatif']) ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--slate-200);">
                        <button type="button" class="btn-soft" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_alternatif" class="btn-brand">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL HAPUS -->
    <div class="modal fade" id="deleteModal<?= $row['id_alter'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius: var(--radius); border: none; text-align: center;">
                <div class="modal-body p-4">
                    <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: #ef4444;"></i>
                    <h5 class="mt-3 mb-2" style="font-weight: 600;">Hapus Alternatif?</h5>
                    <p style="color: var(--slate-500); font-size: .9rem;">
                        Data <strong><?= e($row['code']) ?> - <?= e($row['alternatif']) ?></strong> beserta nilai bobotnya akan dihapus permanen.
                    </p>
                </div>
                <div class="modal-footer justify-content-center" style="border-top: none; padding-top: 0;">
                    <form action="" method="post" class="d-flex gap-2 w-100 px-3 pb-3">
                        <input type="hidden" name="id" value="<?= $row['id_alter'] ?>">
                        <button type="button" class="btn-soft w-50 justify-content-center" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="delete_id" class="btn-brand w-50 justify-content-center" style="background: #ef4444;">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- MODAL TAMBAH (Bisa tetap di bawah) -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: var(--radius); border: none;">
            <div class="modal-header" style="border-bottom: 1px solid var(--slate-200);">
                <h5 class="modal-title" style="font-size: 1rem; font-weight: 600;">
                    <i class="bi bi-plus-circle text-primary"></i> Tambah Alternatif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size: .85rem; font-weight: 600;">Kode Alternatif</label>
                        <input type="text" class="form-control" name="kode_alternatif" placeholder="Contoh: R13" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: .85rem; font-weight: 600;">Nama Alternatif</label>
                        <input type="text" class="form-control" name="nama_alternatif" placeholder="Contoh: Komplek Harmoni" required>
                    </div>
                    <div class="callout-modern" style="font-size: .8rem; margin-bottom: 0;">
                        <i class="bi bi-info-circle"></i> Nilai C1-C8 akan otomatis dibuat dengan nilai 0.
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--slate-200);">
                    <button type="button" class="btn-soft" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="add_alternatif" class="btn-brand">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'template/footer.php'; ?>