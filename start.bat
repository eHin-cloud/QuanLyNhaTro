@echo off
echo =========================================
echo   SmartRoom ^& Renty Review - Startup
echo =========================================
echo.
echo [1/4] Resetting Laravel Database...
call php artisan migrate:fresh --seed
if %errorlevel% neq 0 (
    echo Database migration failed!
    exit /b %errorlevel%
)

echo.
echo [2/4] Clearing Laravel Cache...
call php artisan config:clear
call php artisan view:clear
call php artisan cache:clear

echo.
echo [3/4] Starting Vite Dev Server in background...
start /b cmd /c npm run dev

echo.
echo [4/4] Starting Laravel Development Server...
start /b cmd /c "ping 127.0.0.1 -n 4 >nul && start http://127.0.0.1:8000/"
call php artisan serve

