<?php 
session_start();
include 'koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Finance') { header("Location: login.php"); exit(); }
$page = 'approval'; 
$query = mysqli_query($conn, "SELECT * FROM pengajuan_approval");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Payment Approval - FoodSync ERP</title>
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
                
                <div class="flex space-x-8 border-b border-slate-200 text-[13px] font-bold text-slate-400 pb-2">
                    <button class="text-[#005bb5] border-b-2 border-[#005bb5] pb-3">Menunggu Persetujuan <span class="bg-blue-100 text-[#005bb5] px-2 py-0.5 rounded-full text-[10px] font-black ml-1">12</span></button>
                    <button class="pb-3 hover:text-slate-600">Riwayat Disetujui</button>
                    <button class="pb-3 hover:text-slate-600">Ditolak/Dikembalikan</button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-2 items-start">
                    
                    <div class="space-y-4">
                        <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <div class="bg-white p-5 rounded-3xl border <?= ($row['doc_number']=='INV-2026-05041')?'border-[#005bb5] shadow-md':'border-slate-100 shadow-sm' ?> cursor-pointer flex flex-col space-y-2">
                            <div class="flex justify-between items-center text-[11px] font-bold text-slate-400">
                                <span class="<?= ($row['doc_number']=='INV-2026-05041')?'text-[#005bb5]':'' ?>"><?= $row['doc_number'] ?></span>
                                <span class="text-red-500 uppercase bg-red-50 px-2 py-0.5 rounded-md tracking-wider"><?= $row['sla'] ?></span>
                            </div>
                            <h4 class="text-[14px] font-black text-slate-900"><?= $row['vendor'] ?></h4>
                            <p class="text-[11px] text-slate-500 font-medium">Pengaju: <?= $row['requestor'] ?> | <?= date('d M Y', strtotime($row['date'])) ?></p>
                            <span class="text-[14px] font-black text-[#005bb5] text-right block pt-2 border-t border-slate-50">Rp <?= number_format($row['amount'], 0, ',', '.') ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-8 flex flex-col justify-between shadow-sm min-h-[650px]">
                        <div class="space-y-8">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-2xl font-black text-slate-900 flex items-center mb-1">PT Berkah Pangan Mandiri <span class="ml-4 text-[10px] font-bold text-white bg-red-600 px-2.5 py-1 rounded-md tracking-widest">URGENT</span></h2>
                                    <p class="text-[13px] text-slate-500 font-medium"><i class="fas fa-file-invoice mr-2"></i> Invoice #INV-2026-05041 | Pengadaan Bahan Baku Gandum</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Total Pembayaran</span>
                                    <span class="text-3xl font-black text-[#005bb5]">Rp 45.200.000</span>
                                </div>
                            </div>

                            <div class="bg-[#f8fafc] rounded-2xl p-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-[12px] font-bold text-slate-700 border border-slate-100">
                                <div><span class="text-[10px] text-slate-400 block mb-1 uppercase tracking-widest">Bank Vendor</span><span class="text-slate-900">BCA - 0822199341</span></div>
                                <div><span class="text-[10px] text-slate-400 block mb-1 uppercase tracking-widest">Metode</span><span class="text-slate-900">Transfer (RTGS)</span></div>
                                <div><span class="text-[10px] text-slate-400 block mb-1 uppercase tracking-widest">Departemen</span><span class="text-slate-900">Procurement</span></div>
                                <div><span class="text-[10px] text-slate-400 block mb-1 uppercase tracking-widest text-red-500">Batas SLA</span><span class="text-red-500">24/05/2026 18:00</span></div>
                            </div>

                            <div class="space-y-3">
                                <h3 class="text-[14px] font-bold text-slate-900"><i class="fas fa-list-ul mr-2 text-slate-400"></i>Rincian Item</h3>
                                <div class="border border-slate-200 rounded-2xl overflow-hidden text-[12px]">
                                    <div class="bg-[#f8fafc] px-6 py-3 flex font-bold text-slate-500 border-b border-slate-200 uppercase text-[10px] tracking-wider">
                                        <div class="w-1/2">Item</div><div class="w-1/6 text-center">Kuantitas</div><div class="w-1/6 text-right">Harga Satuan</div><div class="w-1/6 text-right">Subtotal</div>
                                    </div>
                                    <div class="p-6 flex flex-col space-y-4 font-medium text-slate-700">
                                        <div class="flex items-center"><div class="w-1/2 font-bold text-slate-900">Gandum Premium Bogasari (Grade A)</div><div class="w-1/6 text-center text-slate-500">500 Zak</div><div class="w-1/6 text-right">Rp 80.000</div><div class="w-1/6 text-right font-bold text-slate-900">Rp 40.000.000</div></div>
                                        <div class="flex items-center border-t border-slate-100 pt-4"><div class="w-1/2 font-bold text-slate-900">Biaya Logistik & Handling</div><div class="w-1/6 text-center text-slate-500">1 Lot</div><div class="w-1/6 text-right">Rp 5.200.000</div><div class="w-1/6 text-right font-bold text-slate-900">Rp 5.200.000</div></div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-[13px]">
                                <div class="border border-slate-200 p-4 rounded-2xl flex items-center justify-between hover:bg-slate-50 cursor-pointer transition-colors bg-white">
                                    <div class="flex items-center space-x-4"><div class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center text-xl"><i class="far fa-file-pdf"></i></div><div><p class="font-bold text-slate-900">Invoice_BPM_05041.pdf</p><p class="text-[11px] text-slate-500 mt-0.5">2.4 MB • Klik untuk melihat</p></div></div><i class="fas fa-external-link-alt text-slate-400"></i>
                                </div>
                                <div class="border border-slate-200 p-4 rounded-2xl flex items-center justify-between hover:bg-slate-50 cursor-pointer transition-colors bg-white">
                                    <div class="flex items-center space-x-4"><div class="w-10 h-10 bg-blue-50 text-[#005bb5] rounded-xl flex items-center justify-center text-xl"><i class="far fa-file-alt"></i></div><div><p class="font-bold text-slate-900">PO_Procurement_2026.pdf</p><p class="text-[11px] text-slate-500 mt-0.5">1.1 MB • Klik untuk melihat</p></div></div><i class="fas fa-external-link-alt text-slate-400"></i>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h3 class="text-[14px] font-bold text-slate-900"><i class="fas fa-history mr-2 text-slate-400"></i>Alur Persetujuan</h3>
                                <div class="pl-5 border-l-2 border-slate-200 ml-2 space-y-5 text-[12px] font-semibold text-slate-600 relative">
                                    <div><span class="absolute -left-[23px] top-1 w-3 h-3 rounded-full bg-green-500 border-2 border-white"></span><p class="font-bold text-slate-900">Diajukan oleh Budi Santoso (Staff)</p><p class="text-[11px] text-slate-400 mt-0.5">23 Mei 2026, 14:20</p></div>
                                    <div><span class="absolute -left-[23px] top-1 w-3 h-3 rounded-full bg-green-500 border-2 border-white"></span><p class="font-bold text-slate-900">Diverifikasi oleh Hendra (Supervisor)</p><p class="text-[11px] text-slate-400 mt-0.5">24 Mei 2026, 09:15</p></div>
                                    <div class="bg-blue-50 p-3 rounded-xl -ml-2"><span class="absolute -left-[23px] top-4 w-3 h-3 rounded-full bg-[#005bb5] border-2 border-white animate-pulse"></span><p class="font-bold text-[#005bb5]">Menunggu Persetujuan Nilam (Manager)</p><p class="text-[11px] text-[#005bb5]/70 mt-0.5">Tahap Terakhir</p></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 border-t border-slate-100 pt-6 mt-8 text-[13px] font-bold">
                            <button class="border border-red-500 text-red-500 hover:bg-red-50 px-6 py-3 rounded-xl transition-colors">✕ Tolak / Kembalikan</button>
                            <button class="bg-[#005bb5] hover:bg-blue-800 text-white px-8 py-3 rounded-xl shadow-md transition-colors">✓ Setujui & Eksekusi</button>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>