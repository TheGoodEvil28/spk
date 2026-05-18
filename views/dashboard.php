<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SPK Inventaris</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard-style.css">
    <link rel="stylesheet" href="assets/css/dashboard-page.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<div class="main-content dp">
<div class="dashboard-container">

    <?php include 'views/sidebar.php'; ?>

    <div class="main-content">

        <h1 class="page-title">Dashboard Inventaris</h1>

        <div class="box-panel ai-summary-box">
            <h3>Selamat datang di Dashboard SPK Inventaris</h3>

            <p>
                Dashboard ini menampilkan ringkasan layanan inventaris kami,
                status stok, dan rekomendasi prioritas pengadaan berdasarkan
                metode SPK dan analisis ML. Gunakan menu di samping untuk
                mengelola barang, kriteria, dan menjalankan perhitungan.
            </p>
        </div>

        <!-- CARDS -->

        <div class="cards-row">

            <div class="info-card">
                <h4>Total Item</h4>
                <p class="big-number">
                    <?= htmlspecialchars($totalItems) ?>
                </p>
            </div>

            <div class="info-card">
                <h4>Stok Kritis</h4>
                <p class="big-number">
                    <?= htmlspecialchars($kritikal) ?>
                </p>
            </div>

            <div class="info-card">
                <h4>Rata-rata Usia Pakai (bulan)</h4>
                <p class="big-number">
                    <?= htmlspecialchars($avgAge) ?>
                </p>
            </div>

            <div class="info-card">
                <h4>Sessions Matriks</h4>

                <?php $sessions = (new HasilModel())->getMatriksSessions(); ?>

                <p class="big-number">
                    <?= count($sessions) ?>
                </p>
            </div>

        </div>

        <!-- PIE + DONUT -->

        <div class="charts-row">

            <div class="chart-panel">
                <h3>Status Stok</h3>
                <div class="chart-body">
                    <canvas id="stockPie"></canvas>
                </div>
            </div>

            <div class="chart-panel">
                <h3>Status Garansi</h3>
                <div class="chart-body">
                    <canvas id="warrantyDonut"></canvas>
                </div>
            </div>

        </div>

        <!-- BAR -->

        <div class="charts-row">

            <div class="chart-panel wide">
                <h3>Top 5 Barang - Berdasarkan Consensus Rank</h3>
                <div class="chart-body">
                    <canvas id="topBar"></canvas>
                </div>
            </div>

        </div>

    </div>

</div>

<script>

    /* PIE CHART */

    const stockData = {
        labels: ['Aman', 'Hampir', 'Kritis'],
        datasets: [{
            data: [
                <?= (int)$aman ?>,
                <?= (int)$hampir ?>,
                <?= (int)$kritikal ?>
            ],
            backgroundColor: [
                '#4CAF50',
                '#FFB300',
                '#E53935'
            ],
            borderWidth:0
        }]
    };

    new Chart(document.getElementById('stockPie'), {

        type:'pie',

        data:stockData,

        options:{
            responsive:true,
            maintainAspectRatio:false,

            plugins:{
                legend:{
                    position:'bottom'
                }
            }
        }

    });

    /* DONUT CHART */

    const warrantyData = {

        labels: <?= json_encode(array_keys($warranty)) ?>,

        datasets:[{
            data: <?= json_encode(array_values($warranty)) ?>,

            backgroundColor:[
                '#2E7D32',
                '#9E9E9E',
                '#FF7043'
            ],

            borderWidth:0
        }]
    };

    new Chart(document.getElementById('warrantyDonut'), {

        type:'doughnut',

        data:warrantyData,

        options:{
            responsive:true,
            maintainAspectRatio:false,

            plugins:{
                legend:{
                    position:'bottom'
                }
            }
        }

    });

    /* BAR CHART */

    const topLabels =
        <?= json_encode(array_map(function($r){
            return $r['nama_barang'];
        }, $top5)) ?>;

    const topValues =
        <?= json_encode(array_map(function($r){
            return (float)($r['nilai_v'] ?? $r['skor'] ?? 0);
        }, $top5)) ?>;

    new Chart(document.getElementById('topBar'), {

        type:'bar',

        data:{
            labels:topLabels,

            datasets:[{
                label:'Nilai V / Skor',
                data:topValues,
                backgroundColor:'#1976D2',
                borderRadius:8
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false,
            indexAxis:'y',

            plugins:{
                legend:{
                    display:false
                }
            },

            scales:{
                x:{
                    beginAtZero:true,
                    grid:{
                        color:'rgba(0,0,0,0.05)'
                    }
                },

                y:{
                    grid:{
                        display:false
                    }
                }
            }
        }

    });

</script>

</body>
</html>