# CONNECTION VERIFICATION CHECKLIST

## Step 1: Test API in Browser (DO THIS NOW!)
I just opened: http://localhost/caftanvue-api/

**What do you see?**
- ✅ JSON message "CaftanVue API is running!" = GOOD!
- ❌ 404 Not Found = Apache issue
- ❌ Connection refused = Apache not running

## Step 2: Test Data Endpoint
Open: http://localhost/caftanvue-api/caftans

**Should see:** Array of 6 caftans in JSON

## Step 3: Verify Android App Configuration
File: `CaftanApiService.kt` line 14

**Must be EXACTLY:**
```kotlin
private const val BASE_URL = "http://10.0.2.2/caftanvue-api/"
```

## Step 4: Check XAMPP
Open XAMPP Control Panel:
- Apache: Must be GREEN (running)
- MySQL: Must be GREEN (running)

## Step 5: Rebuild App
In Android Studio:
```
Build → Clean Project
Build → Rebuild Project
Run ▶️
```

## Quick Test Commands:

```powershell
# Is Apache running?
Get-Process -Name "httpd" -ErrorAction SilentlyContinue

# Does API file exist?
Test-Path "C:\xampp\htdocs\caftanvue-api\index.php"

# Test API
Start-Process "http://localhost/caftanvue-api/caftans"
```

## Tell Me:
1. What does http://localhost/caftanvue-api/ show?
2. Is XAMPP Apache GREEN?
3. What's in Android Studio Logcat (filter: "error")?
