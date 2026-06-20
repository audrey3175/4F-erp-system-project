<?php 
session_start();
include 'koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Finance') { header("Location: login.php"); exit(); }
$page = 'ar'; 
$query = mysqli_query($conn, "SELECT * FROM accounts_receivable ORDER BY due_date ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Accounts Receivable - FoodSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#f4f7fa] antialiased text-slate-800">
    <div class="flex min-h-screen p-4 gap-6 w-full">
        
        <?php include('components/sidebar.php'); ?>

        <main class="flex-1 flex flex-col pt-2 pr-4 w-full overflow-x-hidden relative">
            <?php include('components/navbar.php'); ?>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 mt-2">
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                    <div class="flex justify-between items-start mb-4"><span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">TOTAL RECEIVABLE</span><i class="fas fa-wallet text-green-500 text-lg"></i></div>
                    <h2 class="text-3xl font-black text-slate-800">IDR 4.25B</h2>
                    <p class="text-[11px] font-bold text-green-500 mt-2"><i class="fas fa-arrow-trend-up mr-1"></i> +5.2% <span class="text-slate-400 font-medium ml-1">vs last month</span></p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                    <div class="flex justify-between items-start mb-4"><span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">OVERDUE RECEIVABLE</span><i class="fas fa-exclamation-triangle text-red-500 text-lg"></i></div>
                    <h2 class="text-3xl font-black text-slate-800">IDR 850M</h2>
                    <p class="text-[11px] font-bold text-red-500 mt-2"><i class="fas fa-arrow-trend-up mr-1"></i> +1.1% <span class="text-slate-400 font-medium ml-1">vs last month</span></p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                    <div class="flex justify-between items-start mb-4"><span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">COLLECTION RATE</span><i class="fas fa-chart-pie text-red-700 text-lg"></i></div>
                    <h2 class="text-3xl font-black text-slate-800 mb-3">82.4%</h2>
                    <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden"><div class="bg-red-700 h-full rounded-full" style="width: 82.4%"></div></div>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                    <div class="flex justify-between items-start mb-4"><span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">OUTSTANDING INVOICES</span><i class="fas fa-file-alt text-[#005bb5] text-lg"></i></div>
                    <h2 class="text-3xl font-black text-slate-800">142</h2>
                    <p class="text-[11px] font-medium text-slate-500 mt-2">28 critical overdue</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-7 py-6 border-b border-slate-100 flex justify-between items-end">
                    <div class="flex space-x-4 items-end">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1.5 uppercase tracking-widest">Date Range</label>
                            <div class="border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-medium text-slate-600 flex items-center bg-[#f8fafc]"><i class="far fa-calendar-alt mr-2 text-slate-400"></i> Oct 1 - Oct 31, 2026</div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1.5 uppercase tracking-widest">Customer Filter</label>
                            <select class="border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-medium text-slate-600 outline-none w-48 bg-[#f8fafc]"><option>All Customers</option></select>
                        </div>
                        <button class="text-red-600 text-[13px] font-bold px-3 py-2.5 flex items-center hover:bg-red-50 rounded-xl transition-colors"><i class="fas fa-filter mr-2"></i> More Filters</button>
                    </div>
                    <div class="flex space-x-4 items-center">
                        <button onclick="window.location.href='export.php?type=ar'" class="text-red-600 text-[13px] font-bold flex items-center hover:bg-red-50 px-4 py-2.5 rounded-xl transition-colors"><i class="fas fa-download mr-2"></i> Export</button>
                        <button onclick="openModal()" class="bg-[#005bb5] hover:bg-blue-800 transition-colors text-white px-5 py-2.5 rounded-xl text-[13px] font-bold shadow-md"><i class="fas fa-plus mr-2"></i> Record Payment</button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px]">
                        <thead class="bg-[#eef2f6] text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-7 py-4 w-10"><input type="checkbox" class="rounded border-slate-300"></th>
                                <th class="px-7 py-4">Invoice Number</th><th class="px-7 py-4">Customer</th><th class="px-7 py-4">Invoice Date</th><th class="px-7 py-4">Due Date</th><th class="px-7 py-4 text-right">Amount</th><th class="px-7 py-4 text-right">Paid Amount</th><th class="px-7 py-4 text-right">Outstanding</th><th class="px-7 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            <?php while($row = mysqli_fetch_assoc($query)): 
                                $is_overdue = ($row['status'] == 'Overdue');
                            ?>
                            <tr class="hover:bg-slate-50 <?= $row['status'] == 'Partially Paid' ? 'bg-green-50/30' : '' ?>">
                                <td class="px-7 py-4"><input type="checkbox" class="rounded border-slate-300"></td>
                                <td class="px-7 py-4 font-bold text-green-600"><?= $row['invoice_number'] ?></td>
                                <td class="px-7 py-4 font-bold text-slate-900"><?= $row['customer'] ?></td>
                                <td class="px-7 py-4 text-slate-500"><?= date('M d, Y', strtotime($row['invoice_date'])) ?></td>
                                <td class="px-7 py-4 <?= $is_overdue ? 'text-red-500 font-bold' : 'text-slate-500' ?>"><?= date('M d, Y', strtotime($row['due_date'])) ?></td>
                                <td class="px-7 py-4 text-right font-bold text-slate-900">IDR <br> <?= number_format($row['amount'],0,',','.') ?></td>
                                <td class="px-7 py-4 text-right text-slate-500">IDR <br> <?= $row['paid_amount'] > 0 ? number_format($row['paid_amount'],0,',','.') : '0' ?></td>
                                <td class="px-7 py-4 text-right font-black text-slate-900">IDR <br> <?= number_format($row['amount'] - $row['paid_amount'],0,',','.') ?></td>
                                <td class="px-7 py-4 text-center">
                                    <?php 
                                    $bg = 'bg-slate-100 text-slate-600';
                                    if($row['status'] == 'Overdue') $bg = 'bg-red-50 text-red-600 border border-red-100';
                                    elseif($row['status'] == 'Partially Paid') $bg = 'bg-red-50 text-red-500 border border-red-100';
                                    elseif($row['status'] == 'Paid') $bg = 'bg-green-50 text-green-700 border border-green-100';
                                    echo "<span class='px-3 py-1 rounded-full text-[11px] font-bold $bg'>{$row['status']}</span>";
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="paymentModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] flex items-center justify-center">
                <div class="bg-white rounded-3xl w-[500px] shadow-2xl overflow-hidden transform transition-all">
                    <div class="px-7 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                        <h3 class="text-[16px] font-bold text-slate-900">Catat Pembayaran Masuk</h3>
                        <button onclick="closeModal()" class="text-slate-400 hover:text-red-500 text-xl transition-colors"><i class="fas fa-times"></i></button>
                    </div>
                    <form action="backend_process.php" method="POST" class="p-7 bg-[#f8fafc]">
                        <input type="hidden" name="action" value="record_payment">
                        
                        <div class="mb-5">
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nomor Invoice</label>
                            <select name="invoice_number" required class="w-full bg-white border border-slate-200 px-4 py-3.5 rounded-xl text-[13px] font-medium text-slate-800 outline-none focus:border-[#005bb5] focus:ring-1 focus:ring-[#005bb5] transition-all">
                                <?php 
                                // Query ulang untuk modal
                                $inv_query = mysqli_query($conn, "SELECT invoice_number, customer FROM accounts_receivable WHERE status != 'Paid'");
                                while($inv = mysqli_fetch_assoc($inv_query)) {
                                    echo "<option value='{$inv['invoice_number']}'>{$inv['invoice_number']} - {$inv['customer']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="mb-8">
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Nominal Pembayaran (IDR)</label>
                            <input type="number" name="payment_amount" required placeholder="Contoh: 50000000" class="w-full bg-white border border-slate-200 px-4 py-3.5 rounded-xl text-[14px] font-black text-slate-800 outline-none focus:border-[#005bb5] focus:ring-1 focus:ring-[#005bb5] transition-all">
                        </div>
                        
                        <div class="flex justify-end space-x-3 border-t border-slate-200 pt-5">
                            <button type="button" onclick="closeModal()" class="px-6 py-3 text-[13px] font-bold text-slate-600 hover:bg-slate-200 rounded-xl transition-colors">Batal</button>
                            <button type="submit" class="bg-[#005bb5] hover:bg-blue-800 transition-colors text-white px-8 py-3 text-[13px] font-bold rounded-xl shadow-md">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script>
        function openModal() {
            document.getElementById('paymentModal').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
        // Menutup modal jika klik area luar modal
        window.onclick = function(event) {
            var modal = document.getElementById('paymentModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>