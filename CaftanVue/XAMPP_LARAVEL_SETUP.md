# Quick Setup: Laravel with XAMPP

## ✅ You Already Have:
- **PHP 8.2.12** (via XAMPP)
- **MySQL** (via XAMPP)
- **phpMyAdmin** (for database management)

## 📥 Step 1: Install Composer (2 minutes)

### Download Composer
1. Go to: https://getcomposer.org/Composer-Setup.exe
2. Run the installer
3. When asked for PHP path, browse to: `C:\xampp\php\php.exe`
4. Complete installation with defaults

### Verify
Open **NEW** PowerShell and run:
```powershell
composer --version
```

---

## 🚀 Step 2: Create Laravel Project

Once Composer is installed:

```powershell
cd C:\xampp\htdocs
composer create-project laravel/laravel CaftanVue-API
cd CaftanVue-API
```

---

## 🗄️ Step 3: Create Database

### Start XAMPP
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL**

### Create Database
1. Open browser: http://localhost/phpmyadmin
2. Click "New" on the left
3. Database name: `caftanvue`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

---

## ⚙️ Step 4: Configure Laravel

Edit `C:\xampp\htdocs\CaftanVue-API\.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=caftanvue
DB_USERNAME=root
DB_PASSWORD=
```

---

## 📋 Step 5: Create Migrations

I'll create all the migration files with proper database schema and indexes!

Then run:
```powershell
php artisan migrate
```

---

## 🌐 Step 6: Start Laravel Server

```powershell
php artisan serve
```

Your API will be at: **http://localhost:8000**

---

## 🔗 Connect Android App

The Android app will connect to:
- **Emulator**: `http://10.0.2.2:8000/api/`
- **Physical Device**: `http://YOUR_COMPUTER_IP:8000/api/`

---

## Let's Go! 🎉

**Install Composer first**, then tell me when it's ready and I'll help you create the Laravel project and all the database files!
