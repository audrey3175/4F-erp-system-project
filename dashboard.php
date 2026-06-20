<?php 
session_start();
include 'koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Finance') { header("Location: login.php"); exit(); }
$page = 'dashboard'; 
$query = mysqli_query($conn, "SELECT * FROM pembayaran_pending");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - FoodSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#f4f7fa] antialiased text-slate-800">
    <div class="flex min-h-screen p-4 gap-6 w-full">
        
        <?php include('components/sidebar.php'); ?>

        <main class="flex-1 flex flex-col pt-2 pr-4 w-full overflow-x-hidden">
            <?php include('components/navbar.php'); ?> 

            <div class="mb-8">
                <h1 class="text-[26px] font-bold text-slate-900">Selamat Datang, <?= $_SESSION['nama'] ?? 'Nizham' ?></h1>
                <p class="text-[13px] text-slate-500 mt-1.5 font-medium flex items-center">
                    <i class="far fa-calendar-alt mr-2"></i> Senin, 24 Mei 2026 • 09:42 WIB <span class="mx-3 text-slate-300">|</span> 
                    <span class="text-[#004085] font-semibold">Sistem Laporan Keuangan Indofood</span>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-[#005bb5] text-white p-7 rounded-3xl shadow-md flex flex-col justify-between min-h-[160px]">
                    <div class="flex justify-between items-start">
                        <i class="fas fa-wallet bg-white/10 p-2.5 rounded-xl text-lg"></i>
                    </div>
                    <div class="mt-4">
                        <span class="text-[12px] font-medium opacity-80 mb-1 block">Total Saldo Kas</span>
                        <h2 class="text-4xl font-black tracking-tight">Rp 42,5M</h2>
                    </div>
                </div>
                
                <div class="bg-white p-7 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between min-h-[160px]">
                    <div class="flex justify-between items-start">
                        <div class="bg-red-50 text-red-500 p-2.5 rounded-xl text-lg"><i class="fas fa-exclamation-triangle"></i></div>
                        <span class="text-[11px] font-extrabold text-red-500 bg-red-50 px-2.5 py-1 rounded-full">+12.5%</span>
                    </div>
                    <div class="mt-4">
                        <span class="text-[12px] font-bold text-slate-500 mb-1 block">Hutang Jatuh Tempo</span>
                        <h2 class="text-2xl font-black text-slate-800">Rp 1,2M</h2>
                        <p class="text-[10px] font-bold text-red-500 mt-1.5 italic">Batas bayar: 7 Hari</p>
                    </div>
                </div>

                <div class="bg-white p-7 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between min-h-[160px]">
                    <div class="flex justify-between items-start">
                        <div class="bg-blue-50 text-[#005bb5] p-2.5 rounded-xl text-lg"><i class="fas fa-file-invoice"></i></div>
                        <span class="text-[11px] font-extrabold text-blue-500 bg-blue-50 px-2.5 py-1 rounded-full">-4.2%</span>
                    </div>
                    <div class="mt-4">
                        <span class="text-[12px] font-bold text-slate-500 mb-1 block">Piutang Belum Tertagih</span>
                        <h2 class="text-2xl font-black text-[#005bb5]">Rp 8,5M</h2>
                        <div class="w-full bg-slate-100 h-2 rounded-full mt-3 overflow-hidden">
                            <div class="bg-[#005bb5] h-full rounded-full" style="width: 78%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="lg:col-span-2 bg-white p-7 rounded-3xl border border-slate-100 shadow-sm flex flex-col min-h-[350px]">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-[15px] font-bold text-slate-800">Arus Kas Per Bulan</h3>
                            <p class="text-[12px] text-slate-400 mt-1">Perbandingan Pendapatan vs Pengeluaran</p>
                        </div>
                        <div class="flex items-center space-x-5 text-[11px] font-bold">
                            <span class="flex items-center text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-[#005bb5] mr-2"></span>Pendapatan</span>
                            <span class="flex items-center text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-slate-300 mr-2"></span>Pengeluaran</span>
                            <span class="bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg text-slate-700">Tahun 2026</span>
                        </div>
                    </div>
                    <div class="flex-1 flex items-end justify-between px-6 border-b-2 border-slate-100 text-[11px] font-bold text-slate-400 pb-3">
                        <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>Mei</span>
                    </div>
                </div>

                <div class="bg-white p-7 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-[15px] font-bold text-slate-800">Status Pelunasan</h3>
                        <i class="fas fa-ellipsis-v text-slate-400 cursor-pointer"></i>
                    </div>
                    <div class="flex justify-center items-center my-6 relative">
                        <svg class="w-40 h-40 transform -rotate-90">
                            <circle cx="80" cy="80" r="65" stroke="#f1f5f9" stroke-width="16" fill="transparent"/>
                            <circle cx="80" cy="80" r="65" stroke="#333399" stroke-width="16" fill="transparent" stroke-dasharray="408.4" stroke-dashoffset="89.8" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute text-center">
                            <span class="text-4xl font-black text-slate-900 block leading-none">78%</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1 block">Tuntas</span>
                        </div>
                    </div>
                    <div class="space-y-3 text-[12px] font-semibold text-slate-600">
                        <div class="flex justify-between items-center"><span class="flex items-center"><span class="w-2 h-2 rounded-full bg-[#333399] mr-3"></span>0 - 30 Hari</span><span class="font-bold text-slate-800">60%</span></div>
                        <div class="flex justify-between items-center"><span class="flex items-center"><span class="w-2 h-2 rounded-full bg-slate-300 mr-3"></span>31 - 60 Hari</span><span class="font-bold text-slate-800">25%</span></div>
                        <div class="flex justify-between items-center"><span class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-100 mr-3"></span>> 60 Hari</span><span class="font-bold text-slate-800">15%</span></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mb-6">
                <div class="px-7 py-5 flex justify-between items-center border-b border-slate-100">
                    <h3 class="text-[14px] font-bold text-slate-800">Persetujuan Pembayaran Pending</h3>
                    <a href="payment-approval.php" class="text-[12px] text-[#005bb5] font-bold hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px]">
                        <thead>
                            <tr class="bg-[#f8fafc] text-slate-500 font-bold uppercase border-b border-slate-200 text-[10px] tracking-wider">
                                <th class="px-7 py-4">ID Transaksi</th><th class="px-7 py-4">Vendor / Supplier</th><th class="px-7 py-4">Nominal</th><th class="px-7 py-4">Jatuh Tempo</th><th class="px-7 py-4">Status</th><th class="px-7 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            <?php while($row = mysqli_fetch_assoc($query)): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-7 py-4 font-bold text-slate-900"><?= $row['id_transaksi'] ?></td>
                                <td class="px-7 py-4 flex items-center"><div class="w-8 h-8 bg-slate-200 rounded-full flex items-center justify-center text-[10px] font-bold text-slate-600 mr-3">PT</div> <?= $row['vendor'] ?></td>
                                <td class="px-7 py-4 font-bold text-slate-900">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                                <td class="px-7 py-4 text-slate-500"><?= date('d M Y', strtotime($row['jatuh_tempo'])) ?></td>
                                <td class="px-7 py-4">
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold <?= ($row['status'] == 'Prioritas') ? 'bg-red-50 text-red-500' : 'bg-slate-100 text-slate-600' ?>"><?= $row['status'] ?></span>
                                </td>
                                <td class="px-7 py-4 text-center"><button class="bg-[#22c55e] hover:bg-green-600 text-white font-bold px-5 py-1.5 rounded-lg text-[12px] shadow-sm">Setujui</button></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</body>
</html>