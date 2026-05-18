<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'controllers/AppController.php';
$app = new AppController();
$action = $_GET['action'] ?? 'login_form';

switch ($action) {
    case 'login_form': $app->loginForm(); break;
    case 'login_process': $app->loginProcess(); break;
    case 'register': $app->register(); break;
    case 'register_process': $app->registerProcess(); break;
    case 'logout': $app->logout(); break;
    case 'manajemenBarang': $app->manajemenBarang(); break;
    case 'dashboard': $app->dashboard(); break;
    case 'tambahBarang': $app->tambahBarang(); break;
    case 'editBarang':      $app->editBarang(); break;
    case 'updateBarang':    $app->updateBarang(); break;
    case 'hapusBarang':     $app->hapusBarang(); break;
    case 'simpanBarang': $app->simpanBarang(); break;
    case 'manajemenKriteria': $app->manajemenKriteria(); break;
    case 'simpanKriteria': $app->simpanKriteria(); break;
    case 'perhitunganSpk': $app->perhitunganSpk(); break;
    case 'prosesPerhitungan': $app->prosesPerhitungan(); break;
    case 'hasilPerhitungan': $app->hasilPerhitungan(); break;
    case 'mlReport': $app->mlReport(); break;
    case 'generateMlReport': $app->generateMlReport(); break;
    case 'hapusKriteria': $app->hapusKriteria(); break; 
    default: $app->loginForm(); break;
}
?>