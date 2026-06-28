$files = Get-ChildItem -Path "c:\Users\organizer\Documents\hr-foodsync\*.html"
$standardButton = '<a href="#" onclick="window.prosesLogout()" class="w-full flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-2xl hover:bg-red-700 transition-colors font-extrabold text-[13px] shadow-sm"><i class="fa-solid fa-arrow-right-from-bracket mr-2 text-white"></i> Logout</a>'
$dropdownWrapper = "<div class=`"px-4 mt-2`">$standardButton</div>"

foreach ($file in $files) {
    $content = Get-Content -Path $file.FullName -Raw
    $original = $content

    $content = [regex]::Replace($content, '(?si)<a[^>]*onclick="prosesLogout\(\)"[^>]*>.*?</a>', $dropdownWrapper)
    $content = [regex]::Replace($content, '(?si)<a[^>]*onclick="window\.prosesLogout\(\)"[^>]*>.*?</a>', $standardButton)

    if ($original -ne $content) {
        Set-Content -Path $file.FullName -Value $content
        Write-Host "Updated $($file.Name)"
    }
}
