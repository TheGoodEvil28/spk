<?php
require_once __DIR__ . '/../config/database.php';

class HasilModel {
    private $db;
    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    public function getRanking($id_matriks = 1) {
        $stmt = $this->db->prepare("SELECT nama_barang, skor, ranking, rekomendasi FROM hasil WHERE id_matriks = ? ORDER BY ranking");
        $stmt->execute([$id_matriks]);
        return $stmt->fetchAll();
    }

    public function getSummary() {
        $stmt = $this->db->query("SELECT daftar_rekomendasi FROM laporan ORDER BY id_laporan DESC LIMIT 1");
        $row = $stmt->fetch();
        return $row ? $row['daftar_rekomendasi'] : 'Belum ada ringkasan.';
    }

    public function getCrossMethodComparison() {
        // Sederhana: gunakan ranking yang sama untuk demo
        $rank = $this->getRanking();
        $out = [];
        foreach ($rank as $r) {
            $out[] = [
                'nama' => $r['nama_barang'],
                'rankWP' => $r['ranking'],
                'rankTOPSIS' => $r['ranking'],
                'rankMOORA' => $r['ranking']
            ];
        }
        return $out;
    }
}
?>