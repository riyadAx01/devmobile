# Quick Start: Database Migration

## Run Migration

Open phpMyAdmin or MySQL command line:

```bash
mysql -u root caftanvue < database_migration_multitenant.sql
```

Or in phpMyAdmin:
1. Select `caftanvue` database
2. Click "SQL" tab
3. Copy contents of `database_migration_multitenant.sql`
4. Click "Go"

## What This Does

- Adds authentication fields to `admins` table
- Adds `admin_id` foreign key to `caftans` table  
- Creates default admin account
- Links existing caftans to default admin
- Creates sample second admin for testing

## Default Admin Credentials

- Email: `admin@caftanvue.com`
- Password: `password`

## Verify Migration

Check tables:
```sql
DESCRIBE admins;
DESCRIBE caftans;
SELECT * FROM admins;
```

You should see new columns and 2 admins!
