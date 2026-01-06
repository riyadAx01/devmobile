<?php
$host = '127.0.0.1';
$dbname = 'caftanvue';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $res = $pdo->query('SELECT id, name, image_url, image_path FROM caftans');
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . "\n";
        echo "Name: " . $row['name'] . "\n";
        echo "URL: [" . $row['image_url'] . "]\n";
        echo "URL (Hex): " . bin2hex($row['image_url'] ?? '') . "\n";
        echo "Path: [" . $row['image_path'] . "]\n";
        echo "-------------------\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
