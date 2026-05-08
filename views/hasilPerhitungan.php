<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perhitungan SPK - SPK Inventaris</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-style.css">
</head>
<body>

<div class="dashboard-container">

    <?php include 'views/sidebar.php'; ?>

    <div class="main-content">

        <h1 class="page-title">Hasil Perhitungan: Sesi Matriks Q1 2026</h1>

        <!-- AI SUMMARY BOX -->
        <div class="ai-summary-box">
            <h3>✨ AI Executive Summary (Sesi Q1 2026)</h3>
            <p><?= htmlspecialchars($summary) ?></p>
        </div>

        <!-- TABEL RANKING -->
        <div class="box-panel table-wrapper" style="margin-bottom: 25px;">
            <h3>Tabel Ranking (Metode Terpilih)</h3>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Skor (TOPSIS)</th>
                            <th>Ranking</th>
                            <th>Label Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ranking as $item): ?>
                            <tr>
                                <td style="text-align:left;"><?= htmlspecialchars($item['nama']) ?></td>
                                <td><?= htmlspecialchars((string) $item['skor']) ?></td>
                                <td><?= htmlspecialchars((string) $item['ranking']) ?></td>
                                <td><?= htmlspecialchars($item['label']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL CROSS-METHOD -->
        <div class="box-panel table-wrapper" style="margin-bottom: 25px;">
            <h3>Cross-Method Comparison Table</h3>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Ranking WP</th>
                            <th>Ranking TOPSIS</th>
                            <th>Ranking MOORA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($crossMethod as $item): ?>
                            <tr>
                                <td style="text-align:left;"><?= htmlspecialchars($item['nama']) ?></td>
                                <td><?= htmlspecialchars((string) $item['rankWP']) ?></td>
                                <td><?= htmlspecialchars((string) $item['rankTOPSIS']) ?></td>
                                <td><?= htmlspecialchars((string) $item['rankMOORA']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TOMBOL EKSPOR -->
        <div class="hasil-export-actions">
            <button class="btn-export btn-pdf">📄 Ekspor Laporan PDF</button>
            <button class="btn-export btn-excel">📊 Ekspor Laporan Excel</button>
        </div>

    </div>
</div>

</body>
</html>
