<header class="flex justify-between items-center mb-8 relative z-50">
    <div class="flex items-center bg-white border border-slate-200 px-4 py-2.5 rounded-full w-[450px] shadow-sm">
        <i class="fas fa-search text-slate-400 text-sm"></i>
        <input type="text" placeholder="Search employee or data..." class="bg-transparent border-none outline-none ml-3 w-full text-[13px] text-slate-700 font-medium placeholder-slate-400">
    </div>
    
    <div class="flex items-center space-x-6">
        <button class="text-slate-500 hover:text-slate-800 text-xl transition-colors"><i class="fas fa-cog"></i></button>
        
        <div class="relative">
            <button id="notifBtn" class="text-slate-500 hover:text-slate-800 text-xl relative focus:outline-none transition-colors">
                <i class="far fa-bell"></i>
                <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
            </button>
            <div id="notifMenu" class="hidden absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-xl border border-slate-100 py-2">
                <div class="px-4 py-2 border-b border-slate-50"><p class="text-xs font-bold text-slate-800">Notifikasi</p></div>
                <div class="px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-50">
                    <p class="text-[11px] font-bold text-slate-800">Tagihan Jatuh Tempo</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Invoice PT. Bahan Baku Utama harus segera dilunasi.</p>
                </div>
                <div class="px-4 py-3 hover:bg-slate-50 cursor-pointer">
                    <p class="text-[11px] font-bold text-slate-800">Persetujuan Baru</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Budi Santoso mengajukan pengeluaran baru.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3 pl-6 border-l border-slate-200 relative">
            <div class="text-right">
                <p class="text-[14px] font-bold text-slate-800 leading-tight"><?= $_SESSION['nama'] ?? 'Nizham' ?></p>
                <p class="text-[11px] font-semibold text-slate-500"><?= $_SESSION['role'] ?? 'Finance' ?></p>
            </div>
            
            <button id="profileBtn" class="w-10 h-10 rounded-full border-[2px] border-[#005bb5] flex items-center justify-center text-[#005bb5] text-xl bg-blue-50 focus:outline-none transition-transform hover:scale-105">
                <i class="far fa-user"></i>
            </button>
            <div id="profileMenu" class="hidden absolute right-0 top-12 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2">
                <a href="#" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-[#005bb5] font-medium"><i class="far fa-id-badge mr-2"></i> Profil Akun</a>
                <a href="#" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-[#005bb5] font-medium"><i class="fas fa-sliders-h mr-2"></i> Pengaturan</a>
                <div class="border-t border-slate-100 my-1"></div>
                <a href="logout.php" class="block px-4 py-2 text-xs text-red-600 hover:bg-red-50 font-bold"><i class="fas fa-sign-out-alt mr-2"></i> Keluar</a>
            </div>
        </div>
    </div>
</header>

<script>
    // Script fungsional interaktivitas dropdown menu
    const notifBtn = document.getElementById('notifBtn');
    const notifMenu = document.getElementById('notifMenu');
    const profileBtn = document.getElementById('profileBtn');
    const profileMenu = document.getElementById('profileMenu');

    notifBtn.addEventListener('click', () => {
        notifMenu.classList.toggle('hidden');
        profileMenu.classList.add('hidden');
    });

    profileBtn.addEventListener('click', () => {
        profileMenu.classList.toggle('hidden');
        notifMenu.classList.add('hidden');
    });

    // Menutup menu jika klik di luar area
    window.addEventListener('click', (e) => {
        if (!notifBtn.contains(e.target) && !notifMenu.contains(e.target)) notifMenu.classList.add('hidden');
        if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) profileMenu.classList.add('hidden');
    });
</script>