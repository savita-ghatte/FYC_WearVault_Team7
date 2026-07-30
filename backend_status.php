<?php
$host = 'localhost';
$db   = 'wearvault';
$user = 'root';
$pass = '';

$status = 'Unknown';
$tablesCount = [];
$errorMsg = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $status = 'Connected to MySQL (wearvault)';

    $tables = ['users', 'admins', 'products', 'orders', 'custom_closet', 'saved_looks'];
    foreach ($tables as $tbl) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM `$tbl`");
            $tablesCount[$tbl] = $stmt->fetch()['cnt'];
        } catch (Exception $e) {
            $tablesCount[$tbl] = 'Table missing';
        }
    }
} catch (Exception $e) {
    $status = 'Connection Failed';
    $errorMsg = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WearVault - Backend Status & Diagnostic Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
        body { background: #0f172a; color: #f8fafc; padding: 40px 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #1e293b; border-radius: 16px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        h1 { color: #38bdf8; font-size: 28px; margin-bottom: 8px; }
        p { color: #94a3b8; font-size: 14px; margin-bottom: 25px; }
        .badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 14px; margin-bottom: 20px; }
        .badge.success { background: #10b981; color: #fff; }
        .badge.danger { background: #ef4444; color: #fff; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .card { background: #334155; padding: 20px; border-radius: 12px; text-align: center; }
        .card h3 { font-size: 28px; color: #38bdf8; }
        .card p { margin-bottom: 0; color: #cbd5e1; font-weight: 500; }
        .btn { display: inline-block; background: #0284c7; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; cursor: pointer; border: none; transition: 0.2s; margin-right: 10px; margin-bottom: 10px; }
        .btn:hover { background: #0369a1; }
        .btn-success { background: #059669; }
        .btn-success:hover { background: #047857; }
        pre { background: #0f172a; padding: 15px; border-radius: 8px; color: #38bdf8; overflow-x: auto; font-size: 13px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✨ WearVault Backend Diagnostic Portal</h1>
        <p>XAMPP PHP & MySQL Backend Status for FYC_WearVault_Team7</p>

        <div>
            <span class="badge <?php echo ($status === 'Connection Failed') ? 'danger' : 'success'; ?>">
                ● <?php echo htmlspecialchars($status); ?>
            </span>
        </div>

        <?php if ($errorMsg): ?>
            <div style="background:#ef444422; color:#ef4444; padding:15px; border-radius:8px; margin-bottom:20px;">
                <strong>Error:</strong> <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <h2>Database Tables Overview</h2>
        <div class="grid" style="margin-top:15px;">
            <?php foreach ($tablesCount as $tbl => $cnt): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars((string)$cnt); ?></h3>
                    <p><?php echo htmlspecialchars($tbl); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>API Endpoint Testing Controls</h2>
        <div style="margin-top:15px;">
            <a href="setup_db.php" target="_blank" class="btn btn-success">🔄 Re-initialize Database</a>
            <button class="btn" onclick="testApi('api/get_users.php')">GET Users</button>
            <button class="btn" onclick="testApi('api/products.php')">GET Products</button>
            <button class="btn" onclick="testApi('api/orders.php')">GET Orders</button>
            <button class="btn" onclick="testApi('api/closet.php')">GET Closet</button>
            <button class="btn" onclick="testApi('api/looks.php')">GET Saved Looks</button>
        </div>

        <div id="outputArea" style="display:none; margin-top:20px;">
            <h3>API Response Output:</h3>
            <pre id="jsonOutput"></pre>
        </div>
    </div>

    <script>
        function testApi(url) {
            document.getElementById('outputArea').style.display = 'block';
            document.getElementById('jsonOutput').textContent = 'Fetching ' + url + ' ...';
            fetch(url)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('jsonOutput').textContent = JSON.stringify(data, null, 2);
                })
                .catch(err => {
                    document.getElementById('jsonOutput').textContent = 'Error: ' + err;
                });
        }
    </script>
</body>
</html>
