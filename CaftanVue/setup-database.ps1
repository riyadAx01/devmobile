# CaftanVue Laravel - Database Setup and Server Start

Write-Host "=== CaftanVue Database Setup ===" -ForegroundColor Green
Write-Host ""

cd C:\xampp\htdocs\CaftanVue-API

Write-Host "[1/3] Creating database migrations..." -ForegroundColor Cyan
Write-Host ""

# Run migration creation commands
php artisan make:migration create_caftans_table
php artisan make:migration create_clients_table
php artisan make:migration create_reservations_table
php artisan make:migration create_admins_table

Write-Host ""
Write-Host "✓ Migration files created!" -ForegroundColor Green
Write-Host ""
Write-Host "IMPORTANT: I will now provide you with the migration code." -ForegroundColor Yellow
Write-Host "You need to copy it into the migration files." -ForegroundColor Yellow
Write-Host ""
Write-Host "Migration files location:" -ForegroundColor Cyan
Write-Host "C:\xampp\htdocs\CaftanVue-API\database\migrations\" -ForegroundColor White
Write-Host ""
Read-Host "Press Enter when you're ready to see the migration code"
