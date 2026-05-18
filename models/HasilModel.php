<?php
require_once __DIR__ . '/../config/database.php';

class HasilModel {
    private $db;
    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    public function getRanking($id_matriks = 1) {
        $stmt = $this->db->prepare("SELECT nama_barang, skor, nilai_s, nilai_v, nilai_yi, nilai_vi_saw, nilai_vi_topsis, rank_moora, rank_saw, rank_topsis, consensus_rank, ranking, rekomendasi FROM hasil WHERE id_matriks = ? ORDER BY consensus_rank");
        $stmt->execute([$id_matriks]);
        return $stmt->fetchAll();
    }

    public function getSummary($id_matriks = null) {
        if ($id_matriks) {
            $stmt = $this->db->prepare("SELECT daftar_rekomendasi FROM laporan WHERE id_matriks = ? ORDER BY id_laporan DESC LIMIT 1");
            $stmt->execute([$id_matriks]);
        } else {
            $stmt = $this->db->query("SELECT daftar_rekomendasi FROM laporan ORDER BY id_laporan DESC LIMIT 1");
        }
        $row = $stmt->fetch();
        return $row ? $row['daftar_rekomendasi'] : 'Belum ada ringkasan.';
    }

    public function getCrossMethodComparison($id_matriks = 1) {
        $stmt = $this->db->prepare("SELECT nama_barang, ranking AS rankWP, rank_moora AS rankMOORA, rank_saw AS rankSAW, rank_topsis AS rankTOPSIS, consensus_rank AS consensus FROM hasil WHERE id_matriks = ? ORDER BY consensus_rank");
        $stmt->execute([$id_matriks]);
        return $stmt->fetchAll();
    }

    public function getMatriksSessions() {
        $stmt = $this->db->query("SELECT id_matriks, nama_matriks, metode FROM matriks_keputusan ORDER BY id_matriks DESC");
        return $stmt->fetchAll();
    }

    public function saveLaporan($id_matriks, $metode, $daftar_rekomendasi) {
        $stmt = $this->db->prepare("INSERT INTO laporan (id_matriks, tgl_laporan, metode_digunakan, daftar_rekomendasi) VALUES (?, CURDATE(), ?, ?)");
        $stmt->execute([$id_matriks, $metode, $daftar_rekomendasi]);
    }
}
?>