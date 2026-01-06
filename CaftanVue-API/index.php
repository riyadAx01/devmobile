<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Read body once for both logging (if needed later) and processing
$rawBody = file_get_contents('php://input');

// Database connection with Self-Healing
$host = '127.0.0.1';
$dbname = 'caftanvue';
$username = 'root';
$password = '';

try {
    // 1. Connect to MySQL Server (no DB)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Create Database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo->exec("USE `$dbname`");

    // 3. Create Tables if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        shop_name VARCHAR(100) DEFAULT 'Caftan Shop',
        shop_address VARCHAR(255) DEFAULT 'Casablanca, Morocco',
        password VARCHAR(255) DEFAULT 'admin123'
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS caftans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        image_url VARCHAR(500),
        image_path VARCHAR(500),
        price DECIMAL(10, 2) NOT NULL,
        collection VARCHAR(100),
        color VARCHAR(50),
        size VARCHAR(20),
        status VARCHAR(50) DEFAULT 'available',
        is_available TINYINT(1) DEFAULT 1,
        shop_address VARCHAR(255),
        shop_name VARCHAR(100),
        admin_id INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (admin_id) REFERENCES admins(id)
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS clients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(100),
        phone VARCHAR(20),
        address TEXT,
        cin VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS reservations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        caftan_id INT,
        client_id INT,
        start_date DATE,
        end_date DATE,
        status VARCHAR(50) DEFAULT 'pending',
        total_price DECIMAL(10, 2),
        notes TEXT,
        FOREIGN KEY (caftan_id) REFERENCES caftans(id)
    )");

    // Ensure at least one admin exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO admins (id, shop_name) VALUES (1, 'Caftan Luxe')");
    }

} catch (PDOException $e) {
    // Return empty list [] for GET requests to prevent App Crash (SerializationException)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode([]);
        exit();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
}

// Get request details
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Helper function for dynamic image URLs
function buildImageUrl($path) {
    if (!$path) return null;
    // Always use 10.0.2.2:8000 for Android emulator compatibility
    $baseUrl = "http://10.0.2.2:8000";
    return $baseUrl . '/' . ltrim($path, '/');
}

// Route requests
if (strpos($path, '/uploads/') !== false) {
    // Direct file check for uploads to handle wrong extensions/content-types
    $filePath = __DIR__ . $path;
    if (file_exists($filePath) && !is_dir($filePath)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        // Ensure CORS for images too
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: $mime");
        header("Content-Length: " . filesize($filePath));
        readfile($filePath);
        exit();
    }
}

if (strpos($path, '/caftans') !== false) {
    handleCaftans($pdo, $method, $path, $rawBody);
} elseif (strpos($path, '/clients') !== false) {
    handleClients($pdo, $method, $path, $rawBody);
} elseif (strpos($path, '/reservations') !== false) {
    handleReservations($pdo, $method, $path, $rawBody);
} else {
    // Return empty list instead of object to prevent Retrofit crash on root URL scan
    // But ideally 404. However, if app hits root, we don't want crash.
    // Let's stick to 404 but ensure JSON is correct.
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found', 'path' => $path]);
}

// ============================================
// CAFTANS ENDPOINTS
// ============================================

