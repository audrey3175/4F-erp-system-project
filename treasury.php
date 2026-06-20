<?php 
session_start();
include 'koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Finance') { header("Location: login.php"); exit(); }
$page = 'treasury'; 
$query_trans = mysqli_query($conn, "SELECT * FROM recent_transactions ORDER BY trans_date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Treasury - FoodSync</title>
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
                    <h1 class="text-2xl font-bold text-slate-900">Cash Management Overview</h1>
                    <p class="text-[13px] text-slate-500 mt-1">Real-time visibility into corporate liquidity and cash positions across all banking partners.</p>
                </div>
                <div class="flex space-x-3">
                    <button class="border border-slate-200 px-4 py-2 rounded-lg text-xs font-bold text-[#005bb5] bg-white"><i class="fas fa-download mr-2"></i> Export</button>
                    <button class="bg-[#005bb5] text-white px-4 py-2 rounded-lg text-xs font-bold shadow-md"><i class="fas fa-exchange-alt mr-2"></i> Create Bank Transfer</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden">
                    <span class="text-[11px] font-bold text-slate-400 uppercase">CURRENT CASH BALANCE</span>
                    <h2 class="text-[28px] font-black tracking-tight mt-2">IDR <br> 482.5B</h2>
                    <p class="text-[11px] font-bold text-green-500 mt-2"><i class="fas fa-arrow-trend-up"></i> ~2.4% <span class="text-slate-400 font-medium">vs last week</span></p>
                    <i class="fas fa-university absolute top-6 right-6 text-2xl text-blue-100"></i>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                    <span class="text-[11px] font-bold text-slate-400 uppercase">INCOMING CASH (TODAY)</span>
                    <h2 class="text-[28px] font-black tracking-tight mt-2">IDR 14.2B</h2>
                    <div class="flex justify-between items-center mt-4">
                        <div class="w-2/3 bg-slate-200 h-1.5 rounded-full"><div class="bg-red-700 h-full rounded-full" style="width: 65%"></div></div>
                        <span class="text-[10px] font-medium text-slate-500">65% Expected</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                    <span class="text-[11px] font-bold text-slate-400 uppercase">OUTGOING CASH (TODAY)</span>
                    <h2 class="text-[28px] font-black tracking-tight mt-2">IDR 8.7B</h2>
                    <div class="flex justify-between items-center mt-4">
                        <div class="w-2/3 bg-slate-200 h-1.5 rounded-full"><div class="bg-red-700 h-full rounded-full" style="width: 40%"></div></div>
                        <span class="text-[10px] font-medium text-slate-500">40% Scheduled</span>
                    </div>
                </div>
                <div class="bg-blue-50 p-6 rounded-3xl border border-blue-100 shadow-sm relative">
                    <span class="text-[11px] font-bold text-[#005bb5] uppercase">30-DAY FORECAST</span>
                    <h2 class="text-[28px] font-black tracking-tight mt-2 text-[#005bb5]">IDR <br> 510.8B</h2>
                    <p class="text-[11px] font-medium text-[#005bb5]/70 mt-2">Projected Net Liquidity</p>
                    <i class="fas fa-chart-line absolute top-6 right-6 text-2xl text-[#005bb5]"></i>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6 mb-8">
                <div class="col-span-2 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm min-h-[300px] flex flex-col justify-center items-center">
                    <p class="text-sm font-bold text-slate-300 border-2 border-dashed border-slate-200 p-10 rounded-xl w-full text-center">[ Grafik Cash Flow Analysis: Inflows vs Outflows (Bar Chart) ]</p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-4">
                        <h3 class="font-bold text-[#004085]">Bank Balances</h3><a href="#" class="text-[11px] font-bold text-blue-600">View All</a>
                    </div>
                    <div class="space-y-4 flex-1">
                        <div class="flex justify-between items-center"><div class="flex items-center"><span class="bg-red-600 text-white font-bold text-[10px] w-8 h-8 flex items-center justify-center rounded mr-3">BCA</span><div><p class="text-[11px] font-bold text-slate-800">Bank Central Asia</p><p class="text-[9px] text-slate-400">*** 4921</p></div></div><div class="text-right"><p class="text-[11px] font-bold text-slate-800">IDR 150.2B</p><p class="text-[9px] font-bold text-green-500">↑ 1.2%</p></div></div>
                        <div class="flex justify-between items-center"><div class="flex items-center"><span class="bg-blue-600 text-white font-bold text-[10px] w-8 h-8 flex items-center justify-center rounded mr-3">MDR</span><div><p class="text-[11px] font-bold text-slate-800">Bank Mandiri</p><p class="text-[9px] text-slate-400">*** 8832</p></div></div><div class="text-right"><p class="text-[11px] font-bold text-slate-800">IDR 120.5B</p><p class="text-[9px] font-bold text-red-500">↓ 0.5%</p></div></div>
                        <div class="flex justify-between items-center"><div class="flex items-center"><span class="bg-orange-500 text-white font-bold text-[10px] w-8 h-8 flex items-center justify-center rounded mr-3">BNI</span><div><p class="text-[11px] font-bold text-slate-800">Bank Negara Indonesia</p><p class="text-[9px] text-slate-400">*** 1109</p></div></div><div class="text-right"><p class="text-[11px] font-bold text-slate-800">IDR 85.0B</p><p class="text-[9px] text-slate-400">-</p></div></div>
                    </div>
                    <button class="w-full mt-4 text-[11px] font-bold text-slate-500 border border-slate-200 py-2 rounded-lg hover:bg-slate-50">+ Add Bank Account</button>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                    <div><h3 class="text-sm font-bold text-[#004085]">Recent Transactions</h3><p class="text-[11px] text-slate-400">Real-time ledger updates across monitored accounts.</p></div>
                    <div class="flex space-x-2"><button class="border border-slate-200 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-bold"><i class="fas fa-filter mr-2"></i>All Types</button></div>
                </div>
                <table class="w-full text-left text-[11px]">
                    <thead class="bg-[#eef2f6] text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr><th class="px-6 py-3 w-40">Transaction Date</th><th class="px-6 py-3">Bank</th><th class="px-6 py-3">Description</th><th class="px-6 py-3 text-center">Type</th><th class="px-6 py-3 text-right">Amount</th><th class="px-6 py-3 text-center">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php while($row = mysqli_fetch_assoc($query_trans)): 
                            $date_fmt = date_format(date_create($row['trans_date']), "Y-m-d H:i");
                            $amt_fmt = number_format($row['amount'], 0, ',', '.');
                            $is_in = ($row['type'] == 'in');
                            $amt_display = $is_in ? "<span class='text-[#005bb5] font-bold'>+ IDR<br>$amt_fmt</span>" : "<span class='text-slate-800 font-bold'>- IDR<br>$amt_fmt</span>";
                            $icon_type = $is_in ? "<i class='fas fa-arrow-down text-green-500 bg-green-50 p-1.5 rounded'></i>" : "<i class='fas fa-arrow-up text-red-500 bg-red-50 p-1.5 rounded'></i>";
                        ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 text-slate-500"><?= $date_fmt ?></td>
                            <td class="px-6 py-3 font-bold text-slate-800">BCA - 4921</td>
                            <td class="px-6 py-3"><?= $row['description'] ?></td>
                            <td class="px-6 py-3 text-center"><?= $icon_type ?></td>
                            <td class="px-6 py-3 text-right"><?= $amt_display ?></td>
                            <td class="px-6 py-3 text-center"><span class="bg-slate-200 text-slate-600 px-3 py-1 rounded text-[10px] font-bold"><?= $row['status'] ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <div id="transferModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] flex items-center justify-center">
    <div class="bg-white rounded-3xl w-[500px] shadow-2xl overflow-hidden">
        <div class="px-7 py-5 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-[16px] font-bold text-slate-900">Create Bank Transfer</h3>
            <button onclick="document.getElementById('transferModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500 text-xl"><i class="fas fa-times"></i></button>
        </div>
        <form action="backend_process.php" method="POST" class="p-7 bg-[#f8fafc]">
            <input type="hidden" name="action" value="create_transfer">
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-2">From Bank</label>
                    <select name="from_bank" class="w-full bg-white border border-slate-200 px-4 py-3 rounded-xl text-[13px] font-medium outline-none"><option>BCA - 4921</option><option>Mandiri - 8832</option></select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-2">To Bank</label>
                    <select name="to_bank" class="w-full bg-white border border-slate-200 px-4 py-3 rounded-xl text-[13px] font-medium outline-none"><option>BNI - 1109</option><option>Citibank - 5543</option></select>
                </div>
            </div>
            <div class="mb-8">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-2">Amount (IDR)</label>
                <input type="number" name="amount" required placeholder="0" class="w-full bg-white border border-slate-200 px-4 py-3 rounded-xl text-[14px] font-black outline-none">
            </div>
            <div class="flex justify-end space-x-3 border-t border-slate-200 pt-5">
                <button type="submit" class="bg-[#005bb5] text-white px-8 py-3 text-[13px] font-bold rounded-xl shadow-md">Execute Transfer</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelector('button:contains("Create Bank Transfer")').onclick = () => document.getElementById('transferModal').classList.remove('hidden');
    document.querySelector('button:contains("Export")').onclick = () => window.location.href = "export.php?type=treasury";
    jQueryExpr = HTMLElement.prototype.matches || HTMLElement.prototype.webkitMatchesSelector;
    document.querySelectorAll("button").forEach(btn => {
        if(btn.textContent.includes("Create Bank Transfer")) btn.onclick = () => document.getElementById('transferModal').classList.remove('hidden');
        if(btn.textContent.includes("Export")) btn.onclick = () => window.location.href = "export.php?type=treasury";
    });
</script>
</body>
</html>