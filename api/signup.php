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
$phone    = trim($input['phone'] ?? '');
$age      = intval($input['age'] ?? 0);
$notify   = trim($input['notify'] ?? 'email');

if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($phone) || $age <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all mandatory fields.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit();
}

if (!preg_match('/^[0-9]{10}$/', $phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Phone number must be exactly 10 digits.']);
    exit();
}

try {
    // Check for existing user
    $check = $pdo->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
    $check->execute([$email]);
    if ($check->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'An account with this email already exists.']);
        exit();
    }

    // Insert user into MySQL
    $stmt = $pdo->prepare("INSERT INTO users (fname, lname, email, password, phone, age, notify) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fname, $lname, $email, $password, $phone, $age, $notify]);

    $userId = $pdo->lastInsertId();

    echo json_encode([
        'status' => 'success',
        'message' => 'Sign-up Successful! Account created in MySQL.',
        'user' => [
            'id' => $userId,
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
            'phone' => $phone,
            'age' => $age,
            'notify' => $notify
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
