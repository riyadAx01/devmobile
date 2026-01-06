<?php
// CaftanVue API - CORRECTED VERSION with Proper Type Conversion
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Database connection
$host = '127.0.0.1';
$db = 'caftanvue';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Get request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '/', '/'));
$endpoint = $request[0] ?? '';
$id = $request[1] ?? null;

// Route
switch ($endpoint) {
    case 'caftans':
        handleCaftans($pdo, $method, $id);
        break;
    case 'clients':
        handleClients($pdo, $method, $id);
        break;
    case 'reservations':
        handleReservations($pdo, $method, $id);
        break;
    default:
        echo json_encode([
            'message' => 'CaftanVue API is running!',
            'endpoints' => ['GET /caftans', 'GET /clients', 'GET /reservations']
        ]);
}

function handleCaftans($pdo, $method, $id)
{
    if ($method === 'GET') {
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM caftans WHERE id = ?");
            $stmt->execute([$id]);
            $caftan = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($caftan) {
                $caftan = convertCaftan($caftan);
            }
            echo json_encode($caftan);
        } else {
            $collection = $_GET['collection'] ?? null;
            $status = $_GET['status'] ?? null;
            $color = $_GET['color'] ?? null;

            $sql = "SELECT * FROM caftans WHERE 1=1";
            $params = [];

            if ($collection) {
                $sql .= " AND collection = ?";
                $params[] = $collection;
            }
            if ($status) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }
            if ($color) {
                $sql .= " AND color = ?";
                $params[] = $color;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $caftans = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($caftans as &$caftan) {
                $caftan = convertCaftan($caftan);
            }
            echo json_encode($caftans);
        }
    }
}

function convertCaftan($c)
{
    return [
        'id' => (int) $c['id'],
        'name' => $c['name'],
        'description' => $c['description'],
        'imageUrl' => $c['image_url'],
        'price' => (float) $c['price'], // IMPORTANT: Convert to number!
        'collection' => $c['collection'],
        'color' => $c['color'],
        'size' => $c['size'],
        'status' => $c['status'],
        'isAvailable' => (bool) $c['is_available']
    ];
}

function handleClients($pdo, $method, $id)
{
    if ($method === 'GET') {
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
            $stmt->execute([$id]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($client) {
                $client = convertClient($client);
            }
            echo json_encode($client);
        } else {
            $stmt = $pdo->query("SELECT * FROM clients ORDER BY name");
            $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($clients as &$client) {
                $client = convertClient($client);
            }
            echo json_encode($clients);
        }
    }
}

function convertClient($c)
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

function handleReservations($pdo, $method, $id)
{
    if ($method === 'GET') {
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
            $stmt->execute([$id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($res) {
                $res = convertReservation($res);
            }
            echo json_encode($res);
        } else {
            $stmt = $pdo->query("SELECT * FROM reservations ORDER BY start_date DESC");
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($reservations as &$res) {
                $res = convertReservation($res);
            }
            echo json_encode($reservations);
        }
    }
}

function convertReservation($r)
{
    return [
        'id' => (int) $r['id'],
        'caftanId' => (int) $r['caftan_id'],
        'clientId' => (int) $r['client_id'],
        'startDate' => $r['start_date'],
        'endDate' => $r['end_date'],
        'status' => $r['status'],
        'totalPrice' => (float) $r['total_price'], // IMPORTANT: Convert to number!
        'notes' => $r['notes']
    ];
}
?>