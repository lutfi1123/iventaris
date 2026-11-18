<?php
require_once __DIR__ . '/../config/Database.php';

class Laporan {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Ringkasan dashboard
    public function getDashboardSummary(): array {
        try {
            $stmt = $this->db->query("CALL sp_ringkasan_dashboard()");
            $data = $stmt->fetch();
            $stmt->closeCursor();
            if ($data) {
                return $data;
            }
        } catch (PDOException $e) {
        }

        $summary = [];
        $summary['total_inventaris'] = (int) $this->db->query("SELECT COUNT(*) FROM inventaris")->fetchColumn();
        $summary['total_stok_barang'] = (int) $this->db->query("SELECT IFNULL(SUM(jumlah), 0) FROM inventaris")->fetchColumn();
        $summary['total_sedang_dipinjam'] = (int) $this->db->query("SELECT COUNT(*) FROM peminjaman WHERE status_peminjaman = 'Dipinjam'")->fetchColumn();
        $summary['total_ruang'] = (int) $this->db->query("SELECT COUNT(*) FROM ruang")->fetchColumn();
        $summary['total_pegawai'] = (int) $this->db->query("SELECT COUNT(*) FROM pegawai")->fetchColumn();
        $summary['total_jenis'] = (int) $this->db->query("SELECT COUNT(*) FROM jenis")->fetchColumn();

        return $summary;
    }

    // Laporan peminjaman
    public function getLaporanPeminjaman(string $tglAwal, string $tglAkhir, string $status = 'ALL'): array {
        try {
            $stmt = $this->db->prepare("CALL sp_laporan_peminjaman(:awal, :akhir, :status)");
            $stmt->execute([
                ':awal'   => $tglAwal,
                ':akhir'  => $tglAkhir,
                ':status' => $status
            ]);
            $rows = $stmt->fetchAll();
            $stmt->closeCursor();
            return $rows;
        } catch (PDOException $e) {
            // Fallback manual jika procedure belum di-load
            $sql = "
                SELECT 
                    p.id_peminjaman,
                    p.kode_peminjaman,
                    p.tanggal_pinjam,
                    p.tanggal_kembali,
                    p.status_peminjaman,
                    pg.nama_pegawai,
                    pg.nip,
                    i.kode_inventaris,
                    i.nama AS nama_barang,
                    j.nama_jenis,
                    r.nama_ruang,
                    dp.jumlah
                FROM peminjaman p
                INNER JOIN pegawai pg ON p.id_pegawai = pg.id_pegawai
                INNER JOIN detail_pinjam dp ON p.id_peminjaman = dp.id_peminjaman
                INNER JOIN inventaris i ON dp.id_inventaris = i.id_inventaris
                LEFT JOIN jenis j ON i.id_jenis = j.id_jenis
                LEFT JOIN ruang r ON i.id_ruang = r.id_ruang
                WHERE (p.tanggal_pinjam BETWEEN :awal AND :akhir)
            ";
            $params = [':awal' => $tglAwal, ':akhir' => $tglAkhir];

            if ($status !== 'ALL') {
                $sql .= " AND p.status_peminjaman = :status";
                $params[':status'] = $status;
            }

            $sql .= " ORDER BY p.tanggal_pinjam DESC, p.id_peminjaman DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }
    }

    public function groupLaporanPeminjaman(array $rows): array {
        $peminjamGrouped = [];

        foreach ($rows as $row) {
            $idTransaksi = (int) $row['id_peminjaman'];
            $idPeminjam = (string) ($row['id_pegawai'] ?? ($row['nip'] . '|' . $row['nama_pegawai']));

            if (!isset($peminjamGrouped[$idPeminjam])) {
                $peminjamGrouped[$idPeminjam] = [
                    'nama_pegawai' => $row['nama_pegawai'],
                    'nip' => $row['nip'],
                    'transaksi' => [],
                ];
            }

            if (!isset($peminjamGrouped[$idPeminjam]['transaksi'][$idTransaksi])) {
                $peminjamGrouped[$idPeminjam]['transaksi'][$idTransaksi] = [
                    'id_peminjaman' => $idTransaksi,
                    'kode_peminjaman' => $row['kode_peminjaman'],
                    'tanggal_pinjam' => $row['tanggal_pinjam'],
                    'tanggal_kembali' => $row['tanggal_kembali'],
                    'status_peminjaman' => $row['status_peminjaman'],
                    'items' => [],
                    'total_jumlah' => 0,
                ];
            }

            $peminjamGrouped[$idPeminjam]['transaksi'][$idTransaksi]['items'][] = [
                'nama_barang' => $row['nama_barang'],
                'kode_inventaris' => $row['kode_inventaris'],
                'nama_jenis' => $row['nama_jenis'],
                'nama_ruang' => $row['nama_ruang'],
                'jumlah' => (int) $row['jumlah'],
            ];
            $peminjamGrouped[$idPeminjam]['transaksi'][$idTransaksi]['total_jumlah'] += (int) $row['jumlah'];
        }

        foreach ($peminjamGrouped as &$peminjam) {
            $peminjam['transaksi'] = array_values($peminjam['transaksi']);
        }
        unset($peminjam);

        return array_values($peminjamGrouped);
    }

    /**
     * Laporan Inventaris Sarana dan Prasarana
     */
    public function getLaporanInventaris(?int $idRuang = null, ?int $idJenis = null, ?string $kondisi = null): array {
        $sql = "
            SELECT 
                i.*,
                j.nama_jenis,
                j.kode_jenis,
                r.nama_ruang,
                r.kode_ruang,
                p.nama_petugas
            FROM inventaris i
            INNER JOIN jenis j ON i.id_jenis = j.id_jenis
            INNER JOIN ruang r ON i.id_ruang = r.id_ruang
            INNER JOIN petugas p ON i.id_petugas = p.id_petugas
            WHERE 1=1
        ";
        $params = [];

        if (!empty($idRuang)) {
            $sql .= " AND i.id_ruang = :ruang";
            $params[':ruang'] = $idRuang;
        }

        if (!empty($idJenis)) {
            $sql .= " AND i.id_jenis = :jenis";
            $params[':jenis'] = $idJenis;
        }

        if (!empty($kondisi) && $kondisi !== 'ALL') {
            $sql .= " AND i.kondisi = :kondisi";
            $params[':kondisi'] = $kondisi;
        }

        $sql .= " ORDER BY i.id_ruang ASC, i.id_inventaris ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
