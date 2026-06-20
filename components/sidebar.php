<?php
$page = $page ?? 'dashboard';
$menu_items = [
    'dashboard' => ['icon' => 'fas fa-th-large', 'label' => 'Dashboard', 'link' => 'dashboard.php'],
    'ar' => ['icon' => 'fas fa-file-invoice-dollar', 'label' => 'Accounts Receivable', 'link' => 'accounts-receivable.php'],
    'ap' => ['icon' => 'fas fa-money-check-alt', 'label' => 'Accounts Payable', 'link' => 'accounts-payable.php'],
    'treasury' => ['icon' => 'fas fa-landmark', 'label' => 'Treasury', 'link' => 'treasury.php'],
    'gl' => ['icon' => 'fas fa-book', 'label' => 'General Ledger', 'link' => 'general-ledger.php'],
    'approval' => ['icon' => 'fas fa-check-double', 'label' => 'Approval', 'link' => 'approval.php'],
    'reports' => ['icon' => 'fas fa-chart-line', 'label' => 'Reports', 'link' => 'reports.php'],
];
?>
<aside class="w-[260px] bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] flex flex-col h-[calc(100vh-2rem)] sticky top-4 flex-shrink-0 border border-slate-100">
    <div class="p-8 pb-4">
        <h2 class="text-3xl font-black text-[#004085] italic tracking-tight leading-none">Indofood</h2>
        <p class="text-[7px] font-bold text-[#004085] tracking-widest uppercase mt-1 border-t-2 border-[#004085] pt-1">THE SYMBOL OF QUALITY FOODS</p>
    </div>
    <div class="px-4 flex-1 overflow-y-auto mt-4">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-4">MENU</p>
        <nav class="space-y-1">
            <?php foreach($menu_items as $key => $item): ?>
            <?php $active = ($page == $key) ? 'bg-[#eef2f6] text-[#004085] font-bold' : 'text-slate-500 hover:bg-slate-50 font-medium'; ?>
            <a href="<?= $item['link'] ?>" class="flex items-center px-4 py-3 rounded-xl text-[13px] transition-all <?= $active ?>">
                <i class="<?= $item['icon'] ?> w-5 text-center mr-3 text-lg"></i> <?= $item['label'] ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </div>
    <div class="p-6">
        <a href="#" class="flex items-center px-4 py-2 text-[13px] font-medium text-slate-500 hover:text-slate-800 mb-4">
            <i class="far fa-question-circle w-5 text-center mr-3 text-lg"></i> Help Center
        </a>
        <a href="logout.php" class="flex items-center justify-center w-full bg-[#b3001b] hover:bg-red-800 text-white font-bold py-3 rounded-xl text-[13px] shadow-md transition-all">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
    </div>
</aside>