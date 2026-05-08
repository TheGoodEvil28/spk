<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Inventaris Lab ICN</title>
    
    <!-- Load Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- PANGGIL FILE CSS EKSTERNAL -->
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

        <!-- BAGIAN KANAN (Form Login) -->
        <div class="right-panel">
            <div class="form-wrapper">
                <img src="assets/images/logo-icn.png" alt="Logo Lab ICN" class="logo-icn">
                
                <h2>Login</h2>
                
                <!-- Menampilkan pesan error dari Controller (Jika email/password salah) -->
                <?php if(isset($error_message)): ?>
                    <div style="background-color: #ffcccc; color: #cc0000; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; font-size: 14px;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Action mengarah ke router utama -->
                <form action="index.php?action=login_process" method="POST">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="email@ub.ac.id">
                    </div>
                    
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Masukkan password">
                    </div>
                    
                    <button type="submit" class="btn-primary">Login</button>
                </form>
                
                <!-- Jembatan menuju halaman Daftar -->
                <p class="link-bawah">Tidak punya akun? <a href="index.php?action=register">Daftar</a></p>
            </div>
        </div>

    </div>
</body>
</html>