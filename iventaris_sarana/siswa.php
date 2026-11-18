<?php
$pageTitle = 'Data Siswa - Inventaris SMK';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Pegawai.php';

Auth::requireSiswa();
$pegawaiObj = new Pegawai();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $nip = trim($_POST['nip'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $isStudentIdentity = preg_match('/^(SISWA-.+|[0-9]{10,13})$/i', $nip) === 1;

    if (in_array($action, ['create', 'update'], true) && !$isStudentIdentity) {
        $_SESSION['flash_error'] = 'Nomor identitas siswa harus berformat SISWA-... atau 10-13 digit.';
        header('Location: siswa.php');
        exit;
    }

    if (in_array($action, ['create', 'update'], true) && ($kelas === '' || strlen($kelas) > 30)) {
        $_SESSION['flash_error'] = 'Kelas wajib diisi dan maksimal 30 karakter.';
        header('Location: siswa.php');
        exit;
    }

    $_POST['kelas'] = $kelas;

    if ($action === 'create') {
        if ($pegawaiObj->create($_POST)) {
            $_SESSION['flash_success'] = 'Data siswa berhasil ditambahkan.';
        } else {
            $_SESSION['flash_error'] = 'Gagal menambahkan siswa.';
        }
        header('Location: siswa.php');
        exit;
    }

    if ($action === 'update') {
        $id = (int) ($_POST['id_pegawai'] ?? 0);
        if ($pegawaiObj->update($id, $_POST, 'siswa')) {
            $_SESSION['flash_success'] = 'Data siswa berhasil diperbarui.';
        } else {
            $_SESSION['flash_error'] = 'Gagal memperbarui data siswa.';
        }
        header('Location: siswa.php');
        exit;
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id_pegawai'] ?? 0);
        $result = $pegawaiObj->delete($id, 'siswa');
        if ($result['status']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }
        header('Location: siswa.php');
        exit;
    }
}

$search = trim($_GET['search'] ?? '');
$listSiswa = $pegawaiObj->getAll($search, 'siswa');

include __DIR__ . '/templates/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Data Siswa</h4>
        <p class="text-muted small mb-0">Kelola data siswa sebagai peminjam sarana sekolah</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
    </button>
</div>

<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, NIS, kelas, atau alamat..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search"></i> Cari</button>
                <?php if ($search !== ''): ?>
                    <a href="siswa.php" class="btn btn-outline-secondary" aria-label="Hapus pencarian"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Alamat</th>
                        <th class="text-center">Total Riwayat Pinjam</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listSiswa)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada data siswa.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($listSiswa as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><span class="badge bg-light text-dark border fs-6"><?= htmlspecialchars($row['nip']) ?></span></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_pegawai']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['kelas'] ?: '-') ?></span></td>
                                <td class="text-muted"><?= htmlspecialchars($row['alamat'] ?: '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <?= (int) $row['total_pinjam'] ?> Kali Transaksi
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
                                                data-id="<?= (int) $row['id_pegawai'] ?>"
                                                data-nama="<?= htmlspecialchars($row['nama_pegawai']) ?>"
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

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <input type="hidden" name="action" value="create">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">NIS <span class="text-danger">*</span></label>
                    <input type="text" name="nip" class="form-control" placeholder="10-13 digit atau SISWA-2026-001" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_pegawai" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                    <input type="text" name="kelas" class="form-control" placeholder="Contoh: X AKT 1" maxlength="30" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id_pegawai" id="edit_id">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">NIS <span class="text-danger">*</span></label>
                    <input type="text" name="nip" id="edit_nip" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_pegawai" id="edit_nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                    <input type="text" name="kelas" id="edit_kelas" class="form-control" maxlength="30" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat</label>
                    <textarea name="alamat" id="edit_alamat" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id_pegawai" id="hapus_id">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-0">Yakin ingin menghapus data siswa <strong id="hapus_nama"></strong>?</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('modalEdit').addEventListener('show.bs.modal', function(event) {
        const item = JSON.parse(event.relatedTarget.getAttribute('data-item'));
        document.getElementById('edit_id').value = item.id_pegawai;
        document.getElementById('edit_nip').value = item.nip;
        document.getElementById('edit_nama').value = item.nama_pegawai;
        document.getElementById('edit_kelas').value = item.kelas || '';
        document.getElementById('edit_alamat').value = item.alamat || '';
    });

    document.getElementById('modalHapus').addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        document.getElementById('hapus_id').value = button.getAttribute('data-id');
        document.getElementById('hapus_nama').textContent = button.getAttribute('data-nama');
    });
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
