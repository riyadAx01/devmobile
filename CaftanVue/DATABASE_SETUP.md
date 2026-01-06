# Quick Database Setup Guide

## Run These Commands in PowerShell:

```powershell
cd C:\xampp\htdocs\CaftanVue-API

# Create migrations
php artisan make:migration create_caftans_table
php artisan make:migration create_clients_table  
php artisan make:migration create_reservations_table
php artisan make:migration create_admins_table

# Create models
php artisan make:model Caftan
php artisan make:model Client
php artisan make:model Reservation
php artisan make:model Admin

# Create API controllers
php artisan make:controller Api/CaftanController --resource
php artisan make:controller Api/ClientController --resource
php artisan make:controller Api/ReservationController --resource

# Run migrations (after editing migration files)
php artisan migrate

# Start server
php artisan serve
```

## Migration Files to Edit:

After creating migrations, you'll need to edit 4 files in:
`C:\xampp\htdocs\CaftanVue-API\database\migrations\`

I'll provide the code for each migration file.

The files will be named something like:
- `2024_12_17_XXXXXX_create_caftans_table.php`
- `2024_12_17_XXXXXX_create_clients_table.php`
- `2024_12_17_XXXXXX_create_reservations_table.php`  
- `2024_12_17_XXXXXX_create_admins_table.php`

Tell me "show migration code" and I'll provide the complete code for all 4 files!
