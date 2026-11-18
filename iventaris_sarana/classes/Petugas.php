<?php
require_once __DIR__ . '/../config/Database.php';

class Petugas {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(string $search = ''): array {
        $sql = "
            SELECT p.id_petugas, p.username, p.nama_petugas, p.id_level, p.id_pegawai,
                   l.nama_level, pg.nama_pegawai, pg.nip
            FROM petugas p
            INNER JOIN level l ON p.id_level = l.id_level
            LEFT JOIN pegawai pg ON p.id_pegawai = pg.id_pegawai
        ";
        if (!empty($search)) {
            $sql .= " WHERE p.username LIKE :s1 OR p.nama_petugas LIKE :s2 OR l.nama_level LIKE :s3 OR pg.nama_pegawai LIKE :s4";
            $sql .= " ORDER BY p.id_petugas ASC";
            $stmt = $this->db->prepare($sql);
            $searchTerm = "%$search%";
            $stmt->execute([':s1' => $searchTerm, ':s2' => $searchTerm, ':s3' => $searchTerm, ':s4' => $searchTerm]);
            return $stmt->fetchAll();
        }

        $sql .= " ORDER BY p.id_petugas ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getLevels(): array {
        return $this->db->query("
            SELECT * FROM level
            WHERE LOWER(nama_level) <> 'peminjam'
            ORDER BY id_level ASC
        ")->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT p.*, l.nama_level, pg.nama_pegawai 
            FROM petugas p
            INNER JOIN level l ON p.id_level = l.id_level
            LEFT JOIN pegawai pg ON p.id_pegawai = pg.id_pegawai
            WHERE p.id_petugas = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): array {
        // Cek username unik
        $chk = $this->db->prepare("SELECT COUNT(*) FROM petugas WHERE username = :u");
        $chk->execute([':u' => trim($data['username'])]);
        if ($chk->fetchColumn() > 0) {
            return ['status' => false, 'message' => 'Username sudah digunakan, silakan gunakan username lain.'];
        }

        $stmt = $this->db->prepare("
            INSERT INTO petugas (username, password, nama_petugas, id_level, id_pegawai)
            VALUES (:u, :p, :n, :l, :peg)
        ");
        $res = $stmt->execute([
            ':u'   => trim($data['username']),
            ':p'   => password_hash($data['password'], PASSWORD_DEFAULT),
            ':n'   => trim($data['nama_petugas']),
            ':l'   => (int) $data['id_level'],
            ':peg' => !empty($data['id_pegawai']) ? (int) $data['id_pegawai'] : null
        ]);

        return ['status' => $res, 'message' => $res ? 'Pengguna baru berhasil ditambahkan.' : 'Gagal menambahkan pengguna.'];
    }

    public function update(int $id, array $data): array {
        // Cek username jika diubah
        $chk = $this->db->prepare("SELECT COUNT(*) FROM petugas WHERE username = :u AND id_petugas != :id");
        $chk->execute([':u' => trim($data['username']), ':id' => $id]);
        if ($chk->fetchColumn() > 0) {
            return ['status' => false, 'message' => 'Username sudah digunakan oleh akun lain.'];
        }

        if (!empty($data['password'])) {
            $stmt = $this->db->prepare("
                UPDATE petugas 
                SET username = :u, password = :p, nama_petugas = :n, id_level = :l, id_pegawai = :peg
                WHERE id_petugas = :id
            ");
            $res = $stmt->execute([
                ':id'  => $id,
                ':u'   => trim($data['username']),
                ':p'   => password_hash($data['password'], PASSWORD_DEFAULT),
                ':n'   => trim($data['nama_petugas']),
                ':l'   => (int) $data['id_level'],
                ':peg' => !empty($data['id_pegawai']) ? (int) $data['id_pegawai'] : null
            ]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE petugas 
                SET username = :u, nama_petugas = :n, id_level = :l, id_pegawai = :peg
                WHERE id_petugas = :id
            ");
            $res = $stmt->execute([
                ':id'  => $id,
                ':u'   => trim($data['username']),
                ':n'   => trim($data['nama_petugas']),
                ':l'   => (int) $data['id_level'],
                ':peg' => !empty($data['id_pegawai']) ? (int) $data['id_pegawai'] : null
            ]);
        }

        return ['status' => $res, 'message' => $res ? 'Data pengguna berhasil diperbarui.' : 'Gagal memperbarui pengguna.'];
    }

    public function delete(int $id, int $currentUserId): array {
        if ($id === $currentUserId) {
            return ['status' => false, 'message' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.'];
        }

        $chk = $this->db->prepare("SELECT COUNT(*) FROM inventaris WHERE id_petugas = :id");
        $chk->execute([':id' => $id]);
        if ($chk->fetchColumn() > 0) {
            return ['status' => false, 'message' => 'Pengguna tidak dapat dihapus karena tercatat mendaftarkan sarana inventaris.'];
        }

        $stmt = $this->db->prepare("DELETE FROM petugas WHERE id_petugas = :id");
        $res = $stmt->execute([':id' => $id]);
        return ['status' => $res, 'message' => $res ? 'Pengguna berhasil dihapus.' : 'Gagal menghapus pengguna.'];
    }
}
