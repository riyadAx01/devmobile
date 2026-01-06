# 🔧 FIX UPDATE & DELETE - STEP BY STEP

## Problem:
Update and Delete show "Failed" because the PHP API doesn't have PUT and DELETE endpoints yet.

## Solution:
Add 3 functions to your `COMPLETE-API.php` file.

---

## 📝 INSTRUCTIONS:

### Step 1: Open Your PHP API File
```
C:\xampp\htdocs\caftanvue-api\COMPLETE-API.php
```

### Step 2: Add These 3 Functions

Copy these functions and paste them **BEFORE** the main routing section in your PHP file:

#### Function 1: CREATE CAFTAN (POST)
```php
function handleCreateCaftan($pdo) {
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
    $stmt = $pdo->query("SELECT c.*, a.shop_name FROM caftans c LEFT JOIN admins a ON c.admin_id = a.id WHERE c.id = $newId");
    $caftan = $stmt->fetch(PDO::FETCH_ASSOC);
    $caftan['imageUrl'] = "http://10.0.2.2/caftanvue-api/" . $caftan['image_path'];
    $caftan['isAvailable'] = $caftan['status'] === 'available';
    
    echo json_encode($caftan);
}
```

#### Function 2: UPDATE CAFTAN (PUT)
```php
function handleUpdateCaftan($pdo, $id) {
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
    $stmt->execute([
        ':id' => $id,
        ':name' => $input['name'],
        ':description' => $input['description'],
        ':price' => $input['price'],
        ':collection' => $input['collection'],
        ':color' => $input['color'],
        ':size' => $input['size'],
        ':status' => $input['status']
    ]);
    
    // Return updated caftan
    $stmt = $pdo->query("SELECT c.*, a.shop_name FROM caftans c LEFT JOIN admins a ON c.admin_id = a.id WHERE c.id = $id");
    $caftan = $stmt->fetch(PDO::FETCH_ASSOC);
    $caftan['imageUrl'] = "http://10.0.2.2/caftanvue-api/" . $caftan['image_path'];
    $caftan['isAvailable'] = $caftan['status'] === 'available';
    
    echo json_encode($caftan);
}
```

#### Function 3: DELETE CAFTAN (DELETE)
```php
function handleDeleteCaftan($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM caftans WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    echo json_encode(['message' => 'Caftan deleted', 'id' => $id]);
}
```

### Step 3: Update handleCaftans Function

Find your `handleCaftans()` function and **REPLACE** it with this:

```php
function handleCaftans($pdo, $method, $path) {
    // Check if path has ID (e.g., /caftans/123)
    $matches = [];
    if (preg_match('/\/caftans\/(\d+)/', $path, $matches)) {
        $id = $matches[1];
        
        switch ($method) {
            case 'GET':
                handleGetCaftan($pdo, $id);
                break;
            case 'PUT':
                handleUpdateCaftan($pdo, $id);  // NEW!
                break;
            case 'DELETE':
                handleDeleteCaftan($pdo, $id);  // NEW!
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    } else {
        // No ID in path
        switch ($method) {
            case 'GET':
                handleGetCaftans($pdo);
                break;
            case 'POST':
                handleCreateCaftan($pdo);  // NEW!
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    }
}
```

### Step 4: Restart Apache

In XAMPP Control Panel:
1. Click **Stop** on Apache
2. Click **Start** on Apache

### Step 5: Test in App

1. **Rebuild** Android app
2. **Run** app
3. Login as admin
4. Go to "My Caftans"
5. Try **Edit** → Change price → Save ✅
6. Try **Delete** → Confirm ✅

---

## ✅ Done!

Update and Delete will now work! 🎉

See `PHP_API_UPDATE_INSTRUCTIONS.php` for complete code reference.
