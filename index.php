<?php
session_start();

// Kalau sudah login, langsung lempar ke admin
if (isset($_SESSION['user_id'])) {
    header("Location: admin/index.php");
    exit;
}
// --- LOGIN DEMO (Tanpa Password) ---
if (isset($_GET['demo']) && $_GET['demo'] == '1') {
    session_regenerate_id(true);
    $_SESSION['user_id']      = 999; // ID dummy
    $_SESSION['username']     = 'demo';
    $_SESSION['nama_lengkap'] = 'Pengunjung Demo';
    $_SESSION['level']        = 'Demo';
    header("Location: admin/index.php");
    exit;
}

require __DIR__ . '/admin/config/database.php';
require __DIR__ . '/admin/functions/helpers.php';

$error = '';

// Proses Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        try {
            $stmt = db()->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // Verifikasi password hash
            if ($user && password_verify($password, $user['password'])) {
                // Login sukses — set session
                session_regenerate_id(true); // Cegah session fixation
                $_SESSION['user_id']      = $user['id'];
                $_SESSION['username']     = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['level']        = $user['level'];

                header("Location: admin/index.php");
                exit;
            } else {
                $error = 'Username atau password salah.';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan sistem. Coba lagi.';
        }
    } else {
        $error = 'Username dan password wajib diisi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SPK Weighted Product</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/png">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-500: #6366f1;
            --brand-600: #4f46e5;
            --brand-700: #4338ca;
            --slate-50:  #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #fff;
        }

        /* === KIRI: Form Login === */
        .login-left {
            flex: 1;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            width: 100%;
            max-width: 400px;
        }

        .login-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }
        .login-brand .logo {
            width: 44px; height: 44px;
            display: grid; place-items: center;
            background: linear-gradient(135deg, var(--brand-500), var(--brand-700));
            border-radius: 12px;
            color: #fff;
            font-size: 1.4rem;
        }
        .login-brand .text {
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--slate-900);
            line-height: 1.2;
        }
        .login-brand .text small {
            display: block;
            font-size: .75rem;
            font-weight: 500;
            color: var(--slate-500);
        }

        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -.02em;
            margin-bottom: 8px;
        }
        .login-subtitle {
            color: var(--slate-500);
            margin-bottom: 32px;
            font-size: .95rem;
        }

        .form-label {
            font-size: .85rem;
            font-weight: 600;
            color: var(--slate-700);
            margin-bottom: 6px;
        }
        .form-control {
            height: 46px;
            border-radius: 10px;
            border: 1px solid var(--slate-200);
            padding: 0 14px 0 42px;
            font-size: .9rem;
            background: var(--slate-50);
            transition: border-color .15s, background .15s;
        }
        .form-control:focus {
            border-color: var(--brand-500);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99,102,241,.1);
        }
        .input-wrap { margin-bottom: 18px; }

/* Wrapper khusus untuk input + ikon (label ada di luar) */
.input-group-custom {
    position: relative;
    width: 100%;
}

.input-group-custom .icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--slate-400);
    font-size: 1rem;
    pointer-events: none;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 46px;             /* sama dengan tinggi input */
}

.input-group-custom .toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--slate-400);
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 46px;
    line-height: 1;
}
.input-group-custom .toggle:hover { color: var(--brand-600); }
        .input-wrap .toggle:hover { color: var(--brand-600); }

        .btn-login {
            width: 100%;
            height: 46px;
            background: var(--brand-600);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: .95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background .15s, transform .05s;
            margin-top: 6px;
        }
        .btn-login:hover { background: var(--brand-700); }
        .btn-login:active { transform: translateY(1px); }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: .85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .login-footer {
            margin-top: 32px;
            text-align: center;
            font-size: .8rem;
            color: var(--slate-400);
        }

        /* === KANAN: Branding Panel === */
