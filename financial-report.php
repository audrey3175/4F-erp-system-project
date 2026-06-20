<?php 
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Finance') { header("Location: login.php"); exit(); }
$page = 'report'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi - FoodSync ERP</title>
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

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start w-full mb-8">
                
                <div class="bg-white p-7 rounded-3xl border border-slate-100 shadow-sm space-y-5 text-[13px] font-semibold sticky top-4">
                    <h3 class="text-[15px] font-bold text-slate-900 border-b border-slate-100 pb-3">Filter Laporan</h3>
                    <div>
                        <label class="text-slate-500 block mb-2 font-bold text-[11px] uppercase tracking-widest">Jenis Laporan</label>
                        <select class="bg-[#f8fafc] border border-slate-200 px-4 py-3 rounded-xl w-full outline-none text-slate-700 font-medium"><option>Laba Rugi (Income Statement)</option></select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-slate-500 block mb-2 font-bold text-[11px] uppercase tracking-widest">Dari</label>
                            <input type="text" value="Jan 2026" class="bg-[#f8fafc] border border-slate-200 px-4 py-3 rounded-xl w-full text-center outline-none font-medium">
                        </div>
                        <div>
                            <label class="text-slate-500 block mb-2 font-bold text-[11px] uppercase tracking-widest">Sampai</label>
                            <input type="text" value="Mei 2026" class="bg-[#f8fafc] border border-slate-200 px-4 py-3 rounded-xl w-full text-center outline-none font-medium">
                        </div>
                    </div>
                    <div>
                        <label class="text-slate-500 block mb-2 font-bold text-[11px] uppercase tracking-widest">Mata Uang</label>
                        <select class="bg-[#f8fafc] border border-slate-200 px-4 py-3 rounded-xl w-full outline-none text-slate-700 font-medium"><option>Rupiah (IDR)</option></select>
                    </div>
                    <button class="w-full bg-[#005bb5] hover:bg-blue-800 transition-colors text-white py-3.5 rounded-xl font-bold mt-4 shadow-md"><i class="fas fa-sync mr-2"></i>Generate Report</button>
                </div>

                <div class="lg:col-span-3 bg-white border border-slate-200 rounded-3xl shadow-lg max-w-4xl mx-auto w-full p-16 flex flex-col justify-between font-mono text-[12px] text-slate-800 min-h-[900px]">
                    <div>
                        <div class="text-center space-y-2 pb-8 border-b-2 border-slate-900">
                            <div class="w-16 h-16 bg-slate-100 rounded-lg mx-auto mb-4 flex items-center justify-center"><i class="fas fa-building text-2xl text-slate-300"></i></div>
                            <h2 class="text-[16px] font-black tracking-widest">PT INDOFOOD CBP SUKSES MAKMUR TBK</h2>
                            <p class="font-bold text-[14px]">Laporan Laba Rugi Komprehensif</p>
                            <p class="text-slate-500 font-sans text-[11px] mt-2">Untuk Periode yang Berakhir pada 31 Mei 2026 (Dalam Jutaan Rupiah)</p>
                        </div>

                        <div class="pt-10 space-y-4">
                            <div class="flex justify-between uppercase font-bold text-slate-400 border-b border-slate-200 pb-2 text-[10px] tracking-widest"><span>Keterangan</span><span class="w-24 text-center">Catatan</span><span class="w-40 text-right">Jumlah (IDR)</span></div>
                            
                            <div class="flex justify-between font-bold text-slate-900 pt-2"><span>PENDAPATAN NETO</span><span class="w-24 text-center font-normal text-slate-400">23</span><span class="w-40 text-right border-b border-slate-900 pb-1">12.450.000</span></div>
                            <div class="flex justify-between text-slate-600 pl-4"><span>Beban Pokok Penjualan</span><span class="w-24 text-center text-slate-400">24</span><span class="w-40 text-right">(7.120.000)</span></div>
                            <div class="flex justify-between font-bold text-slate-900 pt-2"><span>LABA BRUTO</span><span class="w-24 text-center"></span><span class="w-40 text-right border-b-2 border-slate-900 pb-1">5.330.000</span></div>
                            
                            <div class="pt-6 font-bold text-slate-400 uppercase text-[10px] tracking-widest">Beban Operasional</div>
                            <div class="flex justify-between text-slate-600 pl-4"><span>Beban Penjualan dan Distribusi</span><span class="w-24 text-center text-slate-400">25</span><span class="w-40 text-right">(1.240.000)</span></div>
                            <div class="flex justify-between text-slate-600 pl-4"><span>Beban Umum dan Administrasi</span><span class="w-24 text-center text-slate-400">26</span><span class="w-40 text-right">(890.000)</span></div>
                            <div class="flex justify-between text-slate-600 pl-4"><span>Penghasilan Operasi Lainnya</span><span class="w-24 text-center text-slate-400">27</span><span class="w-40 text-right">125.000</span></div>
                            <div class="flex justify-between font-bold text-slate-900 pt-2"><span>LABA USAHA</span><span class="w-24 text-center"></span><span class="w-40 text-right border-b-2 border-slate-900 pb-1">3.325.000</span></div>

                            <div class="pt-6 font-bold text-slate-400 uppercase text-[10px] tracking-widest">Penghasilan (Beban) Lain-Lain</div>
                            <div class="flex justify-between text-slate-600 pl-4"><span>Penghasilan Keuangan</span><span class="w-24 text-center text-slate-400">28</span><span class="w-40 text-right">45.000</span></div>
                            <div class="flex justify-between text-slate-600 pl-4"><span>Beban Keuangan</span><span class="w-24 text-center text-slate-400">29</span><span class="w-40 text-right">(210.000)</span></div>
                            <div class="flex justify-between text-slate-600 pl-4"><span>Pajak Final atas Bunga Simpanan</span><span class="w-24 text-center text-slate-400"></span><span class="w-40 text-right border-b border-slate-900 pb-1">(9.000)</span></div>
                            <div class="flex justify-between font-black text-slate-900 pt-3"><span>LABA SEBELUM PAJAK PENGHASILAN</span><span class="w-24 text-center"></span><span class="w-40 text-right border-b-[3px] border-slate-900 pb-1">3.151.000</span></div>
                        </div>
                    </div>

                    <div class="pt-24">
                        <div class="grid grid-cols-2 text-center font-sans text-[11px] font-semibold text-slate-700">
                            <div class="flex flex-col justify-between h-24">
                                <span>Jakarta, 05 Juni 2026</span>
                                <div><p class="font-bold text-slate-900 border-b border-slate-900 max-w-[160px] mx-auto pb-1">Anthoni Salim</p><p class="text-[10px] text-slate-400 mt-1">Direktur Utama</p></div>
                            </div>
                            <div class="flex flex-col justify-between h-24">
                                <span>Mengetahui,</span>
                                <div><p class="font-bold text-slate-900 border-b border-slate-900 max-w-[160px] mx-auto pb-1"><?= $_SESSION['nama'] ?? 'Nizham' ?></p><p class="text-[10px] text-slate-400 mt-1">Finance Manager</p></div>
                            </div>
                        </div>
                        <div class="flex justify-between text-[8px] text-slate-400 mt-16 pt-4 border-t border-slate-100 font-sans">
                            <span>ID_FIN_REP_202605_V1.0</span><span>Halaman 1 dari 1</span><span>Dicetak oleh: Sistem - <?= date('d/m/Y H:i') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>