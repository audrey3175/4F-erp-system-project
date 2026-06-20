<?php 
session_start();
include 'koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Finance') { header("Location: login.php"); exit(); }
$page = 'approval'; 
$query = mysqli_query($conn, "SELECT * FROM approvals");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Approval Center - FoodSync</title>
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

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900">Approval Center</h1>
                <p class="text-[13px] text-slate-500 mt-1">Review and process pending financial documents.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute bottom-4 left-6 right-6 h-1 bg-[#005bb5] rounded"></div>
                    <h3 class="text-lg font-bold text-slate-800">Pending Payment</h3>
                    <p class="text-3xl font-black text-[#005bb5] mt-2">12 <span class="text-[13px] font-medium text-slate-500">documents</span></p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute bottom-4 left-6 right-6 h-1 bg-red-600 rounded"></div>
                    <h3 class="text-lg font-bold text-slate-800">Pending Journal</h3>
                    <p class="text-3xl font-black text-red-600 mt-2">5 <span class="text-[13px] font-medium text-slate-500">entries</span></p>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute bottom-4 left-6 right-6 h-1 bg-red-600 rounded"></div>
                    <h3 class="text-lg font-bold text-slate-800">Pending AP</h3>
                    <p class="text-3xl font-black text-red-600 mt-2">3 <span class="text-[13px] font-medium text-slate-500">invoices</span></p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-[#f8fafc]">
                    <h3 class="text-[15px] font-bold text-slate-800 flex items-center"><i class="fas fa-list-alt text-green-500 mr-2 text-lg"></i> Approval Queue</h3>
                    <div class="flex space-x-3">
                        <button class="border border-slate-200 bg-white px-4 py-2 rounded-lg text-xs font-bold text-slate-600 flex items-center"><i class="fas fa-filter mr-2"></i> Filter</button>
                        <button class="border border-red-200 bg-white text-red-600 px-4 py-2 rounded-lg text-xs font-bold shadow-sm flex items-center"><i class="fas fa-download mr-2"></i> Export</button>
                    </div>
                </div>
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-white text-slate-400 font-bold text-[11px] border-b border-slate-200">
                        <tr><th class="px-6 py-4">Document Number</th><th class="px-6 py-4">Document Type</th><th class="px-6 py-4">Requestor</th><th class="px-6 py-4">Submission Date</th><th class="px-6 py-4">Status</th><th class="px-6 py-4 text-right">Amount</th><th class="px-6 py-4 text-center">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700 bg-slate-50/30">
                        <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-red-600"><?= $row['doc_number'] ?></td>
                            <td class="px-6 py-4 font-bold text-slate-800"><?= $row['doc_type'] ?></td>
                            <td class="px-6 py-4 flex items-center"><div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 font-bold text-[10px] flex items-center justify-center mr-3"><?= substr($row['requestor'],0,2) ?></div> <?= $row['requestor'] ?></td>
                            <td class="px-6 py-4 text-slate-500"><?= date('M d, Y', strtotime($row['submission_date'])) ?></td>
                            <td class="px-6 py-4">
                                <?php 
                                $status_html = ($row['status'] == 'Revision Req.') ? '<span class="text-red-500 text-xs font-bold flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-2"></span>Revision Req.</span>' : '<span class="text-[#005bb5] text-xs font-bold flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-[#005bb5] mr-2"></span>Pending</span>';
                                echo $status_html;
                                ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold"><?= $row['amount'] ? 'Rp '.number_format($row['amount'],0,',','.') : '-' ?></td>
                            <td class="px-6 py-4 text-center">
    <form action="backend_process.php" method="POST" class="inline-block">
        <input type="hidden" name="action" value="process_approval">
        <input type="hidden" name="doc_number" value="<?= $row['doc_number'] ?>">
        <button type="submit" name="status_update" value="Approved" class="bg-green-500 hover:bg-green-600 text-white text-[10px] font-bold px-2 py-1 rounded mb-1 w-full">Approve</button>
        <button type="submit" name="status_update" value="Rejected" class="bg-red-500 hover:bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded w-full">Reject</button>
    </form>
</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script>
    document.querySelectorAll("button").forEach(btn => {
        if(btn.textContent.includes("Export")) btn.onclick = () => window.location.href = "export.php?type=approval";
    });
</script>
</body>
</html>