.login-right {
    flex: 1;
    background: linear-gradient(135deg, rgba(67, 56, 202, 0.85) 0%, rgba(15, 23, 42, 0.9) 100%), 
                url('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?q=80&w=1200&auto=format&fit=crop') center/cover no-repeat;
    color: #fff;
    padding: 60px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    overflow: hidden;
}
        .login-right::before {
            content: '';
            position: absolute;
            top: -100px; right: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(99,102,241,.4) 0%, transparent 70%);
            border-radius: 50%;
        }
        .login-right::after {
            content: '';
            position: absolute;
            bottom: -150px; left: -100px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(67,56,202,.3) 0%, transparent 70%);
            border-radius: 50%;
        }

        .login-right .content {
            position: relative;
            z-index: 1;
            max-width: 500px;
        }
        .login-right h2 {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -.02em;
            margin-bottom: 20px;
        }
        .login-right p {
            font-size: 1rem;
            line-height: 1.6;
            color: rgba(255,255,255,.8);
            margin-bottom: 40px;
        }
        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .feature-item {
    display: flex;
    align-items: center;
    gap: 14px;
    background: rgba(255,255,255,.08);  /* Sedikit lebih terang */
    border: 1px solid rgba(255,255,255,.15); /* Border lebih jelas */
    border-radius: 12px;
    padding: 14px 18px;
    backdrop-filter: blur(12px); /* Efek kaca */
    -webkit-backdrop-filter: blur(12px);
    transition: transform 0.2s ease;
}
.feature-item:hover {
    transform: translateX(4px); /* Efek geser saat hover */
}
        .feature-item .ico {
            width: 36px; height: 36px;
            display: grid; place-items: center;
            background: rgba(255,255,255,.1);
            border-radius: 8px;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .feature-item .txt strong {
            display: block;
            font-size: .9rem;
            font-weight: 600;
        }
        .feature-item .txt span {
            font-size: .78rem;
            color: rgba(255,255,255,.6);
        }

        /* === Responsive === */
        @media (max-width: 900px) {
            .login-right { display: none; }
            .login-left { padding: 24px; }
            .login-title { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <!-- KIRI: Form Login -->
    <div class="login-left">
        <div class="login-box">
            <div class="login-brand">
                <div class="logo"><i class="bi bi-diagram-3"></i></div>
                <div class="text">
                    SPK Weighted Product
                    <small>Sistem Pendukung Keputusan</small>
                </div>
            </div>

            <h1 class="login-title">Selamat Datang 👋</h1>
            <p class="login-subtitle">Masuk untuk mengelola data dan melihat hasil perhitungan.</p>

            <?php if ($error): ?>
                <div class="alert-error">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
    <div class="input-wrap">
        <label class="form-label">Username</label>
        <div class="input-group-custom">
            <i class="bi bi-person icon"></i>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
        </div>
    </div>

    <div class="input-wrap">
        <label class="form-label">Password</label>
        <div class="input-group-custom">
            <i class="bi bi-lock icon"></i>
            <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
            <button type="button" class="toggle" onclick="togglePassword()">
                <i class="bi bi-eye" id="toggleIcon"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn-login">
        <i class="bi bi-box-arrow-in-right"></i> Masuk
    </button>

    <div style="text-align: center; margin-top: 16px;">
    <a href="?demo=1" class="btn-soft" style="display: inline-flex; width: 100%; justify-content: center; text-decoration: none; padding: 10px; font-size: .9rem;">
        <i class="bi bi-eye"></i> Coba Mode Demo (Read-Only)
    </a>
</div>
</form>

            <div class="login-footer">
    <i class="bi bi-c-circle"></i> <?= date('Y') ?> - <strong>Dear Haens</strong>. All rights reserved.
</div>
        </div>
    </div>

    <!-- KANAN: Branding -->
    <div class="login-right">
        <div class="content">
            <h2>Sistem Pendukung Keputusan Pemilihan Rumah Tinggal</h2>
            <p>Menggunakan metode Weighted Product (WP) untuk menghasilkan rekomendasi rumah terbaik berdasarkan 8 kriteria penilaian.</p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="ico"><i class="bi bi-calculator"></i></div>
                    <div class="txt">
                        <strong>Metode Weighted Product</strong>
                        <span>Perhitungan matematis yang transparan</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="ico"><i class="bi bi-list-check"></i></div>
                    <div class="txt">
                        <strong>8 Kriteria Penilaian</strong>
                        <span>Harga, Lokasi, Fasilitas, dan lainnya</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="ico"><i class="bi bi-trophy"></i></div>
                    <div class="txt">
                        <strong>Perangkingan Otomatis</strong>
                        <span>Hasil rekomendasi terurut dan akurat</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
</body>
</html>