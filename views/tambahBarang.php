<?php if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; } ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang - SPK Inventaris</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-style.css?v=1">
</head>
<body>
<div class="dashboard-container">
    <?php include 'views/sidebar.php'; ?>
    <div class="main-content">
        <h1 class="page-title">Manajemen Master Barang > Tambah Barang</h1>
        <form action="index.php?action=simpanBarang" method="POST"> <!-- perbaiki action -->
            <div class="form-group"><label>Nama Barang</label><input type="text" name="nama_barang" class="form-control" required></div>
            <div class="form-group"><label>Spesifikasi</label><textarea name="spesifikasi" class="form-control" required></textarea></div>
            <div class="form-group"><label>Tanggal Pembelian</label><input type="date" name="tanggal_pembelian" class="form-control" required></div>
            <div class="form-group"><label>Usia Pakai (Bulan)</label><input type="number" name="usia_pakai_bulan" class="form-control" value="0" min="0" required></div>
            <div class="form-group"><label>Status Garansi</label>
                <select name="status_garansi" class="form-control" required>
                    <option value="Aktif">Aktif</option>
                    <option value="Hampir Habis">Hampir Habis</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>
            <div class="form-group"><label>Stok Tersedia</label><input type="number" name="stok_tersedia" class="form-control" required></div>
            <div class="form-group"><label>Stok Minimum</label><input type="number" name="stok_minimum" class="form-control" required></div>
            <div class="form-actions"><button type="submit" class="btn-simpan">Simpan</button></div>
        </form>
    </div>
</div>
</body>
</html>