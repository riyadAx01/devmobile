<?php
// ADD THESE FUNCTIONS TO YOUR COMPLETE-API.php FILE

// ============================================
// UPDATE CAFTAN (PUT REQUEST)
// ============================================
function handleUpdateCaftan($pdo, $id)
{
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        return;
    }

    // Build UPDATE query
    $sql = "UPDATE caftans SET 
            name = :name,
            description = :description,
            price = :price,
            collection = :collection,
            color = :color,
            size = :size,
            status = :status";

    // Add shop_address if provided
    if (isset($input['shopAddress'])) {
        $sql .= ", shop_address = :shop_address";
    }

    $sql .= " WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $input['name']);
    $stmt->bindParam(':description', $input['description']);
    $stmt->bindParam(':price', $input['price']);
    $stmt->bindParam(':collection', $input['collection']);
    $stmt->bindParam(':color', $input['color']);
    $stmt->bindParam(':size', $input['size']);
    $stmt->bindParam(':status', $input['status']);

    if (isset($input['shopAddress'])) {
        $stmt->bindParam(':shop_address', $input['shopAddress']);
    }

    if ($stmt->execute()) {
        // Fetch updated caftan
        $stmt = $pdo->prepare("
            SELECT c.*, a.shop_name, a.shop_address 
            FROM caftans c 
            LEFT JOIN admins a ON c.admin_id = a.id 
            WHERE c.id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $caftan = $stmt->fetch(PDO::FETCH_ASSOC);

        // Format response
        $caftan['imageUrl'] = "http://10.0.2.2/caftanvue-api/" . $caftan['image_path'];
        $caftan['isAvailable'] = $caftan['status'] === 'available';

        echo json_encode($caftan);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update caftan']);
    }
}

// ============================================
// DELETE CAFTAN (DELETE REQUEST)
// ============================================
function handleDeleteCaftan($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM caftans WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Caftan deleted successfully', 'id' => $id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete caftan']);
    }
}

// ============================================
// CREATE CAFTAN (POST REQUEST)
// ============================================
function handleCreateCaftan($pdo)
{
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        return;
    }

    // Default admin_id if not provided
    $adminId = isset($input['adminId']) ? $input['adminId'] : 1;

    $sql = "INSERT INTO caftans 
            (name, description, price, collection, color, size, status, admin_id";

    // Add shop_address if provided
    if (isset($input['shopAddress'])) {
        $sql .= ", shop_address";
    }

    $sql .= ") VALUES (:name, :description, :price, :collection, :color, :size, :status, :admin_id";

    if (isset($input['shopAddress'])) {
        $sql .= ", :shop_address";
    }

    $sql .= ")";

    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':name', $input['name']);
    $stmt->bindParam(':description', $input['description']);
    $stmt->bindParam(':price', $input['price']);
    $stmt->bindParam(':collection', $input['collection']);
    $stmt->bindParam(':color', $input['color']);
    $stmt->bindParam(':size', $input['size']);
    $stmt->bindParam(':status', $input['status']);
    $stmt->bindParam(':admin_id', $adminId, PDO::PARAM_INT);

    if (isset($input['shopAddress'])) {
        $stmt->bindParam(':shop_address', $input['shopAddress']);
    }

    if ($stmt->execute()) {
        $newId = $pdo->lastInsertId();

        // Fetch created caftan
        $stmt = $pdo->prepare("
            SELECT c.*, a.shop_name, a.shop_address 
            FROM caftans c 
            LEFT JOIN admins a ON c.admin_id = a.id 
            WHERE c.id = :id
        ");
        $stmt->bindParam(':id', $newId, PDO::PARAM_INT);
        $stmt->execute();

        $caftan = $stmt->fetch(PDO::FETCH_ASSOC);

        // Format response
        $caftan['imageUrl'] = isset($caftan['image_path'])
            ? "http://10.0.2.2/caftanvue-api/" . $caftan['image_path']
            : null;
        $caftan['isAvailable'] = $caftan['status'] === 'available';

        http_response_code(201);
        echo json_encode($caftan);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create caftan']);
    }
}

// ============================================
// MAIN ROUTER - UPDATE THIS SECTION
// ============================================
/*
In your COMPLETE-API.php, find the handleCaftans() function and UPDATE it:

function handleCaftans($pdo, $method, $path) {
    // Extract ID from path if present
    $matches = [];
    if (preg_match('/\/caftans\/(\d+)/', $path, $matches)) {
        $id = $matches[1];
        
        switch ($method) {
            case 'GET':
                // Get single caftan
                handleGetCaftan($pdo, $id);
                break;
            case 'PUT':
                // UPDATE CAFTAN (NEW!)
                handleUpdateCaftan($pdo, $id);
                break;
            case 'DELETE':
                // DELETE CAFTAN (NEW!)
                handleDeleteCaftan($pdo, $id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    } else {
        switch ($method) {
            case 'GET':
                // Get all caftans
                handleGetCaftans($pdo);
                break;
            case 'POST':
                // CREATE CAFTAN (NEW!)
                handleCreateCaftan($pdo);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    }
}
*/
?>