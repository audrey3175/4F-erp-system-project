<?php
session_start();
require 'koneksi.php';

// Jika sudah login, langsung lempar ke landing.php (halaman modul)
if (isset($_SESSION['id_karyawan'])) { header("Location: dashboard.php"); exit; }
$error = '';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password_input = $_POST['password'];

    // Pintu Darurat Super Admin
    if ($email === 'superadmin@foodsync.com' && $password_input === 'adminmaster') {
        $_SESSION['id_karyawan'] = 'SUPER-ADMIN';
        $_SESSION['nama_lengkap'] = 'Super Admin (Master)';
        $_SESSION['role_akun']    = 'Admin';
        $_SESSION['departemen']   = 'All Department';
        header("Location: dashboard.php"); exit;
    }

    $query = "SELECT * FROM karyawan WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password_input, $row['password']) || $password_input === $row['password']) {
            $_SESSION['id_karyawan'] = $row['id_karyawan'];
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
            $_SESSION['role_akun']    = $row['role_akun'];
            $_SESSION['departemen']   = $row['departemen'];
            header("Location: dashboard.php"); exit; // Arahkan ke landing modul
        } else { $error = 'Password salah!'; }
    } else { $error = 'Email tidak terdaftar!'; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - FoodSync ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 0; padding: 0; height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; align-items: center; justify-content: center; background: linear-gradient(rgba(10, 58, 106, 0.9), rgba(10, 58, 106, 0.9)), url('Group 9.jpg') center/cover; }
        .split-card { display: flex; width: 950px; height: 550px; background: white; border-radius: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); overflow: hidden; }
        
        /* FORM KIRI */
        .form-panel { width: 50%; padding: 60px 70px; display: flex; flex-direction: column; justify-content: center; }
        .title-main { font-size: 38px; font-weight: 800; color: #003366; text-align: center; margin-bottom: 5px; letter-spacing: -1px;}
        .subtitle { font-size: 14px; color: #555; text-align: center; margin-bottom: 40px; font-weight: 500;}
        .form-label { font-size: 13px; font-weight: 700; color: #111; margin-bottom: 8px; }
        .form-control { border-radius: 20px; border: 2px solid #6ea8fe; padding: 12px 20px; font-size: 13px; margin-bottom: 15px; color: #333; }
        .form-control::placeholder { color: #aab2bd; }
        .form-control:focus { box-shadow: none; border-color: #003366; }
        .extra-links { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; margin-top: -5px;}
        .checkbox-text { font-size: 12px; color: #111; font-weight: 600; margin-left: 5px; cursor: pointer;}
        .forgot-pass { font-size: 11px; color: #999; text-decoration: none;}
        .btn-submit { background-color: #003366; color: white; border: none; border-radius: 30px; padding: 14px; width: 100%; font-weight: 700; font-size: 16px; transition: 0.3s; }
        .btn-submit:hover { background-color: #002244; transform: translateY(-2px); }

        /* GAMBAR KANAN */
        .image-panel { width: 50%; position: relative; background: url('Group 9.jpg') center/cover; display: flex; align-items: center; justify-content: center; }
        .glass-card { background: rgba(30, 30, 30, 0.55); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-radius: 30px; padding: 45px 30px; text-align: center; color: white; width: 75%; border: 1px solid rgba(255,255,255,0.1); }
        .glass-title { font-size: 32px; font-weight: 800; line-height: 1.2; margin-bottom: 10px; letter-spacing: -1px;}
        .glass-desc { font-size: 13px; font-weight: 500; margin-bottom: 35px; opacity: 0.9;}
        .glass-ask { font-size: 11px; opacity: 0.8; margin-bottom: 10px; }
        .btn-glass-action { background-color: #0d6efd; color: white; border: none; border-radius: 30px; padding: 10px 40px; font-weight: 700; font-size: 14px; text-decoration: none; display: inline-block; transition: 0.3s; }
        .btn-glass-action:hover { background-color: #0b5ed7; color: white; transform: translateY(-2px);}
    </style>
</head>
<body>
    <div class="split-card">
        <div class="form-panel">
            <div class="title-main">Sign In</div>
            <div class="subtitle">Please enter your details</div>
            <?php if($error) echo "<div class='alert alert-danger p-2 text-center' style='font-size:12px; border-radius:15px;'>$error</div>"; ?>
            <form method="POST">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="E.g. nayla.mardhiyah24@mhs.uinjkt.ac.id" required>
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="E.g. nayla.mardhiyah24" required>
                <div class="extra-links">
                    <div class="d-flex align-items-center">
                        <input type="checkbox" id="remember" checked style="width:16px; height:16px; accent-color:#0d6efd;">
                        <label for="remember" class="checkbox-text">Remember log in activity</label>
                    </div>
                    <a href="#" class="forgot-pass">forgot password?</a>
                </div>
                <button type="submit" name="login" class="btn-submit">Sign In</button>
            </form>
        </div>
        <div class="image-panel">
            <div class="glass-card">
                <div class="glass-title">Hey,<br>Welcome Back!</div>
                <div class="glass-desc">We hope you had a great day</div>
                <div class="glass-ask">Not yet a member?</div>
                <a href="register.php" class="btn-glass-action">Register</a>
            </div>
        </div>
    </div>
</body>
</html>