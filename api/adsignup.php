<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$input = getJsonInput();

$fname    = trim($input['fname'] ?? '');
$lname    = trim($input['lname'] ?? '');
$email    = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

if (empty($fname) || empty($lname) || empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all mandatory administrator fields.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid administrator email address.']);
    exit();
}

try {
    $check = $pdo->prepare("SELECT id FROM admins WHERE LOWER(email) = LOWER(?)");
    $check->execute([$email]);
    if ($check->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'An administrator account with this email already exists.']);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO admins (fname, lname, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$fname, $lname, $email, $password]);

    $adminId = $pdo->lastInsertId();

    echo json_encode([
        'status' => 'success',
        'message' => 'Admin Account Created Successfully!',
        'admin' => [
            'id' => $adminId,
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
