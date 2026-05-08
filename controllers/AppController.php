<?php
require_once __DIR__ . '/../models/BarangModel.php';
require_once __DIR__ . '/../models/KriteriaModel.php';
require_once __DIR__ . '/../models/HasilModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class AppController
{
    // ========== AUTENTIKASI ==========
    public function loginForm() {
        require_once __DIR__ . '/../views/login.php';
    }

    public function loginProcess() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $userModel = new UserModel();
            $result = $userModel->login($email, $password);
            if ($result['status']) {
                header('Location: index.php?action=manajemenBarang');
                exit;
            } else {
                $error_message = $result['message'];
                require_once __DIR__ . '/../views/login.php';
            }
        }
    }

    public function register() {
        require_once __DIR__ . '/../views/daftar.php';
    }

    public function registerProcess() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $_POST['nama'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if ($password !== $password_confirm) {
                $error_message = 'Konfirmasi password tidak cocok.';
                require_once __DIR__ . '/../views/daftar.php';
                return;
            }
            if (strlen($password) < 8) {
                $error_message = 'Password minimal 8 karakter.';
                require_once __DIR__ . '/../views/daftar.php';
                return;
            }

            $userModel = new UserModel();
            $result = $userModel->register($nama, $email, $password);
            if ($result['status']) {
                echo "<script>alert('{$result['message']} Silakan login.'); window.location.href='index.php?action=login_form';</script>";
            } else {
                $error_message = $result['message'];
                require_once __DIR__ . '/../views/daftar.php';
            }
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    }

    // ========== MANAJEMEN BARANG ==========
    public function manajemenBarang() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        $barangModel = new BarangModel();
        $barangs = $barangModel->getAllBarang();
        require_once __DIR__ . '/../views/manajemenBarang.php';
    }

    public function tambahBarang() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        require_once __DIR__ . '/../views/tambahBarang.php';
    }

    public function simpanBarang() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $barangModel = new BarangModel();
            $result = $barangModel->tambahBarang($_POST);
            if ($result) {
                $_SESSION['flash_message'] = 'Barang berhasil disimpan.';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Gagal menyimpan barang.';
                $_SESSION['flash_type'] = 'error';
            }
            header('Location: index.php?action=manajemenBarang');
            exit;
        }
    }

    // Edit Barang - menampilkan form edit
    public function editBarang() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $_SESSION['flash_message'] = 'ID barang tidak valid.';
            $_SESSION['flash_type'] = 'error';
            header('Location: index.php?action=manajemenBarang');
            exit;
        }
        $barangModel = new BarangModel();
        $barang = $barangModel->getBarangById($id);
        if (!$barang) {
            $_SESSION['flash_message'] = 'Barang tidak ditemukan.';
            $_SESSION['flash_type'] = 'error';
            header('Location: index.php?action=manajemenBarang');
            exit;
        }
        require_once __DIR__ . '/../views/editBarang.php';
    }

    // Update Barang - proses update data
    public function updateBarang() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id_barang']) ? (int)$_POST['id_barang'] : 0;
            if ($id <= 0) {
                $_SESSION['flash_message'] = 'ID barang tidak valid.';
                $_SESSION['flash_type'] = 'error';
                header('Location: index.php?action=manajemenBarang');
                exit;
            }
            $barangModel = new BarangModel();
            $result = $barangModel->updateBarang($id, $_POST);
            if ($result) {
                $_SESSION['flash_message'] = 'Barang berhasil diupdate.';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Gagal mengupdate barang.';
                $_SESSION['flash_type'] = 'error';
            }
            header('Location: index.php?action=manajemenBarang');
            exit;
        }
    }

    // Hapus Barang
    public function hapusBarang() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $barangModel = new BarangModel();
            $result = $barangModel->deleteBarang($id);
            if ($result) {
                $_SESSION['flash_message'] = 'Barang berhasil dihapus.';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Gagal menghapus barang. Mungkin data terkait dengan matriks keputusan.';
                $_SESSION['flash_type'] = 'error';
            }
        } else {
            $_SESSION['flash_message'] = 'ID barang tidak valid.';
            $_SESSION['flash_type'] = 'error';
        }
        header('Location: index.php?action=manajemenBarang');
        exit;
    }

    // ========== MANAJEMEN KRITERIA ==========
    public function manajemenKriteria() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        $kriteriaModel = new KriteriaModel();
        $kriterias = $kriteriaModel->getAll();
        require_once __DIR__ . '/../views/manajemenKriteria.php';
    }

    public function simpanKriteria() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new KriteriaModel();
            $result = $model->tambah($_POST['nama'], $_POST['bobot'], $_POST['tipe']);
            if ($result) {
                $_SESSION['flash_message'] = 'Kriteria berhasil disimpan.';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Gagal menyimpan kriteria.';
                $_SESSION['flash_type'] = 'error';
            }
            header('Location: index.php?action=manajemenKriteria');
            exit;
        }
    }

    public function hapusKriteria() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $kriteriaModel = new KriteriaModel();
            $result = $kriteriaModel->delete($id);
            if ($result) {
                $_SESSION['flash_message'] = 'Kriteria berhasil dihapus.';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Gagal menghapus kriteria. Mungkin data masih digunakan di matriks.';
                $_SESSION['flash_type'] = 'error';
            }
        } else {
            $_SESSION['flash_message'] = 'ID kriteria tidak valid.';
            $_SESSION['flash_type'] = 'error';
        }
        header('Location: index.php?action=manajemenKriteria');
        exit;
    }

    // ========== SPK ==========
    public function perhitunganSpk() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        $barangModel = new BarangModel();
        $dataBarang = $barangModel->getAllBarang();
        require_once __DIR__ . '/../views/perhitunganSpk.php';
    }

    public function hasilPerhitungan() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        $hasilModel = new HasilModel();
        $summary = $hasilModel->getSummary();
        $ranking = $hasilModel->getRanking();
        $crossMethod = $hasilModel->getCrossMethodComparison();
        require_once __DIR__ . '/../views/hasilPerhitungan.php';
    }
}
?>