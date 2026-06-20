<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['id_karyawan']) || ($_SESSION['role_akun'] !== 'HRD' && $_SESSION['role_akun'] !== 'Admin')) { 
    header("Location: dashboard.php"); exit; 
}

$user_name = $_SESSION['nama_lengkap'];
$role = $_SESSION['role_akun'];

if (!isset($_GET['id'])) { header("Location: hrd.php"); exit; }
$id_target = mysqli_real_escape_string($conn, $_GET['id']);
$notif_sukses = ''; $notif_error = '';

if(isset($_POST['update_karyawan'])) {
    $departemen   = mysqli_real_escape_string($conn, $_POST['departemen']);
    $jabatan      = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $role_akun    = mysqli_real_escape_string($conn, $_POST['role_akun']);

    $query_update = "UPDATE karyawan SET departemen = '$departemen', jabatan = '$jabatan', role_akun = '$role_akun' WHERE id_karyawan = '$id_target'";
    if(mysqli_query($conn, $query_update)) { $notif_sukses = 'Berhasil! Data penempatan karyawan telah diperbarui.'; }
    else { $notif_error = 'Gagal: ' . mysqli_error($conn); }
}

if(isset($_POST['hapus_karyawan'])) {
    $query_hapus = "DELETE FROM karyawan WHERE id_karyawan = '$id_target'";
    if(mysqli_query($conn, $query_hapus)) {
        echo "<script>alert('Karyawan dan seluruh riwayat absensinya berhasil dihapus permanen!'); window.location.href='hrd.php';</script>"; exit;
    } else { $notif_error = 'Gagal menghapus: ' . mysqli_error($conn); }
}

$query_data = mysqli_query($conn, "SELECT * FROM karyawan WHERE id_karyawan = '$id_target'");
if(mysqli_num_rows($query_data) == 0) { header("Location: hrd.php"); exit; }
$karyawan = mysqli_fetch_assoc($query_data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Karyawan - FoodSync</title>
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
        .card-panel { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); border: 1px solid #f0f0f0; margin-bottom: 20px;}
        
        .form-label { font-size: 12.5px; font-weight: 700; color: #555; text-transform: uppercase; margin-bottom: 8px;}
        .form-control, .form-select { border-radius: 8px; border: 1px solid #ddd; padding: 12px 15px; font-size: 14px; background-color: #fcfcfc;}
        .btn-submit { background-color: #004a8f; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 700; font-size: 14px; transition: 0.2s;}
        .btn-submit:hover { background-color: #003366;}
        .btn-hapus { background-color: white; color: #dc3545; border: 2px solid #dc3545; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 13.5px; transition: 0.2s;}
        .btn-hapus:hover { background-color: #dc3545; color: white;}
        
        /* TOMBOL BACK ICON */
        .btn-back-icon { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; background-color: #e8f0fe; color: #004a8f; border-radius: 10px; text-decoration: none; font-size: 18px; transition: 0.2s; flex-shrink: 0;}
        .btn-back-icon:hover { background-color: #004a8f; color: #ffffff; }
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
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="hrd.php" class="btn-back-icon" title="Kembali ke Direktori"><i class="fas fa-arrow-left"></i></a>
                <div>
                    <h4 class="fw-bold mb-1 text-dark" style="font-size: 22px;">Profil Karyawan</h4>
                    <p class="text-muted mb-0" style="font-size: 13px;">Kelola informasi dan hak akses untuk akun ini.</p>
                </div>
                <div class="ms-auto">
                    <form method="POST" onsubmit="return confirm('PERINGATAN: Menghapus karyawan akan menghapus seluruh data absensi dan pengajuannya juga secara permanen. Anda yakin?');">
                        <button type="submit" name="hapus_karyawan" class="btn-hapus"><i class="fas fa-trash-alt me-2"></i> Cabut & Hapus Akun</button>
                    </form>
                </div>
            </div>
            
            <?php if($notif_sukses) echo "<div class='alert alert-success border-0 bg-success bg-opacity-10 text-success fw-bold rounded-3'><i class='fas fa-check-circle me-2'></i> $notif_sukses</div>"; ?>
            <?php if($notif_error) echo "<div class='alert alert-danger border-0 bg-danger bg-opacity-10 text-danger fw-bold rounded-3'><i class='fas fa-exclamation-circle me-2'></i> $notif_error</div>"; ?>
            
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card-panel text-center pt-5 pb-5 m-0 h-100">
                        <div style="width:90px; height:90px; background:linear-gradient(135deg, #004a8f, #002d5c); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:30px; margin:0 auto 20px; font-weight:800; box-shadow: 0 5px 15px rgba(0,74,143,0.3);">
                            <i class="far fa-user"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1"><?= $karyawan['nama_lengkap']; ?></h5>
                        <p class="text-primary fw-bold mb-3" style="font-size: 14px;"><?= $karyawan['id_karyawan']; ?></p>
                        
                        <div class="text-start mt-4 px-3" style="font-size: 13px;">
                            <div class="mb-3"><span class="text-muted d-block" style="font-size: 11px; text-transform:uppercase; font-weight:700;">Nomor Induk (NIK)</span> <span class="fw-bold"><?= $karyawan['nik']; ?></span></div>
                            <div><span class="text-muted d-block" style="font-size: 11px; text-transform:uppercase; font-weight:700;">Email Terdaftar</span> <span class="fw-bold"><?= $karyawan['email']; ?></span></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <div class="card-panel m-0 h-100">
                        <h6 class="fw-bold mb-4" style="color: #004a8f; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">Update Data Struktural</h6>
                        <form method="POST">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Departemen Baru</label>
                                    <select name="departemen" class="form-select">
                                        <option value="Manufacturing" <?= $karyawan['departemen']=='Manufacturing'?'selected':''; ?>>Manufacturing</option>
                                        <option value="Finance" <?= $karyawan['departemen']=='Finance'?'selected':''; ?>>Finance</option>
                                        <option value="HRD" <?= $karyawan['departemen']=='HRD'?'selected':''; ?>>HRD</option>
                                        <option value="Sales" <?= $karyawan['departemen']=='Sales'?'selected':''; ?>>Sales</option>
                                        <option value="Logistics" <?= $karyawan['departemen']=='Logistics'?'selected':''; ?>>Logistics</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jabatan Baru</label>
                                    <input type="text" name="jabatan" class="form-control" value="<?= $karyawan['jabatan']; ?>" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Hak Akses Sistem (Role)</label>
                                <select name="role_akun" class="form-select">
                                    <option value="Karyawan" <?= $karyawan['role_akun']=='Karyawan'?'selected':''; ?>>Karyawan</option>
                                    <option value="Supervisor" <?= $karyawan['role_akun']=='Supervisor'?'selected':''; ?>>Supervisor</option>
                                    <option value="HRD" <?= $karyawan['role_akun']=='HRD'?'selected':''; ?>>HRD</option>
                                    <option value="Finance" <?= $karyawan['role_akun']=='Finance'?'selected':''; ?>>Finance</option>
                                </select>
                            </div>
                            <button type="submit" name="update_karyawan" class="btn-submit"><i class="fas fa-sync-alt me-2"></i> Perbarui Data Karyawan</button>
                        </form>
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