<?php
$pageTitle = 'Data Ruangan - Inventaris SMK';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Ruang.php';

Auth::requireAdmin();
$ruangObj = new Ruang();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        if ($ruangObj->create($_POST)) {
            $_SESSION['flash_success'] = 'Ruangan baru berhasil ditambahkan.';
        } else {
            $_SESSION['flash_error'] = 'Gagal menambahkan ruangan.';
        }
        header('Location: ruang.php');
        exit;
    }

    if ($action === 'update') {
        $id = (int) $_POST['id_ruang'];
        if ($ruangObj->update($id, $_POST)) {
            $_SESSION['flash_success'] = 'Data ruangan berhasil diperbarui.';
        } else {
            $_SESSION['flash_error'] = 'Gagal memperbarui ruangan.';
        }
        header('Location: ruang.php');
        exit;
    }

    if ($action === 'delete') {
        $id = (int) $_POST['id_ruang'];
        $res = $ruangObj->delete($id);
        if ($res['status']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }
        header('Location: ruang.php');
        exit;
    }
}

$search = trim($_GET['search'] ?? '');
$listRuang = $ruangObj->getAll($search);

include __DIR__ . '/templates/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Data Ruangan</h4>
        <p class="text-muted small mb-0">Lokasi dan ruang penempatan sarana prasarana sekolah</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-1"></i> Tambah Ruang Baru
    </button>
</div>

<!-- Search Card -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama ruangan atau kode..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search"></i> Cari</button>
                <?php if (!empty($search)): ?>
                    <a href="ruang.php" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Kode Ruang</th>
                        <th>Nama Ruangan</th>
                        <th>Keterangan / Lokasi</th>
                        <th class="text-center">Total Sarana Ditempatkan</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listRuang)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada data ruangan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($listRuang as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><span class="badge bg-light text-primary border border-primary-subtle fs-6"><?= htmlspecialchars($row['kode_ruang']) ?></span></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_ruang']) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($row['keterangan'] ?: '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        <?= (int) $row['total_barang'] ?> Barang
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit"
                                                data-item='<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'
                                                title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalHapus"
                                                data-id="<?= $row['id_ruang'] ?>"
                                                data-nama="<?= htmlspecialchars($row['nama_ruang']) ?>"
                                                title="Hapus">
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="create">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Ruangan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Ruang <span class="text-danger">*</span></label>
                    <input type="text" name="kode_ruang" class="form-control" placeholder="Contoh: RNG-LAB1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Ruangan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_ruang" class="form-control" placeholder="Contoh: Lab Komputer RPL 1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Keterangan / Lokasi</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Gedung B Lantai 2..."></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id_ruang" id="edit_id">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Ruang <span class="text-danger">*</span></label>
                    <input type="text" name="kode_ruang" id="edit_kode" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Ruangan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_ruang" id="edit_nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Keterangan / Lokasi</label>
                    <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id_ruang" id="hapus_id">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-0">Yakin ingin menghapus ruangan <strong id="hapus_nama"></strong>?</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modalEdit = document.getElementById('modalEdit');
    modalEdit.addEventListener('show.bs.modal', function(event) {
        const item = JSON.parse(event.relatedTarget.getAttribute('data-item'));
        document.getElementById('edit_id').value = item.id_ruang;
        document.getElementById('edit_kode').value = item.kode_ruang;
        document.getElementById('edit_nama').value = item.nama_ruang;
        document.getElementById('edit_keterangan').value = item.keterangan || '';
    });

    const modalHapus = document.getElementById('modalHapus');
    modalHapus.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        document.getElementById('hapus_id').value = btn.getAttribute('data-id');
        document.getElementById('hapus_nama').textContent = btn.getAttribute('data-nama');
    });
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
