<?php
$host = '127.0.0.1';
$dbname = 'caftanvue';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "UPDATE caftans SET 
            name = :name,
            description = :description,
            image_url = :image_url,
            price = :price,
            collection = :collection,
            color = :color,
            size = :size,
            status = :status
            WHERE id = :id";
            
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':id' => 2,
        ':name' => 'j jd updated',
        ':description' => 'ndjvnfjdnvdj updated',
        ':image_url' => 'caftan_traditional_green_1766001038146.png',
        ':price' => 3000.0,
        ':collection' => 'TRADITIONAL',
        ':color' => 'GREEN',
        ':size' => 'One Size',
        ':status' => 'available'
    ]);

    echo "Update result: " . ($result ? "Success" : "Failure") . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
