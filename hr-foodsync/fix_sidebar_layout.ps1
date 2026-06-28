$files = Get-ChildItem -Path "c:\Users\organizer\Documents\hr-foodsync\*.html"
foreach ($file in $files) {
    $content = Get-Content -Path $file.FullName -Raw
    $original = $content
    
    # Replace the opening <div> immediately after <aside> with a flex-1 scrolling container
    $content = [regex]::Replace($content, '(?si)(<aside[^>]*>)\s*<div>', '$1' + "`r`n        <div class=`"flex-1 overflow-y-auto overflow-x-hidden`">")
    
    if ($original -ne $content) {
        Set-Content -Path $file.FullName -Value $content
        Write-Host "Updated $($file.Name)"
    }
}
