<?php
require_once __DIR__ . '/classes/Auth.php';

$auth = new Auth();
$error = '';

if (Auth::check() && !Auth::isStaff()) {
    $auth->logout();
}

if (Auth::isStaff()) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Silakan masukkan username dan password.';
    } else {
        $result = $auth->login($username, $password);
        if ($result['status']) {
            $_SESSION['flash_success'] = 'Selamat datang kembali, ' . htmlspecialchars($_SESSION['nama_petugas']) . '!';
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SARPRAS 666</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-icons.min.css">
    <!-- Custom Login Styles (External CSS) -->
    <link rel="stylesheet" href="assets/css/login.css?v=2">
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <img class="login-logo" src="assets/logo-baknus.png" alt="Logo Bakti Nusantara 666">
        <h4 class="fw-bold mb-1">SARPRAS 666</h4>
        <p class="mb-0 text-white-50 small">Inventaris Digital Sarana &amp; Prasarana</p>
    </div>

    <div class="login-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="small"><?= htmlspecialchars($error) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="off">
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold text-secondary small">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" autocomplete="off" required autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold text-secondary small">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                Masuk ke Sistem
            </button>
        </form>

        <a href="peminjaman.php" class="btn btn-outline-secondary w-100 mt-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Akses Peminjam
        </a>

        <div class="text-center text-muted small mt-4 pt-2 border-top">
            Aplikasi Inventaris Sarana dan Prasarana Sekolah &bull; SMK
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
