<?php
// Quick script to update image paths
$host = '127.0.0.1';
$db = 'caftanvue';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update image paths
    $pdo->exec("UPDATE caftans SET image_path = 'uploads/caftans/caftan_royal_blue_1766001008496.png' WHERE id = 1");
    $pdo->exec("UPDATE caftans SET image_path = 'uploads/caftans/caftan_wedding_gold_1766001024860.png' WHERE id = 2");
    $pdo->exec("UPDATE caftans SET image_path = 'uploads/caftans/caftan_traditional_green_1766001038146.png' WHERE id = 3");
    $pdo->exec("UPDATE caftans SET image_path = 'uploads/caftans/caftan_summer_white_1766001063178.png' WHERE id = 4");
    $pdo->exec("UPDATE caftans SET image_path = 'uploads/caftans/caftan_modern_burgundy_1766001077890.png' WHERE id = 5");
    $pdo->exec("UPDATE caftans SET image_path = 'uploads/caftans/caftan_evening_silver_1766001092921.png' WHERE id = 6");

    echo "✅ Images updated successfully!\n\n";

    // Show results
    $stmt = $pdo->query("SELECT id, name, image_path FROM caftans");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID {$row['id']}: {$row['name']} -> {$row['image_path']}\n";
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>