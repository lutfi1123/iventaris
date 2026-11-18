<?php
require_once __DIR__ . '/../config/Database.php';

class Jenis {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(string $search = ''): array {
        if (!empty($search)) {
            $stmt = $this->db->prepare("
                SELECT j.*, COUNT(i.id_inventaris) AS total_barang
                FROM jenis j
                LEFT JOIN inventaris i ON j.id_jenis = i.id_jenis
                WHERE j.nama_jenis LIKE :s1 OR j.kode_jenis LIKE :s2 OR j.keterangan LIKE :s3
                GROUP BY j.id_jenis
                ORDER BY j.id_jenis ASC
            ");
            $searchTerm = "%$search%";
            $stmt->execute([':s1' => $searchTerm, ':s2' => $searchTerm, ':s3' => $searchTerm]);
            return $stmt->fetchAll();
        }

        $stmt = $this->db->query("
            SELECT j.*, COUNT(i.id_inventaris) AS total_barang
            FROM jenis j
            LEFT JOIN inventaris i ON j.id_jenis = i.id_jenis
            GROUP BY j.id_jenis
            ORDER BY j.id_jenis ASC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM jenis WHERE id_jenis = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO jenis (nama_jenis, kode_jenis, keterangan)
            VALUES (:nama_jenis, :kode_jenis, :keterangan)
        ");
        return $stmt->execute([
            ':nama_jenis' => trim($data['nama_jenis']),
            ':kode_jenis' => strtoupper(trim($data['kode_jenis'])),
            ':keterangan' => trim($data['keterangan'] ?? '')
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE jenis 
            SET nama_jenis = :nama_jenis, kode_jenis = :kode_jenis, keterangan = :keterangan
            WHERE id_jenis = :id
        ");
        return $stmt->execute([
            ':id'         => $id,
            ':nama_jenis' => trim($data['nama_jenis']),
            ':kode_jenis' => strtoupper(trim($data['kode_jenis'])),
            ':keterangan' => trim($data['keterangan'] ?? '')
        ]);
    }

    public function delete(int $id): array {
        // Cek apakah jenis masih digunakan pada inventaris
        $chk = $this->db->prepare("SELECT COUNT(*) FROM inventaris WHERE id_jenis = :id");
        $chk->execute([':id' => $id]);
        if ($chk->fetchColumn() > 0) {
            return ['status' => false, 'message' => 'Tidak dapat menghapus jenis karena masih terkait dengan data inventaris.'];
        }

        $stmt = $this->db->prepare("DELETE FROM jenis WHERE id_jenis = :id");
        $res = $stmt->execute([':id' => $id]);
        return ['status' => $res, 'message' => $res ? 'Jenis berhasil dihapus.' : 'Gagal menghapus jenis.'];
    }
}
