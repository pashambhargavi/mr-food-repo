@echo off
set "PATH=C:\Users\DELL\php;%PATH%"
cd /d "d:\BHARGAVI\agency-agents\sweet-shop-backend"
echo ==============================================================================
echo   ANANDAM SWEETS - LARAVEL 11 BACKEND API & ADMIN PANEL
echo ==============================================================================
echo.
echo   Admin Panel URL: http://127.0.0.1:8000/admin
echo   REST API Base:   http://127.0.0.1:8000/api
echo.
echo   Default Admin Login: admin@anandamsweets.in / admin123
echo.
echo   Press Ctrl+C to stop the server anytime.
echo.
php artisan serve --port=8000
pause
