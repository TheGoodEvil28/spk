<?php 
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang - SPK Inventaris</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-style.css?v=1">
</head>
<body>
<div class="dashboard-container">
    <?php include 'views/sidebar.php'; ?>
    <div class="main-content">
        <h1 class="page-title">Manajemen Master Barang > Edit Barang</h1>
        <form action="index.php?action=updateBarang" method="POST">
            <input type="hidden" name="id_barang" value="<?= $barang['id_barang'] ?>">
            
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
            </div>
            <div class="form-group">
                <label>Spesifikasi</label>
                <textarea name="spesifikasi" class="form-control" required><?= htmlspecialchars($barang['spesifikasi']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Tanggal Pembelian</label>
                <input type="date" name="tanggal_pembelian" class="form-control" value="<?= $barang['tgl_beli'] ?>" required>
            </div>
            <div class="form-group">
                <label>Stok Tersedia</label>
                <input type="number" name="stok_tersedia" class="form-control" value="<?= $barang['stok_tersedia'] ?>" required>
            </div>
            <div class="form-group">
                <label>Stok Minimum</label>
                <input type="number" name="stok_minimum" class="form-control" value="<?= $barang['stok_minimum'] ?>" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-simpan">Update</button>
                <a href="index.php?action=manajemenBarang" class="btn-batal" style="background:#6c757d; color:white; padding:8px 15px; border-radius:5px; text-decoration:none;">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>