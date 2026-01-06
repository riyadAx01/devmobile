# Laravel Backend Installation Guide for CaftanVue

## Step 1: Install PHP (5-10 minutes)

### Download PHP
1. Go to: https://windows.php.net/download/
2. Download **PHP 8.2** (VS16 x64 Thread Safe)
3. Extract the ZIP to `C:\php`

### Configure PHP
1. In `C:\php`, find `php.ini-development`
2. Copy and rename it to `php.ini`
3. Open `php.ini` in a text editor
4. Find and uncomment these lines (remove the `;` at the start):
   ```ini
   extension=curl
   extension=fileinfo
   extension=mbstring
   extension=openssl
   extension=pdo_mysql
   extension=pdo_sqlite
   extension=sqlite3
   ```

### Add PHP to PATH
1. Press `Win + X` → Select "System"
2. Click "Advanced system settings"
3. Click "Environment Variables"
4. Under "System variables", find "Path" and click "Edit"
5. Click "New" and add: `C:\php`
6. Click OK on all dialogs

### Verify Installation
Open **NEW** PowerShell window and run:
```powershell
php --version
```
You should see: `PHP 8.2.x`

---

## Step 2: Install Composer (2 minutes)

### Download and Install
1. Go to: https://getcomposer.org/Composer-Setup.exe
2. Run the installer
3. It will automatically detect PHP
4. Click through the installation (use defaults)

### Verify Installation
```powershell
composer --version
```
You should see: `Composer version 2.x.x`

---

## Step 3: Create Laravel Project (5 minutes)

### Create Project
```powershell
cd C:\Users\abdel\AndroidStudioProjects
composer create-project laravel/laravel CaftanVue-API
```
**This will take 3-5 minutes** - it downloads Laravel and all dependencies.

### Navigate to Project
```powershell
cd CaftanVue-API
```

---

## Step 4: Configure Database (SQLite - Easiest)

### Edit .env file
Open `CaftanVue-API\.env` in a text editor and change:
```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```
(Comment out or delete the MySQL lines)

### Create SQLite Database File
```powershell
New-Item -Path database\database.sqlite -ItemType File -Force
```

---

## Step 5: Create Database Migrations

### Create Migration Files
```powershell
php artisan make:migration create_caftans_table
php artisan make:migration create_clients_table
php artisan make:migration create_reservations_table
php artisan make:migration create_admins_table
```

### Copy Migration Code
I'll create the migration files for you in the next step with proper database schema!

---

## Step 6: Run Migrations

After migration files are created:
```powershell
php artisan migrate
```

---

## Step 7: Create Models and Controllers

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

---

## Step 8: Set Up API Routes

Edit `routes/api.php` - I'll create this file with all endpoints!

---

## Step 9: Enable CORS

```powershell
php artisan install:api
```

---

## Step 10: Start Laravel Server

```powershell
php artisan serve
```

Server will run at: **http://localhost:8000**

Test API:
- http://localhost:8000/api/caftans
- http://localhost:8000/api/clients
- http://localhost:8000/api/reservations

---

## Next Steps

After the server is running:
1. Your Android app will connect to `http://10.0.2.2:8000/api/` (for emulator)
2. Test the endpoints in a browser or Postman
3. Run your Android app and see real data! 🎉

---

## Let's Start!

**Ready to begin?** 

Tell me when you've completed **Step 1 (PHP)** and **Step 2 (Composer)**, then I'll help you with the Laravel project creation and database setup! 🚀

Or if you already have them installed, just run:
```powershell
php --version
composer --version
```
And tell me the output!
