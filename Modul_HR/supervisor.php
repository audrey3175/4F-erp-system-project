<?php
session_start();
require 'koneksi.php';

// Cek apakah yang masuk punya wewenang persetujuan
if (!isset($_SESSION['id_karyawan']) || ($_SESSION['role_akun'] !== 'Supervisor' && $_SESSION['role_akun'] !== 'Admin' && $_SESSION['role_akun'] !== 'HRD')) { 
    header("Location: dashboard.php"); exit; 
}

$user_name = $_SESSION['nama_lengkap'];
$role = $_SESSION['role_akun'];
$departemen_user = $_SESSION['departemen'];
$notif_pesan = ''; $notif_tipe = '';

// Eksekusi Persetujuan Cuti
if (isset($_POST['aksi_cuti'])) {
    $id_cuti = $_POST['id_cuti'];
    $status_baru = $_POST['aksi_cuti'] == 'acc' ? 'Disetujui' : 'Ditolak';
    if (mysqli_query($conn, "UPDATE tb_cuti SET status = '$status_baru' WHERE id_cuti = '$id_cuti'")) {
        $notif_pesan = "Pengajuan cuti berhasil di-$status_baru."; $notif_tipe = "success";
    }
}
// Eksekusi Persetujuan Reimburse
if (isset($_POST['aksi_reimburse'])) {
    $id_reimburse = $_POST['id_reimburse'];
    $status_baru = $_POST['aksi_reimburse'] == 'acc' ? 'Disetujui' : 'Ditolak';
    if (mysqli_query($conn, "UPDATE tb_reimbursement SET status = '$status_baru' WHERE id_reimburse = '$id_reimburse'")) {
        $notif_pesan = "Reimbursement berhasil di-$status_baru."; $notif_tipe = "success";
    }
}

// =========================================================
// LOGIKA SMART APPROVAL MATRIX (Pemisahan Wewenang)
// =========================================================
if ($role == 'Supervisor') {
    // Supervisor HANYA bisa ACC Karyawan di departemennya
    $filter_role = "AND k.role_akun = 'Karyawan' AND k.departemen = '$departemen_user'";
    $judul_halaman = "Persetujuan Tim (Dept. $departemen_user)";
    $desc_halaman = "Tinjau pengajuan dari staf Karyawan di bawah naungan Anda.";
} elseif ($role == 'HRD') {
    // HRD HANYA bisa ACC Supervisor & Finance (Mencegah HRD ACC diri sendiri)
    $filter_role = "AND k.role_akun IN ('Supervisor', 'Finance')";
    $judul_halaman = "Persetujuan Tingkat Manajemen";
    $desc_halaman = "Tinjau pengajuan dari level Supervisor dan Finance lintas departemen.";
} else {
    // Admin (Superadmin) bisa ACC semuanya yang nyangkut
    $filter_role = ""; 
    $judul_halaman = "Persetujuan Global (Superadmin)";
    $desc_halaman = "Tinjau seluruh pengajuan lintas departemen dan jabatan.";
}

