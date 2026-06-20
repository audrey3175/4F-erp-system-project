<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['id_karyawan'])) { header("Location: dashboard.php"); exit; }
$error = ''; $success = '';

if (isset($_POST['register'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $password_mentah = $_POST['password']; 
    $password_hash   = password_hash($password_mentah, PASSWORD_DEFAULT);
    $nama_lengkap = trim($first_name . ' ' . $last_name);
    
    $cek_email = mysqli_query($conn, "SELECT email FROM karyawan WHERE email = '$email'");
    if(mysqli_num_rows($cek_email) > 0) {
        $error = 'Email sudah terdaftar! Silakan Sign In.';
    } else {
        $id_karyawan = 'KRY-' . rand(1000, 9999);
        $nik = rand(10000000, 99999999); 
        
        $query = "INSERT INTO karyawan (id_karyawan, nik, nama_lengkap, email, password, departemen, jabatan, role_akun) 
                  VALUES ('$id_karyawan', '$nik', '$nama_lengkap', '$email', '$password_hash', 'Belum Ditentukan', 'Staff Baru', 'Karyawan')";
        if(mysqli_query($conn, $query)) {
            $success = 'Akun berhasil dibuat! Silakan Sign In.';
        } else { $error = 'Gagal membuat akun sistem.'; }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - FoodSync ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { margin: 0; padding: 0; height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; align-items: center; justify-content: center; background: linear-gradient(rgba(10, 58, 106, 0.85), rgba(10, 58, 106, 0.85)), url('Group 9.jpg') center/cover; }
        .split-card { display: flex; width: 950px; height: 550px; background: white; border-radius: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); overflow: hidden; }
        
        /* GAMBAR KIRI */
        .image-panel { width: 45%; position: relative; background: url('Group 9.jpg') center/cover; display: flex; align-items: center; justify-content: center; }
        .glass-card { background: rgba(30, 30, 30, 0.55); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-radius: 30px; padding: 45px 30px; text-align: center; color: white; width: 80%; border: 1px solid rgba(255,255,255,0.1); }
        .glass-title { font-size: 30px; font-weight: 800; line-height: 1.2; margin-bottom: 10px; letter-spacing: -1px;}
        .glass-desc { font-size: 13px; font-weight: 500; margin-bottom: 35px; opacity: 0.9;}
        .glass-ask { font-size: 11px; opacity: 0.8; margin-bottom: 10px; }
        .btn-glass-action { background-color: #0d6efd; color: white; border: none; border-radius: 30px; padding: 10px 40px; font-weight: 700; font-size: 14px; text-decoration: none; display: inline-block; transition: 0.3s; }
        .btn-glass-action:hover { background-color: #0b5ed7; color: white; transform: translateY(-2px);}

        /* FORM KANAN */
        .form-panel { width: 55%; padding: 40px 60px; display: flex; flex-direction: column; justify-content: center; }
        .title-main { font-size: 34px; font-weight: 800; color: #003366; text-align: center; margin-bottom: 5px; letter-spacing: -1px;}
        .subtitle { font-size: 13px; color: #555; text-align: center; margin-bottom: 25px; font-weight: 500;}
        .form-label { font-size: 12px; font-weight: 700; color: #111; margin-bottom: 6px; }
        .form-control { border-radius: 20px; border: 2px solid #6ea8fe; padding: 10px 18px; font-size: 13px; margin-bottom: 12px; color: #333; }
        .form-control::placeholder { color: #aab2bd; }
        .checkbox-text { font-size: 11px; color: #111; font-weight: 600; margin-left: 5px; cursor: pointer;}
        .btn-submit { background-color: #003366; color: white; border: none; border-radius: 30px; padding: 12px; width: 100%; font-weight: 700; font-size: 15px; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { background-color: #002244; transform: translateY(-2px); }
    </style>
</head>
<body>

    <div class="split-card">
        <div class="image-panel">
            <div class="glass-card">
                <div class="glass-title">Welcome To<br>PT Indofood</div>
                <div class="glass-desc">We hope you had a great day</div>
                <div class="glass-ask">Have an account?</div>
                <a href="homepage.php" class="btn-glass-action">Sign In</a>
            </div>
        </div>

        <div class="form-panel">
            <div class="title-main">Create Account</div>
            <div class="subtitle">Please enter your details</div>
            <?php if($error) echo "<div class='alert alert-danger p-2 text-center' style='font-size:11px; border-radius:15px;'>$error</div>"; ?>
            <?php if($success) echo "<div class='alert alert-success p-2 text-center' style='font-size:11px; border-radius:15px;'>$success</div>"; ?>

            <form method="POST">
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" placeholder="E.g. Nara" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" placeholder="E.g. Nala" required>
                    </div>
                </div>
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="E.g. nayla.mardhiyah24" required>
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="E.g. nayla.mardhiyah24" required>
                <div class="d-flex align-items-center mt-1">
                    <input type="checkbox" id="remember_reg" checked style="width:14px; height:14px; accent-color:#0d6efd;">
                    <label for="remember_reg" class="checkbox-text">Remember log in activity</label>
                </div>
                <button type="submit" name="register" class="btn-submit">Sign Up</button>
            </form>
        </div>
    </div>

</body>
</html>