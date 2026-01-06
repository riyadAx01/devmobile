# COMPLETE MIGRATION CODE - Ready to Copy & Paste

After Laravel project is created, you'll find migration files in:
`C:\xampp\htdocs\CaftanVue-API\database\migrations\`

They'll be named like: `YYYY_MM_DD_XXXXXX_create_caftans_table.php`

---

## MIGRATION 1: Caftans Table

Find the file ending with `_create_caftans_table.php` and replace its content with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caftans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->text('description');
            $table->string('image_url', 500);
            $table->decimal('price', 10, 2);
            $table->enum('collection', ['Traditional', 'Modern', 'Wedding', 'Casual'])->index();
            $table->string('color', 50)->index();
            $table->enum('size', ['S', 'M', 'L', 'XL']);
            $table->enum('status', ['available', 'rented', 'maintenance'])->default('available')->index();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            
            // Composite index for better query performance
            $table->index(['collection', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caftans');
    }
};
```

---

## MIGRATION 2: Clients Table

Find the file ending with `_create_clients_table.php` and replace its content with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
```

---

## MIGRATION 3: Reservations Table

Find the file ending with `_create_reservations_table.php` and replace its content with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caftan_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending')->index();
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Composite index for availability checks
            $table->index(['status', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
```

---

## MIGRATION 4: Admins Table

Find the file ending with `_create_admins_table.php` and replace its content with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
```

---

## AFTER COPYING ALL MIGRATIONS:

Run these commands in PowerShell:

```powershell
cd C:\xampp\htdocs\CaftanVue-API

# Create migration files
php artisan make:migration create_caftans_table
php artisan make:migration create_clients_table
php artisan make:migration create_reservations_table
php artisan make:migration create_admins_table

# Then copy the code above into each file

# Run migrations to create tables
php artisan migrate

# Create models
php artisan make:model Caftan
php artisan make:model Client
php artisan make:model Reservation
php artisan make:model Admin

# Install API support
php artisan install:api

# Start server
php artisan serve
```

Your API will be at: **http://localhost:8000**

---

## 🎉 DONE!

After running these, your database will have:
- ✅ 4 tables with proper structure
- ✅ All indexes for fast queries
- ✅ Foreign keys for data integrity
- ✅ Laravel API server running

Your Android app connects to: `http://10.0.2.2:8000/api/`
