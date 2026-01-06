# Complete Laravel Backend Setup - Run This!

Write-Host "=== CaftanVue Laravel Backend Setup ===" -ForegroundColor Green
Write-Host ""

# Step 1: Create Laravel Project
Write-Host "[Step 1] Creating Laravel project..." -ForegroundColor Cyan
Write-Host "Location: C:\xampp\htdocs\CaftanVue-API" -ForegroundColor Yellow
Write-Host "This will take 3-5 minutes..." -ForegroundColor Yellow
Write-Host ""

cd C:\xampp\htdocs
composer create-project laravel/laravel CaftanVue-API

if (Test-Path "CaftanVue-API") {
    Write-Host "✓ Laravel project created successfully!" -ForegroundColor Green
}
else {
    Write-Host "✗ Failed to create Laravel project" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit
}

cd CaftanVue-API

# Step 2: Configure Database
Write-Host ""
Write-Host "[Step 2] Configuring database..." -ForegroundColor Cyan

$envContent = @"
APP_NAME=CaftanVue
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=caftanvue
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

CACHE_STORE=database
CACHE_PREFIX=
"@

$envContent | Set-Content .env
Write-Host "✓ Database configured!" -ForegroundColor Green

# Step 3: Generate App Key
Write-Host ""
Write-Host "[Step 3] Generating application key..." -ForegroundColor Cyan
php artisan key:generate
Write-Host "✓ App key generated!" -ForegroundColor Green

Write-Host ""
Write-Host "=== Laravel Project Created! ===" -ForegroundColor Green
Write-Host ""
Write-Host "Now create MySQL database:" -ForegroundColor Yellow
Write-Host "1. Open: http://localhost/phpmyadmin" -ForegroundColor Cyan
Write-Host "2. Click 'New'" -ForegroundColor Cyan
Write-Host "3. Database name: caftanvue" -ForegroundColor Cyan
Write-Host "4. Collation: utf8mb4_unicode_ci" -ForegroundColor Cyan
Write-Host "5. Click 'Create'" -ForegroundColor Cyan
Write-Host ""
Write-Host "After creating the database, tell me 'database ready'!" -ForegroundColor Yellow
Write-Host ""

Start-Process "http://localhost/phpmyadmin"
