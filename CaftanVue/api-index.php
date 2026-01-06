<?php
// CaftanVue Simple PHP API
// INSTALLATION: Copy this file to C:\xampp\htdocs\caftanvue-api\index.php

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

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '/', '/'));
$endpoint = $request[0] ?? '';
$id = $request[1] ?? null;

// Route requests
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
            'endpoints' => [
                'GET /api/caftans',
                'GET /api/clients',
                'GET /api/reservations'
            ]
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
                $caftan['imageUrl'] = $caftan['image_url'];
                $caftan['isAvailable'] = (bool) $caftan['is_available'];
                unset($caftan['image_url']);
                unset($caftan['is_available']);
                unset($caftan['created_at']);
                unset($caftan['updated_at']);
            }
            echo json_encode($caftan);
        } else {
            // Support search parameters
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
            // Convert field names
            foreach ($caftans as &$caftan) {
                $caftan['imageUrl'] = $caftan['image_url'];
                $caftan['isAvailable'] = (bool) $caftan['is_available'];
                unset($caftan['image_url']);
                unset($caftan['is_available']);
                unset($caftan['created_at']);
                unset($caftan['updated_at']);
            }
            echo json_encode($caftans);
        }
    }
}

function handleClients($pdo, $method, $id)
{
    if ($method === 'GET') {
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
            $stmt->execute([$id]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($client) {
                $client['createdAt'] = $client['created_at'];
                unset($client['created_at']);
                unset($client['updated_at']);
            }
            echo json_encode($client);
        } else {
            $stmt = $pdo->query("SELECT * FROM clients ORDER BY name");
            $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Convert field names to camelCase
            foreach ($clients as &$client) {
                $client['createdAt'] = $client['created_at'] ?? date('Y-m-d H:i:s');
                unset($client['created_at']);
                unset($client['updated_at']);
            }
            echo json_encode($clients);
        }
    }
}

function handleReservations($pdo, $method, $id)
{
    if ($method === 'GET') {
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
            $stmt->execute([$id]);
            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($reservation) {
                $reservation['caftanId'] = $reservation['caftan_id'];
                $reservation['clientId'] = $reservation['client_id'];
                $reservation['startDate'] = $reservation['start_date'];
                $reservation['endDate'] = $reservation['end_date'];
                $reservation['totalPrice'] = (float) $reservation['total_price'];
                unset($reservation['caftan_id']);
                unset($reservation['client_id']);
                unset($reservation['start_date']);
                unset($reservation['end_date']);
                unset($reservation['total_price']);
                unset($reservation['created_at']);
                unset($reservation['updated_at']);
            }
            echo json_encode($reservation);
        } else {
            $stmt = $pdo->query("SELECT * FROM reservations ORDER BY start_date DESC");
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Convert field names
            foreach ($reservations as &$reservation) {
                $reservation['caftanId'] = $reservation['caftan_id'];
                $reservation['clientId'] = $reservation['client_id'];
                $reservation['startDate'] = $reservation['start_date'];
                $reservation['endDate'] = $reservation['end_date'];
                $reservation['totalPrice'] = (float) $reservation['total_price'];
                unset($reservation['caftan_id']);
                unset($reservation['client_id']);
                unset($reservation['start_date']);
                unset($reservation['end_date']);
                unset($reservation['total_price']);
                unset($reservation['created_at']);
                unset($reservation['updated_at']);
            }
            echo json_encode($reservations);
        }
    }
}
?>