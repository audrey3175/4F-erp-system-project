<?php
session_start();
require 'koneksi.php';

// HANYA FINANCE DAN ADMIN YANG DIIZINKAN MASUK PAYROLL
if (!isset($_SESSION['id_karyawan']) || ($_SESSION['role_akun'] !== 'Finance' && $_SESSION['role_akun'] !== 'Admin')) { 
    header("Location: dashboard.php"); exit; 
}
$user_name = $_SESSION['nama_lengkap'];
$role = $_SESSION['role_akun'];
$bulan_ini = date('m');
$tahun_ini = date('Y');

$query_payroll = mysqli_query($conn, "
    SELECT k.id_karyawan, k.nik, k.nama_lengkap, k.departemen, k.jabatan,
           COUNT(a.id_absen) as total_hadir 
    FROM karyawan k 
    LEFT JOIN tb_absensi a ON k.id_karyawan = a.id_karyawan 
                           AND MONTH(a.tanggal) = '$bulan_ini' 
                           AND YEAR(a.tanggal) = '$tahun_ini'
    GROUP BY k.id_karyawan
    ORDER BY k.departemen ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll System - FoodSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; overflow-x: hidden; color: #333; }
        .sidebar { width: 250px; height: 100vh; background-color: #ffffff; position: fixed; top: 0; left: 0; display: flex; flex-direction: column; border-right: 1px solid #eaeaea; z-index: 1000; overflow-y: auto;}
        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
        
        .brand-logo-container { padding: 20px; text-align: center; border-bottom: 1px solid #f0f0f0; margin-bottom: 20px; height: 100px; display: flex; align-items: center; justify-content: center;}
        .brand-logo-img { width: 100%; max-width: 200px; max-height: 75px; object-fit: contain; }
        
        .menu-title { font-size: 11px; font-weight: 700; color: #a0a0a0; padding: 0 25px; margin-bottom: 10px; margin-top: 10px; text-transform: uppercase; letter-spacing: 1px;}
        .nav-link { color: #555; font-weight: 600; padding: 10px 25px; margin: 2px 15px; border-radius: 8px; font-size: 13.5px; transition: 0.2s;}
        .nav-link i { width: 25px; color: #888; font-size: 15px;}
        .nav-link.active { background-color: #e8f0fe; color: #004a8f;}
        .nav-link.active i { color: #004a8f; }
        .nav-link:hover:not(.active) { background-color: #f8f9fa; color: #333; }
        .btn-logout { background-color: #d32f2f; color: white; border-radius: 8px; padding: 12px; font-weight: 600; font-size: 14px; text-align: center; text-decoration: none; margin: 20px; transition: 0.2s;}
        .btn-logout:hover { background-color: #b71c1c; color: white; }

        .main-content { margin-left: 250px; padding: 0; }
        .top-bar { background-color: #f4f7f6; padding: 20px 40px; display: flex; justify-content: flex-end; align-items: center; }
        .user-profile { display: flex; align-items: center; gap: 15px; }
        .user-info { text-align: right; line-height: 1.2; }
        .user-name { font-weight: 700; font-size: 14px; color: #222; }
        .user-role { font-size: 12px; color: #888; }
        
        .content-body { padding: 0 40px 40px 40px; }
        .card-panel { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); border: 1px solid #f0f0f0; margin-bottom: 25px;}
        
        .table-custom th { background-color: #fcfcfc; color: #888; padding: 15px; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #f0f0f0; font-weight: 700;}
        .table-custom td { padding: 15px; font-size: 13px; vertical-align: middle; color: #444; border-bottom: 1px solid #f8f9fa;}
    </style>
</head>
<body>
    
   <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <div class="sidebar">
        <div class="brand-logo-container">
            <img src="logo.png" alt="FoodSync Logo" class="brand-logo-img">
        </div>
        
        <div class="menu-title">MAIN MENU</div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link <?= in_array($current_page, ['karyawan.php', 'karyawan_cuti.php', 'karyawan_reimbursement.php']) ? 'active' : ''; ?>" href="karyawan.php"><i class="fas fa-user-check"></i> Portal Pribadi</a></li>
        </ul>

        <?php if ($role == 'HRD' || $role == 'Admin' || $role == 'Supervisor' || $role == 'Finance'): ?>
        <div class="menu-title">MANAGEMENT</div>
        <ul class="nav flex-column">
            
            <?php if ($role == 'Supervisor' || $role == 'Admin' || $role == 'HRD'): ?>
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'supervisor.php') ? 'active' : ''; ?>" href="supervisor.php"><i class="fas fa-check-square"></i> Panel Persetujuan</a></li>
            <?php endif; ?>
            
            <?php if ($role == 'HRD' || $role == 'Admin'): ?>
                <li class="nav-item"><a class="nav-link <?= in_array($current_page, ['hrd.php', 'hrd_tambah_karyawan.php', 'hrd_detail_karyawan.php']) ? 'active' : ''; ?>" href="hrd.php"><i class="fas fa-id-badge"></i> HRD Panel</a></li>
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'laporan.php') ? 'active' : ''; ?>" href="laporan.php"><i class="fas fa-file-alt"></i> Laporan</a></li>
            <?php endif; ?>
            
            <?php if ($role == 'Finance' || $role == 'Admin'): ?>
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'payroll.php') ? 'active' : ''; ?>" href="payroll.php"><i class="fas fa-file-invoice-dollar"></i> Payroll</a></li>
            <?php endif; ?>

        </ul>
        <?php endif; ?>

        <div class="mt-auto">
            <ul class="nav flex-column mb-3">
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'help_center.php') ? 'active' : ''; ?>" href="help_center.php"><i class="far fa-question-circle"></i> Help Center</a></li>
            </ul>
            <a href="logout.php" class="btn-logout d-block"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
       <div class="top-bar">
            <!-- CSS Tambahan Khusus Animasi Notifikasi -->
            <style>
                @keyframes pulse-red {
                    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
                    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
                    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
                }
                .notif-pulse { animation: pulse-red 2s infinite; }
                .notif-item { transition: 0.3s; border-left: 4px solid transparent; position: relative; }
                .notif-item:hover { background-color: #f8f9fa; border-left: 4px solid #004a8f; }
                .notif-unread { background-color: #f4f8ff; border-left: 4px solid #004a8f; }
                .notif-time { font-size: 10px; color: #888; margin-top: 5px; display:flex; align-items:center; gap: 4px; font-weight: 500;}
                .icon-circle { width: 40px; height: 40px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
                
                /* Tombol X untuk menghapus pesan (Muncul saat di-hover) */
                .btn-close-notif { position: absolute; right: 15px; top: 15px; font-size: 13px; color: #ccc; cursor: pointer; display: none; z-index: 10; padding: 5px; }
                .notif-item:hover .btn-close-notif { display: block; }
                .btn-close-notif:hover { color: #dc3545; transform: scale(1.1); transition: 0.2s;}
            </style>

            <div class="user-profile d-flex align-items-center">
                
                <!-- TOMBOL PENGATURAN -->
                <i class="fas fa-cog text-muted fs-5 me-4" style="cursor:pointer; transition:0.2s;" onmouseover="this.style.color='#004a8f'" onmouseout="this.style.color='#6c757d'" data-bs-toggle="modal" data-bs-target="#modalPengaturan" title="Keamanan Akun"></i>
                
                <!-- TOMBOL NOTIFIKASI PINTAR -->
                <div class="dropdown me-4">
                    <div data-bs-toggle="dropdown" aria-expanded="false" style="position: relative; cursor:pointer;" id="bell-icon-container">
                        <i class="far fa-bell text-muted fs-5" style="transition:0.2s;" onmouseover="this.style.color='#004a8f'" onmouseout="this.style.color='#6c757d'"></i>
                        <!-- Titik Merah Berdenyut -->
                        <span id="notif-dot" class="position-absolute top-0 start-100 translate-middle bg-danger border border-light rounded-circle notif-pulse" style="width: 10px; height: 10px; margin-top: 2px;"></span>
                    </div>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-0" style="width: 350px; border-radius: 12px; margin-top: 15px; overflow: hidden;">
                        <!-- Header Notif -->
                        <li class="bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">Notifikasi</h6>
                            <span id="notif-badge" class="badge bg-primary rounded-pill">Baru</span>
                        </li>
                        
                        <!-- List Pesan -->
                        <div style="max-height: 320px; overflow-y: auto; overflow-x: hidden;" id="notif-list-container">
                            
                            <!-- Pesan 1 -->
                            <li class="notif-li-item"><a class="dropdown-item py-3 px-3 notif-item notif-unread border-bottom" href="#">
                                <i class="fas fa-times btn-close-notif" title="Hapus pesan ini"></i> <!-- Tombol Hapus -->
                                <div class="d-flex align-items-start">
                                    <div class="bg-primary bg-opacity-10 text-primary icon-circle me-3"><i class="fas fa-bullhorn"></i></div>
                                    <div class="flex-grow-1" style="padding-right: 15px;">
                                        <span class="d-block fw-bold text-dark mb-1" style="font-size: 13px;">Update Sistem HRD v2.1</span>
                                        <span class="text-muted d-block lh-sm text-wrap" style="font-size: 12px;">Sistem Hakim Absensi otomatis sekarang telah aktif.</span>
                                        <span class="notif-time"><i class="far fa-clock"></i> Baru saja</span>
                                    </div>
                                </div>
                            </a></li>

                            <!-- Pesan 2 Khusus Manajemen -->
                            <?php if($role == 'Supervisor' || $role == 'HRD' || $role == 'Admin'): ?>
                            <li class="notif-li-item"><a class="dropdown-item py-3 px-3 notif-item notif-unread border-bottom" href="supervisor.php">
                                <i class="fas fa-times btn-close-notif" title="Hapus pesan ini"></i> <!-- Tombol Hapus -->
                                <div class="d-flex align-items-start">
                                    <div class="bg-warning bg-opacity-10 text-warning icon-circle me-3"><i class="fas fa-file-signature"></i></div>
                                    <div class="flex-grow-1" style="padding-right: 15px;">
                                        <span class="d-block fw-bold text-dark mb-1" style="font-size: 13px;">Menunggu Persetujuan</span>
                                        <span class="text-muted d-block lh-sm text-wrap" style="font-size: 12px;">Ada antrean pengajuan cuti yang butuh tinjauan Anda.</span>
                                        <span class="notif-time"><i class="far fa-clock"></i> 2 jam yang lalu</span>
                                    </div>
                                </div>
                            </a></li>
                            <?php endif; ?>
                            
                        </div>

                        <!-- Footer Notif -->
                        <li class="bg-light p-2 text-center border-top">
                            <a href="#" id="btn-read-all" class="text-primary text-decoration-none fw-bold" style="font-size: 12px;">Tandai semua dibaca <i class="fas fa-check-double ms-1"></i></a>
                        </li>
                    </ul>
                </div>

                <!-- INFO USER -->
                <div class="user-info text-end border-end pe-3 me-3">
                    <div class="user-name mb-1"><?= explode(' ', trim($user_name))[0]; ?></div>
                    <div class="user-role fw-bold text-primary" style="background:#e8f0fe; padding:2px 8px; border-radius:4px; display:inline-block;"><?= $role; ?></div>
                </div>
                <div style="width: 42px; height: 42px; background: linear-gradient(135deg, #004a8f, #002d5c); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; box-shadow: 0 4px 8px rgba(0,74,143,0.2);">
                    <?= substr($user_name, 0, 1); ?>
                </div>
                
            </div>

            <!-- JAVASCRIPT MESIN NOTIFIKASI -->
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // Membuat memori penyimpanan per-karyawan
                    const userId = "<?= isset($_SESSION['id_karyawan']) ? $_SESSION['id_karyawan'] : 'guest' ?>";
                    const storageKey = 'notif_read_status_' + userId;
                    
                    const notifDot = document.getElementById('notif-dot');
                    const notifBadge = document.getElementById('notif-badge');
                    const unreadItems = document.querySelectorAll('.notif-unread');
                    const btnReadAll = document.getElementById('btn-read-all');

                    // 1. Cek Memori Browser (Apakah sebelumnya user sudah menandai dibaca?)
                    if (localStorage.getItem(storageKey) === 'true') {
                        if (notifDot) notifDot.style.display = 'none';
                        if (notifBadge) notifBadge.style.display = 'none';
                        unreadItems.forEach(item => {
                            item.classList.remove('notif-unread');
                            item.style.backgroundColor = '#ffffff';
                        });
                    }

                    // 2. Fungsi Tombol "Tandai Semua Dibaca"
                    if (btnReadAll) {
                        btnReadAll.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation(); // Mencegah dropdown tertutup otomatis
                            
                            // Hilangkan titik merah dan ubah warna background biru
                            if (notifDot) notifDot.style.display = 'none';
                            if (notifBadge) notifBadge.style.display = 'none';
                            unreadItems.forEach(item => {
                                item.classList.remove('notif-unread');
                                item.style.backgroundColor = '#ffffff';
                            });

                            // Simpan status ke memori komputer permanen
                            localStorage.setItem(storageKey, 'true');
                            
                            btnReadAll.innerHTML = '<span class="text-muted">Semua telah dibaca</span>';
                            btnReadAll.style.pointerEvents = 'none';
                        });
                    }

                    // 3. Fungsi Tombol 'X' (Hapus Pesan per item)
                    const closeBtns = document.querySelectorAll('.btn-close-notif');
                    closeBtns.forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation(); // Mencegah klik masuk ke link
                            
                            const notifItem = this.closest('.notif-li-item');
                            
                            // Animasi transisi geser kanan & menghilang
                            notifItem.style.transition = 'all 0.3s ease';
                            notifItem.style.opacity = '0';
                            notifItem.style.transform = 'translateX(50px)';
                            
                            setTimeout(() => {
                                notifItem.remove(); // Hapus elemen dari list
                                
                                // Jika semua pesan sudah dihapus, munculkan teks kosong
                                const listContainer = document.getElementById('notif-list-container');
                                if (listContainer && listContainer.children.length === 0) {
                                    listContainer.innerHTML = '<div class="text-center p-5 text-muted"><i class="fas fa-bell-slash fs-1 mb-3 opacity-25"></i><br><small>Tidak ada notifikasi tersisa.</small></div>';
                                    if (notifDot) notifDot.style.display = 'none';
                                    if (notifBadge) notifBadge.style.display = 'none';
                                }
                            }, 300);
                        });
                    });
                });
            </script>
        </div>

        <div class="content-body">
            <div class="mb-4">
                <h4 class="fw-bold mb-1 text-dark" style="font-size: 22px;">Pengelolaan Payroll</h4>
                <p class="text-muted mb-0" style="font-size: 13px;">Bulan Periode: <strong><?= date('F Y'); ?></strong>. Perhitungan otomatis berdasarkan data kehadiran.</p>
            </div>
            
            <div class="card-panel m-0 p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-custom text-center mb-0">
                        <thead>
                            <tr><th class="text-start ps-4">Nama Karyawan</th><th>Departemen</th><th>Jabatan</th><th>Kehadiran</th><th>Tunj. Kehadiran (Rp50k/hari)</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(mysqli_num_rows($query_payroll) > 0): 
                                while($row = mysqli_fetch_assoc($query_payroll)): 
                                    $tunjangan = $row['total_hadir'] * 50000;
                            ?>
                                    <tr>
                                        <td class="fw-bold text-dark text-start ps-4"><?= $row['nama_lengkap']; ?></td>
                                        <td><?= $row['departemen']; ?></td>
                                        <td><?= $row['jabatan']; ?></td>
                                        <td><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill"><?= $row['total_hadir']; ?> Hari</span></td>
                                        <td class="fw-bold text-success">Rp <?= number_format($tunjangan, 0, ',', '.'); ?></td>
                                        <td>
                                            <button type="button" onclick="cetakSlipGaji(this)" 
        data-nama="<?= $row['nama_lengkap']; ?>" 
        data-dept="<?= $row['departemen']; ?>" 
        data-jabatan="<?= $row['jabatan']; ?>" 
        data-hadir="<?= $row['total_hadir']; ?> Hari" 
        data-tunjangan="Rp <?= number_format($tunjangan, 0, ',', '.'); ?>"
        class="btn btn-outline-secondary btn-sm fw-bold" style="border-radius: 6px;">
    <i class="fas fa-print me-1"></i> Cetak
</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-muted py-5">Data karyawan belum tersedia.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL PENGATURAN AKUN (UBAH PASSWORD) -->
    <div class="modal fade" id="modalPengaturan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="modal-header border-bottom-0 pb-0 mt-2 mx-2">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-cog text-primary me-2"></i> Keamanan Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 bg-primary bg-opacity-10 text-primary mb-4" style="font-size: 13px; border-radius: 8px;">
                        <i class="fas fa-shield-alt me-2"></i> Pastikan password baru Anda kuat dan mudah diingat.
                    </div>
                    <form action="proses_pengaturan.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 12px; font-weight: 700; color: #888; text-transform: uppercase;">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control bg-light" placeholder="Masukkan password baru" required style="border-radius: 8px; font-size: 14px; border: 1px solid #eee;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label" style="font-size: 12px; font-weight: 700; color: #888; text-transform: uppercase;">Konfirmasi Password</label>
                            <input type="password" name="konfirmasi_password" class="form-control bg-light" placeholder="Ketik ulang password baru" required style="border-radius: 8px; font-size: 14px; border: 1px solid #eee;">
                        </div>
                        <button type="submit" name="ubah_password" class="btn btn-primary w-100 fw-bold" style="border-radius: 8px; padding: 12px;"><i class="fas fa-save me-2"></i> Simpan Password Baru</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <div style="display: none;">
        <!-- ========================================== -->
    <!-- TEMPLATE SLIP GAJI FINAL (A4 PORTRAIT)     -->
    <!-- ========================================== -->
    <div style="position: absolute; left: -9999px; top: -9999px;">
        <!-- LEBAR DIKECILKAN JADI 680px AGAR TIDAK KELUAR DARI KERTAS A4 -->
        <div id="template-slip-gaji" style="padding: 40px; font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif; color: #333; width: 680px; background: white; box-sizing: border-box;">
            
            <!-- Kop Surat -->
            <div style="text-align: center; border-bottom: 3px solid #004a8f; padding-bottom: 15px; margin-bottom: 35px;">
                <img src="logo.png" alt="FoodSync Logo" style="max-height: 60px; display: block; margin: 0 auto 10px auto;">
                <p style="margin: 0; color: #666; font-size: 13px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;">Slip Gaji Karyawan - Periode <?= date('F Y'); ?></p>
            </div>
            
            <!-- Data Profil Karyawan -->
            <table style="width: 100%; margin-bottom: 30px; font-size: 14px; color: #444;">
                <tr>
                    <td style="width: 140px; font-weight: bold; padding: 6px 0;">Nama Karyawan</td>
                    <td style="padding: 6px 0;">: <span id="slip-nama" style="font-weight: 800; color: #004a8f; font-size: 15px;"></span></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 6px 0;">Departemen</td>
                    <td style="padding: 6px 0;">: <span id="slip-dept"></span></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 6px 0;">Jabatan</td>
                    <td style="padding: 6px 0;">: <span id="slip-jabatan"></span></td>
                </tr>
            </table>

            <!-- Tabel Rincian Kehadiran & Tunjangan -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 45px; font-size: 14px;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="padding: 14px 15px; border: 1px solid #dee2e6; text-align: left; color: #444; font-weight: 800;">KETERANGAN PENDAPATAN</th>
                        <th style="padding: 14px 15px; border: 1px solid #dee2e6; text-align: right; color: #444; font-weight: 800; width: 35%;">NOMINAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 15px; border: 1px solid #dee2e6; line-height: 1.6;">
                            <strong style="color: #222;">Tunjangan Kehadiran Terintegrasi</strong> (<span id="slip-hadir"></span>) <br>
                            <span style="color: #888; font-size: 11px;">*Dihitung otomatis berdasarkan tarif dasar Rp 50.000 / Hari masuk.</span>
                        </td>
                        <td style="padding: 15px; border: 1px solid #dee2e6; text-align: right; font-weight: 800; color: #198754; font-size: 17px;" id="slip-tunjangan"></td>
                    </tr>
                </tbody>
            </table>

            <!-- Zona Tanda Tangan Manajemen -->
            <div style="text-align: right; font-size: 14px; margin-top: 10px;">
                <p style="margin-bottom: 70px; color: #555;">Jakarta, <?= date('d F Y'); ?></p>
                <p style="font-weight: 800; text-decoration: underline; margin: 0; color: #222; font-size: 15px;">Nayla HRD</p>
                <p style="color: #666; font-size: 12px; margin-top: 4px;">HR Manager FoodSync</p>
            </div>
        </div>
    </div>

    <!-- Pustaka Utama HTML2PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <!-- Script Mesin Cetak -->
    <script>
        function cetakSlipGaji(tombol) {
            // Ekstrak Data
            var namaKaryawan = tombol.getAttribute('data-nama');
            var deptKaryawan = tombol.getAttribute('data-dept');
            var jabatanKaryawan = tombol.getAttribute('data-jabatan');
            var hadirKaryawan = tombol.getAttribute('data-hadir');
            var tunjanganKaryawan = tombol.getAttribute('data-tunjangan');

            // Suntik Data
            document.getElementById('slip-nama').innerText = namaKaryawan;
            document.getElementById('slip-dept').innerText = deptKaryawan;
            document.getElementById('slip-jabatan').innerText = jabatanKaryawan;
            document.getElementById('slip-hadir').innerText = hadirKaryawan;
            document.getElementById('slip-tunjangan').innerText = tunjanganKaryawan;

            var elemenSlip = document.getElementById('template-slip-gaji');
            
            // Konfigurasi PDF
            var pengaturan = {
                margin:       [0.5, 0.5, 0.5, 0.5], // Margin Atas, Kanan, Bawah, Kiri
                filename:     'Slip_Gaji_' + namaKaryawan.replace(/\s+/g, '_') + '.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true }, 
                jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
            };

            // Jalankan Generate
            html2pdf().set(pengaturan).from(elemenSlip).save();
        }
    </script>
</body>
</html>