<?php
// CaftanVue Multi-Tenant API with Authentication & Image Upload
// Enhanced from simple PHP API to support admin features

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
$host = '127.0.0.1';
$db = 'caftanvue';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Simple JWT functions (for demonstration - use a library in production)
function generateToken($adminId)
{
    $secret = 'your-secret-key-change-this-in-production';
    $payload = [
        'admin_id' => $adminId,
        'exp' => time() + (86400 * 30) // 30 days
    ];
    return base64_encode(json_encode($payload)) . '.' . hash_hmac('sha256', json_encode($payload), $secret);
}

function verifyToken($token)
{
    if (!$token)
        return null;
    $parts = explode('.', $token);
    if (count($parts) !== 2)
        return null;

    $payload = json_decode(base64_decode($parts[0]), true);
    if (!$payload || $payload['exp'] < time())
        return null;

    return $payload['admin_id'];
}

function getAuthToken()
{
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        return $matches[1];
    }
    return null;
}

// Get request details
$method = $_SERVER['REQUEST_METHOD'];
$path = trim($_SERVER['PATH_INFO'] ?? '/', '/');
$segments = explode('/', $path);

// Route handling
$endpoint = $segments[0] ?? '';

switch ($endpoint) {
    case 'v1':
        // Handle v1 API routes
        $resource = $segments[1] ?? '';
        if ($resource === 'caftans') {
            handlePublicCaftans($pdo, array_slice($segments, 1), $method);
        } elseif ($resource === 'clients') {
            handleClients($pdo, array_slice($segments, 1), $method);
        } elseif ($resource === 'reservations') {
            handleReservations($pdo, array_slice($segments, 1), $method);
        } elseif ($resource === 'admin') {
            handleAdminRoutes($pdo, $segments, $method);
        }
        break;
    case 'auth':
        handleAuth($pdo, $segments, $method);
        break;
    case 'caftans':
        handlePublicCaftans($pdo, $segments, $method);
        break;
    case 'clients':
        handleClients($pdo, $segments, $method);
        break;
    case 'reservations':
        handleReservations($pdo, $segments, $method);
        break;
    case 'admin':
        handleAdminRoutes($pdo, $segments, $method);
        break;
    default:
        echo json_encode(['message' => 'CaftanVue Multi-Tenant API', 'version' => '2.0']);
}

