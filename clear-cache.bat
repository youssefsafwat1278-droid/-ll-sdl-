@echo off
echo Clearing Laravel Cache...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo.
echo Deleting bootstrap cache manually...
del /Q bootstrap\cache\*.php 2>nul

echo.
echo Cache cleared successfully!
pause
