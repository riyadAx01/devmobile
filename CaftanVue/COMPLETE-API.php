<?php
// CaftanVue Complete API - Caftans, Clients, Reservations, Auth
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database
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

// Route
$method = $_SERVER['REQUEST_METHOD'];
$path = trim($_SERVER['PATH_INFO'] ?? '/', '/');
$segments = explode('/', $path);
$endpoint = $segments[0] ?? '';

switch ($endpoint) {
    case 'v1':
        $resource = $segments[1] ?? '';
        if ($resource === 'caftans') {
            handleCaftans($pdo, array_slice($segments, 1), $method);
        } elseif ($resource === 'clients') {
            handleClients($pdo, array_slice($segments, 1), $method);
        } elseif ($resource === 'reservations') {
            handleReservations($pdo, array_slice($segments, 1), $method);
        }
        break;
    case 'caftans':
    case 'clients':
    case 'reservations':
        $handler = 'handle' . ucfirst($endpoint);
        $handler($pdo, $segments, $method);
        break;
    default:
        echo json_encode(['message' => 'CaftanVue API v3', 'status' => 'online']);
}

function handleCaftans($pdo, $segments, $method)
{
    if ($method === 'GET') {
        $id = $segments[1] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("SELECT c.*, a.shop_name, a.shop_address as admin_shop_address FROM caftans c LEFT JOIN admins a ON c.admin_id = a.id WHERE c.id = ?");
            $stmt->execute([$id]);
            $caftan = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($caftan ? formatCaftan($caftan) : null);
        } else {
            $stmt = $pdo->query("SELECT c.*, a.shop_name, a.shop_address as admin_shop_address FROM caftans c LEFT JOIN admins a ON c.admin_id = a.id ORDER BY c.created_at DESC");
            echo json_encode(array_map('formatCaftan', $stmt->fetchAll(PDO::FETCH_ASSOC)));
        }
    }
}

function formatCaftan($c)
{
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $imageUrl = $c['image_path'] ? $baseUrl . '/caftanvue-api/' . $c['image_path'] : ($c['image_url'] ?: null);

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
        'adminId' => (int) ($c['admin_id'] ?? 1)
    ];
}

function handleClients($pdo, $segments, $method)
{
    if ($method === 'GET') {
        $id = $segments[1] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
            $stmt->execute([$id]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($client ? formatClient($client) : null);
        } else {
            $stmt = $pdo->query("SELECT * FROM clients ORDER BY name");
            echo json_encode(array_map('formatClient', $stmt->fetchAll(PDO::FETCH_ASSOC)));
        }
    }
}

function formatClient($c)
{
    return [
        'id' => (int) $c['id'],
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
            echo json_encode($res ? formatReservation($res) : null);
        } else {
            $stmt = $pdo->query("SELECT * FROM reservations ORDER BY start_date DESC");
            echo json_encode(array_map('formatReservation', $stmt->fetchAll(PDO::FETCH_ASSOC)));
        }
    }
}

function formatReservation($r)
{
    return [
        'id' => (int) $r['id'],
        'caftanId' => (int) $r['caftan_id'],
        'clientId' => (int) $r['client_id'],
        'startDate' => $r['start_date'],
        'endDate' => $r['end_date'],
        'status' => $r['status'],
        'totalPrice' => (float) $r['total_price'],
        'notes' => $r['notes']
    ];
}
?>