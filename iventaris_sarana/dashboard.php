<?php
$pageTitle = 'Dashboard - Aplikasi Inventaris Sarana & Prasarana';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Laporan.php';
require_once __DIR__ . '/classes/Peminjaman.php';

Auth::requireLogin();
$currentUser = Auth::user();

$laporanObj = new Laporan();
$peminjamanObj = new Peminjaman();

// Mengambil ringkasan dari Stored Procedure sp_ringkasan_dashboard
$summary = $laporanObj->getDashboardSummary();

// Ambil data transaksi terkini sesuai role
$recentFilters = [];
if (Auth::isPeminjam() && !empty($currentUser['id_pegawai'])) {
    $recentFilters['id_pegawai'] = $currentUser['id_pegawai'];
}
$recentLoans = array_slice($peminjamanObj->getAllGrouped($recentFilters), 0, 5);

include __DIR__ . '/templates/header.php';
?>

<!-- Welcome Banner -->
<div class="card border-0 bg-primary text-white mb-4 shadow-sm overflow-hidden position-relative">
    <div class="card-body p-4 position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="fw-bold mb-1">Selamat Datang, <?= htmlspecialchars($currentUser['nama']) ?>!</h3>
                <p class="mb-0 text-white-50">
                    Anda masuk sebagai <strong><?= strtoupper(htmlspecialchars($currentUser['level'])) ?></strong> 
                    pada Aplikasi Inventaris Sarana dan Prasarana Digital SMK.
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-white text-primary px-3 py-2 fw-semibold fs-6">
                    <i class="bi bi-calendar3 me-1"></i> <?= date('d F Y') ?>
                </span>
            </div>
        </div>
    </div>
    <div class="position-absolute end-0 top-0 bottom-0 opacity-25 d-none d-md-flex align-items-center pe-4" style="z-index: 1;">
        <i class="bi bi-buildings" style="font-size: 8rem;"></i>
    </div>
</div>

