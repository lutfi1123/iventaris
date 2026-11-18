<?php
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Peminjaman.php';

Auth::requireLogin();
$currentUser = Auth::user();

$id = !empty($_GET['id']) ? (int) $_GET['id'] : 0;
$peminjamanObj = new Peminjaman();
$data = $peminjamanObj->getById($id);

if (!$data) {
    die("Data peminjaman tidak ditemukan.");
}

// Cek permission jika Peminjam mencoba melihat bukti orang lain
if (Auth::isPeminjam() && $currentUser['id_pegawai'] && (int)$data['id_pegawai'] !== (int)$currentUser['id_pegawai']) {
    die("Akses ditolak: Anda hanya dapat melihat bukti peminjaman milik Anda sendiri.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Peminjaman - <?= htmlspecialchars($data['kode_peminjaman']) ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-icons.min.css">
    <!-- Custom Print & Receipt Styles (External CSS) -->
    <link rel="stylesheet" href="assets/css/print.css">
</head>
<body>

<div class="no-print text-center my-3">
    <button class="btn btn-primary btn-sm me-2" onclick="window.print()"><i class="bi bi-printer"></i> Cetak Struk Peminjaman</button>
    <button class="btn btn-secondary btn-sm" onclick="window.close()"><i class="bi bi-x-circle"></i> Tutup</button>
</div>

<div class="receipt-card">
    <div class="text-center pb-3 border-bottom mb-3">
        <h5 class="fw-bold mb-0">SMK SARANA & PRASARANA</h5>
        <div class="small text-muted">BUKTI RESMI PEMINJAMAN SARANA DAN PRASARANA SEKOLAH</div>
        <div class="fw-bold fs-6 mt-1 text-primary"><?= htmlspecialchars($data['kode_peminjaman']) ?></div>
    </div>

    <table class="table table-sm table-borderless table-struk mb-3">
        <tr>
            <td style="width: 35%;" class="text-muted">Tanggal Pinjam</td>
            <td style="width: 5%;">:</td>
            <td class="fw-bold"><?= date('d F Y', strtotime($data['tanggal_pinjam'])) ?></td>
        </tr>
        <tr>
            <td class="text-muted">Status Transaksi</td>
            <td>:</td>
            <td>
                <?php if ($data['status_peminjaman'] === 'Dipinjam'): ?>
                    <span class="badge bg-warning text-dark border">Sedang Dipinjam</span>
                <?php elseif ($data['status_peminjaman'] === 'Sudah Kembali'): ?>
                    <span class="badge bg-success">Sudah Dikembalikan (<?= date('d/m/Y', strtotime($data['tanggal_kembali'])) ?>)</span>
                <?php else: ?>
                    <span class="badge bg-secondary"><?= htmlspecialchars($data['status_peminjaman']) ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="text-muted">Nama Peminjam</td>
            <td>:</td>
            <td class="fw-bold"><?= htmlspecialchars($data['nama_pegawai']) ?></td>
        </tr>
        <tr>
            <td class="text-muted">NIP / Identitas</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['nip']) ?></td>
        </tr>
        <tr>
            <td class="text-muted">Alamat / Kelas</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['alamat'] ?: '-') ?></td>
        </tr>
    </table>

    <div class="fw-bold small mb-2 border-bottom pb-1">RINCIAN SARANA / BARANG DIPINJAM:</div>
    <table class="table table-bordered table-sm table-struk mb-3">
        <thead class="table-light">
            <tr>
                <th>Kode Inventaris</th>
                <th>Nama Barang</th>
                <th>Kondisi</th>
                <th>Ruang Asal</th>
                <th class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['items'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['kode_inventaris']) ?></td>
                    <td class="fw-bold"><?= htmlspecialchars($item['nama_barang']) ?></td>
                    <td><?= htmlspecialchars($item['kondisi']) ?></td>
                    <td><?= htmlspecialchars($item['nama_ruang'] ?? '-') ?></td>
                    <td class="text-center fw-bold fs-6"><?= (int) $item['jumlah'] ?> Unit</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="alert alert-light border small py-2 mb-3" style="font-size: 8.5pt;">
        <strong>Ketentuan Peminjaman:</strong>
        <ol class="mb-0 ps-3">
            <li>Peminjam wajib menjaga keutuhan, kebersihan, dan fungsi sarana/prasarana.</li>
            <li>Kerusakan atau kehilangan akibat kelalaian peminjam menjadi tanggung jawab peminjam.</li>
            <li>Wajib menunjukkan struk ini pada saat proses pengembalian sarana.</li>
        </ol>
    </div>

    <div class="row ttd-block text-center">
        <div class="col-6">
            <p class="mb-0">Peminjam,</p>
            <br><br><br>
            <p class="fw-bold mb-0" style="text-decoration: underline;"><?= htmlspecialchars($data['nama_pegawai']) ?></p>
        </div>
        <div class="col-6">
            <p class="mb-0">Petugas Sarpras,</p>
            <br><br><br>
            <p class="fw-bold mb-0" style="text-decoration: underline;"><?= htmlspecialchars($currentUser['nama']) ?></p>
        </div>
    </div>
</div>

</body>
</html>
