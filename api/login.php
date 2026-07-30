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
    echo json_encode(['status' => 'error', 'message' => 'Please provide both email and password.']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID. Please Sign Up first!']);
        exit();
    }

    if ($user['password'] === $password) {
        unset($user['password']); // don't send password hash/plain in response
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful!',
            'user' => $user
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid password. Please try again.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
