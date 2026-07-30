<?php
require_once __DIR__ . '/db.php';

try {
    $stmt = $pdo->query("SELECT id, fname, lname, email, phone, age, notify, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'count' => count($users),
        'data' => $users
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
