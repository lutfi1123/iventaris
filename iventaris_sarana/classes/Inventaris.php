<?php
require_once __DIR__ . '/../config/Database.php';

class Inventaris {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(string $search = '', ?int $id_jenis = null, ?int $id_ruang = null): array {
        $sql = "
            SELECT i.*, j.nama_jenis, j.kode_jenis, r.nama_ruang, r.kode_ruang, p.nama_petugas
            FROM inventaris i
            INNER JOIN jenis j ON i.id_jenis = j.id_jenis
            INNER JOIN ruang r ON i.id_ruang = r.id_ruang
            INNER JOIN petugas p ON i.id_petugas = p.id_petugas
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (i.nama LIKE :s1 OR i.kode_inventaris LIKE :s2 OR i.kondisi LIKE :s3 OR j.nama_jenis LIKE :s4 OR r.nama_ruang LIKE :s5)";
            $searchTerm = "%$search%";
            $params[':s1'] = $searchTerm;
            $params[':s2'] = $searchTerm;
            $params[':s3'] = $searchTerm;
            $params[':s4'] = $searchTerm;
            $params[':s5'] = $searchTerm;
        }

        if (!empty($id_jenis)) {
            $sql .= " AND i.id_jenis = :j";
            $params[':j'] = $id_jenis;
        }

        if (!empty($id_ruang)) {
            $sql .= " AND i.id_ruang = :r";
            $params[':r'] = $id_ruang;
        }

        $sql .= " ORDER BY i.id_inventaris DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT i.*, j.nama_jenis, r.nama_ruang, p.nama_petugas
            FROM inventaris i
            INNER JOIN jenis j ON i.id_jenis = j.id_jenis
            INNER JOIN ruang r ON i.id_ruang = r.id_ruang
            INNER JOIN petugas p ON i.id_petugas = p.id_petugas
            WHERE i.id_inventaris = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function generateKode(): string {
        $year = date('Y');
        $stmt = $this->db->query("SELECT MAX(id_inventaris) AS max_id FROM inventaris");
        $nextId = (int) $stmt->fetchColumn() + 1;
        return sprintf("INV-%s-%04d", $year, $nextId);
    }

    public function create(array $data, int $id_petugas): array {
        $kode = !empty($data['kode_inventaris']) ? trim($data['kode_inventaris']) : $this->generateKode();

        // Cek kode unik
        $chk = $this->db->prepare("SELECT COUNT(*) FROM inventaris WHERE kode_inventaris = :k");
        $chk->execute([':k' => $kode]);
        if ($chk->fetchColumn() > 0) {
            return ['status' => false, 'message' => "Kode inventaris '$kode' sudah terdaftar."];
        }

        $stmt = $this->db->prepare("
            INSERT INTO inventaris (nama, kondisi, keterangan, jumlah, id_jenis, tanggal_register, id_ruang, kode_inventaris, id_petugas)
            VALUES (:nama, :kondisi, :ket, :jumlah, :id_jenis, :tgl, :id_ruang, :kode, :petugas)
        ");

        $res = $stmt->execute([
            ':nama'     => trim($data['nama']),
            ':kondisi'  => $data['kondisi'] ?? 'Baik',
            ':ket'      => trim($data['keterangan'] ?? ''),
            ':jumlah'   => (int) $data['jumlah'],
            ':id_jenis' => (int) $data['id_jenis'],
            ':tgl'      => !empty($data['tanggal_register']) ? $data['tanggal_register'] : date('Y-m-d'),
            ':id_ruang' => (int) $data['id_ruang'],
            ':kode'     => $kode,
            ':petugas'  => $id_petugas
        ]);

        return ['status' => $res, 'message' => $res ? 'Data sarana inventaris berhasil disimpan.' : 'Gagal menyimpan sarana inventaris.'];
    }

    public function update(int $id, array $data): array {
        $kode = trim($data['kode_inventaris']);

        // Cek kode unik
        $chk = $this->db->prepare("SELECT COUNT(*) FROM inventaris WHERE kode_inventaris = :k AND id_inventaris != :id");
        $chk->execute([':k' => $kode, ':id' => $id]);
        if ($chk->fetchColumn() > 0) {
            return ['status' => false, 'message' => "Kode inventaris '$kode' sudah digunakan oleh barang lain."];
        }

        $stmt = $this->db->prepare("
            UPDATE inventaris
            SET nama = :nama, kondisi = :kondisi, keterangan = :ket, jumlah = :jumlah,
                id_jenis = :id_jenis, tanggal_register = :tgl, id_ruang = :id_ruang, kode_inventaris = :kode
            WHERE id_inventaris = :id
        ");

        $res = $stmt->execute([
            ':id'       => $id,
            ':nama'     => trim($data['nama']),
            ':kondisi'  => $data['kondisi'] ?? 'Baik',
            ':ket'      => trim($data['keterangan'] ?? ''),
            ':jumlah'   => (int) $data['jumlah'],
            ':id_jenis' => (int) $data['id_jenis'],
            ':tgl'      => !empty($data['tanggal_register']) ? $data['tanggal_register'] : date('Y-m-d'),
            ':id_ruang' => (int) $data['id_ruang'],
            ':kode'     => $kode
        ]);

        return ['status' => $res, 'message' => $res ? 'Data sarana inventaris berhasil diperbarui.' : 'Gagal memperbarui inventaris.'];
    }

    public function delete(int $id): array {
        $chk = $this->db->prepare("SELECT COUNT(*) FROM detail_pinjam WHERE id_inventaris = :id");
        $chk->execute([':id' => $id]);
        if ($chk->fetchColumn() > 0) {
            return ['status' => false, 'message' => 'Barang inventaris tidak dapat dihapus karena tercatat dalam transaksi peminjaman.'];
        }

        $stmt = $this->db->prepare("DELETE FROM inventaris WHERE id_inventaris = :id");
        $res = $stmt->execute([':id' => $id]);
        return ['status' => $res, 'message' => $res ? 'Barang inventaris berhasil dihapus.' : 'Gagal menghapus inventaris.'];
    }

    public function getAvailableForBorrow(): array {
        $stmt = $this->db->query("
            SELECT i.*, j.nama_jenis, r.nama_ruang
            FROM inventaris i
            INNER JOIN jenis j ON i.id_jenis = j.id_jenis
            INNER JOIN ruang r ON i.id_ruang = r.id_ruang
            WHERE i.jumlah > 0
            ORDER BY i.nama ASC
        ");
        return $stmt->fetchAll();
    }
}
