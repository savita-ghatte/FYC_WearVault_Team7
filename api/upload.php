<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$targetDir = __DIR__ . '/../uploads/';
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (!isset($_FILES['file'])) {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded.']);
    exit();
}

$file = $_FILES['file'];
$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

$allowed = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];
if (!in_array($ext, $allowed)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file extension. Only images allowed.']);
    exit();
}

$newFileName = 'img_' . uniqid() . '.' . $ext;
$targetFile  = $targetDir . $newFileName;

if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'File uploaded successfully!',
        'file_path' => 'uploads/' . $newFileName
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save uploaded file.']);
}
