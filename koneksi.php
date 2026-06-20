<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "foodsync_erp";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>