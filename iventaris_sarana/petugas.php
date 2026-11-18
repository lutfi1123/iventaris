<?php
$pageTitle = 'Manajemen Pengguna - Inventaris SMK';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Petugas.php';
require_once __DIR__ . '/classes/Pegawai.php';

Auth::requireAdmin();
$currentUser = Auth::user();

$petugasObj = new Petugas();
$pegawaiObj = new Pegawai();

$levels = $petugasObj->getLevels();
$listPegawai = $pegawaiObj->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $res = $petugasObj->create($_POST);
        if ($res['status']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }
        header('Location: petugas.php');
        exit;
    }

    if ($action === 'update') {
        $id = (int) $_POST['id_petugas'];
        $res = $petugasObj->update($id, $_POST);
        if ($res['status']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }
        header('Location: petugas.php');
        exit;
    }

    if ($action === 'delete') {
        $id = (int) $_POST['id_petugas'];
        $res = $petugasObj->delete($id, $currentUser['id']);
        if ($res['status']) {
            $_SESSION['flash_success'] = $res['message'];
        } else {
            $_SESSION['flash_error'] = $res['message'];
        }
        header('Location: petugas.php');
        exit;
    }
}

$search = trim($_GET['search'] ?? '');
$listPetugas = $petugasObj->getAll($search);

include __DIR__ . '/templates/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Manajemen Pengguna Sistem</h4>
        <p class="text-muted small mb-0">Kelola akun pengguna (Administrator dan Operator)</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle me-1"></i> Tambah Pengguna Baru
    </button>
</div>

<!-- Search Card -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari username, nama pengguna, atau level..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search"></i> Cari</button>
                <?php if (!empty($search)): ?>
                    <a href="petugas.php" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
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
                        <th>Username</th>
                        <th>Nama Pengguna</th>
                        <th>Level Hak Akses</th>
                        <th>Terkait Pegawai</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listPetugas)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($listPetugas as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border fs-6">
                                        <i class="bi bi-person me-1"></i><?= htmlspecialchars($row['username']) ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_petugas']) ?></td>
                                <td>
                                    <?php
                                    $badge = match(strtolower($row['nama_level'])) {
                                        'administrator' => 'bg-primary-subtle text-primary border-primary-subtle',
                                        'operator'      => 'bg-warning-subtle text-warning border-warning-subtle',
                                        default         => 'bg-success-subtle text-success border-success-subtle'
                                    };
                                    ?>
                                    <span class="badge border <?= $badge ?> text-uppercase">
                                        <?= htmlspecialchars($row['nama_level']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($row['nama_pegawai'])): ?>
                                        <i class="bi bi-link-45deg text-primary"></i> <?= htmlspecialchars($row['nama_pegawai']) ?> (<?= htmlspecialchars($row['nip']) ?>)
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
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
                                        <?php if ($row['id_petugas'] != $currentUser['id']): ?>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalHapus"
                                                    data-id="<?= $row['id_petugas'] ?>"
                                                    data-nama="<?= htmlspecialchars($row['username']) ?>"
                                                    title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
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
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_petugas" class="form-control" placeholder="Contoh: Ahmad Fauzi" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" placeholder="Contoh: ahmad" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Level Hak Akses <span class="text-danger">*</span></label>
                    <select name="id_level" class="form-select" required>
                        <option value="">-- Pilih Level --</option>
                        <?php foreach ($levels as $lvl): ?>
                            <option value="<?= $lvl['id_level'] ?>"><?= strtoupper(htmlspecialchars($lvl['nama_level'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tautkan ke Data Pegawai (Opsional)</label>
                    <select name="id_pegawai" class="form-select">
                        <option value="">-- Tidak Ditautkan --</option>
                        <?php foreach ($listPegawai as $p): ?>
                            <option value="<?= $p['id_pegawai'] ?>"><?= htmlspecialchars($p['nama_pegawai']) ?> (<?= htmlspecialchars($p['nip']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id_petugas" id="edit_id">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_petugas" id="edit_nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" id="edit_username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Level Hak Akses <span class="text-danger">*</span></label>
                    <select name="id_level" id="edit_level" class="form-select" required>
                        <?php foreach ($levels as $lvl): ?>
                            <option value="<?= $lvl['id_level'] ?>"><?= strtoupper(htmlspecialchars($lvl['nama_level'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="id_level" id="edit_level_legacy" disabled>
                    <div id="edit_level_legacy_info" class="form-text d-none">
                        Level akun ini dipertahankan karena tidak tersedia untuk akun baru.
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tautkan ke Data Pegawai</label>
                    <select name="id_pegawai" id="edit_pegawai" class="form-select">
                        <option value="">-- Tidak Ditautkan --</option>
                        <?php foreach ($listPegawai as $p): ?>
                            <option value="<?= $p['id_pegawai'] ?>"><?= htmlspecialchars($p['nama_pegawai']) ?> (<?= htmlspecialchars($p['nip']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Perbarui Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id_petugas" id="hapus_id">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus Pengguna</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-0">Yakin ingin menghapus akun <strong id="hapus_nama"></strong>?</p>
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
        document.getElementById('edit_id').value = item.id_petugas;
        document.getElementById('edit_nama').value = item.nama_petugas;
        document.getElementById('edit_username').value = item.username;
        const editLevel = document.getElementById('edit_level');
        const legacyLevel = document.getElementById('edit_level_legacy');
        const legacyLevelInfo = document.getElementById('edit_level_legacy_info');
        const hasAssignableLevel = Array.from(editLevel.options).some(option => option.value === String(item.id_level));
        editLevel.disabled = !hasAssignableLevel;
        legacyLevel.disabled = hasAssignableLevel;
        legacyLevel.value = hasAssignableLevel ? '' : item.id_level;
        legacyLevelInfo.classList.toggle('d-none', hasAssignableLevel);
        if (hasAssignableLevel) {
            editLevel.value = item.id_level;
        }
        document.getElementById('edit_pegawai').value = item.id_pegawai || '';
    });

    const modalHapus = document.getElementById('modalHapus');
    modalHapus.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        document.getElementById('hapus_id').value = btn.getAttribute('data-id');
        document.getElementById('hapus_nama').textContent = btn.getAttribute('data-nama');
    });
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>
