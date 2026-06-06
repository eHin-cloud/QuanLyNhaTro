Write-Host "=========================================" -ForegroundColor Magenta
Write-Host "  SmartRoom & Renty Review - Startup  " -ForegroundColor Magenta
Write-Host "=========================================" -ForegroundColor Magenta
Write-Host ""

Write-Host "[1/3] Resetting Laravel Database..." -ForegroundColor Cyan
php artisan migrate:fresh --seed

Write-Host ""
Write-Host "[2/3] Starting Vite Dev Server in background..." -ForegroundColor Cyan
Start-Process npm -ArgumentList "run dev" -NoNewWindow

Write-Host ""
Write-Host "[3/3] Starting Laravel Development Server..." -ForegroundColor Cyan
php artisan serve
