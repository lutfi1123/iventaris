<?php
$pageTitle = 'Generate Laporan - Inventaris SMK';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Laporan.php';
require_once __DIR__ . '/classes/Jenis.php';
require_once __DIR__ . '/classes/Ruang.php';

Auth::requireLaporan();

$laporanObj = new Laporan();
$jenisObj = new Jenis();
$ruangObj = new Ruang();

$listJenis = $jenisObj->getAll();
$listRuang = $ruangObj->getAll();

// Tipe Laporan: 'peminjaman' atau 'inventaris'
$tipe = $_GET['tipe'] ?? 'peminjaman';

// Parameter Filter Laporan Peminjaman
$tglAwal = $_GET['tgl_awal'] ?? date('Y-m-01');
$tglAkhir = $_GET['tgl_akhir'] ?? date('Y-m-d');
$statusFilter = $_GET['status'] ?? 'ALL';

// Parameter Filter Laporan Inventaris
$idRuang = !empty($_GET['ruang']) ? (int) $_GET['ruang'] : null;
$idJenis = !empty($_GET['jenis']) ? (int) $_GET['jenis'] : null;
$kondisi = $_GET['kondisi'] ?? 'ALL';

$filterParams = $tipe === 'peminjaman'
    ? [
        'tipe' => 'peminjaman',
        'tgl_awal' => $tglAwal,
        'tgl_akhir' => $tglAkhir,
        'status' => $statusFilter,
    ]
    : [
        'tipe' => 'inventaris',
        'ruang' => $idRuang ?? '',
        'jenis' => $idJenis ?? '',
        'kondisi' => $kondisi,
    ];
$printUrl = 'cetak_laporan.php?' . http_build_query($filterParams);

// Data Laporan
$dataPeminjaman = [];
$dataInventaris = [];

if ($tipe === 'peminjaman') {
    // Memanggil Stored Procedure sp_laporan_peminjaman
    $dataPeminjaman = $laporanObj->getLaporanPeminjaman($tglAwal, $tglAkhir, $statusFilter);
    $peminjamGrouped = $laporanObj->groupLaporanPeminjaman($dataPeminjaman);
} else {
    $dataInventaris = $laporanObj->getLaporanInventaris($idRuang, $idJenis, $kondisi);
}

include __DIR__ . '/templates/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Generate Pelaporan</h4>
        <p class="text-muted small mb-0"><?= $tipe === 'peminjaman' ? 'Klik nama peminjam untuk melihat transaksi dan rincian barangnya' : 'Form pelaporan inventaris sarana' ?></p>
    </div>
    <a href="<?= htmlspecialchars($printUrl) ?>" target="_blank" class="btn btn-primary">
        <i class="bi bi-printer me-1"></i> Cetak Laporan
    </a>
</div>

<!-- Nav Tipe Laporan -->
<ul class="nav nav-pills mb-4 gap-2">
    <li class="nav-item">
        <a class="nav-link <?= $tipe === 'peminjaman' ? 'active' : 'bg-white border' ?>" href="laporan.php?tipe=peminjaman">
            <i class="bi bi-arrow-left-right me-1"></i> 1. Laporan Transaksi Peminjaman Sarana
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tipe === 'inventaris' ? 'active' : 'bg-white border' ?>" href="laporan.php?tipe=inventaris">
            <i class="bi bi-boxes me-1"></i> 2. Laporan Data Inventaris Sarana & Prasarana
        </a>
    </li>
</ul>

