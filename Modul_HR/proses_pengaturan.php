<?php
session_start();
require 'koneksi.php';

// Keamanan: Cek apakah user sedang login
if (!isset($_SESSION['id_karyawan'])) { header("Location: homepage.php"); exit; }

if (isset($_POST['ubah_password'])) {
    $id_karyawan = $_SESSION['id_karyawan'];
    $pass_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi_password'];

    // Cek apakah ketikan pertama dan kedua sama
    if ($pass_baru === $konfirmasi) {
        // Enkripsi password baru sebelum masuk database (SANGAT PENTING!)
        $password_hash = password_hash($pass_baru, PASSWORD_DEFAULT);
        
        $query = "UPDATE karyawan SET password = '$password_hash' WHERE id_karyawan = '$id_karyawan'";
        
        if (mysqli_query($conn, $query)) {
            // Jika sukses, munculkan pop-up berhasil lalu kembali ke halaman sebelumnya
            echo "<script>alert('Berhasil! Password akun Anda telah diperbarui.'); window.history.back();</script>";
        } else {
            echo "<script>alert('Gagal sistem: Terjadi kesalahan database.'); window.history.back();</script>";
        }
    } else {
        // Jika ketikan tidak cocok
        echo "<script>alert('Gagal: Password baru dan konfirmasi tidak cocok!'); window.history.back();</script>";
    }
} else {
    // Kalau iseng buka file ini langsung lewat URL
    header("Location: dashboard.php"); exit;
}
?>