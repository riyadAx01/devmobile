# Laravel Backend Setup Instructions for CaftanVue

## Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL or MariaDB

## Installation Steps

### 1. Create Laravel Project
```powershell
cd C:\Users\abdel\AndroidStudioProjects
composer create-project laravel/laravel CaftanVue-API
cd CaftanVue-API
```

### 2. Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=caftanvue
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Create Database
```sql
CREATE DATABASE caftanvue CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Create Migrations

Run these commands to create migration files:

```powershell
php artisan make:migration create_caftans_table
php artisan make:migration create_clients_table
php artisan make:migration create_reservations_table
php artisan make:migration create_admins_table
```

### 5. Run Migrations
```powershell
php artisan migrate
```

### 6. Create Models and Controllers
```powershell
php artisan make:model Caftan
php artisan make:model Client
php artisan make:model Reservation
php artisan make:model Admin
php artisan make:controller Api/CaftanController --api
php artisan make:controller Api/ClientController --api
php artisan make:controller Api/ReservationController --api
php artisan make:controller Api/AuthController
```

### 7. Create Image Directory
```powershell
New-Item -ItemType Directory -Force -Path "public\images\caftans"
```

### 8. Run Image Download Script
```powershell
..\CaftanVue\download_caftans.ps1
```

### 9. Seed Database (Optional)
```powershell
php artisan make:seeder CaftanSeeder
php artisan make:seeder AdminSeeder
php artisan db:seed
```

### 10. Start Development Server
```powershell
php artisan serve
```

Server will run at: http://localhost:8000

### 11. Test API Endpoints
GET http://localhost:8000/api/caftans
GET http://localhost:8000/api/clients
GET http://localhost:8000/api/reservations

## API Routes Structure

The `routes/api.php` file should contain:

```php
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('caftans', CaftanController::class);
Route::get('caftans/search', [CaftanController::class, 'search']);

Route::apiResource('clients', ClientController::class);
Route::apiResource('reservations', ReservationController::class);
```

## Database Schema (Implemented in migrations)

### caftans table
- id, name, description, image_url, price, collection, color, size, status, created_at, updated_at
- Indexes: name, collection, color, status, (collection, status)

### clients table  
- id, name, email, phone, address, cin, created_at, updated_at
- Indexes: name, email (unique), cin (unique)

### reservations table
- id, caftan_id, client_id, start_date, end_date, status, total_price, notes, created_at, updated_at
- Foreign keys: caftan_id → caftans(id), client_id → clients(id)
- Indexes: caftan_id, client_id, status, start_date, end_date

### admins table
- id, username, password, email, created_at, updated_at
- Indexes: username (unique), email (unique)

## CORS Configuration

Add to `config/cors.php` to allow Android app:
```php
'paths' => ['api/*'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

## Next Steps
1. Implement controller logic for CRUD operations
2. Add validation rules
3. Implement authentication with Sanctum
4. Test endpoints with Postman or from Android app
