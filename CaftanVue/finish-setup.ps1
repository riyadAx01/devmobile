# Complete Database Setup - Run After Laravel is Created

Write-Host "=== Finishing Database Setup ===" -ForegroundColor Green

cd C:\xampp\htdocs\CaftanVue-API

# Update migrations with full schema
$migrations = Get-ChildItem "database\migrations" -Filter "*create*table.php"

foreach ($m in $migrations) {
    if ($m.Name -like "*caftans*") {
        @'
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
        });
    }
    public function down(): void { Schema::dropIfExists('caftans'); }
};
'@ | Set-Content $m.FullName
    }
    elseif ($m.Name -like "*clients*") {
        @'
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
    public function down(): void { Schema::dropIfExists('clients'); }
};
'@ | Set-Content $m.FullName
    }
    elseif ($m.Name -like "*reservations*") {
        @'
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
        });
    }
    public function down(): void { Schema::dropIfExists('reservations'); }
};
'@ | Set-Content $m.FullName
    }
}

Write-Host "✓ Migration files updated!" -ForegroundColor Green

# Run migrations
Write-Host "Running migrations..." -ForegroundColor Cyan
C:\xampp\php\php.exe artisan migrate --force

Write-Host ""
Write-Host "=== ✓ DONE! ===" -ForegroundColor Green
Write-Host "Database tables created with indexes!" -ForegroundColor Green
Write-Host ""
Write-Host "Start server: C:\xampp\php\php.exe artisan serve" -ForegroundColor Cyan
