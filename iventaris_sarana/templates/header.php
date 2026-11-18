<?php
require_once __DIR__ . '/../classes/Auth.php';
$isStaff = Auth::isStaff();
$currentUser = $isStaff ? Auth::user() : null;
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Inventaris Sarana & Prasarana BAKNUS 666' ?></title>
    <!-- Local Bootstrap 5 CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Local Bootstrap Icons -->
    <link rel="stylesheet" href="assets/css/bootstrap-icons.min.css">
    <!-- Custom Modern UI Styles (External CSS) -->
    <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <div class="sidebar-heading">
            <div class="sidebar-brand-mark">
                <img src="assets/logo-baknus.png" alt="Logo Bakti Nusantara 666">
            </div>
            <div>
                <span class="d-block text-white fw-bold sidebar-brand-title">SARPRAS 666</span>
                <small class="text-white-50 sidebar-brand-subtitle">Inventaris Digital</small>
            </div>
        </div>

        <div class="list-group list-group-flush my-2">
            <?php if ($isStaff): ?>
                <a href="dashboard.php" class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>

                <?php if (Auth::canInventarisir() || Auth::canManagePegawai() || Auth::canManageSiswa()): ?>
                    <div class="nav-item-header">INVENTARISIR</div>

                    <?php if (Auth::canInventarisir()): ?>
                    <a href="inventaris.php" class="nav-link <?= $currentPage === 'inventaris.php' ? 'active' : '' ?>">
                        <i class="bi bi-box-seam"></i>
                        <span>Sarana &amp; Prasarana</span>
                    </a>

                    <a href="jenis.php" class="nav-link <?= $currentPage === 'jenis.php' ? 'active' : '' ?>">
                        <i class="bi bi-tags"></i>
                        <span>Jenis Sarana</span>
                    </a>

                    <a href="ruang.php" class="nav-link <?= $currentPage === 'ruang.php' ? 'active' : '' ?>">
                        <i class="bi bi-door-open"></i>
                        <span>Ruangan</span>
                    </a>
                    <?php endif; ?>

                    <?php if (Auth::canManagePegawai()): ?>
                    <a href="pegawai.php" class="nav-link <?= $currentPage === 'pegawai.php' ? 'active' : '' ?>">
                        <i class="bi bi-people"></i>
                        <span>Data Pegawai / Guru</span>
                    </a>
                    <?php endif; ?>

                    <?php if (Auth::canManageSiswa()): ?>
                    <a href="siswa.php" class="nav-link <?= $currentPage === 'siswa.php' ? 'active' : '' ?>">
                        <i class="bi bi-person-badge"></i>
                        <span>Data Siswa</span>
                    </a>
                    <?php endif; ?>

                    <?php if (Auth::canInventarisir()): ?>
                    <a href="petugas.php" class="nav-link <?= $currentPage === 'petugas.php' ? 'active' : '' ?>">
                        <i class="bi bi-person-gear"></i>
                        <span>Kelola Pengguna</span>
                    </a>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="nav-item-header">TRANSAKSI</div>

                <a href="peminjaman.php" class="nav-link <?= $currentPage === 'peminjaman.php' ? 'active' : '' ?>">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Peminjaman</span>
                </a>

                <?php if (Auth::canPengembalian()): ?>
                    <a href="pengembalian.php" class="nav-link <?= $currentPage === 'pengembalian.php' ? 'active' : '' ?>">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Pengembalian</span>
                    </a>
                <?php endif; ?>

                <?php if (Auth::canGenerateLaporan()): ?>
                    <div class="nav-item-header">PELAPORAN</div>
                    <a href="laporan.php" class="nav-link <?= $currentPage === 'laporan.php' ? 'active' : '' ?>">
                        <i class="bi bi-layout-text-window"></i>
                        <span>Generate Laporan</span>
                    </a>
                <?php endif; ?>

                <div class="nav-item-header mt-3">AKUN</div>
                <a href="logout.php" class="nav-link text-danger" onclick="return confirm('Apakah Anda yakin ingin logout?');">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar (Logout)</span>
                </a>
            <?php else: ?>
                <div class="nav-item-header">PEMINJAM</div>
                <a href="peminjaman.php" class="nav-link active">
                    <i class="bi bi-handbag"></i>
                    <span>Ajukan Peminjaman</span>
                </a>
                <div class="nav-item-header mt-3">PETUGAS</div>
                <a href="login.php" class="nav-link">
                    <i class="bi bi-shield-lock"></i>
                    <span>Login Admin / Operator</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg top-navbar">
            <div class="container-fluid px-0">
                <button class="btn btn-outline-secondary btn-sm me-3 d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                
                <div class="d-none d-md-flex align-items-center gap-2">
                    <span class="fw-semibold text-secondary">Aplikasi Inventaris Sarana dan Prasarana</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">SMK 2026/2027</span>
                </div>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <?php if ($isStaff): ?>
                        <div class="text-end">
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($currentUser['nama']) ?></div>
                            <div>
                                <?php
                                $roleClass = match($currentUser['level']) {
                                    'administrator' => 'badge-role-admin',
                                    'operator'      => 'badge-role-operator',
                                    default         => 'badge-role-peminjam'
                                };
                                ?>
                                <span class="badge user-badge <?= $roleClass ?>">
                                    <?= strtoupper(htmlspecialchars($currentUser['level'])) ?>
                                </span>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light rounded-circle p-2 border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-5 text-secondary"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li class="dropdown-header">
                                    <strong><?= htmlspecialchars($currentUser['username']) ?></strong>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="logout.php">
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <span class="badge bg-primary-subtle text-primary">Akses Peminjam</span>
                        <a class="btn btn-outline-primary btn-sm" href="login.php">Login Petugas</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <!-- Main Body Container -->
        <div class="container-fluid p-4">
            <?php include __DIR__ . '/alerts.php'; ?>
