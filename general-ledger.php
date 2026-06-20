<?php 
session_start();
include 'koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Finance') { header("Location: login.php"); exit(); }
$page = 'gl'; 
$query = mysqli_query($conn, "SELECT * FROM general_ledger");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>General Ledger - FoodSync</title>
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
                    <h1 class="text-2xl font-bold text-slate-900">General Ledger</h1>
                    <p class="text-[13px] text-slate-500 mt-1">Manage and review central accounting records.</p>
                </div>
                <div class="flex space-x-3">
                    <button class="border border-red-200 text-red-600 px-4 py-2 rounded-lg text-xs font-bold bg-white"><i class="fas fa-download mr-2"></i> Export</button>
                    <button class="bg-[#005bb5] text-white px-4 py-2 rounded-lg text-xs font-bold shadow-md"><i class="fas fa-plus mr-2"></i> Create Manual Journal</button>
                </div>
            </div>

            <div class="flex space-x-8 border-b border-slate-200 text-[13px] font-bold text-slate-400 mb-6 px-2">
                <button class="pb-3 hover:text-slate-600">Auto Journal</button>
                <button class="text-[#005bb5] border-b-2 border-[#005bb5] pb-3">General Ledger</button>
                <button class="pb-3 hover:text-slate-600">Manual Journal</button>
                <button class="pb-3 hover:text-slate-600">COA</button>
            </div>

            <div class="flex space-x-4 mb-6">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-slate-500 mb-1.5">Date Range</label>
                    <div class="bg-white border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] font-medium text-slate-700 flex items-center"><i class="far fa-calendar-alt mr-2 text-slate-400"></i> Oct 01, 2026 - Oct 31, 2026</div>
                </div>
                <div class="w-64">
                    <label class="block text-[11px] font-bold text-slate-500 mb-1.5">Account Code</label>
                    <select class="bg-white border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] font-medium text-slate-700 w-full outline-none"><option>All Accounts</option></select>
                </div>
                <div class="w-48">
                    <label class="block text-[11px] font-bold text-slate-500 mb-1.5">Status</label>
                    <select class="bg-white border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] font-medium text-slate-700 w-full outline-none"><option>All Status</option></select>
                </div>
                <div class="flex items-end">
                    <button class="border border-slate-200 bg-white text-slate-600 px-4 py-2.5 rounded-lg text-[13px] font-bold flex items-center"><i class="fas fa-filter mr-2"></i> More Filters</button>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#eef2f6] text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr><th class="px-6 py-4 w-10"><input type="checkbox" class="rounded border-slate-300"></th><th class="px-6 py-4">Journal Number</th><th class="px-6 py-4">Date</th><th class="px-6 py-4">Account Code</th><th class="px-6 py-4">Account Name</th><th class="px-6 py-4 text-right">Debit</th><th class="px-6 py-4 text-right">Credit</th><th class="px-6 py-4 text-center">Status</th><th class="px-6 py-4 text-center">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4"><input type="checkbox" class="rounded border-slate-300"></td>
                            <td class="px-6 py-4 font-bold text-[#005bb5]"><?= $row['journal_number'] ?></td>
                            <td class="px-6 py-4 text-slate-500"><?= $row['date'] ?></td>
                            <td class="px-6 py-4 font-bold"><?= $row['account_code'] ?></td>
                            <td class="px-6 py-4"><?= $row['account_name'] ?></td>
                            <td class="px-6 py-4 text-right"><?= $row['debit'] > 0 ? 'Rp <br>'.number_format($row['debit'],0,',','.') : '-' ?></td>
                            <td class="px-6 py-4 text-right"><?= $row['credit'] > 0 ? 'Rp <br>'.number_format($row['credit'],0,',','.') : '-' ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php 
                                $bg = ($row['status'] == 'Posted') ? 'bg-[#005bb5] text-white' : 'bg-slate-200 text-slate-600';
                                echo "<span class='px-3 py-1 rounded-full text-[10px] font-bold $bg'>{$row['status']}</span>";
                                ?>
                            </td>
                            <td class="px-6 py-4 text-center"></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <div id="journalModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] flex items-center justify-center">
    <div class="bg-white rounded-3xl w-[500px] shadow-2xl overflow-hidden">
        <div class="px-7 py-5 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-[16px] font-bold text-slate-900">Create Manual Journal</h3>
            <button onclick="document.getElementById('journalModal').classList.add('hidden')" class="text-slate-400 hover:text-red-500 text-xl"><i class="fas fa-times"></i></button>
        </div>
        <form action="backend_process.php" method="POST" class="p-7 bg-[#f8fafc]">
            <input type="hidden" name="action" value="create_journal">
            <div class="mb-4">
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-2">Account Code & Name</label>
                <div class="flex space-x-2">
                    <input type="text" name="account_code" required placeholder="Code" class="w-1/3 bg-white border border-slate-200 px-4 py-3 rounded-xl text-[13px] font-medium outline-none">
                    <input type="text" name="account_name" required placeholder="Account Name" class="w-2/3 bg-white border border-slate-200 px-4 py-3 rounded-xl text-[13px] font-medium outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-2">Type</label>
                    <select name="type" class="w-full bg-white border border-slate-200 px-4 py-3 rounded-xl text-[13px] font-medium outline-none"><option value="debit">Debit</option><option value="credit">Credit</option></select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-2">Amount</label>
                    <input type="number" name="amount" required placeholder="0" class="w-full bg-white border border-slate-200 px-4 py-3 rounded-xl text-[14px] font-black outline-none">
                </div>
            </div>
            <div class="flex justify-end space-x-3 border-t border-slate-200 pt-5">
                <button type="submit" class="bg-[#005bb5] text-white px-8 py-3 text-[13px] font-bold rounded-xl shadow-md">Post Journal</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll("button").forEach(btn => {
        if(btn.textContent.includes("Create Manual Journal")) btn.onclick = () => document.getElementById('journalModal').classList.remove('hidden');
        if(btn.textContent.includes("Export")) btn.onclick = () => window.location.href = "export.php?type=gl";
    });
</script>
</body>
</html>