$query_cuti_pending = mysqli_query($conn, "SELECT c.*, k.nama_lengkap, k.departemen, k.role_akun FROM tb_cuti c JOIN karyawan k ON c.id_karyawan = k.id_karyawan WHERE c.status = 'Pending' $filter_role");
$query_reimb_pending = mysqli_query($conn, "SELECT r.*, k.nama_lengkap, k.departemen, k.role_akun FROM tb_reimbursement r JOIN karyawan k ON r.id_karyawan = k.id_karyawan WHERE r.status = 'Pending' $filter_role");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Persetujuan - FoodSync</title>
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
        .card-panel { background: white; border-radius: 16px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); border: 1px solid #f0f0f0; margin-bottom: 25px;}
        
        .btn-acc { background-color: #198754; color: white; border: none; padding: 8px 15px; border-radius: 6px; font-size: 12.5px; font-weight: bold; margin-right: 5px; transition:0.2s;}
        .btn-acc:hover { background-color: #157347;}
        .btn-tolak { background-color: white; color: #dc3545; border: 1px solid #dc3545; padding: 8px 15px; border-radius: 6px; font-size: 12.5px; font-weight: bold; transition:0.2s;}
        .btn-tolak:hover { background-color: #dc3545; color: white;}
        
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
                <!-- Judul dan deskripsi berubah otomatis mengikuti yang login -->
                <h4 class="fw-bold mb-1 text-dark" style="font-size: 22px;"><?= $judul_halaman; ?></h4>
                <p class="text-muted mb-0" style="font-size: 13px;"><?= $desc_halaman; ?></p>
            </div>

            <?php if($notif_pesan != ''): ?>
                <div class="alert alert-<?= $notif_tipe; ?> border-0 bg-<?= $notif_tipe; ?> bg-opacity-10 text-<?= $notif_tipe; ?> fw-bold rounded-3 mb-4">
                    <i class="fas <?= $notif_tipe == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-2"></i> <?= $notif_pesan; ?>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Tabel Cuti -->
                <div class="col-lg-12">
                    <div class="card-panel m-0 p-0 overflow-hidden">
                        <div class="p-4 border-bottom" style="background-color: #fcfcfc;">
                            <h6 class="fw-bold mb-0" style="color: #004a8f;"><i class="fas fa-calendar-check me-2"></i> Persetujuan Cuti (Menunggu)</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom text-center align-middle mb-0">
                                <thead><tr><th class="text-start ps-4">Nama Pemohon</th><th>Role & Dept</th><th>Tgl Mulai</th><th>Tgl Selesai</th><th>Alasan</th><th>Aksi</th></tr></thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($query_cuti_pending) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($query_cuti_pending)): ?>
                                            <tr>
                                                <td class="fw-bold text-dark text-start ps-4"><?= $row['nama_lengkap']; ?></td>
                                                <td><span class="badge bg-secondary bg-opacity-10 text-dark"><?= $row['role_akun']; ?> - <?= $row['departemen']; ?></span></td>
                                                <td><?= date('d/m/Y', strtotime($row['tgl_mulai'])); ?></td>
                                                <td><?= date('d/m/Y', strtotime($row['tgl_selesai'])); ?></td>
                                                <td class="text-start"><?= $row['alasan']; ?></td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="id_cuti" value="<?= $row['id_cuti']; ?>">
                                                        <button type="submit" name="aksi_cuti" value="acc" class="btn-acc"><i class="fas fa-check me-1"></i> ACC</button>
                                                        <button type="submit" name="aksi_cuti" value="tolak" class="btn-tolak"><i class="fas fa-times me-1"></i> Tolak</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-muted py-5">Antrean pengajuan cuti kosong.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tabel Reimbursement -->
                <div class="col-lg-12">
                    <div class="card-panel m-0 p-0 overflow-hidden">
                        <div class="p-4 border-bottom" style="background-color: #fcfcfc;">
                            <h6 class="fw-bold mb-0" style="color: #004a8f;"><i class="fas fa-money-check-alt me-2"></i> Persetujuan Reimbursement (Menunggu)</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom text-center align-middle mb-0">
                                <thead><tr><th class="text-start ps-4">Nama Pemohon</th><th>Role & Dept</th><th>Nominal</th><th>Keterangan</th><th>Bukti</th><th>Aksi</th></tr></thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($query_reimb_pending) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($query_reimb_pending)): ?>
                                            <tr>
                                                <td class="fw-bold text-dark text-start ps-4"><?= $row['nama_lengkap']; ?></td>
                                                <td><span class="badge bg-secondary bg-opacity-10 text-dark"><?= $row['role_akun']; ?> - <?= $row['departemen']; ?></span></td>
                                                <td class="text-success fw-bold">Rp <?= number_format($row['nominal'], 0, ',', '.'); ?></td>
                                                <td class="text-start"><?= $row['keterangan']; ?></td>
                                                <td>
                                                    <?php if(!empty($row['bukti_foto'])): ?>
                                                        <a href="uploads/<?= $row['bukti_foto']; ?>" target="_blank" class="text-primary text-decoration-none fw-bold" style="font-size: 12px;"><i class="fas fa-eye me-1"></i> Cek</a>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="id_reimburse" value="<?= $row['id_reimburse']; ?>">
                                                        <button type="submit" name="aksi_reimburse" value="acc" class="btn-acc"><i class="fas fa-check me-1"></i> ACC</button>
                                                        <button type="submit" name="aksi_reimburse" value="tolak" class="btn-tolak"><i class="fas fa-times me-1"></i> Tolak</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-muted py-5">Antrean klaim reimbursement kosong.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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
</body>
</html>