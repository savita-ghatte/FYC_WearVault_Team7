<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // 1. Connect without database selected to create database
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // 2. Read and run schema.sql
    $sqlFile = __DIR__ . '/api/schema.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("schema.sql not found at " . $sqlFile);
    }

    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);

    // 3. Select database
    $pdo->exec("USE `wearvault`;");

    // Upgrade pre-existing users table if missing columns
    $userCols = $pdo->query("DESCRIBE `users`")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('fname', $userCols)) {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `fname` VARCHAR(100) DEFAULT '' AFTER `id`");
    }
    if (!in_array('lname', $userCols)) {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `lname` VARCHAR(100) DEFAULT '' AFTER `fname`");
    }
    if (!in_array('phone', $userCols)) {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(20) DEFAULT ''");
    }
    if (!in_array('age', $userCols)) {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `age` INT DEFAULT 18");
    }
    if (!in_array('notify', $userCols)) {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `notify` VARCHAR(20) DEFAULT 'email'");
    }
    if (!in_array('created_at', $userCols)) {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    }

    // 4. Ensure Default Admin Account Exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `admins` WHERE `email` = ?");
    $stmt->execute(['admin@wearvault.com']);
    if ($stmt->fetchColumn() == 0) {
        $insAdmin = $pdo->prepare("INSERT INTO `admins` (`fname`, `lname`, `email`, `password`, `role`) VALUES (?, ?, ?, ?, ?)");
        $insAdmin->execute(['System', 'Admin', 'admin@wearvault.com', 'admin123', 'Administrator']);
    }

    // 5. Populate initial products if empty
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `products`");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $sampleProducts = [
            ['Modern Denim Jacket', 'Outerwear', 2499.00, 15, 'c1.jpeg', 'Premium stylish denim jacket.'],
            ['Casual Cotton Shirt', 'Tops', 1299.00, 25, 'c2.jpeg', 'Breathable cotton shirt for everyday comfort.'],
            ['Urban Street Hoodie', 'Outerwear', 1999.00, 20, 'c3.jpeg', 'Warm and cozy hoodie with street style.'],
            ['Classic Leather Boots', 'Footwear', 3499.00, 10, 'c4.jpeg', 'Durable genuine leather boots.'],
            ['Slim Fit Chinos', 'Bottoms', 1799.00, 30, 'c5.jpeg', 'Elegant slim fit trousers.'],
            ['Graphic Print Tee', 'Tops', 799.00, 50, 'c6.jpeg', '100% cotton crewneck graphic tee.']
        ];
        $insProduct = $pdo->prepare("INSERT INTO `products` (`name`, `category`, `price`, `stock`, `image_url`, `description`) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($sampleProducts as $prod) {
            $insProduct->execute($prod);
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'WearVault XAMPP Database setup & initialized successfully!',
        'database' => 'wearvault'
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
