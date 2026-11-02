<div class="app-main">
    <header class="app-topbar">
        <div class="d-flex align-items-center gap-3">
            <!-- Tombol toggle sidebar untuk mobile -->
            <button class="btn btn-sm btn-soft d-lg-none" onclick="document.getElementById('appSidebar').classList.toggle('show')">
                <i class="bi bi-list"></i>
            </button>
            <h6 class="page-title mb-0">Sistem Pendukung Keputusan</h6>
        </div>
        
        <div class="user-chip">
            <span class="d-none d-sm-inline">
                Halo, <strong><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin') ?></strong>
            </span>
            <div class="avatar" title="<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin') ?>">
                <?= htmlspecialchars(strtoupper(substr($_SESSION['nama_lengkap'] ?? 'A', 0, 1))) ?>
            </div>
        </div>
    </header>

    <!-- Banner Mode Demo -->
    <?php if (IS_DEMO): ?>
        <div style="background: #fef3c7; border-bottom: 1px solid #fcd34d; color: #92400e; padding: 10px 28px; font-size: 0.85rem; font-weight: 500; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-info-circle-fill" style="font-size: 1rem;"></i>
            <span>Anda sedang berada dalam <strong>Mode Demo</strong>. Semua fitur tambah, edit, dan hapus dinonaktifkan. Data hanya dapat dilihat.</span>
        </div>
    <?php endif; ?>

    <main class="app-content">