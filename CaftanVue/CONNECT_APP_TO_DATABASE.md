# HOW TO CONNECT YOUR APP TO THE DATABASE

## ✅ Current Status:
- Android App: ✅ Ready
- Database: ✅ Created with tables and data
- API Server: ❌ Not running (THIS IS WHAT YOU NEED!)

## 🚀 SETUP API SERVER (3 Steps - 2 Minutes!)

### Step 1: Create API Folder
```powershell
mkdir C:\xampp\htdocs\caftanvue-api
```

### Step 2: Copy API File
Copy the file `api-index.php` to `C:\xampp\htdocs\caftanvue-api\index.php`

### Step 3: Test API
1. Make sure XAMPP Apache is running
2. Open browser: http://localhost/caftanvue-api
3. You should see: `{"message":"CaftanVue API is running!"}`

## 📱 UPDATE ANDROID APP

Your app is configured to use: `http://10.0.2.2:8000/api/`

But XAMPP runs on port **80**, so change it to: `http://10.0.2.2/caftanvue-api/`

### Edit This File:
`app/src/main/java/com/example/caftanvue/data/CaftanApiService.kt`

Change line 14 from:
```kotlin
private const val BASE_URL = "http://10.0.2.2:8000/api/"
```

To:
```kotlin
private const val BASE_URL = "http://10.0.2.2/caftanvue-api/"
```

## ✅ DONE!

Now:
1. Run your Android app
2. Data from your MySQL database will show up!

## Test Endpoints:
- http://localhost/caftanvue-api/caftans (6 caftans)
- http://localhost/caftanvue-api/clients (3 clients)
- http://localhost/caftanvue-api/reservations (3 reservations)

🎉 Your app will now connect to the real database!
