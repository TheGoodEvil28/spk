<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan ML - SPK Inventaris</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include 'views/sidebar.php'; ?>
    <div class="main-content">
        <h1 class="page-title">Laporan Machine Learning</h1>

        <div class="box-panel" style="margin-bottom: 25px;">
            <h3>Jalankan Analisis ML untuk Laporan Pengadaan</h3>
            <form action="index.php?action=generateMlReport" method="POST">
                <div class="spk-form-group">
                    <label for="id_matriks">Pilih Sesi Matriks</label>
                    <select id="id_matriks" name="id_matriks" class="form-control" required>
                        <?php foreach ($matriksSessions as $session): ?>
                            <option value="<?= htmlspecialchars($session['id_matriks']) ?>" <?= $session['id_matriks'] == ($selectedMatriks ?? 0) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($session['nama_matriks']) ?> (ID <?= htmlspecialchars($session['id_matriks']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-hitung" style="background:#007bff; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">Buat Laporan ML</button>
            </form>
        </div>

        <?php if (!empty($reportResult) && !isset($reportResult['success'])): ?>
            <div class="box-panel table-wrapper" style="margin-bottom: 25px;">
                <h3>Hasil Laporan ML</h3>
                <p><?= htmlspecialchars($reportResult['message'] ?? 'Tidak ada aksi.') ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($reportResult['output_link'])): ?>
            <div class="box-panel table-wrapper" style="margin-bottom: 25px;">
                <h3>Hasil Laporan ML</h3>
                <p><?= htmlspecialchars($reportResult['message']) ?></p>
                <p>File laporan: <a href="<?= htmlspecialchars($reportResult['output_link']) ?>" target="_blank">Download Laporan</a></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($reportResult['rows']) && is_array($reportResult['rows'])): ?>
            <div class="box-panel table-wrapper" style="margin-bottom: 25px;">
                <h3>Ringkasan Prioritas Pengadaan</h3>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Prioritas Pengadaan</th>
                                <th>Consensus Rank</th>
                                <th>Nilai V</th>
                                <th>Nilai Yi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportResult['rows'] as $row): ?>
                                <tr>
                                    <td style="text-align:left;"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><?= htmlspecialchars($row['procurement_priority']) ?></td>
                                    <td><?= htmlspecialchars((string) $row['consensus_rank']) ?></td>
                                    <td><?= htmlspecialchars(number_format($row['nilai_v'], 6)) ?></td>
                                    <td><?= htmlspecialchars(number_format($row['nilai_yi'], 6)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
</body>
</html>