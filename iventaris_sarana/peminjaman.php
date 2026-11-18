<?php
$pageTitle = 'Peminjaman Sarana - Inventaris SMK';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Peminjaman.php';
require_once __DIR__ . '/classes/Inventaris.php';
require_once __DIR__ . '/classes/Pegawai.php';

$isStaff = Auth::isStaff();

$peminjamanObj = new Peminjaman();
$inventarisObj = new Inventaris();
$pegawaiObj = new Pegawai();

$listPegawai = $pegawaiObj->getAll();
$listBarang = $inventarisObj->getAvailableForBorrow();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

// Handle Submit Form Peminjaman Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'approve_peminjaman' || $action === 'reject_peminjaman') {
        if (!Auth::isStaff()) {
            http_response_code(403);
            exit('Akses ditolak. Hanya petugas yang dapat memproses pengajuan peminjaman.');
        }
        if (
            empty($_POST['csrf_token'])
            || empty($_SESSION['csrf_token'])
            || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        ) {
            $_SESSION['flash_error'] = 'Sesi formulir tidak valid. Silakan muat ulang halaman dan coba kembali.';
            header('Location: peminjaman.php');
            exit;
        }

        $idPeminjaman = (int) ($_POST['id_peminjaman'] ?? 0);
        $decision = $action === 'approve_peminjaman' ? 'approve' : 'reject';
        $result = $peminjamanObj->resolvePeminjaman($idPeminjaman, $decision);
        if ($result['status']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }
        header('Location: peminjaman.php');
        exit;
    }

    if ($action === 'create_pinjam') {
        if (!$isStaff && (
            empty($_POST['csrf_token'])
            || empty($_SESSION['csrf_token'])
            || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
        )) {
            $_SESSION['flash_error'] = 'Sesi formulir tidak valid. Silakan muat ulang halaman dan coba kembali.';
            header('Location: peminjaman.php');
            exit;
        }

        $idPegawai = (int) ($_POST['id_pegawai'] ?? 0);
        $inventoryIds = $_POST['id_inventaris'] ?? [];
        $quantities = $_POST['jumlah'] ?? [];
        $items = [];
        if (is_array($inventoryIds) && is_array($quantities)) {
            foreach ($inventoryIds as $index => $inventoryId) {
                $items[] = [
                    'id_inventaris' => (int) $inventoryId,
                    'jumlah' => (int) ($quantities[$index] ?? 0),
                ];
            }
        }
        $tanggalPinjam = !empty($_POST['tanggal_pinjam']) ? $_POST['tanggal_pinjam'] : date('Y-m-d');
        $status = $isStaff ? 'Dipinjam' : 'Menunggu Konfirmasi';

        if (empty($idPegawai)) {
            $_SESSION['flash_error'] = 'Identitas peminjam wajib dipilih.';
        } else {
            $result = $peminjamanObj->createPeminjamanMany($idPegawai, $items, $tanggalPinjam, $status);
            if ($result['status']) {
                $message = $result['message'];
                if (!$isStaff) {
                    $message .= ' Pengajuan menunggu persetujuan operator.';
                }
                $_SESSION['flash_success'] = $message . " Kode Peminjaman: " . $result['kode'];
            } else {
                $_SESSION['flash_error'] = $result['message'];
            }
        }
        header('Location: peminjaman.php');
        exit;
    }
}

// Filter data query
$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$filters = ['search' => $search];

if (!empty($statusFilter)) {
    $filters['status'] = $statusFilter;
}

$listPeminjaman = $isStaff ? $peminjamanObj->getAllGrouped($filters) : [];
$peminjamGrouped = [];
foreach ($listPeminjaman as $transaksi) {
    $idPegawai = (int) $transaksi['id_pegawai'];
    if (!isset($peminjamGrouped[$idPegawai])) {
        $peminjamGrouped[$idPegawai] = [
            'id_pegawai' => $idPegawai,
            'nama_pegawai' => $transaksi['nama_pegawai'],
            'nip' => $transaksi['nip'],
            'transaksi' => [],
        ];
    }
    $peminjamGrouped[$idPegawai]['transaksi'][] = $transaksi;
}

