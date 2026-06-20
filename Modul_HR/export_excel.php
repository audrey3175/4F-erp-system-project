<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['id_karyawan']) || ($_SESSION['role_akun'] !== 'HRD' && $_SESSION['role_akun'] !== 'Admin')) { 
    die("Akses Ditolak!"); 
}

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Direktori_FoodSync.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<center>
    <h2>LAPORAN DIREKTORI KARYAWAN PT INDOFOOD (FOODSYNC ERP)</h2>
    <p>Dicetak pada: <?php echo date('d F Y H:i:s'); ?></p>
</center>
<table border="1" cellpadding="8">
    <thead style="background-color: #004a8f; color: white;">
        <tr>
            <th>NO</th><th>ID KARYAWAN</th><th>NIK</th><th>NAMA LENGKAP</th><th>EMAIL PERUSAHAAN</th><th>DEPARTEMEN</th><th>JABATAN</th><th>ROLE AKSES</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = mysqli_query($conn, "SELECT * FROM karyawan WHERE nama_lengkap IS NOT NULL AND nama_lengkap != '' ORDER BY departemen ASC");
        $no = 1;
        while($data = mysqli_fetch_assoc($query)){
            echo "<tr>
                    <td>".$no++."</td>
                    <td>".$data['id_karyawan']."</td>
                    <td>'".$data['nik']."</td> <td>".$data['nama_lengkap']."</td>
                    <td>".$data['email']."</td>
                    <td>".$data['departemen']."</td>
                    <td>".$data['jabatan']."</td>
                    <td>".$data['role_akun']."</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>