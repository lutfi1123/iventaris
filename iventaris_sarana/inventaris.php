<?php
$pageTitle = 'Sarana & Prasarana - Inventaris SMK';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Inventaris.php';
require_once __DIR__ . '/classes/Jenis.php';
require_once __DIR__ . '/classes/Ruang.php';

Auth::requireAdmin();
$currentUser = Auth::user();

$inventarisObj = new Inventaris();
$jenisObj = new Jenis();
$ruangObj = new Ruang();

$listJenis = $jenisObj->getAll();
$listRuang = $ruangObj->getAll();

// Handle Form Submissions (Create, Update, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $res = $inventarisObj->create($_POST, $currentUser['id']);
        if ($res['status']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }
        header('Location: inventaris.php');
        exit;
    }

    if ($action === 'update') {
        $id = (int) $_POST['id_inventaris'];
        $res = $inventarisObj->update($id, $_POST);
        if ($res['status']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }
        header('Location: inventaris.php');
        exit;
    }

    if ($action === 'delete') {
        $id = (int) $_POST['id_inventaris'];
        $res = $inventarisObj->delete($id);
        if ($res['status']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }
        header('Location: inventaris.php');
        exit;
    }
}

// Filters & Data Query
$search = trim($_GET['search'] ?? '');
$filterJenis = !empty($_GET['jenis']) ? (int) $_GET['jenis'] : null;
$filterRuang = !empty($_GET['ruang']) ? (int) $_GET['ruang'] : null;

$items = $inventarisObj->getAll($search, $filterJenis, $filterRuang);
$nextKode = $inventarisObj->generateKode();

include __DIR__ . '/templates/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Inventaris Sarana & Prasarana</h4>
        <p class="text-muted small mb-0">Pendataan dan registrasi barang sarana prasarana sekolah</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        Tambah Sarana Baru
    </button>
</div>

