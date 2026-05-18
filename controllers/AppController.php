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
                header('Location: index.php?action=dashboard');
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
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? 'semua');

        $barangModel = new BarangModel();
        $barangs = $barangModel->getAllBarang($search, $status);
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

    public function prosesPerhitungan() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=perhitunganSpk');
            exit;
        }

        $nama_sesi = trim($_POST['nama_sesi'] ?? '');
        $id_barang = $_POST['id_barang'] ?? [];
        $nama_barang = $_POST['nama_barang'] ?? [];
        $nilai_kelangkaan = $_POST['nilai_kelangkaan'] ?? [];
        $nilai_usia = $_POST['nilai_usia'] ?? [];
        $nilai_garansi = $_POST['nilai_garansi'] ?? [];
        $nilai_spesifikasi = $_POST['nilai_spesifikasi'] ?? [];
        $stok_tersedia = $_POST['stok_tersedia'] ?? [];

        if ($nama_sesi === '' || count($id_barang) === 0) {
            $_SESSION['flash_message'] = 'Nama sesi dan setidaknya satu baris matriks harus diisi.';
            $_SESSION['flash_type'] = 'error';
            header('Location: index.php?action=perhitunganSpk');
            exit;
        }

        global $pdo;
        $kriteriaModel = new KriteriaModel();
        $kriteriaList = $kriteriaModel->getAll();
        $kriteriaByName = [];
        foreach ($kriteriaList as $k) {
            $kriteriaByName[$k['nama']] = $k;
        }

        $expected = [
            'Stok Tersedia',
            'Rasio Kelangkaan',
            'Usia Pakai',
            'Skor Garansi',
            'Kesesuaian Spesifikasi'
        ];

        foreach ($expected as $name) {
            if (!isset($kriteriaByName[$name])) {
                $_SESSION['flash_message'] = "Kriteria '$name' tidak ditemukan. Mohon tambahkan kriteria tersebut terlebih dahulu.";
                $_SESSION['flash_type'] = 'error';
                header('Location: index.php?action=manajemenKriteria');
                exit;
            }
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('INSERT INTO matriks_keputusan (nama_matriks, metode) VALUES (?, ?)');
            $stmt->execute([$nama_sesi, 'MULTI-METHOD']);
            $id_matriks = $pdo->lastInsertId();

            $detailStmt = $pdo->prepare('INSERT INTO matriks_keputusan_detail (id_matriks, id_barang, id_kriteria, nilai) VALUES (?, ?, ?, ?)');

            $matrixRows = [];
            for ($i = 0; $i < count($id_barang); $i++) {
                $barangId = trim($id_barang[$i]);
                $barangName = trim($nama_barang[$i]);
                if ($barangName === '') {
                    continue;
                }

                if ($barangId === '') {
                    $insertBarang = $pdo->prepare('INSERT INTO barang (nama_barang, stok_tersedia, stok_minimum, tgl_beli, status_garansi, spesifikasi) VALUES (?, ?, ?, NULL, ?, ?)');
                    $insertBarang->execute([$barangName, 0, 0, 'Tidak Aktif', '']);
                    $barangId = $pdo->lastInsertId();
                }

                $nilaiStok = isset($stok_tersedia[$i]) ? floatval($stok_tersedia[$i]) : 0;
                $nilaiKelangkaan = isset($nilai_kelangkaan[$i]) ? floatval($nilai_kelangkaan[$i]) : 0;
                $nilaiUsia = isset($nilai_usia[$i]) ? floatval($nilai_usia[$i]) : 0;
                $nilaiGaransi = isset($nilai_garansi[$i]) ? floatval($nilai_garansi[$i]) : 0;
                $nilaiSpesifikasi = isset($nilai_spesifikasi[$i]) ? floatval($nilai_spesifikasi[$i]) : 0;

                $detailStmt->execute([$id_matriks, $barangId, $kriteriaByName['Stok Tersedia']['id_kriteria'], $nilaiStok]);
                $detailStmt->execute([$id_matriks, $barangId, $kriteriaByName['Rasio Kelangkaan']['id_kriteria'], $nilaiKelangkaan]);
                $detailStmt->execute([$id_matriks, $barangId, $kriteriaByName['Usia Pakai']['id_kriteria'], $nilaiUsia]);
                $detailStmt->execute([$id_matriks, $barangId, $kriteriaByName['Skor Garansi']['id_kriteria'], $nilaiGaransi]);
                $detailStmt->execute([$id_matriks, $barangId, $kriteriaByName['Kesesuaian Spesifikasi']['id_kriteria'], $nilaiSpesifikasi]);

                $matrixRows[] = [
                    'id_barang' => $barangId,
                    'nama_barang' => $barangName,
                    'Stok Tersedia' => $nilaiStok,
                    'Rasio Kelangkaan' => $nilaiKelangkaan,
                    'Usia Pakai' => $nilaiUsia,
                    'Skor Garansi' => $nilaiGaransi,
                    'Kesesuaian Spesifikasi' => $nilaiSpesifikasi
                ];
            }

            $scoreRows = [];
            // Normalize weights for WP
            $totalBobot = array_sum(array_column($kriteriaList, 'bobot'));
            $normalizedWeights = [];
            foreach ($kriteriaList as $k) {
                $normalizedWeights[$k['id_kriteria']] = $k['bobot'] / $totalBobot;
            }

            // Compute Vector S for each alternative
            $vectorS = [];
            foreach ($matrixRows as $row) {
                $S = 1.0;
                foreach ($expected as $name) {
                    $kriteria = $kriteriaByName[$name];
                    $value = $row[$name];
                    $w = $normalizedWeights[$kriteria['id_kriteria']];
                    if ($value <= 0) {
                        $value = 0.0001; // Avoid log(0) or division by zero
                    }
                    if ($kriteria['tipe'] === 'Benefit') {
                        $S *= pow($value, $w);
                    } else { // Cost
                        $S *= pow($value, -$w);
                    }
                }
                $vectorS[] = [
                    'id_barang' => $row['id_barang'],
                    'nama_barang' => $row['nama_barang'],
                    'nilai_s' => $S
                ];
            }

            // Compute total sum of S
            $totalS = array_sum(array_column($vectorS, 'nilai_s'));

            // Compute Vector V and prepare scoreRows
            foreach ($vectorS as $item) {
                $V = $totalS > 0 ? $item['nilai_s'] / $totalS : 0;
                $scoreRows[] = [
                    'id_barang' => $item['id_barang'],
                    'nama_barang' => $item['nama_barang'],
                    'nilai_s' => $item['nilai_s'],
                    'nilai_v' => $V,
                    'skor' => $V // Use V as skor for ranking
                ];
            }

            // Sort by V descending
            usort($scoreRows, function($a, $b) {
                return $b['nilai_v'] <=> $a['nilai_v'];
            });

            $hasilStmt = $pdo->prepare('INSERT INTO hasil (id_matriks, id_barang, nama_barang, skor, nilai_s, nilai_v, ranking, rekomendasi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $recommendations = [];
            foreach ($scoreRows as $index => $item) {
                $ranking = $index + 1;
                $label = $ranking === 1 ? 'Sangat Direkomendasikan' : ($ranking <= 3 ? 'Direkomendasikan' : 'Perlu Pertimbangan');
                $hasilStmt->execute([$id_matriks, $item['id_barang'], $item['nama_barang'], $item['skor'], $item['nilai_s'], $item['nilai_v'], $ranking, $label]);
                if ($ranking <= 3) {
                    $recommendations[] = $item['nama_barang'];
                }
            }

            // Calculate rankings for other methods
            $this->calculateOtherMethods($id_matriks, $matrixRows, $kriteriaList, $expected);

            // Calculate consensus ranking based on number of recommendations (rank <=3)
            $this->calculateConsensusRanking($id_matriks);

            $laporanStmt = $pdo->prepare('INSERT INTO laporan (id_matriks, tgl_laporan, metode_digunakan, daftar_rekomendasi) VALUES (?, ?, ?, ?)');
            $laporanStmt->execute([$id_matriks, date('Y-m-d'), 'ALL METHODS', implode(', ', $recommendations)]);

            $pdo->commit();

            $_SESSION['flash_message'] = 'Perhitungan berhasil disimpan dan matriks berhasil dibuat.';
            $_SESSION['flash_type'] = 'success';
            header('Location: index.php?action=hasilPerhitungan&id_matriks=' . $id_matriks);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_message'] = 'Terjadi kesalahan saat menyimpan perhitungan: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'error';
            header('Location: index.php?action=perhitunganSpk');
            exit;
        }
    }

    private function calculateOtherMethods($id_matriks, $matrixRows, $kriteriaList, $expected) {
        global $pdo;

        // Prepare kriteria by name
        $kriteriaByName = [];
        foreach ($kriteriaList as $k) {
            $kriteriaByName[$k['nama']] = $k;
        }

        // MOORA
        $mooraScores = [];
        foreach ($matrixRows as $row) {
            $benefitSum = 0;
            $costSum = 0;
            foreach ($expected as $name) {
                $kriteria = $kriteriaByName[$name];
                $value = $row[$name];
                // Vector normalization
                $sumSq = 0;
                foreach ($matrixRows as $r) {
                    $sumSq += pow($r[$name], 2);
                }
                $norm = $sumSq > 0 ? $value / sqrt($sumSq) : 0;
                $weighted = $kriteria['bobot'] * $norm;
                if ($kriteria['tipe'] === 'Benefit') {
                    $benefitSum += $weighted;
                } else {
                    $costSum += $weighted;
                }
            }
            $yi = $benefitSum - $costSum;
            $mooraScores[] = [
                'id_barang' => $row['id_barang'],
                'yi' => $yi
            ];
        }
        usort($mooraScores, function($a, $b) {
            return $b['yi'] <=> $a['yi'];
        });
        foreach ($mooraScores as $index => $item) {
            $rank = $index + 1;
            $stmt = $pdo->prepare('UPDATE hasil SET nilai_yi = ?, rank_moora = ? WHERE id_matriks = ? AND id_barang = ?');
            $stmt->execute([$item['yi'], $rank, $id_matriks, $item['id_barang']]);
        }

        // SAW
        $totalBobot = array_sum(array_column($kriteriaList, 'bobot'));
        $sawScores = [];
        foreach ($matrixRows as $row) {
            $vi = 0;
            foreach ($expected as $name) {
                $kriteria = $kriteriaByName[$name];
                $value = $row[$name];
                $wj = $totalBobot > 0 ? $kriteria['bobot'] / $totalBobot : 0;
                if ($kriteria['tipe'] === 'Benefit') {
                    $maxVal = max(array_column($matrixRows, $name));
                    $rij = $maxVal > 0 ? $value / $maxVal : 0;
                } else {
                    $minVal = min(array_column($matrixRows, $name));
                    $rij = $value > 0 ? $minVal / $value : 0;
                }
                $vi += $wj * $rij;
            }
            $sawScores[] = [
                'id_barang' => $row['id_barang'],
                'vi' => $vi
            ];
        }
        usort($sawScores, function($a, $b) {
            return $b['vi'] <=> $a['vi'];
        });
        foreach ($sawScores as $index => $item) {
            $rank = $index + 1;
            $stmt = $pdo->prepare('UPDATE hasil SET nilai_vi_saw = ?, rank_saw = ? WHERE id_matriks = ? AND id_barang = ?');
            $stmt->execute([$item['vi'], $rank, $id_matriks, $item['id_barang']]);
        }

        // TOPSIS
        $topsisScores = [];
        // Normalization
        $normMatrix = [];
        foreach ($expected as $name) {
            $sumSq = 0;
            foreach ($matrixRows as $r) {
                $sumSq += pow($r[$name], 2);
            }
            $sqrtSum = sqrt($sumSq);
            foreach ($matrixRows as $idx => $r) {
                $normMatrix[$idx][$name] = $sqrtSum > 0 ? $r[$name] / $sqrtSum : 0;
            }
        }
        // Weighted normalization
        $weightedMatrix = [];
        foreach ($matrixRows as $idx => $row) {
            foreach ($expected as $name) {
                $kriteria = $kriteriaByName[$name];
                $wj = $kriteria['bobot'] / $totalBobot;
                $weightedMatrix[$idx][$name] = $wj * $normMatrix[$idx][$name];
            }
        }
        // Ideal solutions
        $idealPositive = [];
        $idealNegative = [];
        foreach ($expected as $name) {
            $kriteria = $kriteriaByName[$name];
            $values = array_column($weightedMatrix, $name);
            if ($kriteria['tipe'] === 'Benefit') {
                $idealPositive[$name] = max($values);
                $idealNegative[$name] = min($values);
            } else {
                $idealPositive[$name] = min($values);
                $idealNegative[$name] = max($values);
            }
        }
        // Distances
        foreach ($matrixRows as $idx => $row) {
            $dPlus = 0;
            $dMinus = 0;
            foreach ($expected as $name) {
                $dPlus += pow($weightedMatrix[$idx][$name] - $idealPositive[$name], 2);
                $dMinus += pow($weightedMatrix[$idx][$name] - $idealNegative[$name], 2);
            }
            $dPlus = sqrt($dPlus);
            $dMinus = sqrt($dMinus);
            $vi = $dMinus / ($dPlus + $dMinus);
            $topsisScores[] = [
                'id_barang' => $row['id_barang'],
                'vi' => $vi
            ];
        }
        usort($topsisScores, function($a, $b) {
            return $b['vi'] <=> $a['vi'];
        });
        foreach ($topsisScores as $index => $item) {
            $rank = $index + 1;
            $stmt = $pdo->prepare('UPDATE hasil SET nilai_vi_topsis = ?, rank_topsis = ? WHERE id_matriks = ? AND id_barang = ?');
            $stmt->execute([$item['vi'], $rank, $id_matriks, $item['id_barang']]);
        }
    }

    private function calculateConsensusRanking($id_matriks) {
        global $pdo;

        // Get all results for this matriks
        $stmt = $pdo->prepare('SELECT id_barang, ranking, rank_moora, rank_saw, rank_topsis FROM hasil WHERE id_matriks = ?');
        $stmt->execute([$id_matriks]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$results) return; // No results, skip

        $consensus = [];
        foreach ($results as $row) {
            $recommendations = 0;
            if ($row['ranking'] <= 3) $recommendations++; // WP
            if ($row['rank_moora'] <= 3) $recommendations++;
            if ($row['rank_saw'] <= 3) $recommendations++;
            if ($row['rank_topsis'] <= 3) $recommendations++;
            $consensus[] = [
                'id_barang' => $row['id_barang'],
                'recommendations' => $recommendations
            ];
        }

        // Sort by recommendations descending, then by WP ranking ascending
        usort($consensus, function($a, $b) use ($results) {
            if ($a['recommendations'] == $b['recommendations']) {
                // If tie, use WP ranking
                $aWp = 0; $bWp = 0;
                foreach ($results as $r) {
                    if ($r['id_barang'] == $a['id_barang']) $aWp = $r['ranking'];
                    if ($r['id_barang'] == $b['id_barang']) $bWp = $r['ranking'];
                }
                return $aWp <=> $bWp;
            }
            return $b['recommendations'] <=> $a['recommendations'];
        });

        foreach ($consensus as $index => $item) {
            $rank = $index + 1;
            $stmt = $pdo->prepare('UPDATE hasil SET consensus_rank = ? WHERE id_matriks = ? AND id_barang = ?');
            $stmt->execute([$rank, $id_matriks, $item['id_barang']]);
        }
    }

    public function hasilPerhitungan() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        $id_matriks = isset($_GET['id_matriks']) ? (int)$_GET['id_matriks'] : 1;
        $hasilModel = new HasilModel();
        $summary = $hasilModel->getSummary($id_matriks);
        $ranking = $hasilModel->getRanking($id_matriks);
        $crossMethod = $hasilModel->getCrossMethodComparison($id_matriks);
        require_once __DIR__ . '/../views/hasilPerhitungan.php';
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        $barangModel = new BarangModel();
        $hasilModel = new HasilModel();
        $kriteriaModel = new KriteriaModel();

        // Basic stats
        $allBarang = $barangModel->getAllBarang();
        $totalItems = count($allBarang);
        $kritikal = count($barangModel->getAllBarang('', 'kritis'));
        $hampir = count($barangModel->getAllBarang('', 'hampir'));
        $aman = count($barangModel->getAllBarang('', 'aman'));

        // Average age in months
        $totalAge = 0;
        $ageCount = 0;
        foreach ($allBarang as $b) {
            if (!empty($b['usia_pakai_bulan'])) {
                $totalAge += (int)$b['usia_pakai_bulan'];
                $ageCount++;
            }
        }
        $avgAge = $ageCount ? round($totalAge / $ageCount, 1) : 0;

        // Warranty status counts
        $warranty = ['Aktif' => 0, 'Tidak Aktif' => 0, 'Hampir Habis' => 0];
        foreach ($allBarang as $b) {
            $s = $b['status_garansi'] ?? 'Tidak Aktif';
            if (!isset($warranty[$s])) $warranty[$s] = 0;
            $warranty[$s]++;
        }

        // Top items by consensus rank (from latest matriks)
        $id_matriks = isset($_GET['id_matriks']) ? (int)$_GET['id_matriks'] : 1;
        $ranking = $hasilModel->getRanking($id_matriks);
        usort($ranking, function($a, $b) { return ($a['consensus_rank'] ?? 999) <=> ($b['consensus_rank'] ?? 999); });
        $top5 = array_slice($ranking, 0, 5);

        require_once __DIR__ . '/../views/dashboard.php';
    }

    public function mlReport() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        $hasilModel = new HasilModel();
        $matriksSessions = $hasilModel->getMatriksSessions();
        $selectedMatriks = isset($_GET['id_matriks']) ? (int)$_GET['id_matriks'] : ($matriksSessions[0]['id_matriks'] ?? 1);
        $reportResult = [];
        require_once __DIR__ . '/../views/mlReport.php';
    }

    public function generateMlReport() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=mlReport');
            exit;
        }

        $id_matriks = isset($_POST['id_matriks']) ? (int)$_POST['id_matriks'] : 1;
        $hasilModel = new HasilModel();
        $matriksSessions = $hasilModel->getMatriksSessions();
        $selectedMatriks = $id_matriks;
        $reportResult = ['success' => false, 'message' => 'Gagal membuat laporan ML.'];

        $reportDir = __DIR__ . '/../ml/reports';
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0755, true);
        }
        $timestamp = date('YmdHis');
        $outputPath = $reportDir . '/report_' . $timestamp . '.xlsx';
        $jsonPath = $reportDir . '/report_' . $timestamp . '.json';

        $python = '/opt/venv/bin/python';
        $script = __DIR__ . '/../ml/generate_report.py';
        $cmd = escapeshellcmd($python) . ' ' . escapeshellarg($script) .
               ' --matriks-id ' . escapeshellarg((string) $id_matriks) .
               ' --output ' . escapeshellarg($outputPath) .
               ' --json-output ' . escapeshellarg($jsonPath);

        exec($cmd . ' 2>&1', $output, $status);

        if ($status === 0 && file_exists($jsonPath)) {
            $jsonContent = file_get_contents($jsonPath);
            $jsonResult = json_decode($jsonContent, true);
            if ($jsonResult && isset($jsonResult['success']) && $jsonResult['success']) {
                $reportResult = $jsonResult;
                $reportResult['output_link'] = 'ml/reports/' . basename($outputPath);
                // Save a summary laporan entry for this ML report
                $recommendationNames = [];
                if (!empty($jsonResult['rows']) && is_array($jsonResult['rows'])) {
                    usort($jsonResult['rows'], function ($a, $b) {
                        return ($a['consensus_rank'] ?? 0) <=> ($b['consensus_rank'] ?? 0);
                    });
                    foreach (array_slice($jsonResult['rows'], 0, 5) as $row) {
                        $recommendationNames[] = $row['nama_barang'];
                    }
                }
                $daftarRekomendasi = $recommendationNames ? implode(', ', $recommendationNames) : 'Tidak ada rekomendasi.';
                $hasilModel->saveLaporan($id_matriks, 'ML Cluster', $daftarRekomendasi);
            } else {
                $reportResult['message'] = 'Laporan ML dibuat tetapi hasil JSON tidak valid.';
                $reportResult['details'] = $output;
            }
        } else {
            $reportResult['message'] = 'Eksekusi script ML gagal.';
            $reportResult['details'] = implode("\n", $output);
        }

        require_once __DIR__ . '/../views/mlReport.php';
    }
}
?>