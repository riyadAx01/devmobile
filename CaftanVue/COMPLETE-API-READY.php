<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'caftanvue';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Get request details
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route requests
if (strpos($path, '/caftans') !== false) {
    handleCaftans($pdo, $method, $path);
} elseif (strpos($path, '/clients') !== false) {
    handleClients($pdo, $method, $path);
} elseif (strpos($path, '/reservations') !== false) {
    handleReservations($pdo, $method, $path);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}

// ============================================
// CAFTANS ENDPOINTS
// ============================================

function handleCaftans($pdo, $method, $path)
{
    $matches = [];
    if (preg_match('/\/caftans\/(\d+)/', $path, $matches)) {
        $id = $matches[1];

        switch ($method) {
            case 'GET':
                handleGetCaftan($pdo, $id);
                break;
            case 'PUT':
                handleUpdateCaftan($pdo, $id);
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
                handleCreateCaftan($pdo);
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
        SELECT c.*, a.shop_name, a.shop_address 
        FROM caftans c 
        LEFT JOIN admins a ON c.admin_id = a.id
    ");
    $caftans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($caftans as &$caftan) {
        $caftan['imageUrl'] = isset($caftan['image_path'])
            ? "http://10.0.2.2/caftanvue-api/" . $caftan['image_path']
            : null;
        $caftan['isAvailable'] = $caftan['status'] === 'available';
    }

    echo json_encode($caftans);
}

function handleGetCaftan($pdo, $id)
{
    $stmt = $pdo->prepare("
        SELECT c.*, a.shop_name, a.shop_address 
        FROM caftans c 
        LEFT JOIN admins a ON c.admin_id = a.id 
        WHERE c.id = :id
    ");
    $stmt->execute([':id' => $id]);
    $caftan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($caftan) {
        $caftan['imageUrl'] = isset($caftan['image_path'])
            ? "http://10.0.2.2/caftanvue-api/" . $caftan['image_path']
            : null;
        $caftan['isAvailable'] = $caftan['status'] === 'available';
        echo json_encode($caftan);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Caftan not found']);
    }
}

function handleCreateCaftan($pdo)
{
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }

    $adminId = isset($input['adminId']) ? $input['adminId'] : 1;

    $sql = "INSERT INTO caftans (name, description, price, collection, color, size, status, admin_id) 
            VALUES (:name, :description, :price, :collection, :color, :size, :status, :admin_id)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $input['name'],
        ':description' => $input['description'],
        ':price' => $input['price'],
        ':collection' => $input['collection'],
        ':color' => $input['color'],
        ':size' => $input['size'],
        ':status' => $input['status'],
        ':admin_id' => $adminId
    ]);

    $newId = $pdo->lastInsertId();

    // Return created caftan
    $stmt = $pdo->prepare("
        SELECT c.*, a.shop_name, a.shop_address 
        FROM caftans c 
        LEFT JOIN admins a ON c.admin_id = a.id 
        WHERE c.id = :id
    ");
    $stmt->execute([':id' => $newId]);
    $caftan = $stmt->fetch(PDO::FETCH_ASSOC);

    $caftan['imageUrl'] = isset($caftan['image_path'])
        ? "http://10.0.2.2/caftanvue-api/" . $caftan['image_path']
        : null;
    $caftan['isAvailable'] = $caftan['status'] === 'available';

    http_response_code(201);
    echo json_encode($caftan);
}

function handleUpdateCaftan($pdo, $id)
{
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }

    $sql = "UPDATE caftans SET 
            name = :name,
            description = :description,
            price = :price,
            collection = :collection,
            color = :color,
            size = :size,
            status = :status
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':id' => $id,
        ':name' => $input['name'],
        ':description' => $input['description'],
        ':price' => $input['price'],
        ':collection' => $input['collection'],
        ':color' => $input['color'],
        ':size' => $input['size'],
        ':status' => $input['status']
    ]);

    if ($result) {
        // Return updated caftan
        $stmt = $pdo->prepare("
            SELECT c.*, a.shop_name, a.shop_address 
            FROM caftans c 
            LEFT JOIN admins a ON c.admin_id = a.id 
            WHERE c.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $caftan = $stmt->fetch(PDO::FETCH_ASSOC);

        $caftan['imageUrl'] = isset($caftan['image_path'])
            ? "http://10.0.2.2/caftanvue-api/" . $caftan['image_path']
            : null;
        $caftan['isAvailable'] = $caftan['status'] === 'available';

        echo json_encode($caftan);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update caftan']);
    }
}

function handleDeleteCaftan($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM caftans WHERE id = :id");
    $result = $stmt->execute([':id' => $id]);

    if ($result) {
        echo json_encode(['message' => 'Caftan deleted successfully', 'id' => $id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete caftan']);
    }
}

// ============================================
// CLIENTS ENDPOINTS
// ============================================

function handleClients($pdo, $method, $path)
{
    switch ($method) {
        case 'GET':
            handleGetClients($pdo);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

function handleGetClients($pdo)
{
    $stmt = $pdo->query("SELECT * FROM clients");
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($clients);
}

// ============================================
// RESERVATIONS ENDPOINTS
// ============================================

function handleReservations($pdo, $method, $path)
{
    $matches = [];
    if (preg_match('/\/reservations\/(\d+)/', $path, $matches)) {
        $id = $matches[1];

        switch ($method) {
            case 'DELETE':
                handleDeleteReservation($pdo, $id);
                break;
            case 'PUT':
                handleUpdateReservation($pdo, $id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    } else {
        switch ($method) {
            case 'GET':
                handleGetReservations($pdo);
                break;
            case 'POST':
                handleCreateReservation($pdo);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    }
}

function handleGetReservations($pdo)
{
    $stmt = $pdo->query("SELECT * FROM reservations");
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reservations);
}

function handleCreateReservation($pdo)
{
    $input = json_decode(file_get_contents('php://input'), true);

    $stmt = $pdo->prepare("
        INSERT INTO reservations (caftan_id, client_id, start_date, end_date, status, total_price, notes)
        VALUES (:caftan_id, :client_id, :start_date, :end_date, :status, :total_price, :notes)
    ");

    $stmt->execute([
        ':caftan_id' => $input['caftanId'],
        ':client_id' => $input['clientId'],
        ':start_date' => $input['startDate'],
        ':end_date' => $input['endDate'],
        ':status' => $input['status'],
        ':total_price' => $input['totalPrice'],
        ':notes' => $input['notes'] ?? null
    ]);

    $newId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = :id");
    $stmt->execute([':id' => $newId]);

    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}

function handleUpdateReservation($pdo, $id)
{
    $input = json_decode(file_get_contents('php://input'), true);

    $stmt = $pdo->prepare("
        UPDATE reservations 
        SET status = :status
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id,
        ':status' => $input['status']
    ]);

    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}

function handleDeleteReservation($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(['message' => 'Reservation deleted successfully']);
}
?>