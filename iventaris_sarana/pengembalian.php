<?php
$pageTitle = 'Pengembalian Sarana - Inventaris SMK';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Peminjaman.php';

Auth::requirePengembalian();

$peminjamanObj = new Peminjaman();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'proses_kembali') {
        $idPeminjaman = (int) $_POST['id_peminjaman'];
        $tanggalKembali = !empty($_POST['tanggal_kembali']) ? $_POST['tanggal_kembali'] : date('Y-m-d');

        $res = $peminjamanObj->returnPeminjaman($idPeminjaman, $tanggalKembali);
        if ($res['status']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }
        header('Location: pengembalian.php');
        exit;
    }
}

// Filter
$search = trim($_GET['search'] ?? '');
$tab = $_GET['tab'] ?? 'aktif'; // 'aktif' (sedang dipinjam) atau 'riwayat' (sudah kembali)

$filters = ['search' => $search];
if ($tab === 'aktif') {
    $filters['status'] = 'Dipinjam';
} else {
    $filters['status'] = 'Sudah Kembali';
}

$listPeminjaman = $peminjamanObj->getAllGrouped($filters);

// Cek apakah ada param id dari peminjaman.php
$autoId = !empty($_GET['id']) ? (int)$_GET['id'] : null;

include __DIR__ . '/templates/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Pengembalian Sarana & Prasarana</h4>
        <p class="text-muted small mb-0">Kelola pengembalian sarana dan pemulihan stok inventaris otomatis</p>
    </div>
</div>

<!-- Tabs Nav -->
<ul class="nav nav-pills mb-3 gap-2">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'aktif' ? 'active' : 'bg-white border' ?>" href="pengembalian.php?tab=aktif">
            <i class="bi bi-hourglass-split me-1"></i> Sedang Dipinjam (Belum Kembali)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'riwayat' ? 'active' : 'bg-white border' ?>" href="pengembalian.php?tab=riwayat">
            <i class="bi bi-check2-all me-1"></i> Riwayat Pengembalian Selesai
        </a>
    </li>
</ul>

<!-- Search Card -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari kode transaksi, peminjam, atau nama barang..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search"></i> Cari</button>
                <?php if (!empty($search)): ?>
                    <a href="pengembalian.php?tab=<?= $tab ?>" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode Peminjaman</th>
                        <th>Peminjam</th>
                        <th>Rincian Sarana Dipinjam</th>
                        <th class="text-center">Total Jml</th>
                        <th>Tanggal Pinjam</th>
                        <th><?= $tab === 'aktif' ? 'Status' : 'Tanggal Dikembalikan' ?></th>
                        <th class="text-center" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listPeminjaman)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                <?= $tab === 'aktif' ? 'Tidak ada sarana yang sedang dipinjam saat ini.' : 'Belum ada riwayat pengembalian sarana.' ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($listPeminjaman as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-bold"><?= htmlspecialchars($row['kode_peminjaman']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_pegawai']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($row['nip']) ?></small>
                                </td>
                                <td>
                                    <ul class="mb-0 ps-3">
                                        <?php foreach ($row['items'] as $item): ?>
                                            <li>
                                                <span class="fw-semibold text-primary"><?= htmlspecialchars($item['nama_barang']) ?></span>
                                                <small class="text-muted">(<?= htmlspecialchars($item['nama_jenis'] ?? '-') ?>, <?= (int) $item['jumlah'] ?> unit)</small>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary fs-6">
                                        <?= (int) $row['total_jumlah'] ?> Unit
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                                <td>
                                    <?php if ($tab === 'aktif'): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                            <i class="bi bi-clock-history me-1"></i>Belum Kembali
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i><?= date('d/m/Y', strtotime($row['tanggal_kembali'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status_peminjaman'] === 'Dipinjam'): ?>
                                        <button type="button" class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalKembali"
                                                data-id="<?= $row['id_peminjaman'] ?>"
                                                data-kode="<?= htmlspecialchars($row['kode_peminjaman']) ?>"
                                                data-peminjam="<?= htmlspecialchars($row['nama_pegawai']) ?>"
                                                data-barang="<?= htmlspecialchars(implode(', ', array_column($row['items'], 'nama_barang'))) ?>"
                                                data-jumlah="<?= (int) $row['total_jumlah'] ?>">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Kembalikan
                                        </button>
                                    <?php else: ?>
                                        <a href="cetak_bukti_pinjam.php?id=<?= $row['id_peminjaman'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-printer me-1"></i> Bukti Kembali
                                        </a>
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

<!-- Modal Proses Pengembalian -->
<div class="modal fade" id="modalKembali" tabindex="-1" aria-labelledby="modalKembaliLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="proses_kembali">
            <input type="hidden" name="id_peminjaman" id="kembali_id">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="modalKembaliLabel">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Konfirmasi Pengembalian Sarana
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-light border mb-3">
                    <div class="row g-2 small">
                        <div class="col-5 text-muted">Kode Pinjam:</div>
                        <div class="col-7 fw-bold" id="kembali_kode">-</div>

                        <div class="col-5 text-muted">Nama Peminjam:</div>
                        <div class="col-7 fw-bold" id="kembali_peminjam">-</div>

                        <div class="col-5 text-muted">Sarana / Barang:</div>
                        <div class="col-7 fw-bold text-primary" id="kembali_barang">-</div>

                        <div class="col-5 text-muted">Jumlah Kembali:</div>
                        <div class="col-7 fw-bold text-success" id="kembali_jumlah">-</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Pengembalian <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_kembali" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    <small class="text-muted">Tanggal saat barang fisik diserahkan kembali.</small>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Simpan Pengembalian</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modalKembali = document.getElementById('modalKembali');
    modalKembali.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        document.getElementById('kembali_id').value = btn.getAttribute('data-id');
        document.getElementById('kembali_kode').textContent = btn.getAttribute('data-kode');
        document.getElementById('kembali_peminjam').textContent = btn.getAttribute('data-peminjam');
        document.getElementById('kembali_barang').textContent = btn.getAttribute('data-barang');
        document.getElementById('kembali_jumlah').textContent = btn.getAttribute('data-jumlah') + ' Unit';
    });

    // Otomatis buka modal jika ada autoId di query param
    <?php if ($autoId): ?>
        window.addEventListener('DOMContentLoaded', () => {
            const btnTarget = document.querySelector('button[data-id="<?= $autoId ?>"]');
            if (btnTarget) {
                btnTarget.click();
            }
        });
    <?php endif; ?>
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
