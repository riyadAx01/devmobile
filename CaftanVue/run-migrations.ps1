# Run Laravel Migrations and Setup API

Write-Host "=== Setting Up CaftanVue Database ===" -ForegroundColor Green
Write-Host ""

cd C:\xampp\htdocs\CaftanVue-API

# Create migration files
Write-Host "[1/5] Creating migration files..." -ForegroundColor Cyan

# Get timestamp for migrations
$timestamp = Get-Date -Format "yyyy_MM_dd_HHmmss"

# Create caftans migration
$caftansMigration = @"
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caftans', function (Blueprint `$table) {
            `$table->id();
            `$table->string('name')->index();
            `$table->text('description');
            `$table->string('image_url', 500);
            `$table->decimal('price', 10, 2);
            `$table->enum('collection', ['Traditional', 'Modern', 'Wedding', 'Casual'])->index();
            `$table->string('color', 50)->index();
            `$table->enum('size', ['S', 'M', 'L', 'XL']);
            `$table->enum('status', ['available', 'rented', 'maintenance'])->default('available')->index();
            `$table->boolean('is_available')->default(true);
            `$table->timestamps();
            
            `$table->index(['collection', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caftans');
    }
};
"@

$caftansMigration | Out-File -FilePath "database\migrations\${timestamp}_01_create_caftans_table.php" -Encoding UTF8

# Create clients migration  
$clientsMigration = @"
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint `$table) {
            `$table->id();
            `$table->string('name')->index();
            `$table->string('email')->unique();
            `$table->string('phone', 20)->index();
            `$table->text('address');
            `$table->string('cin', 20)->unique()->index();
            `$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
"@

$clientsMigration | Out-File -FilePath "database\migrations\${timestamp}_02_create_clients_table.php" -Encoding UTF8

# Create reservations migration
$reservationsMigration = @"
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint `$table) {
            `$table->id();
            `$table->foreignId('caftan_id')->constrained()->onDelete('cascade');
            `$table->foreignId('client_id')->constrained()->onDelete('cascade');
            `$table->date('start_date')->index();
            `$table->date('end_date')->index();
            `$table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending')->index();
            `$table->decimal('total_price', 10, 2);
            `$table->text('notes')->nullable();
            `$table->timestamps();
            
            `$table->index(['status', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
"@

$reservationsMigration | Out-File -FilePath "database\migrations\${timestamp}_03_create_reservations_table.php" -Encoding UTF8

# Create admins migration
$adminsMigration = @"
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint `$table) {
            `$table->id();
            `$table->string('username', 100)->unique();
            `$table->string('password');
            `$table->string('email')->unique();
            `$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
"@

$adminsMigration | Out-File -FilePath "database\migrations\${timestamp}_04_create_admins_table.php" -Encoding UTF8

Write-Host "✓ Migration files created!" -ForegroundColor Green

# Run migrations
Write-Host ""
Write-Host "[2/5] Running migrations..." -ForegroundColor Cyan
php artisan migrate

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Database tables created with indexes!" -ForegroundColor Green
}
else {
    Write-Host "✗ Migration failed" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit
}

# Create Models
Write-Host ""
Write-Host "[3/5] Creating models..." -ForegroundColor Cyan
php artisan make:model Caftan
php artisan make:model Client  
php artisan make:model Reservation
php artisan make:model Admin
Write-Host "✓ Models created!" -ForegroundColor Green

# Create Controllers
Write-Host ""
Write-Host "[4/5] Creating API controllers..." -ForegroundColor Cyan
php artisan make:controller Api/CaftanController --api
php artisan make:controller Api/ClientController --api
php artisan make:controller Api/ReservationController --api
php artisan make:controller Api/AuthController
Write-Host "✓ Controllers created!" -ForegroundColor Green

# Start Laravel Server
Write-Host ""
Write-Host "[5/5] Starting Laravel server..." -ForegroundColor Cyan
Write-Host ""
Write-Host "==================================" -ForegroundColor Green
Write-Host "  Laravel Backend Is Ready! 🎉   " -ForegroundColor Green
Write-Host "==================================" -ForegroundColor Green
Write-Host ""
Write-Host "API Server: http://localhost:8000" -ForegroundColor Cyan
Write-Host "Test endpoint: http://localhost:8000/api/caftans" -ForegroundColor Cyan
Write-Host ""
Write-Host "Starting server..." -ForegroundColor Yellow
Write-Host "Press Ctrl+C to stop" -ForegroundColor Yellow
Write-Host ""

php artisan serve
