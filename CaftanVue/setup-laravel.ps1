# CaftanVue Backend - Complete Setup Script
# Run this AFTER Composer is installed

Write-Host "======================================" -ForegroundColor Green
Write-Host "  CaftanVue Laravel Backend Setup    " -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host ""

# Step 1: Check Composer
Write-Host "[1/5] Checking Composer..." -ForegroundColor Cyan
$composerCommand = Get-Command composer -ErrorAction SilentlyContinue
if (-not $composerCommand) {
    Write-Host "✗ Composer not found!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please close this PowerShell window and open a NEW one," -ForegroundColor Yellow
    Write-Host "then run this script again." -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit
}
Write-Host "✓ Composer found!" -ForegroundColor Green

# Step 2: Create Laravel Project
Write-Host ""
Write-Host "[2/5] Creating Laravel project..." -ForegroundColor Cyan
Write-Host "This will take 3-5 minutes. Please wait..." -ForegroundColor Yellow

cd C:\xampp\htdocs

if (Test-Path "CaftanVue-API") {
    Write-Host "✓ CaftanVue-API already exists" -ForegroundColor Yellow  
}
else {
    composer create-project laravel/laravel CaftanVue-API
    Write-Host "✓ Laravel project created!" -ForegroundColor Green
}

cd CaftanVue-API

# Step 3: Configure .env
Write-Host ""
Write-Host "[3/5] Configuring environment..." -ForegroundColor Cyan

if (Test-Path ".env") {
    $envContent = Get-Content .env -Raw
    $envContent = $envContent -replace 'DB_CONNECTION=.*', 'DB_CONNECTION=mysql'
    $envContent = $envContent -replace 'DB_DATABASE=.*', 'DB_DATABASE=caftanvue'
    $envContent = $envContent -replace 'DB_USERNAME=.*', 'DB_USERNAME=root'
    $envContent = $envContent -replace 'DB_PASSWORD=.*', 'DB_PASSWORD='
    $envContent | Set-Content .env
    Write-Host "✓ Environment configured!" -ForegroundColor Green
}

# Step 4: Create Database
Write-Host ""
Write-Host "[4/5] Creating database..." -ForegroundColor Cyan
Write-Host "Opening phpMyAdmin to create database..." -ForegroundColor Yellow
Write-Host ""
Write-Host "Please do the following in your browser:" -ForegroundColor Yellow
Write-Host "1. Go to: http://localhost/phpmyadmin" -ForegroundColor Cyan
Write-Host "2. Click 'New' on the left" -ForegroundColor Cyan
Write-Host "3. Database name: caftanvue" -ForegroundColor Cyan
Write-Host "4. Collation: utf8mb4_unicode_ci" -ForegroundColor Cyan
Write-Host "5. Click 'Create'" -ForegroundColor Cyan
Write-Host ""

Start-Process "http://localhost/phpmyadmin"

# Step 5: Summary
Write-Host ""
Write-Host "======================================" -ForegroundColor Green
Write-Host "  Laravel Project Created!           " -ForegroundColor Green 
Write-Host "======================================" -ForegroundColor Green
Write-Host ""
Write-Host "Location: C:\xampp\htdocs\CaftanVue-API" -ForegroundColor Cyan
Write-Host ""
Write-Host "NEXT STEPS:" -ForegroundColor Yellow
Write-Host "1. Create the database using phpMyAdmin (opened in browser)" -ForegroundColor White
Write-Host "2. Tell me 'database created' and I'll set up the tables!" -ForegroundColor White
Write-Host ""
