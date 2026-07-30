<?php
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            $category = $_GET['category'] ?? null;
            if ($category) {
                $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY id DESC");
                $stmt->execute([$category]);
            } else {
                $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
            }
            $products = $stmt->fetchAll();
            echo json_encode(['status' => 'success', 'data' => $products]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'POST':
        $input = getJsonInput();
        $name        = trim($input['name'] ?? '');
        $category    = trim($input['category'] ?? '');
        $price       = floatval($input['price'] ?? 0);
        $stock       = intval($input['stock'] ?? 10);
        $image_url   = trim($input['image_url'] ?? '');
        $description = trim($input['description'] ?? '');

        if (empty($name) || empty($category) || $price <= 0 || empty($image_url)) {
            echo json_encode(['status' => 'error', 'message' => 'Product name, category, valid price, and image URL are required.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image_url, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category, $price, $stock, $image_url, $description]);
            $id = $pdo->lastInsertId();

            echo json_encode([
                'status' => 'success',
                'message' => 'Product Added Successfully!',
                'product' => [
                    'id' => $id,
                    'name' => $name,
                    'category' => $category,
                    'price' => $price,
                    'stock' => $stock,
                    'image_url' => $image_url,
                    'description' => $description
                ]
            ]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        $input = getJsonInput();
        $id = intval($_GET['id'] ?? ($input['id'] ?? 0));

        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Valid Product ID is required.']);
            exit();
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Method not supported.']);
        break;
}
