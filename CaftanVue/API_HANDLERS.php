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