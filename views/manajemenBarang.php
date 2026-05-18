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
    <title>Manajemen Barang - SPK Inventaris</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-style.css?v=2">
</head>
<body>
<div class="dashboard-container">
    <?php include 'views/sidebar.php'; ?>
    <div class="main-content">
        <div class="header-action">
            <h1 class="page-title">Manajemen Master Barang</h1>
            <a href="index.php?action=tambahBarang" class="btn-tambah">+ Tambah Barang</a>
        </div>

        <form method="get" class="filter-form">
            <input type="hidden" name="action" value="manajemenBarang">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search">Cari Barang</label>
                    <input type="text" id="search" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Nama atau spesifikasi...">
                </div>
                <div class="filter-group">
                    <label for="status">Status Stok</label>
                    <select id="status" name="status">
                        <option value="semua" <?= ($status ?? 'semua') === 'semua' ? 'selected' : '' ?>>Semua</option>
                        <option value="aman" <?= ($status ?? '') === 'aman' ? 'selected' : '' ?>>Stok Aman</option>
                        <option value="hampir" <?= ($status ?? '') === 'hampir' ? 'selected' : '' ?>>Hampir Habis</option>
                        <option value="kritis" <?= ($status ?? '') === 'kritis' ? 'selected' : '' ?>>Kritis</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-tambah" style="margin-right:10px;">Terapkan Filter</button>
                    <a href="index.php?action=manajemenBarang" class="btn-tambah" style="background:#6c757d;">Reset</a>
                </div>
            </div>
        </form>

        <!-- Tampilkan pesan flash -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] == 'success' ? 'success' : 'danger' ?>" style="padding:10px; margin-bottom:20px; border-radius:5px; <?= $_SESSION['flash_type'] == 'success' ? 'background:#d4edda; color:#155724;' : 'background:#f8d7da; color:#721c24;' ?>">
                <?= htmlspecialchars($_SESSION['flash_message']) ?>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

        <!-- Ringkasan Stok -->
        <?php
        $total = count($barangs);
        $aman = $hampir = $kritis = 0;
        foreach ($barangs as $b) {
            if ($b['stok_tersedia'] >= $b['stok_minimum'] * 1.2) $aman++;
            elseif ($b['stok_tersedia'] >= $b['stok_minimum']) $hampir++;
            else $kritis++;
        }
        ?>
        <div class="summary-grid">
            <div class="summary-card"><div class="card-color-box bg-blue">📦</div><div class="card-info"><h4>Total Barang</h4><h2><?= $total ?></h2><p>Semua item inventaris</p></div></div>
            <div class="summary-card"><div class="card-color-box bg-green">✔</div><div class="card-info"><h4>Stok Aman</h4><h2><?= $aman ?></h2><p>Stok di atas minimum</p></div></div>
            <div class="summary-card"><div class="card-color-box bg-yellow">⚠</div><div class="card-info"><h4>Stok Hampir Habis</h4><h2><?= $hampir ?></h2><p>Stok mendekati minimum</p></div></div>
            <div class="summary-card"><div class="card-color-box bg-red">✖</div><div class="card-info"><h4>Stok Kritis</h4><h2><?= $kritis ?></h2><p>Stok di bawah minimum</p></div></div>
        </div>

        <!-- Tabel Barang -->
        <div class="box-panel table-wrapper">
            <h3>Data Barang</h3>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Barang</th>
                            <th>Stok Tersedia</th>
                            <th>Kebutuhan Minimum</th>
                            <th>Tgl Beli</th>
                            <th>Status Garansi</th>
                            <th>Spesifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($barangs as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['id_barang']) ?></td>
                            <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                            <td><?= $b['stok_tersedia'] ?></td>
                            <td><?= $b['stok_minimum'] ?></td>
                            <td><?= $b['tgl_beli'] ?></td>
                            <td><?= $b['status_garansi'] ?></td>
                            <td><?= htmlspecialchars(substr($b['spesifikasi'], 0, 50)) ?>...</td>
                            <td>
                                <a href="index.php?action=editBarang&id=<?= $b['id_barang'] ?>" class="btn-edit" style="background:#007bff; color:white; padding:5px 10px; border-radius:4px; text-decoration:none; font-size:13px; display:inline-block; margin-right:5px;">Edit</a>
                                <a href="index.php?action=hapusBarang&id=<?= $b['id_barang'] ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus barang <?= htmlspecialchars($b['nama_barang']) ?>?')" style="background:#dc3545; color:white; padding:5px 10px; border-radius:4px; text-decoration:none; font-size:13px; display:inline-block;">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.filter-form {
    margin-bottom: 20px;
}
.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    align-items: flex-end;
}
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.filter-group input,
.filter-group select {
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #fff;
    width: 240px;
}
.filter-actions {
    display: flex;
    gap: 8px;
}
.btn-edit:hover { background:#0069d9 !important; }
.btn-hapus:hover { background:#c82333 !important; }
</style>
</body>
</html>