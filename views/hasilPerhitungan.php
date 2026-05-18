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

      
        <!-- TABEL RANKING WP -->
        <div class="box-panel table-wrapper" style="margin-bottom: 25px;">
            <h3>Tabel Ranking (Weighted Product - WP)</h3>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Nilai S</th>
                            <th>Nilai V</th>
                            <th>Ranking</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rankingWP = $ranking;
                        usort($rankingWP, function($a, $b) {
                            return $a['ranking'] <=> $b['ranking'];
                        });
                        foreach ($rankingWP as $item): ?>
                            <tr>
                                <td style="text-align:left;"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                <td><?= number_format($item['nilai_s'], 6) ?></td>
                                <td><?= number_format($item['nilai_v'], 6) ?></td>
                                <td><?= htmlspecialchars((string) $item['ranking']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL CONSENSUS RANKING -->
        <div class="box-panel table-wrapper" style="margin-bottom: 25px;">
            <h3>Consensus Ranking (Berdasarkan Rekomendasi Terbanyak)</h3>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah Rekomendasi</th>
                            <th>Consensus Ranking</th>
                            <th>Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Calculate recommendations for each
                        $recCounts = [];
                        foreach ($ranking as $item) {
                            $count = 0;
                            if ($item['ranking'] <= 3) $count++;
                            if ($item['rank_moora'] <= 3) $count++;
                            if ($item['rank_saw'] <= 3) $count++;
                            if ($item['rank_topsis'] <= 3) $count++;
                            $recCounts[$item['nama_barang']] = $count;
                        }
                        foreach ($ranking as $item): ?>
                            <tr>
                                <td style="text-align:left;"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                <td><?= $recCounts[$item['nama_barang']] ?>/4</td>
                                <td><?= htmlspecialchars((string) $item['consensus_rank']) ?></td>
                                <td>
                                    <?php 
                                    $rank = $item['consensus_rank'];
                                    echo $rank === 1 ? 'Sangat Direkomendasikan' : ($rank <= 3 ? 'Direkomendasikan' : 'Perlu Pertimbangan');
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL RANKING MOORA -->
        <div class="box-panel table-wrapper" style="margin-bottom: 25px;">
            <h3>Tabel Ranking (MOORA)</h3>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Nilai Yi</th>
                            <th>Ranking</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rankingMOORA = $ranking;
                        usort($rankingMOORA, function($a, $b) {
                            return $a['rank_moora'] <=> $b['rank_moora'];
                        });
                        foreach ($rankingMOORA as $item): ?>
                            <tr>
                                <td style="text-align:left;"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                <td><?= number_format($item['nilai_yi'], 6) ?></td>
                                <td><?= htmlspecialchars((string) $item['rank_moora']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL RANKING SAW -->
        <div class="box-panel table-wrapper" style="margin-bottom: 25px;">
            <h3>Tabel Ranking (SAW)</h3>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Nilai Vi</th>
                            <th>Ranking</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rankingSAW = $ranking;
                        usort($rankingSAW, function($a, $b) {
                            return $a['rank_saw'] <=> $b['rank_saw'];
                        });
                        foreach ($rankingSAW as $item): ?>
                            <tr>
                                <td style="text-align:left;"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                <td><?= number_format($item['nilai_vi_saw'], 6) ?></td>
                                <td><?= htmlspecialchars((string) $item['rank_saw']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL RANKING TOPSIS -->
        <div class="box-panel table-wrapper" style="margin-bottom: 25px;">
            <h3>Tabel Ranking (TOPSIS)</h3>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Nilai Vi</th>
                            <th>Ranking</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rankingTOPSIS = $ranking;
                        usort($rankingTOPSIS, function($a, $b) {
                            return $a['rank_topsis'] <=> $b['rank_topsis'];
                        });
                        foreach ($rankingTOPSIS as $item): ?>
                            <tr>
                                <td style="text-align:left;"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                <td><?= number_format($item['nilai_vi_topsis'], 6) ?></td>
                                <td><?= htmlspecialchars((string) $item['rank_topsis']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- CROSS-METHOD COMPARISON -->
        <div class="box-panel table-wrapper" style="margin-bottom: 25px;">
            <h3>Cross-Method Comparison Table</h3>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Ranking WP</th>
                            <th>Ranking MOORA</th>
                            <th>Ranking SAW</th>
                            <th>Ranking TOPSIS</th>
                            <th>Consensus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($crossMethod as $item): ?>
                            <tr>
                                <td style="text-align:left;"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                <td><?= htmlspecialchars((string) $item['rankWP']) ?></td>
                                <td><?= htmlspecialchars((string) $item['rankMOORA']) ?></td>
                                <td><?= htmlspecialchars((string) $item['rankSAW']) ?></td>
                                <td><?= htmlspecialchars((string) $item['rankTOPSIS']) ?></td>
                                <td><?= htmlspecialchars((string) $item['consensus']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

      

    </div>
</div>

</body>
</html>