include __DIR__ . '/templates/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark"><?= $isStaff ? 'Transaksi Peminjaman Sarana' : 'Ajukan Peminjaman Sarana' ?></h4>
        <p class="text-muted small mb-0"><?= $isStaff ? 'Klik nama peminjam untuk melihat seluruh transaksi dan rincian barangnya' : 'Pilih sarana yang tersedia dan ajukan peminjaman tanpa login' ?></p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPinjam">
        <i class="bi bi-plus-circle me-1"></i> <?= $isStaff ? 'Input Peminjaman Baru' : 'Ajukan Peminjaman' ?>
    </button>
</div>

<?php if ($isStaff): ?>
<!-- Search & Filter Card -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="" class="row g-2 align-items-center">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari kode pinjam, nama peminjam, atau nama barang..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status Pinjam --</option>
                    <option value="Menunggu Konfirmasi" <?= $statusFilter === 'Menunggu Konfirmasi' ? 'selected' : '' ?>>Menunggu Konfirmasi</option>
                    <option value="Dipinjam" <?= $statusFilter === 'Dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                    <option value="Sudah Kembali" <?= $statusFilter === 'Sudah Kembali' ? 'selected' : '' ?>>Sudah Kembali</option>
                    <option value="Ditolak" <?= $statusFilter === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel"></i> Filter</button>
                <?php if (!empty($search) || !empty($statusFilter)): ?>
                    <a href="peminjaman.php" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($isStaff): ?>
