<!-- views/daftar.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Sistem Manajemen Inventaris Lab ICN</title>
    
    <!-- Load Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- PANGGIL FILE CSS EKSTERNAL (Pastikan path folder benar) -->
    <link rel="stylesheet" href="assets/css/auth-style.css">
</head>
<body>
    <div class="container">
        
        <!-- BAGIAN KIRI (Visual & Branding) -->
        <div class="left-panel">
            <div class="left-panel-content">
                <div class="main-title">
                    <h1>SISTEM<br>MANAJEMEN<br>INVENTARIS</h1>
                    <p>Lab ICN</p>
                </div>
            </div>
        </div>

        <!-- BAGIAN KANAN (Form Pendaftaran) -->
        <div class="right-panel">
            <div class="form-wrapper">
                <img src="assets/images/logo-icn.png" alt="Logo Lab ICN" class="logo-icn">
                
                <h2>Daftar Akun</h2>
                
                <!-- Notifikasi Error/Sukses (Di-handle oleh Controller nantinya) -->
                <?php if(isset($error_message)): ?>
                    <div style="background-color: #ffcccc; color: #cc0000; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; font-size: 14px;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Action mengarah ke router untuk memproses registrasi -->
                <form action="index.php?action=register_process" method="POST">
                    
                    <div class="input-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="email@ub.ac.id">
                    </div>
                    
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter">
                    </div>

                    <div class="input-group">
                        <label for="password_confirm">Konfirmasi Password</label>
                        <input type="password" id="password_confirm" name="password_confirm" required placeholder="Ulangi password">
                    </div>
                    
                    <button type="submit" class="btn-primary">Daftar Sekarang</button>
                </form>
                
                <!-- Jembatan kembali ke halaman Login -->
                <p class="link-bawah">Sudah punya akun? <a href="index.php?action=login_form">Login</a></p>
            </div>
        </div>

    </div>
</body>
</html>