<?php
require_once __DIR__ . '/../config/database.php';

class UserModel {
    private $db;
    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    public function register($nama, $email, $password) {
        // Cek email sudah ada?
        $stmt = $this->db->prepare("SELECT id_user FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['status' => false, 'message' => 'Email sudah terdaftar.'];
        }
        // Hash password
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (nama_lengkap, email, password) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$nama, $email, $hashed]);
        if ($result) {
            return ['status' => true, 'message' => 'Registrasi berhasil.'];
        } else {
            return ['status' => false, 'message' => 'Gagal registrasi.'];
        }
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT id_user, nama_lengkap, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_name'] = $user['nama_lengkap'];
            return ['status' => true, 'message' => 'Login sukses.'];
        } else {
            return ['status' => false, 'message' => 'Email atau password salah.'];
        }
    }
}
?>