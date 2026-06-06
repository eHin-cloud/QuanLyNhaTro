@echo off
echo =========================================
echo   SmartRoom ^& Renty Review - Startup
echo =========================================
echo.
echo [1/3] Resetting Laravel Database...
call php artisan migrate:fresh --seed
if %errorlevel% neq 0 (
    echo Database migration failed!
    exit /b %errorlevel%
)

echo.
echo [2/3] Starting Vite Dev Server in background...
start /b npm run dev

echo.
echo [3/3] Starting Laravel Development Server...
call php artisan serve
