<?php 
    // Menangkap 'action' dari URL saat ini
    $current_action = isset($_GET['action']) ? $_GET['action'] : 'dashboard'; 
?>

<div class="sidebar">
    <img src="assets/images/logo-icn.png" alt="Logo Lab ICN" class="sidebar-logo">
    
    <ul class="sidebar-menu">
        
        <li class="<?php echo ($current_action == 'manajemenBarang' || $current_action == 'tambahBarang') ? 'active' : ''; ?>">
            <a href="index.php?action=manajemenBarang">Manajemen Barang</a>
        </li>
        
        <li class="<?php echo ($current_action == 'manajemenKriteria') ? 'active' : ''; ?>">
            <a href="index.php?action=manajemenKriteria">Manajemen Kriteria</a>
        </li>

        <li class="<?php echo ($current_action == 'perhitunganSpk') ? 'active' : ''; ?>">
            <a href="index.php?action=perhitunganSpk">Perhitungan SPK</a>
        </li>

        <li class="<?php echo ($current_action == 'hasilPerhitungan') ? 'active' : ''; ?>">
            <a href="index.php?action=hasilPerhitungan">Hasil Perhitungan</a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="index.php?action=logout" class="btn-logout">Log Out</a>
    </div>
</div>
