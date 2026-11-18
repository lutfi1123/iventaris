<?php
require_once __DIR__ . '/../config/Database.php';

class Peminjaman {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function generateKode(): string {
        $dateStr = date('Ymd');
        $stmt = $this->db->query("SELECT MAX(id_peminjaman) AS max_id FROM peminjaman");
        $nextId = (int) $stmt->fetchColumn() + 1;
        return sprintf("PJ-%s-%03d", $dateStr, $nextId);
    }

    public function getAll(array $filters = []): array {
        $sql = "
            SELECT 
                p.id_peminjaman,
                p.kode_peminjaman,
                p.tanggal_pinjam,
                p.tanggal_kembali,
                p.status_peminjaman,
                p.id_pegawai,
                pg.nama_pegawai,
                pg.nip,
                dp.id_detail_pinjam,
                dp.id_inventaris,
                dp.jumlah,
                i.nama AS nama_barang,
                i.kondisi,
                i.kode_inventaris,
                j.nama_jenis,
                r.nama_ruang
            FROM peminjaman p
            INNER JOIN pegawai pg ON p.id_pegawai = pg.id_pegawai
            INNER JOIN detail_pinjam dp ON p.id_peminjaman = dp.id_peminjaman
            INNER JOIN inventaris i ON dp.id_inventaris = i.id_inventaris
            LEFT JOIN jenis j ON i.id_jenis = j.id_jenis
            LEFT JOIN ruang r ON i.id_ruang = r.id_ruang
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (
                p.kode_peminjaman LIKE :s1
                OR pg.nama_pegawai LIKE :s2
                OR pg.nip LIKE :s3
                OR EXISTS (
                    SELECT 1
                    FROM detail_pinjam dp_search
                    INNER JOIN inventaris i_search ON dp_search.id_inventaris = i_search.id_inventaris
                    WHERE dp_search.id_peminjaman = p.id_peminjaman
                      AND (i_search.nama LIKE :s4 OR i_search.kode_inventaris LIKE :s5)
                )
            )";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[':s1'] = $searchTerm;
            $params[':s2'] = $searchTerm;
            $params[':s3'] = $searchTerm;
            $params[':s4'] = $searchTerm;
            $params[':s5'] = $searchTerm;
        }

        if (!empty($filters['status'])) {
            $sql .= " AND p.status_peminjaman = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['id_pegawai'])) {
            $sql .= " AND p.id_pegawai = :id_pegawai";
            $params[':id_pegawai'] = $filters['id_pegawai'];
        }

        if (!empty($filters['tgl_awal']) && !empty($filters['tgl_akhir'])) {
            $sql .= " AND p.tanggal_pinjam BETWEEN :tgl_awal AND :tgl_akhir";
            $params[':tgl_awal'] = $filters['tgl_awal'];
            $params[':tgl_akhir'] = $filters['tgl_akhir'];
        }

        $sql .= " ORDER BY p.id_peminjaman DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getAllGrouped(array $filters = []): array {
        $transactions = [];

        foreach ($this->getAll($filters) as $row) {
            $id = (int) $row['id_peminjaman'];
            if (!isset($transactions[$id])) {
                $transactions[$id] = $row;
                $transactions[$id]['items'] = [];
                $transactions[$id]['total_jumlah'] = 0;
            }

            $transactions[$id]['items'][] = [
                'id_detail_pinjam' => $row['id_detail_pinjam'],
                'id_inventaris' => $row['id_inventaris'],
                'nama_barang' => $row['nama_barang'],
                'kondisi' => $row['kondisi'],
                'kode_inventaris' => $row['kode_inventaris'],
                'nama_jenis' => $row['nama_jenis'],
                'nama_ruang' => $row['nama_ruang'],
                'jumlah' => (int) $row['jumlah'],
            ];
            $transactions[$id]['total_jumlah'] += (int) $row['jumlah'];
        }

        return array_values($transactions);
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                p.id_peminjaman,
                p.kode_peminjaman,
                p.tanggal_pinjam,
                p.tanggal_kembali,
                p.status_peminjaman,
                p.id_pegawai,
                pg.nama_pegawai,
                pg.nip,
                pg.alamat
            FROM peminjaman p
            INNER JOIN pegawai pg ON p.id_pegawai = pg.id_pegawai
            WHERE p.id_peminjaman = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $itemStmt = $this->db->prepare("
            SELECT
                dp.id_detail_pinjam,
                dp.id_inventaris,
                dp.jumlah,
                i.nama AS nama_barang,
                i.kode_inventaris,
                i.kondisi,
                j.nama_jenis,
                r.nama_ruang
            FROM detail_pinjam dp
            INNER JOIN inventaris i ON dp.id_inventaris = i.id_inventaris
            LEFT JOIN jenis j ON i.id_jenis = j.id_jenis
            LEFT JOIN ruang r ON i.id_ruang = r.id_ruang
            WHERE dp.id_peminjaman = :id
            ORDER BY dp.id_detail_pinjam ASC
        ");
        $itemStmt->execute([':id' => $id]);
        $row['items'] = $itemStmt->fetchAll();
        return $row;
    }

