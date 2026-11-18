<?php
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Laporan.php';
require_once __DIR__ . '/classes/Ruang.php';
require_once __DIR__ . '/classes/Jenis.php';

Auth::requireLaporan();
$currentUser = Auth::user();

$laporanObj = new Laporan();
$ruangObj = new Ruang();
$jenisObj = new Jenis();

$tipe = $_GET['tipe'] ?? 'peminjaman';

if ($tipe === 'peminjaman') {
    $tglAwal = $_GET['tgl_awal'] ?? date('Y-m-01');
    $tglAkhir = $_GET['tgl_akhir'] ?? date('Y-m-d');
    $statusFilter = $_GET['status'] ?? 'ALL';
    $excelParams = [
        'tipe' => 'peminjaman',
        'tgl_awal' => $tglAwal,
        'tgl_akhir' => $tglAkhir,
        'status' => $statusFilter,
    ];
    $data = $laporanObj->getLaporanPeminjaman($tglAwal, $tglAkhir, $statusFilter);
    $peminjamGrouped = $laporanObj->groupLaporanPeminjaman($data);
    $judulLaporan = "LAPORAN TRANSAKSI PEMINJAMAN SARANA DAN PRASARANA";
    $subJudul = "Periode: " . date('d F Y', strtotime($tglAwal)) . " s/d " . date('d F Y', strtotime($tglAkhir)) . " (Status: " . ($statusFilter === 'ALL' ? 'Semua' : $statusFilter) . ")";
} else {
    $idRuang = !empty($_GET['ruang']) ? (int) $_GET['ruang'] : null;
    $idJenis = !empty($_GET['jenis']) ? (int) $_GET['jenis'] : null;
    $kondisi = $_GET['kondisi'] ?? 'ALL';
    $excelParams = [
        'tipe' => 'inventaris',
        'ruang' => $idRuang ?? '',
        'jenis' => $idJenis ?? '',
        'kondisi' => $kondisi,
    ];
    $data = $laporanObj->getLaporanInventaris($idRuang, $idJenis, $kondisi);
    $judulLaporan = "LAPORAN REKAPITULASI INVENTARIS SARANA DAN PRASARANA";
    $subJudul = "Kondisi: " . ($kondisi === 'ALL' ? 'Semua' : $kondisi);
}
$excelUrl = 'export_laporan.php?' . http_build_query($excelParams);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $judulLaporan ?> - BAKNUS 666</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-icons.min.css">
    <!-- Custom Print Styles (External CSS) -->
    <link rel="stylesheet" href="assets/css/print.css?v=2">
</head>
<body class="<?= $tipe === 'peminjaman' ? 'laporan-peminjaman' : 'laporan-inventaris' ?>">

<div class="no-print text-center my-3">
    <button class="btn btn-danger me-2" onclick="window.print()"><i class="bi bi-file-earmark-pdf"></i> Simpan sebagai PDF</button>
    <a href="<?= htmlspecialchars($excelUrl) ?>" class="btn btn-success me-2"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
    <button class="btn btn-secondary" onclick="window.close()"><i class="bi bi-x-circle"></i> Tutup</button>
</div>