function handleCaftans($pdo, $method, $path, $body)
{
    $matches = [];
    if (preg_match('/\/caftans\/(\d+)/', $path, $matches)) {
        $id = $matches[1];
 
        switch ($method) {
            case 'GET':
                handleGetCaftan($pdo, $id);
                break;
            case 'PUT':
                handleUpdateCaftan($pdo, $id, $body);
                break;
            case 'DELETE':
                handleDeleteCaftan($pdo, $id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    } else {
        switch ($method) {
            case 'GET':
                handleGetCaftans($pdo);
                break;
            case 'POST':
                handleCreateCaftan($pdo, $body);
                break;
            case 'OPTIONS':
                http_response_code(200);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    }
}

function handleGetCaftans($pdo)
{
    $stmt = $pdo->query("
        SELECT c.*, a.shop_name, a.shop_address as admin_shop_address
        FROM caftans c 
        LEFT JOIN admins a ON c.admin_id = a.id
        ORDER BY c.created_at DESC
    ");
    $caftans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(array_map('formatCaftanStrict', $caftans));
}

function handleGetCaftan($pdo, $id)
{
    $stmt = $pdo->prepare("
        SELECT c.*, a.shop_name, a.shop_address as admin_shop_address
        FROM caftans c 
        LEFT JOIN admins a ON c.admin_id = a.id 
        WHERE c.id = :id
    ");
    $stmt->execute([':id' => $id]);
    $caftan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($caftan) {
        echo json_encode(formatCaftanStrict($caftan));
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Caftan not found']);
    }
}

function handleCreateCaftan($pdo, $body)
{
    $input = json_decode($body, true);

    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }

    $adminId = isset($input['adminId']) ? $input['adminId'] : 1;
    
    // Check if ID is provided and valid (greater than 0)
    $hasId = isset($input['id']) && (int)$input['id'] > 0;
    
    if ($hasId) {
        $sql = "INSERT INTO caftans (id, name, description, image_url, price, collection, color, size, status, admin_id) 
                VALUES (:id, :name, :description, :image_url, :price, :collection, :color, :size, :status, :admin_id)";
    } else {
        $sql = "INSERT INTO caftans (name, description, image_url, price, collection, color, size, status, admin_id) 
                VALUES (:name, :description, :image_url, :price, :collection, :color, :size, :status, :admin_id)";
    }

    try {
        $stmt = $pdo->prepare($sql);
        
        $params = [
            ':name' => $input['name'],
            ':description' => $input['description'],
            ':image_url' => $input['imageUrl'] ?? null,
            ':price' => $input['price'],
            ':collection' => $input['collection'],
            ':color' => $input['color'],
            ':size' => $input['size'],
            ':status' => $input['status'],
            ':admin_id' => $adminId
        ];
        
        if ($hasId) {
            $params[':id'] = (int)$input['id'];
        }
        
        $stmt->execute($params);
        $newId = $hasId ? (int)$input['id'] : $pdo->lastInsertId();
        handleGetCaftan($pdo, $newId);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage(), 'code' => $e->getCode()]);
    }
}

function handleUpdateCaftan($pdo, $id, $body)
{
    $input = json_decode($body, true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }

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

    try {
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':id' => $id,
            ':name' => $input['name'],
            ':description' => $input['description'],
            ':image_url' => $input['imageUrl'] ?? null,
            ':price' => $input['price'],
            ':collection' => $input['collection'],
            ':color' => $input['color'],
            ':size' => $input['size'],
            ':status' => $input['status']
        ]);

        if ($result) {
            handleGetCaftan($pdo, $id);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update caftan']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function handleDeleteCaftan($pdo, $id)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM caftans WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);
        if ($result) {
            echo json_encode(['message' => 'Caftan deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete']);
        }
    } catch (PDOException $e) {
         http_response_code(500);
         echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function formatCaftanStrict($c) {
    if (!$c) return null;
    
    $rawImageUrl = trim($c['image_url'] ?? '');
    
    // Clean null bytes and other invisible characters
    $rawImageUrl = str_replace("\0", '', $rawImageUrl);
    $rawImageUrl = preg_replace('/[\x00-\x1F\x7F]/', '', $rawImageUrl);
    
    $imageUrl = null;
    
    if (!empty($rawImageUrl)) {
        // If it's a remote URL (not pointing to our own 10.0.2.2 or localhost), keep it
        if (preg_match('/^https?:\/\//', $rawImageUrl) && 
            strpos($rawImageUrl, '10.0.2.2') === false && 
            strpos($rawImageUrl, 'localhost') === false && 
            strpos($rawImageUrl, '127.0.0.1') === false) {
            $imageUrl = $rawImageUrl;
        } else {
            // It's a filename, a relative path, or a local path (C:\...)
            // Extract just the filename to be robust
            $filename = basename($rawImageUrl);
            
            // Search for the file in uploads/caftans with common extensions
            // This handles cases where user pastes "image" but file is "image.png"
            // or "image.png" but file is actually a JPEG.
            $extensions = ['', '.png', '.jpg', '.jpeg', '.webp'];
            $foundPath = null;
            
            foreach ($extensions as $ext) {
                $testPath = 'uploads/caftans/' . $filename . $ext;
                if (file_exists(__DIR__ . '/' . $testPath)) {
                    $foundPath = $testPath;
                    break;
                }
            }
            
            if ($foundPath) {
                $imageUrl = buildImageUrl($foundPath);
            } else {
                // If not found in uploads, but it was a full URL to our server, try to clean it
                if (strpos($rawImageUrl, 'http') === 0) {
                     // Extract filename and retry
                     $filename = basename(parse_url($rawImageUrl, PHP_URL_PATH));
                     foreach ($extensions as $ext) {
                        $testPath = 'uploads/caftans/' . $filename . $ext;
                        if (file_exists(__DIR__ . '/' . $testPath)) {
                            $foundPath = $testPath;
                            break;
                        }
                    }
                    if ($foundPath) {
                        $imageUrl = buildImageUrl($foundPath);
                    } else {
                        $imageUrl = $rawImageUrl; // Fallback to original
                    }
                } else {
                    $imageUrl = $rawImageUrl; // Fallback
                }
            }
        }
    }
    
    return [
        'id' => (int)$c['id'],
        'name' => $c['name'],
        'description' => $c['description'],
        'imageUrl' => $imageUrl,
        'price' => (float)$c['price'], // CRITICAL: Ensure float for Double
        'collection' => $c['collection'] ?? '',
        'color' => $c['color'] ?? '',
        'size' => $c['size'] ?? '',
        'status' => $c['status'] ?? 'available',
        'isAvailable' => (bool)($c['is_available'] ?? ($c['status'] === 'available')),
        'shopAddress' => $c['shop_address'] ?? $c['admin_shop_address'] ?? null,
        'shopName' => $c['shop_name'] ?? null,
        'adminId' => (int)($c['admin_id'] ?? 1)
    ];
}

// ============================================
// CLIENTS ENDPOINTS
// ============================================



function handleClients($pdo, $method, $path, $body)
{
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM clients ORDER BY name");
            $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(array_map('formatClient', $clients));
            break;
        case 'POST':
            handleCreateClient($pdo, $body);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

function handleCreateClient($pdo, $body)
{
    $input = json_decode($body, true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO clients (name, email, phone, address, cin)
        VALUES (:name, :email, :phone, :address, :cin)
    ");
    
    try {
        $stmt->execute([
            ':name' => $input['name'],
            ':email' => $input['email'],
            ':phone' => $input['phone'],
            ':address' => $input['address'],
            ':cin' => $input['cin']
        ]);
        $newId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$newId]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        http_response_code(201);
        echo json_encode(formatClient($client));
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function formatClient($c) {
    if (!$c) return null;
    return [
        'id' => (int)$c['id'],
        'name' => $c['name'],
        'email' => $c['email'],
        'phone' => $c['phone'],
        'address' => $c['address'],
        'cin' => $c['cin'],
        'createdAt' => $c['created_at'] ?? date('Y-m-d H:i:s')
    ];
}

// ============================================
// RESERVATIONS ENDPOINTS
// ============================================

function handleReservations($pdo, $method, $path, $body)
{
    $matches = [];
    if (preg_match('/\/reservations\/(\d+)/', $path, $matches)) {
        $id = $matches[1];
        switch ($method) {
            case 'PUT':
                handleUpdateReservation($pdo, $id, $body);
                break;
            case 'DELETE':
                $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['message' => 'Deleted']);
                break;
             // Add other methods if needed
            default:
                http_response_code(405);
                 echo json_encode(['error' => 'Method not allowed']);
        }
    } else {
        switch ($method) {
            case 'GET':
                 $stmt = $pdo->query("SELECT * FROM reservations ORDER BY start_date DESC");
                 $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                 echo json_encode(array_map('formatReservation', $reservations));
                 break;
            case 'POST':
                handleCreateReservation($pdo, $body);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    }
}

function handleCreateReservation($pdo, $body)
{
    $input = json_decode($body, true);
    $stmt = $pdo->prepare("
        INSERT INTO reservations (caftan_id, client_id, start_date, end_date, status, total_price, notes)
        VALUES (:cid, :clid, :start, :end, :status, :price, :notes)
    ");
    $stmt->execute([
        ':cid' => $input['caftanId'],
        ':clid' => $input['clientId'],
        ':start' => $input['startDate'],
        ':end' => $input['endDate'],
        ':status' => $input['status'],
        ':price' => $input['totalPrice'],
        ':notes' => $input['notes'] ?? null
    ]);
    $newId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
    $stmt->execute([$newId]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(formatReservation($reservation));
}

function handleUpdateReservation($pdo, $id, $body)
{
    $input = json_decode($body, true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }

    $stmt = $pdo->prepare("
        UPDATE reservations SET 
            caftan_id = :cid,
            client_id = :clid,
            start_date = :start,
            end_date = :end,
            status = :status,
            total_price = :price,
            notes = :notes
        WHERE id = :id
    ");

    try {
        $stmt->execute([
            ':id' => $id,
            ':cid' => $input['caftanId'],
            ':clid' => $input['clientId'],
            ':start' => $input['startDate'],
            ':end' => $input['endDate'],
            ':status' => $input['status'],
            ':price' => $input['totalPrice'],
            ':notes' => $input['notes'] ?? null
        ]);
        
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$id]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(formatReservation($reservation));
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

function formatReservation($r) {
    if (!$r) return null;
    return [
        'id' => (int)$r['id'],
        'caftanId' => (int)$r['caftan_id'],
        'clientId' => (int)$r['client_id'],
        'startDate' => $r['start_date'],
        'endDate' => $r['end_date'],
        'status' => $r['status'],
        'totalPrice' => (float)$r['total_price'],
        'notes' => $r['notes'] ?? null
    ];
}
?>