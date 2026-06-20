<?php
session_start();
// Jika sudah login, langsung lewati halaman ini dan masuk ke menu Modul
if (isset($_SESSION['id_karyawan'])) {
    header("Location: landing.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to FoodSync ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 0; padding: 0; height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #ffffff; overflow: hidden; position: relative; }
        
        /* Gambar Gedung 60% Kanan */
        .bg-image { position: absolute; top: 0; right: 0; width: 60%; height: 100%; background-image: url('Plaza_Sudirman-Marein_-_panoramio.jpg.jpg'); background-size: cover; background-position: center; z-index: 0; }
        
        /* Gradasi Putih Transisi */
        .gradient-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, rgba(255,255,255,1) 45%, rgba(255,255,255,0.85) 55%, rgba(255,255,255,0) 100%); z-index: 1; }
        
        .content-wrapper { position: relative; z-index: 2; height: 100%; display: flex; align-items: center; padding-left: 12%; }
        .logo-box { margin-bottom: 25px; }
        .logo-text { font-family: 'Georgia', serif; font-size: 65px; font-weight: 800; color: #003380; font-style: italic; margin: 0; line-height: 1; letter-spacing: -2px; }
        .logo-sub { font-size: 13px; font-weight: 700; color: #003380; margin: 0; letter-spacing: 1.5px; margin-top: 5px; }
        .main-desc { font-size: 16px; color: #000; font-weight: 500; line-height: 1.4; max-width: 400px; margin-bottom: 45px; }
        .action-buttons { display: flex; flex-direction: column; gap: 15px; width: 170px; }
        
        /* Tombol Sign In & Register */
        .btn-signin { background: linear-gradient(180deg, #4da3ff 0%, #0066cc 100%); color: white; border: none; border-radius: 50px; padding: 12px 0; text-align: center; text-decoration: none; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3); transition: 0.3s; }
        .btn-signin:hover { color: white; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 102, 204, 0.5); }
        .btn-register { background: white; color: #003380; border: 2px solid #0056b3; border-radius: 50px; padding: 10px 0; text-align: center; text-decoration: none; font-weight: 700; font-size: 16px; transition: 0.3s; }
        .btn-register:hover { background: #f8f9fa; color: #003380; transform: translateY(-2px); }
        
        .footer-text { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); font-size: 11px; color: #999; font-weight: 600; z-index: 2; letter-spacing: 0.5px; }
    </style>
</head>
<body>
    <div class="bg-image"></div>
    <div class="gradient-overlay"></div>
    <div class="content-wrapper">
        <div>
            <div class="logo-box">
                <h1 class="logo-text">FoodSync</h1>
                <p class="logo-sub">THE SYSTEM OF INTEGRATED FOOD ERP</p>
            </div>
            <p class="main-desc">
                Integrated Enterprise System Analysis<br>
                Optimizing Business Processes Through<br>
                Technology and Data Integration
            </p>
            <div class="action-buttons">
                <a href="homepage.php" class="btn-signin">Sign In</a>
                <a href="register.php" class="btn-register">Register</a>
            </div>
        </div>
    </div>
    <div class="footer-text">Project Sistem Enterprise 4F @2026</div>
</body>
</html>