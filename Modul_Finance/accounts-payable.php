<?php 
session_start();
include 'koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Finance') { header("Location: login.php"); exit(); }
$page = 'ap'; 
$query = mysqli_query($conn, "SELECT * FROM invoice_vendor");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Accounts Payable - FoodSync ERP</title>
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

            <div class="space-y-6 w-full">
                
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-[26px] font-bold text-slate-900">Manajemen Hutang (AP)</h1>
                        <p class="text-[13px] text-slate-500 mt-1">Kelola seluruh tagihan vendor dan jadwal pembayaran dalam satu tampilan terpusat.</p>
                    </div>
                    <button class="bg-[#005bb5] hover:bg-blue-800 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm"><i class="fas fa-plus-circle mr-2"></i>Input Invoice Baru</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between min-h-[130px]">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Total Bills</span>
                        <div>
                            <h3 class="text-3xl font-black mt-1">1,482</h3>
                            <p class="text-[11px] text-green-500 font-bold mt-2"><i class="fas fa-arrow-up mr-1"></i> +12% dari bln lalu</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between min-h-[130px]">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Pending Verifikasi</span>
                        <div>
                            <h3 class="text-3xl font-black mt-1">42</h3>
                            <p class="text-[11px] text-amber-500 font-semibold mt-2">Butuh perhatian segera</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm border-l-4 border-red-500 flex flex-col justify-between min-h-[130px]">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Discrepancy</span>
                        <div>
                            <h3 class="text-3xl font-black text-red-500 mt-1">08</h3>
                            <p class="text-[11px] text-red-400 font-bold mt-2">Selisih PO & Invoice</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between min-h-[130px]">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Due This Week</span>
                        <div>
                            <h3 class="text-3xl font-black text-[#005bb5] mt-1">Rp 12,4M</h3>
                            <p class="text-[11px] text-slate-400 mt-2 font-medium">Proyeksi arus kas</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4 text-xs font-semibold">
                    <input type="text" placeholder="No. Invoice / Vendor" class="bg-[#f4f7fa] border border-slate-200 px-4 py-3 rounded-xl outline-none w-full">
                    <select class="bg-[#f4f7fa] border border-slate-200 px-4 py-3 rounded-xl text-slate-500 w-full outline-none"><option>Semua Status</option></select>
                    <input type="text" value="Mei 2026" class="bg-[#f4f7fa] border border-slate-200 px-4 py-3 rounded-xl text-center outline-none w-full">
                    <button class="bg-[#005bb5] text-white py-3 rounded-xl font-bold shadow-sm">Terapkan Filter</button>
                </div>

                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mb-6">
                    <div class="px-7 py-5 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-[14px] font-bold text-slate-800">Daftar Invoice Vendor</h3>
                        <div class="text-slate-400 space-x-4 text-sm">
                            <i class="fas fa-download cursor-pointer hover:text-slate-600"></i>
                            <i class="fas fa-print cursor-pointer hover:text-slate-600"></i>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-[13px] border-collapse">
                            <thead>
                                <tr class="bg-[#f8fafc] text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                                    <th class="px-7 py-4">No. Invoice</th><th class="px-7 py-4">PO Reference</th><th class="px-7 py-4">Vendor Name</th><th class="px-7 py-4">Due Date</th><th class="px-7 py-4">Amount</th><th class="px-7 py-4">Status</th><th class="px-7 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                <?php while($row = mysqli_fetch_assoc($query)): ?>
                                <tr class="<?= ($row['status']=='Discrepancy') ? 'bg-red-50/30' : 'hover:bg-slate-50' ?>">
                                    <td class="px-7 py-4 text-[#005bb5] font-bold"><?= $row['no_invoice'] ?></td>
                                    <td class="px-7 py-4 text-slate-400"><?= $row['po_reference'] ?></td>
                                    <td class="px-7 py-4 font-bold text-slate-900"><?= $row['vendor_name'] ?></td>
                                    <td class="px-7 py-4"><?= date('d M Y', strtotime($row['due_date'])) ?></td>
                                    <td class="px-7 py-4 font-bold text-slate-900">Rp <?= number_format($row['amount'], 0, ',', '.') ?></td>
                                    <td class="px-7 py-4">
                                        <?php
                                        $badge = ($row['status'] == 'Verified') ? 'bg-green-100 text-green-700' : (($row['status'] == 'Pending') ? 'bg-amber-100 text-amber-600' : (($row['status'] == 'Paid') ? 'bg-blue-100 text-[#005bb5]' : 'bg-red-100 text-red-600'));
                                        echo "<span class='px-3 py-1 rounded-full text-[11px] font-bold $badge'>● {$row['status']}</span>";
                                        ?>
                                    </td>
                                    <td class="px-7 py-4 text-center space-x-2">
                                        <?php if($row['status'] == 'Discrepancy'): ?>
                                            <button class="bg-red-500 text-white font-bold px-3 py-1.5 rounded-lg text-[11px]">Cek Selisih</button>
                                        <?php else: ?>
                                            <button class="bg-[#005bb5] text-white font-bold px-3 py-1.5 rounded-lg text-[11px]">Ajukan Pembayaran</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>