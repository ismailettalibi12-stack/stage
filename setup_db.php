<?php
/**
 * Quick Database Setup Endpoint
 * Access: https://your-app.railway.app/setup_db.php
 */

// Get database connection info from environment
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$dbname = getenv('DB_NAME') ?: 'mvc_gestion';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
        .info { color: #0066cc; margin: 10px 0; font-size: 14px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>MVC Gestion Database Setup</h1>
        <p class='info'>Connecting to: <strong>$host:$port</strong></p>
        <p class='info'>Database: <strong>$dbname</strong></p>
        <hr>";

try {
    // Connect to MySQL without specifying database (to create it)
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p class='info'>✓ Connected to MySQL server</p>";
    
    // Read and execute schema file
    $schema_path = __DIR__ . '/database/schema.sql';
    if (!file_exists($schema_path)) {
        $schema_path = __DIR__ . '/schema.sql';
    }
    
    if (!file_exists($schema_path)) {
        die("<p class='error'>❌ Error: schema.sql not found</p></div></body></html>");
    }
    
    $sql = file_get_contents($schema_path);
    
    // Replace database name in schema if needed
    $sql = str_replace('CREATE DATABASE IF NOT EXISTS mvc_gestion', "CREATE DATABASE IF NOT EXISTS `$dbname`", $sql);
    $sql = str_replace('USE mvc_gestion', "USE `$dbname`", $sql);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $count = 0;
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $preview = substr($statement, 0, 70);
            echo "<p class='info'>→ " . htmlspecialchars($preview) . "...</p>";
            $pdo->exec($statement);
            $count++;
        }
    }
    
    // Verify tables were created
    $result = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$dbname'");
    $tables = $result->fetchAll();
    
    echo "<hr>";
    echo "<p class='success'>✅ Database setup completed successfully!</p>";
    echo "<p class='info'>Created/Updated <strong>" . count($tables) . " tables</strong> in database <strong>$dbname</strong>:</p>";
    echo "<pre>";
    foreach ($tables as $table) {
        echo "- " . $table['TABLE_NAME'] . "\n";
    }
    echo "</pre>";
    echo "<p style='color: green; margin-top: 20px;'><strong>Your database is ready!</strong> You can now log in and use the application.</p>";
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Error setting up database:</p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<p class='info'>Troubleshooting:</p>";
    echo "<ul>";
    echo "<li>Check that MySQL service is running on Railway</li>";
    echo "<li>Verify environment variables are set (DB_HOST, DB_USER, DB_PASSWORD)</li>";
    echo "<li>Check database user has CREATE permissions</li>";
    echo "</ul>";
}

echo "
    </div>
</body>
</html>";
?>