<div class="print-container">
    <!-- Kop Surat Resmi Sekolah -->
    <div class="kop-surat">
        <img class="kop-surat-logo" src="assets/logo-baknus.png" alt="Logo Bakti Nusantara 666">
        <div class="kop-surat-text">
            <h4>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h4>
            <h4>DINAS PENDIDIKAN DAN KEBUDAYAAN</h4>
            <h3>SMK REKAYASA PERANGKAT LUNAK</h3>
            <p>Bidang Keahlian: Teknologi Informasi dan Komunikasi | Program Keahlian: Rekayasa Perangkat Lunak</p>
            <p>Jalan Pendidikan No. 100, Bandung &bull; Telp: (022) 1234567 &bull; Website: www.smk-sarpras.sch.id</p>
        </div>
    </div>

    <!-- Judul Laporan -->
    <div class="text-center mb-4">
        <h5 class="fw-bold mb-1" style="text-decoration: underline;"><?= $judulLaporan ?></h5>
        <div style="font-size: 10pt;"><?= $subJudul ?></div>
    </div>

    <!-- Tabel Data Laporan -->
    <?php if ($tipe === 'peminjaman'): ?>
        <div class="table-transaksi-wrapper">
            <table class="table-laporan table-transaksi">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Nama Peminjam</th>
                        <th>NIP / Identitas</th>
                        <th>Kode Pinjam</th>
                        <th>Tgl Pinjam</th>
                        <th>Kategori</th>
                        <th>Nama Sarana</th>
                        <th>Kode Inventaris</th>
                        <th>Ruang</th>
                        <th>Jumlah</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($peminjamGrouped)): ?>
                        <tr>
                            <td colspan="12" class="text-center">Tidak ada data transaksi peminjaman pada periode ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $no = 1;
                        $totalItem = 0;
                        foreach ($peminjamGrouped as $peminjam):
                            $peminjamRowspan = array_sum(array_map(static fn($transaksi) => count($transaksi['items']), $peminjam['transaksi']));
                            $peminjamRowStarted = false;
                            foreach ($peminjam['transaksi'] as $transaksi):
                                $transaksiRowspan = count($transaksi['items']);
                                $transaksiRowStarted = false;
                                foreach ($transaksi['items'] as $itemIndex => $item):
                                    $totalItem += (int) $item['jumlah'];
                        ?>
                                    <tr>
                                        <?php if (!$peminjamRowStarted): ?>
                                            <td class="text-center" rowspan="<?= $peminjamRowspan ?>"><?= $no++ ?></td>
                                            <td rowspan="<?= $peminjamRowspan ?>"><?= htmlspecialchars($peminjam['nama_pegawai']) ?></td>
                                            <td class="kolom-nip" rowspan="<?= $peminjamRowspan ?>"><?= htmlspecialchars($peminjam['nip']) ?></td>
                                            <?php $peminjamRowStarted = true; ?>
                                        <?php endif; ?>
                                        <?php if (!$transaksiRowStarted): ?>
                                            <td class="kolom-kode" rowspan="<?= $transaksiRowspan ?>"><?= htmlspecialchars($transaksi['kode_peminjaman']) ?></td>
                                            <td class="text-center" rowspan="<?= $transaksiRowspan ?>"><?= date('d/m/Y', strtotime($transaksi['tanggal_pinjam'])) ?></td>
                                            <?php $transaksiRowStarted = true; ?>
                                        <?php endif; ?>
                                        <td><?= htmlspecialchars($item['nama_jenis'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                        <td><?= htmlspecialchars($item['kode_inventaris']) ?></td>
                                        <td><?= htmlspecialchars($item['nama_ruang'] ?? '-') ?></td>
                                        <td class="text-center"><?= (int) $item['jumlah'] ?></td>
                                        <?php if ($itemIndex === 0): ?>
                                            <td class="text-center" rowspan="<?= $transaksiRowspan ?>"><?= !empty($transaksi['tanggal_kembali']) ? date('d/m/Y', strtotime($transaksi['tanggal_kembali'])) : '-' ?></td>
                                            <td class="text-center" rowspan="<?= $transaksiRowspan ?>"><?= htmlspecialchars($transaksi['status_peminjaman']) ?></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        <tr style="font-weight: bold; background-color: #f8fafc;">
                            <td colspan="9" class="text-end">Total Jumlah Unit Dipinjam:</td>
                            <td class="text-center"><?= $totalItem ?></td>
                            <td colspan="2"></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <table class="table-laporan">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Kode Inventaris</th>
                    <th>Nama Sarana</th>
                    <th>Jenis</th>
                    <th>Ruangan</th>
                    <th>Kondisi</th>
                    <th>Stok Unit</th>
                    <th>Tgl Register</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data sarana inventaris sesuai kriteria.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; $totalStok = 0; foreach ($data as $r): $totalStok += $r['jumlah']; ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($r['kode_inventaris']) ?></td>
                            <td><?= htmlspecialchars($r['nama']) ?></td>
                            <td><?= htmlspecialchars($r['nama_jenis']) ?></td>
                            <td><?= htmlspecialchars($r['nama_ruang']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($r['kondisi']) ?></td>
                            <td class="text-center"><?= $r['jumlah'] ?></td>
                            <td class="text-center"><?= date('d/m/Y', strtotime($r['tanggal_register'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr style="font-weight: bold; background-color: #f8fafc;">
                        <td colspan="6" class="text-end">Total Seluruh Stok Sarana:</td>
                        <td class="text-center"><?= $totalStok ?> Unit</td>
                        <td></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Tanda Tangan Pengesahan -->
    <div class="row ttd-box">
        <div class="col-6 text-center">
            <p>Mengetahui,<br>Kepala Sekolah SMK</p>
            <br><br><br>
            <p class="fw-bold mb-0" style="text-decoration: underline;">Drs. H. Mulyadi, M.Pd</p>
            <p>NIP. 196803121994031002</p>
        </div>
        <div class="col-6 text-center">
            <p>Bandung, <?= date('d F Y') ?><br>Petugas Pengelola Sarana & Prasarana</p>
            <br><br><br>
            <p class="fw-bold mb-0" style="text-decoration: underline;"><?= htmlspecialchars($currentUser['nama']) ?></p>
            <p>NIP. <?= htmlspecialchars($currentUser['username']) ?></p>
        </div>
    </div>
</div>

</body>
</html>
