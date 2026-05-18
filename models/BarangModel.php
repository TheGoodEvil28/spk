<?php
require_once __DIR__ . '/../config/database.php';

class BarangModel {
    private $db;
    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    public function getAllBarang($search = '', $status = 'semua') {
        $sql = "SELECT * FROM barang";
        $conditions = [];
        $params = [];

        if ($search !== '') {
            $conditions[] = "(nama_barang LIKE :search OR spesifikasi LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($status === 'aman') {
            $conditions[] = "stok_tersedia >= stok_minimum * 1.2";
        } elseif ($status === 'hampir') {
            $conditions[] = "stok_tersedia >= stok_minimum AND stok_tersedia < stok_minimum * 1.2";
        } elseif ($status === 'kritis') {
            $conditions[] = "stok_tersedia < stok_minimum";
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY id_barang ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Ambil satu barang berdasarkan ID
    public function getBarangById($id) {
        $stmt = $this->db->prepare("SELECT * FROM barang WHERE id_barang = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function tambahBarang($data) {
        $sql = "INSERT INTO barang (nama_barang, stok_tersedia, stok_minimum, tgl_beli, usia_pakai_bulan, status_garansi, spesifikasi)
                VALUES (:nama, :stok, :stok_min, :tgl_beli, :usia, :garansi, :spesifikasi)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nama' => $data['nama_barang'],
            ':stok' => $data['stok_tersedia'],
            ':stok_min' => $data['stok_minimum'],
            ':tgl_beli' => $data['tanggal_pembelian'],
            ':usia' => $data['usia_pakai_bulan'] ?? 0,
            ':garansi' => $data['status_garansi'] ?? 'Tidak Aktif',
            ':spesifikasi' => $data['spesifikasi']
        ]);
    }

    // Update data barang
    public function updateBarang($id, $data) {
        $sql = "UPDATE barang SET 
                    nama_barang = :nama,
                    stok_tersedia = :stok,
                    stok_minimum = :stok_min,
                    tgl_beli = :tgl_beli,
                    usia_pakai_bulan = :usia,
                    status_garansi = :garansi,
                    spesifikasi = :spesifikasi
                WHERE id_barang = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nama' => $data['nama_barang'],
            ':stok' => $data['stok_tersedia'],
            ':stok_min' => $data['stok_minimum'],
            ':tgl_beli' => $data['tanggal_pembelian'],
            ':usia' => $data['usia_pakai_bulan'] ?? 0,
            ':garansi' => $data['status_garansi'] ?? 'Tidak Aktif',
            ':spesifikasi' => $data['spesifikasi']
        ]);
    }

    // Hapus barang
    public function deleteBarang($id) {
        $stmt = $this->db->prepare("DELETE FROM barang WHERE id_barang = ?");
        return $stmt->execute([$id]);
    }
}
?>