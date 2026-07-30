<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$input = getJsonInput();

$email       = trim($input['email'] ?? '');
$newPassword = trim($input['new_password'] ?? '');

if (empty($email) || empty($newPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and new password are required.']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$newPassword, $email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Password reset successfully! You can now log in.']);
    } else {
        // Try updating admin table if user table row was 0
        $stmtAdmin = $pdo->prepare("UPDATE admins SET password = ? WHERE LOWER(email) = LOWER(?)");
        $stmtAdmin->execute([$newPassword, $email]);
        if ($stmtAdmin->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Admin password reset successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No account found with this email address.']);
        }
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
