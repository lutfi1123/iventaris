<?php
require_once __DIR__ . '/../config/Database.php';

class Ruang {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(string $search = ''): array {
        if (!empty($search)) {
            $stmt = $this->db->prepare("
                SELECT r.*, COUNT(i.id_inventaris) AS total_barang
                FROM ruang r
                LEFT JOIN inventaris i ON r.id_ruang = i.id_ruang
                WHERE r.nama_ruang LIKE :s1 OR r.kode_ruang LIKE :s2 OR r.keterangan LIKE :s3
                GROUP BY r.id_ruang
                ORDER BY r.id_ruang ASC
            ");
            $searchTerm = "%$search%";
            $stmt->execute([':s1' => $searchTerm, ':s2' => $searchTerm, ':s3' => $searchTerm]);
            return $stmt->fetchAll();
        }

        $stmt = $this->db->query("
            SELECT r.*, COUNT(i.id_inventaris) AS total_barang
            FROM ruang r
            LEFT JOIN inventaris i ON r.id_ruang = i.id_ruang
            GROUP BY r.id_ruang
            ORDER BY r.id_ruang ASC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM ruang WHERE id_ruang = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO ruang (nama_ruang, kode_ruang, keterangan)
            VALUES (:nama_ruang, :kode_ruang, :keterangan)
        ");
        return $stmt->execute([
            ':nama_ruang' => trim($data['nama_ruang']),
            ':kode_ruang' => strtoupper(trim($data['kode_ruang'])),
            ':keterangan' => trim($data['keterangan'] ?? '')
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE ruang 
            SET nama_ruang = :nama_ruang, kode_ruang = :kode_ruang, keterangan = :keterangan
            WHERE id_ruang = :id
        ");
        return $stmt->execute([
            ':id'         => $id,
            ':nama_ruang' => trim($data['nama_ruang']),
            ':kode_ruang' => strtoupper(trim($data['kode_ruang'])),
            ':keterangan' => trim($data['keterangan'] ?? '')
        ]);
    }

    public function delete(int $id): array {
        $chk = $this->db->prepare("SELECT COUNT(*) FROM inventaris WHERE id_ruang = :id");
        $chk->execute([':id' => $id]);
        if ($chk->fetchColumn() > 0) {
            return ['status' => false, 'message' => 'Tidak dapat menghapus ruang karena masih terdapat data inventaris di ruangan ini.'];
        }

        $stmt = $this->db->prepare("DELETE FROM ruang WHERE id_ruang = :id");
        $res = $stmt->execute([':id' => $id]);
        return ['status' => $res, 'message' => $res ? 'Ruang berhasil dihapus.' : 'Gagal menghapus ruang.'];
    }
}
