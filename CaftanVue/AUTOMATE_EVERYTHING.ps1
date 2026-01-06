# MASTER SETUP SCRIPT - Fully Automated CaftanVue Backend
# This does EVERYTHING automatically!

Write-Host "========================================" -ForegroundColor Green
Write-Host "  CaftanVue Backend - Full Automation  " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

# Step 1: Create Laravel Project
Write-Host "[1/6] Creating Laravel project (this takes 3-5 min)..." -ForegroundColor Cyan
cd C:\xampp\htdocs

if (Test-Path "CaftanVue-API") {
    Write-Host "! CaftanVue-API already exists, using it..." -ForegroundColor Yellow
}
else {
    C:\xampp\php\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar create-project laravel/laravel CaftanVue-API --no-interaction
    if ($LASTEXITCODE -ne 0) {
        Write-Host "✗ Failed to create Laravel project" -ForegroundColor Red
        Write-Host "Trying alternate method..." -ForegroundColor Yellow
        composer create-project laravel/laravel CaftanVue-API --no-interaction
    }
}

cd CaftanVue-API
Write-Host "✓ Laravel project ready!" -ForegroundColor Green

# Step 2: Configure Environment
Write-Host ""
Write-Host "[2/6] Configuring database..." -ForegroundColor Cyan
$envPath = ".env"
if (Test-Path $envPath) {
    (Get-Content $envPath) -replace 'DB_CONNECTION=.*', 'DB_CONNECTION=mysql' `
        -replace 'DB_DATABASE=.*', 'DB_DATABASE=caftanvue' `
        -replace 'DB_USERNAME=.*', 'DB_USERNAME=root' `
        -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=' | Set-Content $envPath
}
Write-Host "✓ Environment configured!" -ForegroundColor Green

# Step 3: Create Migration Files
Write-Host ""
Write-Host "[3/6] Creating migrations..." -ForegroundColor Cyan

# Create migrations
C:\xampp\php\php.exe artisan make:migration create_caftans_table --quiet
C:\xampp\php\php.exe artisan make:migration create_clients_table --quiet
C:\xampp\php\php.exe artisan make:migration create_reservations_table --quiet
C:\xampp\php\php.exe artisan make:migration create_admins_table --quiet

# Find and update migration files
$migrationPath = "database\migrations"
$migrations = Get-ChildItem $migrationPath -Filter "*_create_*_table.php"

foreach ($migration in $migrations) {
    $content = Get-Content $migration.FullName -Raw
    
    if ($migration.Name -like "*caftans*") {
        $newContent = @'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('caftans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->text('description');
            $table->string('image_url', 500);
            $table->decimal('price', 10, 2);
            $table->string('collection', 50)->index();
            $table->string('color', 50)->index();
            $table->string('size', 10);
            $table->string('status', 20)->default('available')->index();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->index(['collection', 'status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('caftans');
    }
};
'@
        $newContent | Set-Content $migration.FullName
    }
    elseif ($migration.Name -like "*clients*") {
        $newContent = @'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('email')->unique();
            $table->string('phone', 20)->index();
            $table->text('address');
            $table->string('cin', 20)->unique()->index();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('clients');
    }
};
'@
        $newContent | Set-Content $migration.FullName
    }
    elseif ($migration.Name -like "*reservations*") {
        $newContent = @'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caftan_id')->index();
            $table->unsignedBigInteger('client_id')->index();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->string('status', 20)->default('pending')->index();
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['status', 'start_date', 'end_date']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('reservations');
    }
};
'@
        $newContent | Set-Content $migration.FullName
    }
    elseif ($migration.Name -like "*admins*") {
        $newContent = @'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('admins');
    }
};
'@
        $newContent | Set-Content $migration.FullName
    }
}

Write-Host "✓ Migration files created and configured!" -ForegroundColor Green

# Step 4: Run Migrations
Write-Host ""
Write-Host "[4/6] Running migrations to create tables..." -ForegroundColor Cyan
C:\xampp\php\php.exe artisan migrate --force
Write-Host "✓ Database tables created with indexes!" -ForegroundColor Green

# Step 5: Create Models
Write-Host ""
Write-Host "[5/6] Creating models..." -ForegroundColor Cyan
C:\xampp\php\php.exe artisan make:model Caftan --quiet
C:\xampp\php\php.exe artisan make:model Client --quiet
C:\xampp\php\php.exe artisan make:model Reservation --quiet
C:\xampp\php\php.exe artisan make:model Admin --quiet
Write-Host "✓ Models created!" -ForegroundColor Green

# Step 6: Start Server
Write-Host ""
Write-Host "[6/6] Starting Laravel server..." -ForegroundColor Cyan
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  🎉 BACKEND IS READY! 🎉              " -ForegroundColor Green  
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "✓ Laravel project created" -ForegroundColor Green
Write-Host "✓ Database tables created with indexes" -ForegroundColor Green
Write-Host "✓ Models generated" -ForegroundColor Green
Write-Host ""
Write-Host "API Server: http://localhost:8000/api" -ForegroundColor Cyan
Write-Host "Android connects to: http://10.0.2.2:8000/api" -ForegroundColor Cyan
Write-Host ""
Write-Host "Starting server now..." -ForegroundColor Yellow
Write-Host "(Press Ctrl+C to stop)" -ForegroundColor Yellow
Write-Host ""

C:\xampp\php\php.exe artisan serve
