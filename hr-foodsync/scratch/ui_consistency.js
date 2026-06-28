const fs = require('fs');
const path = require('path');

const dir = 'c:/Users/organizer/Documents/hr-foodsync';
const files = fs.readdirSync(dir).filter(f => f.endsWith('.html'));

const settingsDropdownHTML = 
                    <div id="settingsDropdown" class="hidden absolute right-0 mt-3 w-[260px] bg-white rounded-3xl shadow-fly border border-gray-100 py-3 fade-in z-50">
                        <h3 class="px-6 py-2 text-[14px] font-black text-gray-400 uppercase tracking-widest mb-1">Pengaturan</h3>
                        <a href="profil.html" class="flex items-center px-6 py-3 hover:bg-gray-50 transition cursor-pointer"><i class="fa-regular fa-circle-user text-lg text-gray-400 w-8"></i><p class="text-[14px] font-bold text-gray-700">Profil Saya</p></a>
                        <a href="keamanan.html" class="flex items-center px-6 py-3 hover:bg-gray-50 transition cursor-pointer"><i class="fa-solid fa-shield-halved text-lg text-gray-400 w-8"></i><p class="text-[14px] font-bold text-gray-700">Keamanan</p></a>
                        <div class="my-2 border-t border-gray-50"></div>
                        <a href="#" onclick="prosesLogout()" class="flex items-center px-6 py-3 hover:bg-red-50 transition cursor-pointer group"><i class="fa-solid fa-arrow-right-from-bracket text-lg text-red-500 w-8 group-hover:translate-x-1 transition-transform"></i><span class="text-[14px] font-bold text-red-600">Logout</span></a>
                    </div>
;

const sidebarLogoutHTML = \<a href="#" onclick="window.prosesLogout()" class="w-full flex items-center justify-center px-4 py-3 bg-red-50 text-red-600 rounded-2xl hover:bg-red-100 transition-colors font-extrabold text-[13px]"><i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Logout</a>\;

files.forEach(f => {
    let content = fs.readFileSync(path.join(dir, f), 'utf-8');
    
    // Replace settings dropdown
    content = content.replace(/<div id="settingsDropdown"([\\s\\S]*?)<\\/div>\\s*<\\/div>/g, settingsDropdownHTML.trim() + '\\n                </div>');
    
    // Replace logout buttons in sidebar
    content = content.replace(/<a href="#" onclick="window\\.prosesLogout\\(\\)"[^>]*>.*?<\\/a>/g, sidebarLogoutHTML);
    content = content.replace(/<a href="#" onclick="prosesLogout\\(\\)"[^>]*>.*?<\\/a>/g, sidebarLogoutHTML);
    
    // Sidebar text labels replacements (no href changes, no logic changes)
    content = content.replace(/>\\s*Cuti &amp; Izin\\s*<\\/a>/g, '> Pengajuan Cuti</a>');
    content = content.replace(/>\\s*Klaim Dana\\s*<\\/a>/g, '> Reimbursement</a>');
    content = content.replace(/>\\s*HRD Data\\s*<\\/a>/g, '> HRD</a>');
    content = content.replace(/>\\s*Analytics\\s*<\\/a>/g, '> Laporan</a>');
    content = content.replace(/>\\s*Laporan &amp; Analitik\\s*<\\/a>/g, '> Laporan</a>');
    
    // Fix icons
    content = content.replace(/fa-user-check/g, 'fa-user-shield');
    content = content.replace(/fa-square-check/g, 'fa-users-gear');
    content = content.replace(/fa-chart-simple/g, 'fa-chart-pie');

    // Title tag for Laporan consistency
    content = content.replace(/<title>.*?Laporan.*?<\\/title>/i, '<title>Laporan & Analitik - FoodSync HRMS</title>');

    fs.writeFileSync(path.join(dir, f), content);
});
console.log("Done phase 2!");