<!-- Statistik Summary Cards (Menggunakan data dari Stored Procedure) -->
<div class="row g-3 mb-4">
    <!-- Card Total Barang -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Total Sarana</div>
                        <h3 class="fw-bold text-dark my-1"><?= number_format($summary['total_inventaris'] ?? 0) ?></h3>
                        <small class="text-primary fw-semibold">
                            <i class="bi bi-box-seam me-1"></i><?= number_format($summary['total_stok_barang'] ?? 0) ?> Unit Total Stok
                        </small>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-3">
                        <i class="bi bi-box-seam fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Sedang Dipinjam -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Sedang Dipinjam</div>
                        <h3 class="fw-bold text-dark my-1"><?= number_format($summary['total_sedang_dipinjam'] ?? 0) ?></h3>
                        <small class="text-warning fw-semibold">
                            <i class="bi bi-clock-history me-1"></i>Transaksi Aktif
                        </small>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-3">
                        <i class="bi bi-arrow-left-right fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Ruangan -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Total Ruangan</div>
                        <h3 class="fw-bold text-dark my-1"><?= number_format($summary['total_ruang'] ?? 0) ?></h3>
                        <small class="text-success fw-semibold">
                            <i class="bi bi-geo-alt me-1"></i>Lokasi Inventaris
                        </small>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-3">
                        <i class="bi bi-door-open fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Pegawai / Peminjam -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Pegawai & Siswa</div>
                        <h3 class="fw-bold text-dark my-1"><?= number_format($summary['total_pegawai'] ?? 0) ?></h3>
                        <small class="text-info fw-semibold">
                            <i class="bi bi-person-check me-1"></i>Peminjam Terdaftar
                        </small>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-3">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Aksi Cepat & Transaksi Terkini -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between bg-white py-3">
                <h6 class="m-0 fw-bold text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Aktivitas Peminjaman Terkini</h6>
                <a href="peminjaman.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Pinjam</th>
                                <th>Peminjam</th>
                                <th>Rincian Sarana</th>
                                <th>Total Jml</th>
                                <th>Tgl Pinjam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentLoans)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                        Belum ada aktivitas peminjaman.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentLoans as $row): ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['kode_peminjaman']) ?></span></td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_pegawai']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($row['nip']) ?></small>
                                        </td>
                                        <td>
                                            <?php foreach ($row['items'] as $item): ?>
                                                <div><?= htmlspecialchars($item['nama_barang']) ?> <small class="text-muted">(<?= (int) $item['jumlah'] ?> unit)</small></div>
                                            <?php endforeach; ?>
                                        </td>
                                        <td><span class="fw-bold"><?= (int) $row['total_jumlah'] ?></span></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                        <td>
                                            <?php if ($row['status_peminjaman'] === 'Dipinjam'): ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Dipinjam</span>
                                            <?php elseif ($row['status_peminjaman'] === 'Sudah Kembali'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">Sudah Kembali</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($row['status_peminjaman']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-dark"><i class="bi bi-grid-fill text-primary me-2"></i>Aksi Cepat Menu</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <!-- Peminjaman dapat diakses semua role -->
                    <a href="peminjaman.php" class="btn btn-primary text-start d-flex align-items-center justify-content-between p-3">
                        <div>
                            <div class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Peminjaman Sarana</div>
                            <small class="text-white-50">Ajukan atau catat peminjaman sarana baru</small>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>

                    <!-- Pengembalian: Admin & Operator -->
                    <?php if (Auth::canPengembalian()): ?>
                        <a href="pengembalian.php" class="btn btn-warning text-start text-dark d-flex align-items-center justify-content-between p-3">
                            <div>
                                <div class="fw-bold"><i class="bi bi-arrow-counterclockwise me-2"></i>Pengembalian Sarana</div>
                                <small class="text-muted">Proses pengembalian barang dan pulihkan stok</small>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>

                    <!-- Inventarisir: Administrator Only -->
                    <?php if (Auth::canInventarisir()): ?>
                        <a href="inventaris.php" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between p-3">
                            <div>
                                <div class="fw-bold"><i class="bi bi-boxes me-2"></i>Kelola Sarana & Prasarana</div>
                                <small class="text-muted">Pendataan dan registrasi inventaris sekolah</small>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (Auth::canManageSiswa()): ?>
                        <a href="siswa.php" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between p-3">
                            <div>
                                <div class="fw-bold"><i class="bi bi-person-badge me-2"></i>Data Siswa</div>
                                <small class="text-muted">Kelola data siswa sebagai peminjam sarana</small>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (Auth::canManagePegawai()): ?>
                        <a href="pegawai.php" class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between p-3">
                            <div>
                                <div class="fw-bold"><i class="bi bi-people me-2"></i>Data Pegawai / Guru</div>
                                <small class="text-muted">Kelola data guru dan staf sekolah</small>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>

                    <?php if (Auth::canGenerateLaporan()): ?>
                        <a href="laporan.php" class="btn btn-outline-success text-start d-flex align-items-center justify-content-between p-3">
                            <div>
                                <div class="fw-bold"><i class="bi bi-printer me-2"></i>Generate Laporan</div>
                                <small class="text-muted">Cetak laporan peminjaman & inventaris</small>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="mt-4 p-3 bg-light rounded-3 border">
                    <div class="fw-bold text-dark mb-2 small"><i class="bi bi-person-check text-primary me-1"></i>Informasi Akun:</div>
                    <div class="small text-muted mb-1">Nama: <strong><?= htmlspecialchars($currentUser['nama']) ?></strong></div>
                    <div class="small text-muted mb-1">Username: <strong><?= htmlspecialchars($currentUser['username']) ?></strong></div>
                    <div class="small text-muted">Hak Akses: <strong class="text-uppercase"><?= htmlspecialchars($currentUser['level']) ?></strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>
