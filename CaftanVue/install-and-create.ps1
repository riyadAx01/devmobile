# FINAL FIX - Direct Setup with XAMPP PHP

Write-Host "=== Installing Composer & Creating Laravel ===" -ForegroundColor Green

cd C:\xampp\htdocs

# Step 1: Install Composer
Write-Host "[1/4] Installing Composer..." -ForegroundColor Cyan
C:\xampp\php\php.exe -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
C:\xampp\php\php.exe composer-setup.php
C:\xampp\php\php.exe -r "unlink('composer-setup.php');"
Write-Host "✓ Composer installed!" -ForegroundColor Green

# Step 2: Create Laravel
Write-Host "[2/4] Creating Laravel project (3-5 min)..." -ForegroundColor Cyan
C:\xampp\php\php.exe composer.phar create-project laravel/laravel CaftanVue-API
Write-Host "✓ Laravel created!" -ForegroundColor Green

# Step 3: Configure
Write-Host "[3/4] Configuring..." -ForegroundColor Cyan
cd CaftanVue-API
$env = @"
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
"@
$env | Set-Content .env
C:\xampp\php\php.exe artisan key:generate
Write-Host "✓ Configured!" -ForegroundColor Green

# Step 4: Create migrations
Write-Host "[4/4] Setting up database..." -ForegroundColor Cyan

# Create migration files
C:\xampp\php\php.exe artisan make:migration create_caftans_table
C:\xampp\php\php.exe artisan make:migration create_clients_table
C:\xampp\php\php.exe artisan make:migration create_reservations_table

Write-Host ""
Write-Host "✓ Laravel is ready at: C:\xampp\htdocs\CaftanVue-API" -ForegroundColor Green
Write-Host ""
Write-Host "Now run: cd C:\Users\abdel\AndroidStudioProjects\CaftanVue" -ForegroundColor Yellow
Write-Host "Then: .\finish-setup.ps1" -ForegroundColor Yellow
