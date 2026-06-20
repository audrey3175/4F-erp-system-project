<?php 
session_start();
include 'koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Finance') { header("Location: login.php"); exit(); }
$page = 'reports'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reports - FoodSync</title>
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

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-[#004085]">Financial Reports</h1>
                    <p class="text-[13px] text-slate-500 mt-1">Comprehensive financial analysis and statement generation.</p>
                </div>
                <div class="flex space-x-3">
                    <button class="text-red-600 text-xs font-bold px-4 py-2 flex items-center"><i class="far fa-file-pdf mr-2 text-lg"></i> Export PDF</button>
                    <button class="text-red-600 text-xs font-bold px-4 py-2 flex items-center"><i class="far fa-file-excel mr-2 text-lg"></i> Export Excel</button>
                    <button class="bg-[#005bb5] text-white px-5 py-2.5 rounded-lg text-xs font-bold shadow-md"><i class="fas fa-plus mr-2"></i> Generate Report</button>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex space-x-4 mb-6">
                <div class="flex-1">
                    <label class="block text-[10px] font-bold text-slate-400 mb-1.5 uppercase">Date Range</label>
                    <div class="border border-slate-200 rounded-lg px-4 py-2 text-[13px] font-medium text-slate-700 flex items-center"><i class="far fa-calendar-alt mr-2 text-slate-400"></i> Jan 1, 2026 - Mar 31, 2026</div>
                </div>
                <div class="w-48">
                    <label class="block text-[10px] font-bold text-slate-400 mb-1.5 uppercase">Month</label>
                    <select class="border border-slate-200 rounded-lg px-4 py-2 text-[13px] font-medium text-slate-700 w-full outline-none"><option>March</option></select>
                </div>
                <div class="w-32">
                    <label class="block text-[10px] font-bold text-slate-400 mb-1.5 uppercase">Year</label>
                    <select class="border border-slate-200 rounded-lg px-4 py-2 text-[13px] font-medium text-slate-700 w-full outline-none"><option>2026</option></select>
                </div>
                <div class="w-64">
                    <label class="block text-[10px] font-bold text-slate-400 mb-1.5 uppercase">Department</label>
                    <select class="border border-slate-200 rounded-lg px-4 py-2 text-[13px] font-medium text-slate-700 w-full outline-none"><option>All Departments</option></select>
                </div>
                <div class="flex items-end">
                    <button class="border border-slate-200 bg-white text-slate-700 px-6 py-2 rounded-lg text-[13px] font-bold flex items-center"><i class="fas fa-filter mr-2"></i> Apply</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4"><div class="w-10 h-10 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center text-lg"><i class="fas fa-chart-line"></i></div><i class="fas fa-arrow-up text-slate-300"></i></div>
                        <h3 class="text-lg font-bold text-slate-800 mb-1">Profit & Loss</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Income, expenses, and costs over the selected</p>
                    </div>
                    <div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-50"><span class="text-[10px] text-slate-400">Last run: Today</span><span class="bg-blue-50 text-[#005bb5] px-2.5 py-1 rounded text-[10px] font-bold">Ready</span></div>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4"><div class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center text-lg"><i class="fas fa-landmark"></i></div><i class="fas fa-arrow-up text-slate-300"></i></div>
                        <h3 class="text-lg font-bold text-slate-800 mb-1">Balance Sheet</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Snapshot of assets, liabilities, and equity.</p>
                    </div>
                    <div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-50"><span class="text-[10px] text-slate-400">Last run: Yesterday</span><span class="bg-blue-50 text-[#005bb5] px-2.5 py-1 rounded text-[10px] font-bold">Ready</span></div>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4"><div class="w-10 h-10 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center text-lg"><i class="fas fa-money-bill-wave"></i></div><i class="fas fa-sync text-slate-300"></i></div>
                        <h3 class="text-lg font-bold text-slate-800 mb-1">Cash Flow</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Operating, investing, and financing cash...</p>
                    </div>
                    <div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-50"><span class="text-[10px] text-slate-400">Last run: Mar 15</span><span class="bg-slate-100 text-slate-500 px-2.5 py-1 rounded text-[10px] font-bold">Draft</span></div>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4"><div class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center text-lg"><i class="fas fa-balance-scale"></i></div><i class="fas fa-arrow-up text-slate-300"></i></div>
                        <h3 class="text-lg font-bold text-slate-800 mb-1">Trial Balance</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Closing balances of all general ledger...</p>
                    </div>
                    <div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-50"><span class="text-[10px] text-slate-400">Last run: Mar 01</span><span class="bg-blue-50 text-[#005bb5] px-2.5 py-1 rounded text-[10px] font-bold">Ready</span></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm min-h-[350px] flex flex-col">
                    <div class="flex justify-between items-center mb-6"><h3 class="font-bold text-slate-800">Revenue vs Expense Trends</h3><i class="fas fa-ellipsis-v text-slate-400"></i></div>
                    <div class="flex-1 border-2 border-dashed border-slate-100 rounded-xl flex items-center justify-center bg-slate-50/50">
                        <p class="text-sm font-bold text-slate-300">[ Line Chart Area ]</p>
                    </div>
                    <div class="flex justify-center items-center space-x-6 mt-4 text-[10px] font-bold text-slate-500">
                        <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-green-600 mr-2"></span>Revenue</span>
                        <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-600 mr-2"></span>Expenses</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                    <h3 class="font-bold text-slate-800 mb-6">Recent Activity</h3>
                    <div class="space-y-6 flex-1">
                        <div class="flex justify-between items-start"><div class="flex"><div class="w-8 h-8 rounded bg-red-50 text-red-500 flex items-center justify-center mr-3"><i class="far fa-file-pdf"></i></div><div><p class="text-xs font-bold text-slate-800">Q1_Profit_Loss_Final.pdf</p><p class="text-[9px] text-slate-400 mt-0.5">Generated by Nilam • 2 hrs ago</p></div></div><i class="fas fa-download text-red-500 text-xs"></i></div>
                        <div class="flex justify-between items-start"><div class="flex"><div class="w-8 h-8 rounded bg-slate-100 text-slate-500 flex items-center justify-center mr-3"><i class="far fa-file-excel"></i></div><div><p class="text-xs font-bold text-slate-800">CashFlow_March_Draft.xlsx</p><p class="text-[9px] text-slate-400 mt-0.5">System Auto-Run • 5 hrs ago</p></div></div><i class="fas fa-download text-red-500 text-xs"></i></div>
                        <div class="flex justify-between items-start"><div class="flex"><div class="w-8 h-8 rounded bg-green-50 text-green-600 flex items-center justify-center mr-3"><i class="fas fa-print"></i></div><div><p class="text-xs font-bold text-slate-800">BalanceSheet_FY23.pdf</p><p class="text-[9px] text-slate-400 mt-0.5">Printed by Admin • Yesterday</p></div></div></div>
                    </div>
                    <div class="mt-8 text-center"><a href="#" class="text-[11px] font-bold text-[#005bb5]">View All Activity</a></div>
                </div>
            </div>
        </main>
    </div>
    <script>
    document.querySelectorAll("button").forEach(btn => {
        if(btn.textContent.includes("Generate Report") || btn.textContent.includes("Apply")) {
            btn.onclick = () => {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
                setTimeout(() => { window.location.reload(); }, 1500); // Simulasi pemrosesan
            };
        }
        // Redirect export ke modul GL karena laporan mengambil data dari Ledger
        if(btn.textContent.includes("Export Excel")) btn.onclick = () => window.location.href = "export.php?type=gl";
        if(btn.textContent.includes("Export PDF")) btn.onclick = () => alert("Sistem: Ekspor PDF memerlukan library tambahan (mis. FPDF/DomPDF). Gunakan Export Excel untuk raw data CSV.");
    });
</script>
</body>
</html>