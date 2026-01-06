# ✅ Step 1: Run Database Migration

## In phpMyAdmin:
1. Open http://localhost/phpmyadmin
2. Click "caftanvue" database
3. Click "SQL" tab
4. Copy & paste from `database_migration_multitenant.sql`
5. Click "Go"

You should see:
- Migration completed successfully 
- 2 admins created
- All caftans now have admin_id

## ✅ Step 2: API is Already Updated!

I've copied the enhanced API to XAMPP.

Test it works:
```
http://localhost/caftanvue-api/
```

Should show: "CaftanVue Multi-Tenant API, version 2.0"

## ✅ Step 3: Test Authentication

### Register a new admin:
```bash
curl -X POST http://localhost/caftanvue-api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "test_admin",
    "email": "test@example.com",
    "password": "password123",
    "shop_name": "Test Shop",
    "shop_address": "Rabat, Morocco"
  }'
```

You should get back a token!

### Login:
```bash
curl -X POST http://localhost/caftanvue-api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@caftanvue.com",
    "password": "password"
  }'
```

## Ready for Android Updates!

Backend is ready. Next: Update Android app to use the new API.