<?php if ($tipe === 'peminjaman'): ?>
    <!-- Filter Laporan Peminjaman -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-dark"><i class="bi bi-funnel text-primary me-2"></i>Filter Rentang Tanggal & Status Peminjaman</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="" class="row g-3 align-items-end">
                <input type="hidden" name="tipe" value="peminjaman">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Awal</label>
                    <input type="date" name="tgl_awal" class="form-control" value="<?= htmlspecialchars($tglAwal) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Akhir</label>
                    <input type="date" name="tgl_akhir" class="form-control" value="<?= htmlspecialchars($tglAkhir) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Status Transaksi</label>
                    <select name="status" class="form-select">
                        <option value="ALL" <?= $statusFilter === 'ALL' ? 'selected' : '' ?>>-- Semua Status --</option>
                        <option value="Dipinjam" <?= $statusFilter === 'Dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                        <option value="Sudah Kembali" <?= $statusFilter === 'Sudah Kembali' ? 'selected' : '' ?>>Sudah Kembali</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Tampilkan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ringkasan Singkat Hasil Filter -->
    <div class="row g-3 mb-3">
        <div class="col-sm-4">
            <div class="card border-0 bg-white p-3 shadow-sm">
                <div class="text-muted small">Total Data Peminjaman</div>
                <?php $totalTransaksi = array_sum(array_map(static fn($peminjam) => count($peminjam['transaksi']), $peminjamGrouped)); ?>
                <h4 class="fw-bold mb-0 text-dark"><?= $totalTransaksi ?> Transaksi</h4>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 bg-white p-3 shadow-sm">
                <div class="text-muted small">Periode Tanggal</div>
                <div class="fw-bold mb-0 text-primary"><?= date('d/m/Y', strtotime($tglAwal)) ?> s/d <?= date('d/m/Y', strtotime($tglAkhir)) ?></div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 bg-white p-3 shadow-sm">
                <div class="text-muted small">Filter Status</div>
                <div class="fw-bold mb-0 text-success"><?= $statusFilter === 'ALL' ? 'Semua Status' : htmlspecialchars($statusFilter) ?></div>
            </div>
        </div>
    </div>

    <!-- Table Preview Peminjaman -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Peminjam</th>
                            <th class="text-center">Jumlah Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($peminjamGrouped)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Tidak ada data transaksi peminjaman pada periode yang dipilih.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($peminjamGrouped as $index => $peminjam): ?>
                                <?php $detailId = 'laporan-peminjam-' . $index; ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <button class="btn btn-link fw-bold text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $detailId ?>" aria-expanded="false" aria-controls="<?= $detailId ?>">
                                            <i class="bi bi-person-lines-fill me-1"></i><?= htmlspecialchars($peminjam['nama_pegawai']) ?>
                                            <i class="bi bi-chevron-down ms-1 small"></i>
                                        </button>
                                        <small class="text-muted d-block"><i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($peminjam['nip']) ?></small>
                                    </td>
                                    <td class="text-center"><span class="badge bg-primary-subtle text-primary"><?= count($peminjam['transaksi']) ?> transaksi</span></td>
                                </tr>
                                <tr class="bg-light">
                                    <td colspan="3" class="p-0 border-0">
                                        <div class="collapse" id="<?= $detailId ?>">
                                            <div class="p-3">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered align-middle bg-white mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Kode / Tanggal Pinjam</th>
                                                                <th>Rincian Sarana per Kategori</th>
                                                                <th>Tanggal Kembali</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($peminjam['transaksi'] as $transaksi): ?>
                                                                <tr>
                                                                    <td>
                                                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($transaksi['kode_peminjaman']) ?></span>
                                                                        <small class="d-block text-muted mt-1"><?= date('d/m/Y', strtotime($transaksi['tanggal_pinjam'])) ?></small>
                                                                    </td>
                                                                    <td>
                                                                        <table class="table table-sm table-bordered align-middle mb-0">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Kategori</th>
                                                                                    <th>Nama Sarana</th>
                                                                                    <th>Kode / Ruang</th>
                                                                                    <th class="text-center">Jumlah</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php foreach ($transaksi['items'] as $item): ?>
                                                                                    <tr>
                                                                                        <td><?= htmlspecialchars($item['nama_jenis'] ?? '-') ?></td>
                                                                                        <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                                                                        <td><small><?= htmlspecialchars($item['kode_inventaris']) ?> / <?= htmlspecialchars($item['nama_ruang'] ?? '-') ?></small></td>
                                                                                        <td class="text-center"><?= (int) $item['jumlah'] ?> unit</td>
                                                                                    </tr>
                                                                                <?php endforeach; ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                    <td><?= !empty($transaksi['tanggal_kembali']) ? date('d/m/Y', strtotime($transaksi['tanggal_kembali'])) : '-' ?></td>
                                                                    <td><?= htmlspecialchars($transaksi['status_peminjaman']) ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Filter Laporan Inventaris -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-dark"><i class="bi bi-funnel text-primary me-2"></i>Filter Data Inventaris Berdasarkan Lokasi & Kondisi</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="" class="row g-3 align-items-end">
                <input type="hidden" name="tipe" value="inventaris">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-secondary">Ruangan</label>
                    <select name="ruang" class="form-select">
                        <option value="">-- Semua Ruangan --</option>
                        <?php foreach ($listRuang as $r): ?>
                            <option value="<?= $r['id_ruang'] ?>" <?= $idRuang === (int)$r['id_ruang'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['nama_ruang']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Jenis Sarana</label>
                    <select name="jenis" class="form-select">
                        <option value="">-- Semua Jenis --</option>
                        <?php foreach ($listJenis as $j): ?>
                            <option value="<?= $j['id_jenis'] ?>" <?= $idJenis === (int)$j['id_jenis'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($j['nama_jenis']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Kondisi</label>
                    <select name="kondisi" class="form-select">
                        <option value="ALL" <?= $kondisi === 'ALL' ? 'selected' : '' ?>>-- Semua Kondisi --</option>
                        <option value="Baik" <?= $kondisi === 'Baik' ? 'selected' : '' ?>>Baik</option>
                        <option value="Rusak Ringan" <?= $kondisi === 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                        <option value="Rusak Berat" <?= $kondisi === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Preview Inventaris -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Kode Inventaris</th>
                            <th>Nama Sarana</th>
                            <th>Jenis</th>
                            <th>Ruangan</th>
                            <th class="text-center">Kondisi</th>
                            <th class="text-center">Stok Unit</th>
                            <th>Tgl Register</th>
                            <th>Petugas Pencatat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataInventaris)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Tidak ada data sarana inventaris sesuai kriteria filter.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($dataInventaris as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['kode_inventaris']) ?></span></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><span class="badge bg-info-subtle text-info"><?= htmlspecialchars($row['nama_jenis']) ?></span></td>
                                    <td><?= htmlspecialchars($row['nama_ruang']) ?></td>
                                    <td class="text-center">
                                        <?php
                                        $badge = match($row['kondisi']) {
                                            'Baik'         => 'bg-success-subtle text-success border-success-subtle',
                                            'Rusak Ringan' => 'bg-warning-subtle text-warning border-warning-subtle',
                                            default        => 'bg-danger-subtle text-danger border-danger-subtle'
                                        };
                                        ?>
                                        <span class="badge border <?= $badge ?>"><?= htmlspecialchars($row['kondisi']) ?></span>
                                    </td>
                                    <td class="text-center"><span class="badge bg-primary fs-6"><?= $row['jumlah'] ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal_register'])) ?></td>
                                    <td><?= htmlspecialchars($row['nama_petugas']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/templates/footer.php'; ?>
