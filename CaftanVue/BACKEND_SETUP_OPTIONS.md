# Quick Start Guide - CaftanVue Backend Setup

## Option 1: Install PHP & Laravel (Recommended for Production)

### Step 1: Install PHP
1. Download PHP from: https://windows.php.net/download/
2. Extract to `C:\php`
3. Add to PATH: 
   - Press Win + X → System → Advanced → Environment Variables
   - Edit PATH, add: `C:\php`
4. Verify: Open new PowerShell and run `php --version`

### Step 2: Install Composer
1. Download from: https://getcomposer.org/Composer-Setup.exe
2. Run installer (it will find PHP automatically)
3. Verify: `composer --version`

### Step 3: Create Laravel Project
```powershell
cd C:\Users\abdel\AndroidStudioProjects
composer create-project laravel/laravel CaftanVue-API
cd CaftanVue-API
```

### Step 4: Configure Database (SQLite - Easiest)
Edit `.env`:
```env
DB_CONNECTION=sqlite
# Remove or comment out MySQL settings
```

Create database file:
```powershell
New-Item -Path database\database.sqlite -ItemType File
```

### Step 5: Create Migrations
Copy the migration examples from your CaftanVue folder and create:

```powershell
php artisan make:migration create_caftans_table
php artisan make:migration create_clients_table
php artisan make:migration create_reservations_table
php artisan make:migration create_admins_table
```

### Step 6: Run Migrations
```powershell
php artisan migrate
```

### Step 7: Start Server
```powershell
php artisan serve
```
Access at: http://localhost:8000

---

## Option 2: JSON Server (Quick Test - No PHP Needed!)

This is a **mock API server** to test your Android app immediately:

### Install Node.js
Download from: https://nodejs.org/ (if not installed)

### Create Mock API
```powershell
cd C:\Users\abdel\AndroidStudioProjects\CaftanVue
npm install -g json-server

# Create db.json
```

Create `db.json` with sample data:
```json
{
  "caftans": [
    {
      "id": 1,
      "name": "Traditional Moroccan Caftan - Blue",
      "description": "Beautiful hand-embroidered caftan",
      "imageUrl": "https://picsum.photos/400/600?random=1",
      "price": 1500.00,
      "collection": "Traditional",
      "color": "Blue",
      "size": "M",
      "status": "available",
      "isAvailable": true
    },
    {
      "id": 2,
      "name": "Modern Wedding Caftan - Gold",
      "description": "Elegant gold caftan for special occasions",
      "imageUrl": "https://picsum.photos/400/600?random=2",
      "price": 2500.00,
      "collection": "Wedding",
      "color": "Gold",
      "size": "L",
      "status": "available",
      "isAvailable": true
    }
  ],
  "clients": [
    {
      "id": 1,
      "name": "Fatima Hassan",
      "email": "fatima@example.com",
      "phone": "+212 6 12 34 56 78",
      "address": "Casablanca, Morocco",
      "cin": "AB123456",
      "createdAt": "2024-01-15"
    }
  ],
  "reservations": [
    {
      "id": 1,
      "caftanId": 1,
      "clientId": 1,
      "startDate": "2024-06-01",
      "endDate": "2024-06-05",
      "status": "confirmed",
      "totalPrice": 1500.00,
      "notes": "Wedding ceremony"
    }
  ]
}
```

### Run Mock Server
```powershell
json-server --watch db.json --port 8000
```

### Update Android App
Change in `CaftanApiService.kt`:
```kotlin
private const val BASE_URL = "http://10.0.2.2:8000/"
```

Test endpoints:
- http://localhost:8000/caftans
- http://localhost:8000/clients
- http://localhost:8000/reservations

---

## Option 3: Use Online Database (Firebase/Supabase)

If you want a cloud solution without local setup, consider:
- **Firebase Realtime Database** (free tier available)
- **Supabase** (PostgreSQL, free tier)

I can help set those up if you prefer!

---

## Which Option Do You Want?

1. **Full Laravel** - Best for learning backend development
2. **JSON Server** - Quickest to test your Android app NOW
3. **Cloud Solution** - No local installation needed

Let me know and I'll help you set it up! 🚀
