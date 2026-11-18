<?php
require_once __DIR__ . '/../config/Database.php';

class Pegawai {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    private function getTypeCondition(?string $type): string {
        return match ($type) {
            'siswa' => "(TRIM(COALESCE(kelas, '')) <> '' OR nip LIKE 'SISWA-%')",
            'pegawai' => "(TRIM(COALESCE(kelas, '')) = '' AND nip NOT LIKE 'SISWA-%')",
            default => '1 = 1'
        };
    }

    public function getAll(string $search = '', ?string $type = null): array {
        $typeCondition = $this->getTypeCondition($type);
        $sql = "
            SELECT p.*, COUNT(pj.id_peminjaman) AS total_pinjam
            FROM pegawai p
            LEFT JOIN peminjaman pj ON p.id_pegawai = pj.id_pegawai
            WHERE $typeCondition
        ";

        $params = [];
        if ($search !== '') {
            $sql .= " AND (p.nama_pegawai LIKE :s1 OR p.nip LIKE :s2 OR p.alamat LIKE :s3 OR p.kelas LIKE :s4)";
            $searchTerm = "%$search%";
            $params = [
                ':s1' => $searchTerm,
                ':s2' => $searchTerm,
                ':s3' => $searchTerm,
                ':s4' => $searchTerm
            ];
        }

        $sql .= "
            GROUP BY p.id_pegawai
            ORDER BY p.id_pegawai ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM pegawai WHERE id_pegawai = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO pegawai (nama_pegawai, nip, alamat, kelas)
            VALUES (:nama_pegawai, :nip, :alamat, :kelas)
        ");
        return $stmt->execute([
            ':nama_pegawai' => trim($data['nama_pegawai']),
            ':nip'          => trim($data['nip']),
            ':alamat'       => trim($data['alamat'] ?? ''),
            ':kelas'        => trim($data['kelas'] ?? '')
        ]);
    }

    public function update(int $id, array $data, ?string $type = null): bool {
        $typeCondition = $type === null ? '' : ' AND ' . $this->getTypeCondition($type);
        $stmt = $this->db->prepare("
            UPDATE pegawai 
            SET nama_pegawai = :nama_pegawai, nip = :nip, alamat = :alamat, kelas = :kelas
            WHERE id_pegawai = :id$typeCondition
        ");
        $params = [
            ':id'           => $id,
            ':nama_pegawai' => trim($data['nama_pegawai']),
            ':nip'          => trim($data['nip']),
            ':alamat'       => trim($data['alamat'] ?? ''),
            ':kelas'        => trim($data['kelas'] ?? '')
        ];
        if (!$stmt->execute($params)) {
            return false;
        }
        if ($stmt->rowCount() > 0) {
            return true;
        }

        $check = $this->db->prepare("
            SELECT COUNT(*) FROM pegawai
            WHERE id_pegawai = :id$typeCondition
        ");
        $check->execute([':id' => $id]);
        return (int) $check->fetchColumn() > 0;
    }

    public function delete(int $id, ?string $type = null): array {
        $typeCondition = $type === null ? '' : ' AND ' . $this->getTypeCondition($type);
        if ($type !== null) {
            $find = $this->db->prepare("
                SELECT COUNT(*) FROM pegawai p
                WHERE p.id_pegawai = :id$typeCondition
            ");
            $find->execute([':id' => $id]);
            if ((int) $find->fetchColumn() === 0) {
                return ['status' => false, 'message' => 'Data tidak ditemukan pada jenis ini.'];
            }
        }

        $chk = $this->db->prepare("SELECT COUNT(*) FROM peminjaman WHERE id_pegawai = :id");
        $chk->execute([':id' => $id]);
        if ($chk->fetchColumn() > 0) {
            return ['status' => false, 'message' => 'Tidak dapat menghapus data karena memiliki riwayat peminjaman sarana.'];
        }

        $stmt = $this->db->prepare("DELETE FROM pegawai WHERE id_pegawai = :id$typeCondition");
        $res = $stmt->execute([':id' => $id]);
        return ['status' => $res, 'message' => $res ? 'Data berhasil dihapus.' : 'Gagal menghapus data.'];
    }
}
