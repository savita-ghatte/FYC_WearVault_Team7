<?php
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            $user_email = $_GET['user_email'] ?? null;
            if ($user_email) {
                $stmt = $pdo->prepare("SELECT * FROM orders WHERE LOWER(user_email) = LOWER(?) ORDER BY id DESC");
                $stmt->execute([$user_email]);
            } else {
                $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
            }
            $orders = $stmt->fetchAll();
            // Decode items_json for clean output
            foreach ($orders as &$ord) {
                if (isset($ord['items_json'])) {
                    $ord['items'] = json_decode($ord['items_json'], true);
                }
            }
            echo json_encode(['status' => 'success', 'data' => $orders]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'POST':
        $input = getJsonInput();
        $customer_name  = trim($input['customer_name'] ?? 'Customer');
        $user_email     = trim($input['user_email'] ?? 'guest@wearvault.com');
        $total_amount   = floatval($input['total_amount'] ?? 0);
        $payment_method = trim($input['payment_method'] ?? 'COD');
        $items          = $input['items'] ?? [];

        if ($total_amount <= 0 || empty($items)) {
            echo json_encode(['status' => 'error', 'message' => 'Valid items and total amount are required.']);
            exit();
        }

        $order_number = 'WV-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        $items_json = json_encode($items);

        try {
            $stmt = $pdo->prepare("INSERT INTO orders (order_number, customer_name, user_email, total_amount, payment_method, items_json) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$order_number, $customer_name, $user_email, $total_amount, $payment_method, $items_json]);
            $orderId = $pdo->lastInsertId();

            echo json_encode([
                'status' => 'success',
                'message' => 'Order placed successfully!',
                'order' => [
                    'id' => $orderId,
                    'order_number' => $order_number,
                    'customer_name' => $customer_name,
                    'total_amount' => $total_amount,
                    'status' => 'Pending'
                ]
            ]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'PUT':
        $input = getJsonInput();
        $id = intval($input['id'] ?? 0);
        $status = trim($input['status'] ?? '');

        if ($id <= 0 || !in_array($status, ['Pending', 'Completed', 'Cancelled'])) {
            echo json_encode(['status' => 'error', 'message' => 'Valid Order ID and Status (Pending, Completed, Cancelled) are required.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Order status updated successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Method not supported.']);
        break;
}