    // Transaksi tambah peminjaman
    public function createPeminjaman(int $id_pegawai, int $id_inventaris, int $jumlah, string $tanggal_pinjam, string $status = 'Dipinjam'): array {
        return $this->createPeminjamanMany(
            $id_pegawai,
            [['id_inventaris' => $id_inventaris, 'jumlah' => $jumlah]],
            $tanggal_pinjam,
            $status
        );
    }

    public function createPeminjamanMany(int $id_pegawai, array $items, string $tanggal_pinjam, string $status = 'Dipinjam'): array {
        $normalizedItems = [];
        foreach ($items as $item) {
            $idInventaris = (int) ($item['id_inventaris'] ?? 0);
            $jumlah = (int) ($item['jumlah'] ?? 0);
            if ($idInventaris <= 0 || $jumlah <= 0) {
                return ['status' => false, 'message' => 'Pilih sarana dan jumlah pinjam harus lebih dari 0 untuk setiap rincian.'];
            }
            $normalizedItems[$idInventaris] = ($normalizedItems[$idInventaris] ?? 0) + $jumlah;
        }

        if ($id_pegawai <= 0 || empty($normalizedItems)) {
            return ['status' => false, 'message' => 'Peminjam dan minimal satu sarana wajib dipilih.'];
        }
        ksort($normalizedItems);

        $kodePeminjaman = $this->generateKode();

        try {
            $this->db->beginTransaction();

            $stockStmt = $this->db->prepare("SELECT jumlah FROM inventaris WHERE id_inventaris = :id FOR UPDATE");
            foreach ($normalizedItems as $idInventaris => $jumlah) {
                $stockStmt->execute([':id' => $idInventaris]);
                $stok = $stockStmt->fetchColumn();
                if ($stok === false) {
                    $this->db->rollBack();
                    return ['status' => false, 'message' => 'Salah satu barang inventaris tidak ditemukan.'];
                }
                if ((int) $stok < $jumlah) {
                    $this->db->rollBack();
                    return ['status' => false, 'message' => 'Stok sarana tidak mencukupi. Sisa stok: ' . (int) $stok];
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO peminjaman (kode_peminjaman, tanggal_pinjam, status_peminjaman, id_pegawai)
                VALUES (:kode, :tgl, :status, :peg)
            ");
            $stmt->execute([
                ':kode'   => $kodePeminjaman,
                ':tgl'    => $tanggal_pinjam,
                ':status' => $status,
                ':peg'    => $id_pegawai
            ]);
            $idPeminjaman = (int) $this->db->lastInsertId();

            $dpStmt = $this->db->prepare("
                INSERT INTO detail_pinjam (id_peminjaman, id_inventaris, jumlah)
                VALUES (:pj, :inv, :jml)
            ");
            $upInv = $this->db->prepare("UPDATE inventaris SET jumlah = jumlah - :jml WHERE id_inventaris = :id");
            foreach ($normalizedItems as $idInventaris => $jumlah) {
                $dpStmt->execute([
                    ':pj' => $idPeminjaman,
                    ':inv' => $idInventaris,
                    ':jml' => $jumlah
                ]);
                if ($status === 'Dipinjam') {
                    $upInv->execute([':jml' => $jumlah, ':id' => $idInventaris]);
                }
            }

            $this->db->commit();
            return ['status' => true, 'message' => 'Peminjaman berhasil dicatat.', 'kode' => $kodePeminjaman];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['status' => false, 'message' => 'Kesalahan transaksi: ' . $e->getMessage()];
        }
    }

    public function resolvePeminjaman(int $idPeminjaman, string $decision): array {
        if (!in_array($decision, ['approve', 'reject'], true)) {
            return ['status' => false, 'message' => 'Keputusan peminjaman tidak valid.'];
        }

        try {
            $this->db->beginTransaction();

            $statusStmt = $this->db->prepare("SELECT status_peminjaman FROM peminjaman WHERE id_peminjaman = :id FOR UPDATE");
            $statusStmt->execute([':id' => $idPeminjaman]);
            $status = $statusStmt->fetchColumn();
            if ($status === false) {
                $this->db->rollBack();
                return ['status' => false, 'message' => 'Data peminjaman tidak ditemukan.'];
            }
            if ($status !== 'Menunggu Konfirmasi') {
                $this->db->rollBack();
                return ['status' => false, 'message' => 'Pengajuan ini sudah pernah diproses.'];
            }

            $itemsStmt = $this->db->prepare("
                SELECT id_inventaris, jumlah
                FROM detail_pinjam
                WHERE id_peminjaman = :id
                ORDER BY id_inventaris ASC
            ");
            $itemsStmt->execute([':id' => $idPeminjaman]);
            $items = $itemsStmt->fetchAll();
            if (empty($items)) {
                $this->db->rollBack();
                return ['status' => false, 'message' => 'Rincian sarana pada pengajuan ini tidak ditemukan.'];
            }

            if ($decision === 'approve') {
                $stockStmt = $this->db->prepare("SELECT jumlah FROM inventaris WHERE id_inventaris = :id FOR UPDATE");
                foreach ($items as $item) {
                    $stockStmt->execute([':id' => (int) $item['id_inventaris']]);
                    $stock = $stockStmt->fetchColumn();
                    if ($stock === false) {
                        $this->db->rollBack();
                        return ['status' => false, 'message' => 'Salah satu barang inventaris tidak ditemukan.'];
                    }
                    if ((int) $stock < (int) $item['jumlah']) {
                        $this->db->rollBack();
                        return ['status' => false, 'message' => 'Stok sarana tidak mencukupi saat ini. Sisa stok: ' . (int) $stock];
                    }
                }

                $updateStock = $this->db->prepare("UPDATE inventaris SET jumlah = jumlah - :jumlah WHERE id_inventaris = :id");
                foreach ($items as $item) {
                    $updateStock->execute([
                        ':jumlah' => (int) $item['jumlah'],
                        ':id' => (int) $item['id_inventaris']
                    ]);
                }
            }

            $newStatus = $decision === 'approve' ? 'Dipinjam' : 'Ditolak';
            $updateStatus = $this->db->prepare("
                UPDATE peminjaman
                SET status_peminjaman = :status
                WHERE id_peminjaman = :id
            ");
            $updateStatus->execute([':status' => $newStatus, ':id' => $idPeminjaman]);

            $this->db->commit();
            return [
                'status' => true,
                'message' => $decision === 'approve'
                    ? 'Peminjaman disetujui dan stok inventaris telah diperbarui.'
                    : 'Pengajuan peminjaman ditolak.'
            ];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['status' => false, 'message' => 'Kesalahan transaksi: ' . $e->getMessage()];
        }
    }

    // Transaksi pengembalian
    public function returnPeminjaman(int $idPeminjaman, string $tanggalKembali): array {
        try {
            $this->db->beginTransaction();

            $statusStmt = $this->db->prepare("SELECT status_peminjaman FROM peminjaman WHERE id_peminjaman = :id FOR UPDATE");
            $statusStmt->execute([':id' => $idPeminjaman]);
            $status = $statusStmt->fetchColumn();
            if ($status === false) {
                $this->db->rollBack();
                return ['status' => false, 'message' => 'Data peminjaman tidak ditemukan.'];
            }
            if ($status === 'Sudah Kembali') {
                $this->db->rollBack();
                return ['status' => false, 'message' => 'Barang sudah pernah dikembalikan sebelumnya.'];
            }
            if ($status !== 'Dipinjam') {
                $this->db->rollBack();
                return ['status' => false, 'message' => 'Hanya peminjaman yang disetujui yang dapat dikembalikan.'];
            }

            $itemsStmt = $this->db->prepare("SELECT id_inventaris, jumlah FROM detail_pinjam WHERE id_peminjaman = :id");
            $itemsStmt->execute([':id' => $idPeminjaman]);
            $items = $itemsStmt->fetchAll();
            if (empty($items)) {
                $this->db->rollBack();
                return ['status' => false, 'message' => 'Rincian sarana pada transaksi ini tidak ditemukan.'];
            }

            $upInv = $this->db->prepare("UPDATE inventaris SET jumlah = jumlah + :jml WHERE id_inventaris = :id");
            foreach ($items as $item) {
                $upInv->execute([
                    ':jml' => (int) $item['jumlah'],
                    ':id' => (int) $item['id_inventaris']
                ]);
            }

            $up = $this->db->prepare("
                UPDATE peminjaman 
                SET status_peminjaman = 'Sudah Kembali', tanggal_kembali = :tgl 
                WHERE id_peminjaman = :id
            ");
            $up->execute([':tgl' => $tanggalKembali, ':id' => $idPeminjaman]);

            $this->db->commit();
            return ['status' => true, 'message' => 'Semua sarana pada transaksi berhasil dikembalikan dan stok telah dipulihkan.'];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['status' => false, 'message' => 'Kesalahan: ' . $e->getMessage()];
        }
    }
}
