<?php
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $user_email = $_GET['user_email'] ?? 'guest@wearvault.com';
        try {
            $stmt = $pdo->prepare("SELECT * FROM custom_closet WHERE LOWER(user_email) = LOWER(?) ORDER BY id DESC");
            $stmt->execute([$user_email]);
            $items = $stmt->fetchAll();
            echo json_encode(['status' => 'success', 'data' => $items]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'POST':
        $input = getJsonInput();
        $user_email  = trim($input['user_email'] ?? 'guest@wearvault.com');
        $item_name   = trim($input['item_name'] ?? 'Clothing Item');
        $category    = trim($input['category'] ?? 'Tops');
        $image_path  = trim($input['image_path'] ?? '');
        $is_favorite = intval($input['is_favorite'] ?? 0);

        if (empty($image_path)) {
            echo json_encode(['status' => 'error', 'message' => 'Image path is required.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO custom_closet (user_email, item_name, category, image_path, is_favorite) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_email, $item_name, $category, $image_path, $is_favorite]);
            $id = $pdo->lastInsertId();

            echo json_encode([
                'status' => 'success',
                'message' => 'Closet item saved successfully!',
                'id' => $id
            ]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        $input = getJsonInput();
        $id = intval($_GET['id'] ?? ($input['id'] ?? 0));
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Valid Item ID required.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM custom_closet WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Item removed from closet.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;
}
