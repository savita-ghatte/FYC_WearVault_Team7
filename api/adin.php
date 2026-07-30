<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$input = getJsonInput();

$email    = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter admin email and password.']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if (!$admin) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Admin ID. Please Sign Up first!']);
        exit();
    }

    if ($admin['password'] === $password) {
        unset($admin['password']);
        echo json_encode([
            'status' => 'success',
            'message' => 'Admin Authentication Successful!',
            'admin' => $admin
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid administrator password. Access denied.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
