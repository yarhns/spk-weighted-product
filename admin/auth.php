<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kalau belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Definisikan konstanta untuk mempermudah pengecekan di file lain
define('IS_DEMO', ($_SESSION['level'] ?? '') === 'Demo');