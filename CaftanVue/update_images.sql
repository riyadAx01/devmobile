-- Update caftans with real image paths
USE caftanvue;

UPDATE caftans SET image_path = 'uploads/caftans/caftan_royal_blue_1766001008496.png' WHERE id = 1;
UPDATE caftans SET image_path = 'uploads/caftans/caftan_wedding_gold_1766001024860.png' WHERE id = 2;
UPDATE caftans SET image_path = 'uploads/caftans/caftan_traditional_green_1766001038146.png' WHERE id = 3;
UPDATE caftans SET image_path = 'uploads/caftans/caftan_summer_white_1766001063178.png' WHERE id = 4;
UPDATE caftans SET image_path = 'uploads/caftans/caftan_modern_burgundy_1766001077890.png' WHERE id = 5;
UPDATE caftans SET image_path = 'uploads/caftans/caftan_evening_silver_1766001092921.png' WHERE id = 6;

SELECT 'Images updated!' AS status;
SELECT id, name, image_path FROM caftans;
