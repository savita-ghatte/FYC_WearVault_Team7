<?php
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $user_email = $_GET['user_email'] ?? 'guest@wearvault.com';
        try {
            $stmt = $pdo->prepare("SELECT * FROM saved_looks WHERE LOWER(user_email) = LOWER(?) ORDER BY id DESC");
            $stmt->execute([$user_email]);
            $looks = $stmt->fetchAll();
            foreach ($looks as &$look) {
                if (isset($look['items_json'])) {
                    $look['items'] = json_decode($look['items_json'], true);
                }
            }
            echo json_encode(['status' => 'success', 'data' => $looks]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'POST':
        $input = getJsonInput();
        $user_email  = trim($input['user_email'] ?? 'guest@wearvault.com');
        $look_name   = trim($input['look_name'] ?? 'My Custom Look');
        $items       = $input['items'] ?? [];
        $is_favorite = intval($input['is_favorite'] ?? 0);

        if (empty($items)) {
            echo json_encode(['status' => 'error', 'message' => 'Items are required for saving look.']);
            exit();
        }

        $items_json = json_encode($items);

        try {
            $stmt = $pdo->prepare("INSERT INTO saved_looks (user_email, look_name, items_json, is_favorite) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_email, $look_name, $items_json, $is_favorite]);
            $id = $pdo->lastInsertId();

            echo json_encode([
                'status' => 'success',
                'message' => 'Outfit look saved successfully!',
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
            echo json_encode(['status' => 'error', 'message' => 'Valid Look ID required.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM saved_looks WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Look removed.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;
}