<!-- Table Card -->
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
                                Tidak ada data peminjaman ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($peminjamGrouped as $peminjam): ?>
                            <?php $detailId = 'peminjam-' . $peminjam['id_pegawai']; ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <button class="btn btn-link fw-bold text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $detailId ?>" aria-expanded="false" aria-controls="<?= $detailId ?>">
                                        <i class="bi bi-person-lines-fill me-1"></i><?= htmlspecialchars($peminjam['nama_pegawai']) ?>
                                        <i class="bi bi-chevron-down ms-1 small"></i>
                                    </button>
                                    <small class="text-muted d-block"><i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($peminjam['nip']) ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary"><?= count($peminjam['transaksi']) ?> transaksi</span>
                                </td>
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
                                                    <th>Status</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($peminjam['transaksi'] as $row): ?>
                                                    <tr>
                                                        <td>
                                                            <span class="badge bg-light text-dark border fw-bold"><?= htmlspecialchars($row['kode_peminjaman']) ?></span>
                                                            <small class="d-block text-muted mt-1"><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></small>
                                                            <?php if (!empty($row['tanggal_kembali'])): ?>
                                                                <small class="d-block text-success">Kembali: <?= date('d/m/Y', strtotime($row['tanggal_kembali'])) ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <table class="table table-sm table-bordered align-middle mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Kategori</th>
                                                                        <th>Nama Sarana</th>
                                                                        <th>Kode / Ruang</th>
                                                                        <th>Kondisi Barang</th>
                                                                        <th class="text-center">Jumlah</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($row['items'] as $item): ?>
                                                                        <tr>
                                                                            <td><?= htmlspecialchars($item['nama_jenis'] ?? '-') ?></td>
                                                                            <td class="fw-semibold text-primary"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                                                            <td><small>Kode: <?= htmlspecialchars($item['kode_inventaris']) ?><br>Ruang: <?= htmlspecialchars($item['nama_ruang'] ?? '-') ?></small></td>
                                                                            <td>
                                                                                <?php
                                                                                $kondisiBadge = match($item['kondisi']) {
                                                                                    'Baik' => 'bg-success-subtle text-success border-success-subtle',
                                                                                    'Rusak Ringan' => 'bg-warning-subtle text-warning border-warning-subtle',
                                                                                    default => 'bg-danger-subtle text-danger border-danger-subtle'
                                                                                };
                                                                                ?>
                                                                                <span class="badge border <?= $kondisiBadge ?>"><?= htmlspecialchars($item['kondisi']) ?></span>
                                                                            </td>
                                                                            <td class="text-center"><?= (int) $item['jumlah'] ?> unit</td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                        <td>
                                                            <?php if ($row['status_peminjaman'] === 'Menunggu Konfirmasi'): ?>
                                                                <span class="badge bg-info-subtle text-info border border-info-subtle">Menunggu Konfirmasi</span>
                                                            <?php elseif ($row['status_peminjaman'] === 'Dipinjam'): ?>
                                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Dipinjam</span>
                                                            <?php elseif ($row['status_peminjaman'] === 'Sudah Kembali'): ?>
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle">Sudah Kembali</span>
                                                            <?php elseif ($row['status_peminjaman'] === 'Ditolak'): ?>
                                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Ditolak</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary"><?= htmlspecialchars($row['status_peminjaman']) ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                                <?php if ($row['status_peminjaman'] === 'Menunggu Konfirmasi'): ?>
                                                                    <form method="POST" action="" class="d-inline">
                                                                        <input type="hidden" name="action" value="approve_peminjaman">
                                                                        <input type="hidden" name="id_peminjaman" value="<?= (int) $row['id_peminjaman'] ?>">
                                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui peminjaman">
                                                                            <i class="bi bi-check-lg"></i> Setujui
                                                                        </button>
                                                                    </form>
                                                                    <form method="POST" action="" class="d-inline" onsubmit="return confirm('Tolak pengajuan peminjaman ini?');">
                                                                        <input type="hidden" name="action" value="reject_peminjaman">
                                                                        <input type="hidden" name="id_peminjaman" value="<?= (int) $row['id_peminjaman'] ?>">
                                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Tolak peminjaman">
                                                                            <i class="bi bi-x-lg"></i> Tolak
                                                                        </button>
                                                                    </form>
                                                                <?php endif; ?>
                                                                <a href="cetak_bukti_pinjam.php?id=<?= $row['id_peminjaman'] ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Cetak Bukti Peminjaman">
                                                                    <i class="bi bi-printer"></i> Struk
                                                                </a>
                                                                <?php if (Auth::canPengembalian() && $row['status_peminjaman'] === 'Dipinjam'): ?>
                                                                    <a href="pengembalian.php?id=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-warning" title="Proses Pengembalian">
                                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
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
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2 text-primary"></i>Sarana Tersedia</h5>
    </div>
    <div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama Sarana</th>
                    <th>Kode Inventaris</th>
                    <th>Ruangan</th>
                    <th class="text-center">Kondisi</th>
                    <th class="text-center">Stok Tersedia</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listBarang)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada sarana yang tersedia untuk dipinjam.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($listBarang as $barang): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($barang['nama']) ?></td>
                            <td><?= htmlspecialchars($barang['kode_inventaris']) ?></td>
                            <td><?= htmlspecialchars($barang['nama_ruang']) ?></td>
                            <td class="text-center">
                                <?php
                                $kondisiBadge = match($barang['kondisi']) {
                                    'Baik' => 'bg-success-subtle text-success border-success-subtle',
                                    'Rusak Ringan' => 'bg-warning-subtle text-warning border-warning-subtle',
                                    default => 'bg-danger-subtle text-danger border-danger-subtle'
                                };
                                ?>
                                <span class="badge border <?= $kondisiBadge ?>"><?= htmlspecialchars($barang['kondisi']) ?></span>
                            </td>
                            <td class="text-center"><span class="badge bg-success-subtle text-success"><?= (int) $barang['jumlah'] ?> unit</span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal Tambah Peminjaman -->
