<?php
require_once __DIR__ . '/../config/database.php';

class KriteriaModel {
    private $db;
    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM kriteria ORDER BY id_kriteria");
        return $stmt->fetchAll();
    }

    public function tambah($nama, $bobot, $tipe) {
        $sql = "INSERT INTO kriteria (nama, bobot, tipe) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nama, $bobot, $tipe]);
    }

    // Method baru untuk menghapus kriteria berdasarkan ID
    public function delete($id) {
        $sql = "DELETE FROM kriteria WHERE id_kriteria = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>