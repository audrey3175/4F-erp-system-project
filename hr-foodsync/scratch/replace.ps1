$dir = "c:\Users\organizer\Documents\hr-foodsync"
$files = Get-ChildItem -Path $dir -Filter "*.html" | Where-Object { $_.Name -ne 'generate_dummy.html' -and $_.Name -ne 'index.html' }

$settingsDropdownHTML = @"
                    <div id="settingsDropdown" class="hidden absolute right-0 mt-3 w-[260px] bg-white rounded-3xl shadow-fly border border-gray-100 py-3 fade-in z-50">
                        <h3 class="px-6 py-2 text-[14px] font-black text-gray-400 uppercase tracking-widest mb-1">Pengaturan</h3>
                        <a href="profil.html" class="flex items-center px-6 py-3 hover:bg-gray-50 transition cursor-pointer"><i class="fa-regular fa-circle-user text-lg text-gray-400 w-8"></i><p class="text-[14px] font-bold text-gray-700">Profil Saya</p></a>
                        <a href="keamanan.html" class="flex items-center px-6 py-3 hover:bg-gray-50 transition cursor-pointer"><i class="fa-solid fa-shield-halved text-lg text-gray-400 w-8"></i><p class="text-[14px] font-bold text-gray-700">Keamanan</p></a>
                        <div class="my-2 border-t border-gray-50"></div>
                        <a href="#" onclick="prosesLogout()" class="flex items-center px-6 py-3 hover:bg-red-50 transition cursor-pointer group"><i class="fa-solid fa-arrow-right-from-bracket text-lg text-red-500 w-8 group-hover:translate-x-1 transition-transform"></i><span class="text-[14px] font-bold text-red-600">Logout</span></a>
                    </div>
"@

$sidebarLogoutHTML = '<a href="#" onclick="window.prosesLogout()" class="w-full flex items-center justify-center px-4 py-3 bg-red-50 text-red-600 rounded-2xl hover:bg-red-100 transition-colors font-extrabold text-[13px]"><i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Logout</a>'

foreach ($file in $files) {
    $content = [System.IO.File]::ReadAllText($file.FullName)
    
    # settingsDropdown block
    $content = [System.Text.RegularExpressions.Regex]::Replace($content, '(?s)<div id="settingsDropdown"[\s\S]*?</div>\s*</div>', $settingsDropdownHTML + "`n                </div>")
    
    # replace sidebar logout buttons (which typically use w-full or flex inside border-t)
    $content = [System.Text.RegularExpressions.Regex]::Replace($content, '(?s)<div class="[^"]*border-t[^"]*">\s*<a href="#" onclick="(window\.)?prosesLogout\(\)".*?</a>\s*</div>', "<div class=`"p-4 border-t border-gray-100`">`n            $sidebarLogoutHTML`n        </div>")

    # Replace labels
    $content = $content -replace '> Cuti &amp; Izin</a>', '> Pengajuan Cuti</a>'
    $content = $content -replace '>\s*Cuti &amp; Izin\s*</a>', '> Pengajuan Cuti</a>'
    $content = $content -replace '>\s*Klaim Dana\s*</a>', '> Reimbursement</a>'
    $content = $content -replace '>\s*HRD Data\s*</a>', '> HRD</a>'
    $content = $content -replace '>\s*Analytics\s*</a>', '> Laporan</a>'
    $content = $content -replace '>\s*Laporan &amp; Analitik\s*</a>', '> Laporan</a>'
    
    # Fix icons
    $content = $content -replace 'fa-user-check', 'fa-user-shield'
    $content = $content -replace 'fa-square-check', 'fa-users-gear'
    $content = $content -replace 'fa-chart-simple', 'fa-chart-pie'
    $content = $content -replace 'fa-calendar-days', 'fa-calendar-minus'
    
    # Title tag for Laporan consistency
    $content = [System.Text.RegularExpressions.Regex]::Replace($content, '(?i)<title>.*?Laporan.*?</title>', '<title>Laporan & Analitik - FoodSync HRMS</title>')
    
    # Replace alternative logout texts
    $content = [System.Text.RegularExpressions.Regex]::Replace($content, '(?i)Keluar Sistem', 'Logout')
    $content = [System.Text.RegularExpressions.Regex]::Replace($content, '(?i)Sign Out', 'Logout')

    [System.IO.File]::WriteAllText($file.FullName, $content)
    Write-Host "Processed $($file.Name)"
}
