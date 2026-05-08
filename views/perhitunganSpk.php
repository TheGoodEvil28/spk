<?php 
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perhitungan SPK - SPK Inventaris</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-style.css">
    <style>
        .form-control-sm {
            width: 100%;
            padding: 6px 8px;
            font-size: 13px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn-add-row, .btn-tambah-matriks {
            margin-top: 10px;
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-add-row:hover, .btn-tambah-matriks:hover { background: #218838; }
        .btn-hapus-baris {
            background: #dc3545;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .custom-table input[readonly] { background-color: #e9ecef; cursor: not-allowed; }
        .pilihan-barang {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .pilihan-barang select {
            flex: 2;
            min-width: 200px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <?php include 'views/sidebar.php'; ?>
    <div class="main-content">
        <h1 class="page-title">Perhitungan Sistem Pendukung Keputusan</h1>

        <form action="index.php?action=prosesPerhitungan" method="POST" id="formPerhitungan">
            <div class="spk-form-container">
                <!-- NAMA SESI -->
                <div class="spk-form-group">
                    <label for="nama-sesi">Nama Sesi Matriks</label>
                    <input type="text" id="nama-sesi" name="nama_sesi" placeholder="Contoh : Evaluasi Q1 2026" required class="form-control">
                </div>

                <!-- INPUT MATRIKS -->
                <div class="spk-form-group">
                    <label>Pilih Barang yang Akan Dinilai</label>
                    <div class="pilihan-barang">
                        <select id="pilihBarang" class="form-control">
                            <option value="">-- Pilih Barang --</option>
                            <?php if (!empty($dataBarang)): ?>
                                <?php foreach ($dataBarang as $b): ?>
                                    <option value="<?= htmlspecialchars($b['id_barang']) ?>" data-nama="<?= htmlspecialchars($b['nama_barang']) ?>">
                                        <?= htmlspecialchars($b['id_barang']) ?> - <?= htmlspecialchars($b['nama_barang']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <button type="button" class="btn-tambah-matriks" id="tambahMatriks">+ Tambah ke Matriks</button>
                     
                    </div>
                    <div class="table-responsive">
                        <table class="custom-table" id="matriks-table">
                            <thead>
                                <tr>
                                    <th>ID Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Rasio Kelangkaan</th>
                                    <th>Usia Pakai</th>
                                    <th>Garansi</th>
                                    <th>Spesifikasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                <!-- Baris akan ditambahkan oleh JS -->
                            </tbody>
                        </table>
                    </div>
                    <small style="color: #666;">Klik "Tambah ke Matriks" untuk memasukkan barang pilihan. Isi nilai sesuai kebutuhan.</small>
                </div>

                <!-- METODE & SUBMIT -->
                <div class="spk-form-group">
                    <label for="metode">Metode Perhitungan</label>
                    <select id="metode" name="metode" required class="form-control">
                        <option value="">-- Pilih Metode --</option>
                        <option value="saw">Simple Additive Weighting (SAW)</option>
                        <option value="wp">Weighted Product (WP)</option>
                        <option value="topsis">TOPSIS</option>
                        <option value="moora">MOORA</option>
                    </select>
                    <div class="spk-action-container" style="margin-top:20px;">
                        <button type="submit" class="btn-hitung" style="background:#007bff; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">Mulai Perhitungan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Data barang dari database (untuk keperluan dropdown sudah di PHP, tapi kita perlu array untuk akses cepat)
    var barangList = <?php 
        $arr = [];
        if (!empty($dataBarang)) {
            foreach ($dataBarang as $b) {
                $arr[] = ['id' => $b['id_barang'], 'nama' => $b['nama_barang']];
            }
        }
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    ?>;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Tambah baris ke tabel matriks (untuk barang dari dropdown)
    function tambahBarisMatriks(idBarang, namaBarang) {
        const tbody = document.getElementById('table-body');
        const row = tbody.insertRow();
        row.insertCell(0).innerHTML = `<input type="text" name="id_barang[]" value="${escapeHtml(idBarang)}" readonly class="form-control-sm">`;
        row.insertCell(1).innerHTML = `<input type="text" name="nama_barang[]" value="${escapeHtml(namaBarang)}" readonly class="form-control-sm">`;
        row.insertCell(2).innerHTML = `<input type="number" step="0.01" name="nilai_kelangkaan[]" class="form-control-sm" placeholder="0" required>`;
        row.insertCell(3).innerHTML = `<input type="number" step="0.01" name="nilai_usia[]" class="form-control-sm" placeholder="0" required>`;
        row.insertCell(4).innerHTML = `<input type="number" step="0.01" name="nilai_garansi[]" class="form-control-sm" placeholder="0" required>`;
        row.insertCell(5).innerHTML = `<input type="number" step="0.01" name="nilai_spesifikasi[]" class="form-control-sm" placeholder="0" required>`;
        const btnHapus = document.createElement('button');
        btnHapus.textContent = 'Hapus';
        btnHapus.className = 'btn-hapus-baris';
        btnHapus.onclick = function() { row.remove(); };
        row.insertCell(6).appendChild(btnHapus);
    }

    // Tambah baris kosong untuk barang baru (manual)
    function tambahBarisManual() {
        const tbody = document.getElementById('table-body');
        const row = tbody.insertRow();
        row.insertCell(0).innerHTML = `<input type="text" name="id_barang[]" placeholder="ID (opsional)" class="form-control-sm">`;
        row.insertCell(1).innerHTML = `<input type="text" name="nama_barang[]" placeholder="Nama Barang" class="form-control-sm" required>`;
        row.insertCell(2).innerHTML = `<input type="number" step="0.01" name="nilai_kelangkaan[]" class="form-control-sm" placeholder="0" required>`;
        row.insertCell(3).innerHTML = `<input type="number" step="0.01" name="nilai_usia[]" class="form-control-sm" placeholder="0" required>`;
        row.insertCell(4).innerHTML = `<input type="number" step="0.01" name="nilai_garansi[]" class="form-control-sm" placeholder="0" required>`;
        row.insertCell(5).innerHTML = `<input type="number" step="0.01" name="nilai_spesifikasi[]" class="form-control-sm" placeholder="0" required>`;
        const btnHapus = document.createElement('button');
        btnHapus.textContent = 'Hapus';
        btnHapus.className = 'btn-hapus-baris';
        btnHapus.onclick = function() { row.remove(); };
        row.insertCell(6).appendChild(btnHapus);
    }

    // Event untuk tombol "Tambah ke Matriks"
    document.getElementById('tambahMatriks').addEventListener('click', function() {
        const select = document.getElementById('pilihBarang');
        const idBarang = select.value;
        const selectedOption = select.options[select.selectedIndex];
        const namaBarang = selectedOption.getAttribute('data-nama');
        if (!idBarang) {
            alert('Silakan pilih barang terlebih dahulu.');
            return;
        }
        // Optional: cek apakah sudah ada di tabel? (boleh dibiarkan double)
        tambahBarisMatriks(idBarang, namaBarang);
        // reset pilihan
        select.value = '';
    });

    // Event tombol tambah manual
    document.getElementById('btn-tambah-manual').addEventListener('click', function() {
        tambahBarisManual();
    });
</script>
</body>
</html>