<div class="modal fade" id="modalTambahPinjam" tabindex="-1" aria-labelledby="modalTambahPinjamLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <form method="POST" action="" class="modal-content">
        <input type="hidden" name="action" value="create_pinjam">
        <?php if (!$isStaff): ?>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <?php endif; ?>
        <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalTambahPinjamLabel">
                    <i class="bi bi-plus-circle me-2"></i>Form Transaksi Peminjaman Sarana
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <?php if (!$isStaff): ?>
                        <div class="col-md-12">
                            <div class="alert alert-info py-2 mb-0">
                                <i class="bi bi-info-circle me-1"></i> Peminjam tidak perlu membuat akun. Pilih nama Anda pada formulir.
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Pilih Pegawai / Peminjam <span class="text-danger">*</span></label>
                        <select name="id_pegawai" class="form-select" required>
                            <option value="">-- Pilih Guru / Staf / Siswa --</option>
                            <?php foreach ($listPegawai as $p): ?>
                                <option value="<?= $p['id_pegawai'] ?>">
                                    <?= htmlspecialchars($p['nama_pegawai']) ?><?= $isStaff ? ' (NIP/Identitas: ' . htmlspecialchars($p['nip']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold mb-0">Rincian Sarana yang Dipinjam</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addItemRow">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Sarana
                            </button>
                        </div>
                        <div id="itemRows" class="d-grid gap-2">
                            <div class="row g-2 align-items-start item-row">
                                <div class="col-md-8">
                                    <select name="id_inventaris[]" class="form-select item-select" required onchange="updateMaxStok(this)">
                                        <option value="">-- Pilih Sarana yang Tersedia --</option>
                                        <?php foreach ($listBarang as $b): ?>
                                            <option value="<?= $b['id_inventaris'] ?>" data-stok="<?= $b['jumlah'] ?>" data-kondisi="<?= htmlspecialchars($b['kondisi'], ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($b['nama']) ?> [<?= htmlspecialchars($b['kode_inventaris']) ?>] - Stok: <?= $b['jumlah'] ?> (<?= htmlspecialchars($b['nama_ruang']) ?>) - Kondisi: <?= htmlspecialchars($b['kondisi']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted stock-info">Pilih sarana untuk cek sisa stok.</small>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="jumlah[]" class="form-control item-quantity" min="1" value="1" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger remove-item" aria-label="Hapus sarana" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <template id="itemRowTemplate">
                            <div class="row g-2 align-items-start item-row">
                                <div class="col-md-8">
                                    <select name="id_inventaris[]" class="form-select item-select" required onchange="updateMaxStok(this)">
                                        <option value="">-- Pilih Sarana yang Tersedia --</option>
                                        <?php foreach ($listBarang as $b): ?>
                                            <option value="<?= $b['id_inventaris'] ?>" data-stok="<?= $b['jumlah'] ?>" data-kondisi="<?= htmlspecialchars($b['kondisi'], ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($b['nama']) ?> [<?= htmlspecialchars($b['kode_inventaris']) ?>] - Stok: <?= $b['jumlah'] ?> (<?= htmlspecialchars($b['nama_ruang']) ?>) - Kondisi: <?= htmlspecialchars($b['kondisi']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted stock-info">Pilih sarana untuk cek sisa stok.</small>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="jumlah[]" class="form-control item-quantity" min="1" value="1" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger remove-item" aria-label="Hapus sarana">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Tanggal Peminjaman <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Proses Peminjaman</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateMaxStok(select) {
        const selectedOption = select.options[select.selectedIndex];
        const stok = selectedOption.getAttribute('data-stok');
        const kondisi = selectedOption.getAttribute('data-kondisi');
        const row = select.closest('.item-row');
        const inputJml = row.querySelector('.item-quantity');
        const stokInfo = row.querySelector('.stock-info');

        if (stok) {
            inputJml.max = stok;
            stokInfo.textContent = 'Maksimal pinjam: ' + stok + ' unit. Kondisi barang: ' + kondisi + '.';
            const kondisiClass = kondisi === 'Baik' ? 'text-success' : (kondisi === 'Rusak Ringan' ? 'text-warning' : 'text-danger');
            stokInfo.className = kondisiClass + ' small';
        } else {
            inputJml.removeAttribute('max');
            stokInfo.textContent = 'Pilih sarana untuk cek sisa stok.';
            stokInfo.className = 'text-muted small';
        }
    }

    const itemRows = document.getElementById('itemRows');
    document.getElementById('addItemRow').addEventListener('click', function() {
        itemRows.appendChild(document.getElementById('itemRowTemplate').content.cloneNode(true));
        updateRemoveButtons();
    });

    itemRows.addEventListener('click', function(event) {
        const removeButton = event.target.closest('.remove-item');
        if (removeButton) {
            removeButton.closest('.item-row').remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rows = itemRows.querySelectorAll('.item-row');
        rows.forEach(function(row) {
            row.querySelector('.remove-item').disabled = rows.length === 1;
        });
    }
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
