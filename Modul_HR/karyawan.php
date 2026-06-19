<?php
session_start();
require 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['id_karyawan'])) { header("Location: homepage.php"); exit; }

$id_karyawan = $_SESSION['id_karyawan'];
$user_name = $_SESSION['nama_lengkap'];
$role = $_SESSION['role_akun'];
$tanggal_hari_ini = date('Y-m-d');
$waktu_sekarang = date('H:i:s');
$notif_pesan = '';
// ==============================================================================
// PENYAPU OTOMATIS: MENGATASI LUPA ABSEN PULANG (AUTO-CHECKOUT)
// ==============================================================================
// Sistem akan mencari data absen di hari-hari sebelumnya yang 'jam_keluar'-nya masih kosong,
// lalu otomatis menutupnya dengan jam standar pulang kantor (17:00:00).
mysqli_query($conn, "
    UPDATE tb_absensi 
    SET jam_keluar = '17:00:00' 
    WHERE id_karyawan = '$id_karyawan' 
    AND (jam_keluar IS NULL OR jam_keluar = '00:00:00' OR jam_keluar = '') 
    AND tanggal < '$tanggal_hari_ini'
");
// ==============================================================================
$cek_user_valid = mysqli_query($conn, "SELECT id_karyawan FROM karyawan WHERE id_karyawan = '$id_karyawan'");
$is_valid_user = (mysqli_num_rows($cek_user_valid) > 0);

$cek_absen = mysqli_query($conn, "SELECT * FROM tb_absensi WHERE id_karyawan = '$id_karyawan' AND tanggal = '$tanggal_hari_ini'");
$data_absen_hari_ini = mysqli_fetch_assoc($cek_absen);

if (isset($_POST['absen_masuk'])) {
    if (!$is_valid_user) { 
        $notif_pesan = "Error: Akun tidak valid untuk melakukan absensi."; 
    } elseif (!$data_absen_hari_ini) {
        mysqli_query($conn, "INSERT INTO tb_absensi (id_karyawan, tanggal, jam_masuk) VALUES ('$id_karyawan', '$tanggal_hari_ini', '$waktu_sekarang')");
        header("Location: karyawan.php?success=masuk"); exit;
    }
}

if (isset($_POST['absen_pulang'])) {
    if ($data_absen_hari_ini && empty($data_absen_hari_ini['jam_keluar'])) {
        mysqli_query($conn, "UPDATE tb_absensi SET jam_keluar = '$waktu_sekarang' WHERE id_karyawan = '$id_karyawan' AND tanggal = '$tanggal_hari_ini'");
        header("Location: karyawan.php?success=pulang"); exit;
    }
}

if(isset($_GET['success'])) {
    if($_GET['success'] == 'masuk') $notif_pesan = "Absen masuk berhasil dicatat! Selamat bekerja, $user_name.";
    if($_GET['success'] == 'pulang') $notif_pesan = "Absen pulang berhasil dicatat. Selamat beristirahat!";
}

$riwayat_absen = mysqli_query($conn, "SELECT * FROM tb_absensi WHERE id_karyawan = '$id_karyawan' ORDER BY tanggal DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pribadi - FoodSync</title>
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
        .card-panel { background: white; border-radius: 16px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); border: 1px solid #f0f0f0; margin-bottom: 20px;}
        .absen-widget { background: linear-gradient(135deg, #1e88e5, #1565c0); border-radius: 16px; padding: 30px; color: white; height: 100%; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden;}
        .cuti-widget { background-color: #002d5c; border-radius: 16px; padding: 30px; color: white; height: 100%; display: flex; flex-direction: column; justify-content: center;}
        .btn-cuti-outline { background: transparent; color: white; border: 2px solid rgba(255,255,255,0.5); padding: 10px 0; border-radius: 8px; font-weight: 600; font-size: 14px; width: 100%; transition: 0.2s; text-decoration: none; text-align: center; margin-top: 15px;}
        .btn-cuti-outline:hover { background: rgba(255,255,255,0.1); color: white; border-color: white;}
        .table-title { font-size: 15px; font-weight: 700; color: #333; margin-bottom: 20px;}
        .table-custom th { font-size: 11px; color: #888; text-transform: uppercase; font-weight: 600; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;}
        .table-custom td { padding: 15px 0; font-size: 13px; color: #444; border-bottom: 1px solid #f8f9fa; vertical-align: middle;}
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
                <h4 class="fw-bold mb-1 text-dark" style="font-size: 22px;">Portal Pribadi (Self-Service)</h4>
                <p class="text-muted" style="font-size: 13px;">Catat kehadiran harian Anda dan kelola pengajuan mandiri.</p>
            </div>

            <?php if($notif_pesan != ''): ?>
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-bold rounded-3 mb-4">
                    <i class="fas fa-check-circle me-2"></i> <?= $notif_pesan; ?>
                </div>
            <?php endif; ?>

            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card-panel h-100 p-0 overflow-hidden" style="display:flex;">
                        <div class="w-50 p-4 d-flex flex-column justify-content-center">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-clock fs-4 text-primary me-2"></i>
                                <h6 class="fw-bold mb-0 text-dark">Waktu Absensi Saat Ini</h6>
                            </div>
                            <h2 class="fw-bold text-dark mb-0" id="clock">00:00:00</h2>
                            <p class="text-muted" style="font-size: 13px;"><?= date('l, d F Y'); ?></p>
                        </div>
                        <div class="w-50 p-4" style="background:#f8f9fa; border-left:1px solid #f0f0f0;">
                            <form method="POST" class="h-100 d-flex flex-column justify-content-center">
                                <?php if(!$data_absen_hari_ini): ?>
                                    <button type="submit" name="absen_masuk" class="btn btn-primary w-100 py-3 fw-bold" style="border-radius: 10px; font-size:15px;"><i class="fas fa-sign-in-alt me-2"></i> Catat Absen Masuk</button>
                                <?php elseif(empty($data_absen_hari_ini['jam_keluar'])): ?>
                                    <button type="submit" name="absen_pulang" class="btn btn-warning w-100 py-3 fw-bold text-dark" style="border-radius: 10px; font-size:15px;"><i class="fas fa-sign-out-alt me-2"></i> Catat Absen Pulang</button>
                                    <small class="text-success fw-bold mt-2 text-center d-block">Telah Masuk: <?= date('H:i', strtotime($data_absen_hari_ini['jam_masuk'])); ?> WIB</small>
                                <?php else: ?>
                                    <button type="button" class="btn btn-secondary w-100 py-3 fw-bold" style="border-radius: 10px; font-size:15px;" disabled><i class="fas fa-check-double me-2"></i> Absensi Selesai Hari Ini</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="cuti-widget">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-size: 12px; opacity:0.8;">Sisa Kuota Cuti Tahunan</span>
                            <i class="fas fa-calendar-alt opacity-50"></i>
                        </div>
                        <div class="d-flex align-items-end mb-1">
                            <h1 class="fw-bold mb-0 me-2" style="font-size: 40px;">12</h1>
                            <span style="padding-bottom: 6px;">Hari</span>
                        </div>
                        <a href="karyawan_cuti.php" class="btn-cuti-outline"><i class="far fa-clock me-2"></i> Form Pengajuan Cuti</a>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-panel h-100">
                        <div class="table-title">Riwayat Kehadiran Pribadi Terakhir</div>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-start">Tanggal</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Keluar</th>
                                        <th class="text-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($riwayat_absen) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($riwayat_absen)): ?>
                                            <?php 
                                                // LOGIKA HAKIM OTOMATIS
                                                $jam_batas = strtotime('08:00:00');
                                                $jam_absen = strtotime($row['jam_masuk']);
                                                
                                                if ($jam_absen > $jam_batas) {
                                                    $status_teks = "TERLAMBAT";
                                                    $status_warna = "bg-danger text-danger";
                                                } else {
                                                    $status_teks = "TEPAT WAKTU";
                                                    $status_warna = "bg-success text-success";
                                                }
                                            ?>
                                            <tr>
                                                <td class="fw-bold text-start"><i class="fas fa-calendar-day text-primary me-2 opacity-50"></i> <?= date('d M Y', strtotime($row['tanggal'])); ?></td>
                                                <td class="text-center fw-bold"><?= date('H:i', strtotime($row['jam_masuk'])); ?></td>
                                                <td class="text-center fw-bold"><?= $row['jam_keluar'] ? date('H:i', strtotime($row['jam_keluar'])) : '-'; ?></td>
                                                <td class="text-end"><span class="badge <?= $status_warna; ?> bg-opacity-10 px-3 py-1 rounded-pill"><?= $status_teks; ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada riwayat absensi.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-panel h-100 d-flex flex-column">
                        <div class="table-title mb-4">Pengajuan Lainnya</div>
                        
                        <div style="border:1px solid #f0f0f0; border-radius:12px; padding:15px; margin-bottom:15px; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <h6 class="fw-bold mb-1" style="font-size:13px;">Cuti Sakit</h6>
                                <small class="text-muted" style="font-size:11px;">02 Mei 2024</small>
                            </div>
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Ditolak</span>
                        </div>

                        <div style="border:1px solid #f0f0f0; border-radius:12px; padding:15px; margin-bottom:auto; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <h6 class="fw-bold mb-1" style="font-size:13px;">Reimbursement</h6>
                                <small class="text-muted" style="font-size:11px;">Makan Client</small>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Disetujui</span>
                        </div>

                        <a href="karyawan_reimbursement.php" class="btn w-100" style="background:#e8f0fe; color:#004a8f; font-weight:600; border-radius:8px; font-size:13px; margin-top: 15px;"><i class="fas fa-receipt me-2"></i> Form Reimbursement</a>
                    </div>
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

    <script>
        function updateTime() {
            var d = new Date();
            var h = d.getHours() < 10 ? "0" + d.getHours() : d.getHours();
            var m = d.getMinutes() < 10 ? "0" + d.getMinutes() : d.getMinutes();
            var s = d.getSeconds() < 10 ? "0" + d.getSeconds() : d.getSeconds();
            document.getElementById("clock").innerHTML = h + ":" + m + ":" + s;
        }
        setInterval(updateTime, 1000); updateTime();
    </script>
</body>
</html>