<!-- Search & Filter Card -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau kode barang..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="jenis" class="form-select">
                    <option value="">-- Semua Jenis Sarana --</option>
                    <?php foreach ($listJenis as $j): ?>
                        <option value="<?= $j['id_jenis'] ?>" <?= $filterJenis === (int)$j['id_jenis'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($j['nama_jenis']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="ruang" class="form-select">
                    <option value="">-- Semua Ruangan --</option>
                    <?php foreach ($listRuang as $r): ?>
                        <option value="<?= $r['id_ruang'] ?>" <?= $filterRuang === (int)$r['id_ruang'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['nama_ruang']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel"></i> Filter</button>
                <?php if (!empty($search) || !empty($filterJenis) || !empty($filterRuang)): ?>
                    <a href="inventaris.php" class="btn btn-outline-secondary" title="Reset filter"><i class="bi bi-x-lg"></i></a>
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
                        <th>Kode</th>
                        <th>Nama Sarana</th>
                        <th>Jenis</th>
                        <th>Ruangan</th>
                        <th class="text-center">Kondisi</th>
                        <th class="text-center">Jumlah Stok</th>
                        <th>Tgl Register</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Tidak ada data sarana inventaris ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($items as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($item['kode_inventaris']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($item['nama']) ?></div>
                                    <?php if (!empty($item['keterangan'])): ?>
                                        <small class="text-muted d-block"><?= htmlspecialchars($item['keterangan']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">
                                        <?= htmlspecialchars($item['nama_jenis']) ?>
                                    </span>
                                </td>
                                <td>
                                    <i class="bi bi-door-open me-1 text-secondary"></i>
                                    <?= htmlspecialchars($item['nama_ruang']) ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $kondisiBadge = match($item['kondisi']) {
                                        'Baik'         => 'bg-success-subtle text-success border-success-subtle',
                                        'Rusak Ringan' => 'bg-warning-subtle text-warning border-warning-subtle',
                                        default        => 'bg-danger-subtle text-danger border-danger-subtle'
                                    };
                                    ?>
                                    <span class="badge border <?= $kondisiBadge ?>">
                                        <?= htmlspecialchars($item['kondisi']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge fs-6 <?= $item['jumlah'] > 0 ? 'bg-primary' : 'bg-danger' ?>">
                                        <?= $item['jumlah'] ?> Unit
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($item['tanggal_register'])) ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit"
                                                data-item='<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'
                                                title="Edit Barang">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalHapus"
                                                data-id="<?= $item['id_inventaris'] ?>"
                                                data-nama="<?= htmlspecialchars($item['nama']) ?>"
                                                title="Hapus Barang">
                                            <i class="bi bi-trash"></i>
                                        </button>
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

<!-- Modal Tambah Sarana -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="create">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalTambahLabel">Tambah Sarana Inventaris Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kode Inventaris <span class="text-danger">*</span></label>
                        <input type="text" name="kode_inventaris" class="form-control" value="<?= $nextKode ?>" required>
                        <small class="text-muted">Kode digenerate otomatis atau dapat disesuaikan.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Register <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_register" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Nama Sarana / Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Laptop Asus VivoBook 14" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jenis Sarana <span class="text-danger">*</span></label>
                        <select name="id_jenis" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            <?php foreach ($listJenis as $j): ?>
                                <option value="<?= $j['id_jenis'] ?>"><?= htmlspecialchars($j['nama_jenis']) ?> (<?= htmlspecialchars($j['kode_jenis']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ruangan Penempatan <span class="text-danger">*</span></label>
                        <select name="id_ruang" class="form-select" required>
                            <option value="">-- Pilih Ruangan --</option>
                            <?php foreach ($listRuang as $r): ?>
                                <option value="<?= $r['id_ruang'] ?>"><?= htmlspecialchars($r['nama_ruang']) ?> (<?= htmlspecialchars($r['kode_ruang']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kondisi Barang <span class="text-danger">*</span></label>
                        <select name="kondisi" class="form-select" required>
                            <option value="Baik" selected>Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jumlah Stok (Unit) <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah" class="form-control" min="0" value="1" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Spesifikasi atau catatan barang..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Sarana</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Sarana -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id_inventaris" id="edit_id">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold" id="modalEditLabel">Edit Data Sarana Inventaris</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kode Inventaris <span class="text-danger">*</span></label>
                        <input type="text" name="kode_inventaris" id="edit_kode" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Register <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_register" id="edit_tgl" class="form-control" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Nama Sarana / Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jenis Sarana <span class="text-danger">*</span></label>
                        <select name="id_jenis" id="edit_jenis" class="form-select" required>
                            <?php foreach ($listJenis as $j): ?>
                                <option value="<?= $j['id_jenis'] ?>"><?= htmlspecialchars($j['nama_jenis']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ruangan Penempatan <span class="text-danger">*</span></label>
                        <select name="id_ruang" id="edit_ruang" class="form-select" required>
                            <?php foreach ($listRuang as $r): ?>
                                <option value="<?= $r['id_ruang'] ?>"><?= htmlspecialchars($r['nama_ruang']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kondisi Barang <span class="text-danger">*</span></label>
                        <select name="kondisi" id="edit_kondisi" class="form-select" required>
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jumlah Stok (Unit) <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah" id="edit_jumlah" class="form-control" min="0" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Keterangan Tambahan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Perbarui Sarana</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id_inventaris" id="hapus_id">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-0">Apakah Anda yakin ingin menghapus sarana inventaris <strong id="hapus_nama"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Event listener modal edit
    const modalEdit = document.getElementById('modalEdit');
    modalEdit.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const item = JSON.parse(button.getAttribute('data-item'));

        document.getElementById('edit_id').value = item.id_inventaris;
        document.getElementById('edit_kode').value = item.kode_inventaris;
        document.getElementById('edit_nama').value = item.nama;
        document.getElementById('edit_tgl').value = item.tanggal_register;
        document.getElementById('edit_jenis').value = item.id_jenis;
        document.getElementById('edit_ruang').value = item.id_ruang;
        document.getElementById('edit_kondisi').value = item.kondisi;
        document.getElementById('edit_jumlah').value = item.jumlah;
        document.getElementById('edit_keterangan').value = item.keterangan || '';
    });

    // Event listener modal hapus
    const modalHapus = document.getElementById('modalHapus');
    modalHapus.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        document.getElementById('hapus_id').value = button.getAttribute('data-id');
        document.getElementById('hapus_nama').textContent = button.getAttribute('data-nama');
    });
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
