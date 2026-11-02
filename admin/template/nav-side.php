<?php
$current = basename($_SERVER['PHP_SELF']);
function nav_active($file, $current) {
    return $file === $current ? 'active' : '';
}
?>
<aside class="app-sidebar" id="appSidebar">
    <div class="brand">
        <div class="logo"><i class="bi bi-diagram-3"></i></div>
        <div>
            SPK WP
            <div style="font-size:.7rem;font-weight:400;color:var(--slate-400);">Weighted Product</div>
        </div>
    </div>

    <!-- Info User Login -->
    <div style="padding: 10px 12px; margin-bottom: 8px; background: rgba(255,255,255,.04); border-radius: 10px; border: 1px solid <?= IS_DEMO ? 'rgba(245,158,11,.3)' : 'transparent' ?>;">
        <div style="font-size: .65rem; color: var(--slate-500); text-transform: uppercase; letter-spacing: .06em; font-weight: 600;">
            <?= IS_DEMO ? 'Mode Demo' : 'Masuk sebagai' ?>
        </div>
        <div style="font-weight: 600; color: <?= IS_DEMO ? '#fcd34d' : '#fff' ?>; font-size: .85rem; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin') ?>
        </div>
    </div>

    <div class="nav-section">Menu</div>
    <a href="index.php"      class="nav-link <?= nav_active('index.php', $current) ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="hitung.php"     class="nav-link <?= nav_active('hitung.php', $current) ?>">
        <i class="bi bi-calculator"></i> Perhitungan WP
    </a>

    <div class="nav-section">Data Master</div>
    <a href="kriteria.php"   class="nav-link <?= nav_active('kriteria.php', $current) ?>">
        <i class="bi bi-list-check"></i> Kriteria
    </a>
    <a href="alternatif.php" class="nav-link <?= nav_active('alternatif.php', $current) ?>">
        <i class="bi bi-houses"></i> Alternatif
    </a>
    <a href="bobot.php"      class="nav-link <?= nav_active('bobot.php', $current) ?>">
        <i class="bi bi-sliders"></i> Nilai Bobot
    </a>

    <div class="nav-section">Akun</div>
    <a href="#" id="btnLogout" class="nav-link">
    <i class="bi bi-box-arrow-right"></i> Logout
</a>
</aside>