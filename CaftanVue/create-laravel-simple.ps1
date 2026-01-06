# Simple Laravel Setup - Fixed

Write-Host "Installing Composer and Creating Laravel..." -ForegroundColor Green

cd C:\xampp\htdocs

# Download and run Composer installer
C:\xampp\php\php.exe -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
C:\xampp\php\php.exe composer-setup.php
C:\xampp\php\php.exe -r "unlink('composer-setup.php');"

# Create Laravel project
C:\xampp\php\php.exe composer.phar create-project laravel/laravel CaftanVue-API

cd CaftanVue-API

# Generate app key
C:\xampp\php\php.exe artisan key:generate

Write-Host "Laravel created! Now run finish-setup.ps1" -ForegroundColor Green
