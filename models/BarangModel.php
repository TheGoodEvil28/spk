<?php
require_once __DIR__ . '/../config/database.php';

class BarangModel {
    private $db;
    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

   public function getAllBarang() {
    // Sebelumnya: ORDER BY id_barang DESC
    $stmt = $this->db->query("SELECT * FROM barang ORDER BY id_barang ASC");
    return $stmt->fetchAll();
}

    // Ambil satu barang berdasarkan ID
    public function getBarangById($id) {
        $stmt = $this->db->prepare("SELECT * FROM barang WHERE id_barang = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function tambahBarang($data) {
        $sql = "INSERT INTO barang (nama_barang, stok_tersedia, stok_minimum, tgl_beli, status_garansi, spesifikasi)
                VALUES (:nama, :stok, :stok_min, :tgl_beli, :garansi, :spesifikasi)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nama' => $data['nama_barang'],
            ':stok' => $data['stok_tersedia'],
            ':stok_min' => $data['stok_minimum'],
            ':tgl_beli' => $data['tanggal_pembelian'],
            ':garansi' => 'Aktif',  // bisa dihitung nanti
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
                    spesifikasi = :spesifikasi
                WHERE id_barang = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nama' => $data['nama_barang'],
            ':stok' => $data['stok_tersedia'],
            ':stok_min' => $data['stok_minimum'],
            ':tgl_beli' => $data['tanggal_pembelian'],
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