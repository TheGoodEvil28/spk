<?php 
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Kriteria</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include 'views/sidebar.php'; ?>
    <div class="main-content">
        <div class="header-action"><h1 class="page-title">Manajemen Kriteria</h1></div>

        <!-- Tampilkan pesan flash jika ada -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] == 'success' ? 'success' : 'danger' ?>" style="padding:10px; margin-bottom:20px; border-radius:5px; <?= $_SESSION['flash_type'] == 'success' ? 'background:#d4edda; color:#155724;' : 'background:#f8d7da; color:#721c24;' ?>">
                <?= htmlspecialchars($_SESSION['flash_message']) ?>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

        <?php 
        $totalBobot = array_sum(array_column($kriterias, 'bobot'));
        $bobotValid = (abs($totalBobot - 1.0) < 0.01);
        ?>
        <div class="box-panel total-bobot-box">
            <div style="display:flex; justify-content:space-between;">
                <div><h4>Total Bobot</h4><h2 style="color:<?= $bobotValid ? 'green' : 'red' ?>;"><?= number_format($totalBobot, 2) ?> (<?= $totalBobot*100 ?>%)</h2></div>
                <div class="total-status"><?= $bobotValid ? '✅ Total bobot valid' : '⚠️ Total bobot harus 1.00 (100%)' ?></div>
            </div>
        </div>

        <div style="display:flex; gap:20px; margin-top:20px;">
            <div class="box-panel table-wrapper" style="flex:2;">
                <h3>Daftar Kriteria</h3>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kriteria</th>
                            <th>Bobot</th>
                            <th>Tipe</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach ($kriterias as $k): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($k['nama']) ?></td>
                            <td><?= $k['bobot'] ?></td>
                            <td><span class="badge <?= $k['tipe']=='Benefit' ? 'benefit' : 'cost' ?>"><?= $k['tipe'] ?></span></td>
                            <td>
                                <a href="index.php?action=hapusKriteria&id=<?= $k['id_kriteria'] ?>" 
                                   class="btn-hapus" 
                                   onclick="return confirm('Yakin ingin menghapus kriteria <?= htmlspecialchars($k['nama']) ?>? Data yang terkait akan terhapus juga.')"
                                   style="background:#dc3545; color:white; padding:5px 10px; border-radius:4px; text-decoration:none; font-size:13px;">
                                   Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="margin-top:15px; background:#e0f2fe; padding:10px; border-radius:6px;">Pastikan total bobot = 1.0 (100%) untuk perhitungan yang valid</div>
            </div>

            <div class="box-panel" style="flex:1;">
                <h3>Tambah Kriteria</h3>
                <form action="index.php?action=simpanKriteria" method="POST">
                    <div class="form-group"><label>Nama Kriteria</label><input type="text" name="nama" class="form-control" required></div>
                    <div class="form-group"><label>Bobot</label><input type="number" step="0.01" name="bobot" class="form-control" placeholder="0.20" required><small style="color:#666;">Nilai 0.0 - 1.0</small></div>
                    <div class="form-group"><label>Tipe</label><br><input type="radio" name="tipe" value="Benefit" checked> Benefit<br><input type="radio" name="tipe" value="Cost"> Cost</div>
                    <div class="form-actions"><button type="submit" class="btn-simpan">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Optional: tambahkan CSS kecil jika diperlukan -->
<style>
.btn-hapus:hover {
    background:#c82333 !important;
    text-decoration:none;
}
</style>
</body>
</html>