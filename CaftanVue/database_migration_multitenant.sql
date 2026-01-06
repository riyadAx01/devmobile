-- CaftanVue Multi-Tenant Schema Migration
-- Run this after the initial database setup

USE caftanvue;

-- Add columns to admins table for authentication
ALTER TABLE admins 
ADD COLUMN email_verified_at TIMESTAMP NULL AFTER email,
ADD COLUMN remember_token VARCHAR(100) NULL AFTER password,
ADD COLUMN shop_name VARCHAR(255) NULL AFTER username,
ADD COLUMN shop_address VARCHAR(500) NULL AFTER shop_name;

-- Add columns to caftans table for multi-tenancy
ALTER TABLE caftans
ADD COLUMN admin_id INT UNSIGNED NOT NULL DEFAULT 1 AFTER id,
ADD COLUMN image_path VARCHAR(255) NULL AFTER image_url,
ADD COLUMN shop_address VARCHAR(500) NULL AFTER status;

-- Add foreign key constraint
ALTER TABLE caftans
ADD CONSTRAINT fk_caftans_admin
FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE;

-- Add index for performance
ALTER TABLE caftans
ADD INDEX idx_admin_id (admin_id);

-- Update existing caftans to belong to a default admin
-- First, ensure we have at least one admin
INSERT INTO admins (username, email, password, shop_name, shop_address, created_at, updated_at)
VALUES (
    'default_admin',
    'admin@caftanvue.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: "password"
    'CaftanVue Main Store',
    'Casablanca, Morocco',
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE id=id;

-- Update existing caftans to belong to this admin
UPDATE caftans SET admin_id = 1 WHERE admin_id = 0 OR admin_id IS NULL;

-- Sample data: Create a second admin for testing
INSERT INTO admins (username, email, password, shop_name, shop_address, created_at, updated_at)
VALUES (
    'mahal_atlas',
    'atlas@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: "password"
    'Mahal Atlas',
    'Rue des Habous, Casablanca',
    NOW(),
    NOW()
);

-- Verify migration
SELECT 'Migration completed successfully' AS status;
SELECT COUNT(*) AS total_admins FROM admins;
SELECT COUNT(*) AS total_caftans FROM caftans;
SELECT COUNT(*) AS caftans_with_admin FROM caftans WHERE admin_id IS NOT NULL;