function handleAuth($pdo, $segments, $method)
{
    $action = $segments[1] ?? '';

    if ($action === 'register' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate
        if (!$data['username'] || !$data['email'] || !$data['password'] || !$data['shop_name']) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already exists']);
            return;
        }

        // Create admin
        $stmt = $pdo->prepare("
            INSERT INTO admins (username, email, password, shop_name, shop_address, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['shop_name'],
            $data['shop_address'] ?? ''
        ]);

        $adminId = $pdo->lastInsertId();
        $token = generateToken($adminId);

        $admin = [
            'id' => $adminId,
            'username' => $data['username'],
            'email' => $data['email'],
            'shopName' => $data['shop_name'],
            'shopAddress' => $data['shop_address'] ?? ''
        ];

        echo json_encode(['admin' => $admin, 'token' => $token]);
    } elseif ($action === 'login' && $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$data['email']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($data['password'], $admin['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }

        $token = generateToken($admin['id']);

        echo json_encode([
            'admin' => [
                'id' => $admin['id'],
                'username' => $admin['username'],
                'email' => $admin['email'],
                'shopName' => $admin['shop_name'],
                'shopAddress' => $admin['shop_address']
            ],
            'token' => $token
        ]);
    }
}

function handlePublicCaftans($pdo, $segments, $method)
{
    if ($method === 'GET') {
        $id = $segments[1] ?? null;

        if ($id) {
            // Single caftan
            $stmt = $pdo->prepare("
                SELECT c.*, a.shop_name, a.shop_address as admin_shop_address
                FROM caftans c
                JOIN admins a ON c.admin_id = a.id
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
            $caftan = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($caftan) {
                echo json_encode(formatCaftan($caftan));
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Caftan not found']);
            }
        } else {
            // All caftans
            $stmt = $pdo->query("
                SELECT c.*, a.shop_name, a.shop_address as admin_shop_address
                FROM caftans c
                JOIN admins a ON c.admin_id = a.id
                ORDER BY c.created_at DESC
            ");
            $caftans = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(array_map('formatCaftan', $caftans));
        }
    }
}

function handleAdminRoutes($pdo, $segments, $method)
{
    // Verify authentication
    $adminId = verifyToken(getAuthToken());
    if (!$adminId) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $resource = $segments[1] ?? '';

    if ($resource === 'caftans') {
        handleAdminCaftans($pdo, $adminId, $segments, $method);
    } elseif ($resource === 'profile') {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            'id' => $admin['id'],
            'username' => $admin['username'],
            'email' => $admin['email'],
            'shopName' => $admin['shop_name'],
            'shopAddress' => $admin['shop_address']
        ]);
    }
}

function handleAdminCaftans($pdo, $adminId, $segments, $method)
{
    if ($method === 'GET') {
        // Get admin's own caftans
        $stmt = $pdo->prepare("SELECT * FROM caftans WHERE admin_id = ?");
        $stmt->execute([$adminId]);
        $caftans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(array_map('formatCaftan', $caftans));
    } elseif ($method === 'POST' && !isset($segments[2])) {
        // Create new caftan with image upload
        if (!isset($_FILES['image'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Image is required']);
            return;
        }

        // Handle image upload
        $uploadDir = __DIR__ . '/uploads/caftans/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $image = $_FILES['image'];
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $filepath = $uploadDir . $filename;

        if (!move_uploaded_file($image['tmp_name'], $filepath)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload image']);
            return;
        }

        // Get form data
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $collection = $_POST['collection'];
        $color = $_POST['color'];
        $size = $_POST['size'];
        $status = $_POST['status'];

        // Get admin's shop address
        $stmt = $pdo->prepare("SELECT shop_address FROM admins WHERE id = ?");
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch();

        // Insert caftan
        $stmt = $pdo->prepare("
            INSERT INTO caftans (admin_id, name, description, price, collection, color, size, status, is_available, image_path, shop_address, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $adminId,
            $name,
            $description,
            $price,
            $collection,
            $color,
            $size,
            $status,
            $status === 'available' ? 1 : 0,
            'uploads/caftans/' . $filename,
            $admin['shop_address']
        ]);

        $caftanId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM caftans WHERE id = ?");
        $stmt->execute([$caftanId]);
        $caftan = $stmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(201);
        echo json_encode(formatCaftan($caftan));
    } elseif ($method === 'DELETE') {
        $id = $segments[2] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Caftan ID required']);
            return;
        }

        // Verify ownership
        $stmt = $pdo->prepare("SELECT * FROM caftans WHERE id = ? AND admin_id = ?");
        $stmt->execute([$id, $adminId]);
        $caftan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$caftan) {
            http_response_code(404);
            echo json_encode(['error' => 'Caftan not found or unauthorized']);
            return;
        }

        // Delete image file
        if ($caftan['image_path']) {
            $filepath = __DIR__ . '/' . $caftan['image_path'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM caftans WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['message' => 'Caftan deleted successfully']);
    }
}

function formatCaftan($c)
{
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $imageUrl = $c['image_path'] ? $baseUrl . '/caftanvue-api/' . $c['image_path'] : null;

    return [
        'id' => (int) $c['id'],
        'name' => $c['name'],
        'description' => $c['description'],
        'imageUrl' => $imageUrl,
        'price' => (float) $c['price'],
        'collection' => $c['collection'],
        'color' => $c['color'],
        'size' => $c['size'],
        'status' => $c['status'],
        'isAvailable' => (bool) $c['is_available'],
        'shopAddress' => $c['shop_address'] ?? $c['admin_shop_address'] ?? null,
        'shopName' => $c['shop_name'] ?? null,
        'adminId' => (int) $c['admin_id']
    ];
}
?>
function handleClients($pdo, $segments, $method)
{
if ($method === 'GET') {
$id = $segments[1] ?? null;

if ($id) {
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);
if ($client) {
echo json_encode(formatClient($client));
} else {
http_response_code(404);
echo json_encode(['error' => 'Client not found']);
}
} else {
$stmt = $pdo->query("SELECT * FROM clients ORDER BY name");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(array_map('formatClient', $clients));
}
}
}

function formatClient($c)
{
return [
'id' => (int)$c['id'],
'name' => $c['name'],
'email' => $c['email'],
'phone' => $c['phone'],
'address' => $c['address'],
'cin' => $c['cin'],
'createdAt' => $c['created_at']
];
}

function handleReservations($pdo, $segments, $method)
{
if ($method === 'GET') {
$id = $segments[1] ?? null;

if ($id) {
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
$stmt->execute([$id]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);
if ($res) {
echo json_encode(formatReservation($res));
} else {
http_response_code(404);
echo json_encode(['error' => 'Reservation not found']);
}
} else {
$stmt = $pdo->query("SELECT * FROM reservations ORDER BY start_date DESC");
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(array_map('formatReservation', $reservations));
}
}
}

function formatReservation($r)
{
return [
'id' => (int)$r['id'],
'caftanId' => (int)$r['caftan_id'],
'clientId' => (int)$r['client_id'],
'startDate' => $r['start_date'],
'endDate' => $r['end_date'],
'status' => $r['status'],
'totalPrice' => (float)$r['total_price'],
'notes' => $r['notes']
];
}
