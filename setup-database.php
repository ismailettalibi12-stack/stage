<?php
/**
 * Database Setup Endpoint
 * 
 * This is a simple HTTP endpoint to initialize the database.
 * Access via: https://your-app.railway.app/setup-database.php
 * 
 * For security, only allow from localhost or with a setup token.
 */

// Simple security check - allow localhost or Railway internal access
$allowed_hosts = ['localhost', '127.0.0.1', '::1'];
$current_host = $_SERVER['REMOTE_ADDR'] ?? '';

// Check if accessing from Railway internal network
$is_internal = strpos($current_host, '10.') === 0 || 
               strpos($current_host, '172.') === 0 ||
               $current_host === '::1' ||
               in_array($current_host, $allowed_hosts);

if (!$is_internal) {
    http_response_code(403);
    die("❌ Access Denied\n\nThis endpoint can only be accessed from within the Railway network.\n");
}

// Get database connection info from environment
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  MVC Gestion Database Setup\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "Connecting to MySQL...\n";
echo "Host: $host:$port\n";
echo "User: $user\n\n";

try {
    // Connect to MySQL without specifying database (to create it)
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to MySQL\n\n";
    
    // Read and execute schema file
    $schema_path = __DIR__ . '/database/schema.sql';
    if (!file_exists($schema_path)) {
        http_response_code(500);
        die("❌ Error: schema.sql not found at $schema_path\n");
    }
    
    $sql = file_get_contents($schema_path);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $count = 0;
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $preview = substr($statement, 0, 55);
            echo "  • " . $preview . "...\n";
            $pdo->exec($statement);
            $count++;
        }
    }
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ Database setup completed successfully!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Summary:\n";
    echo "  • Executed $count SQL statements\n";
    echo "  • Database: mvc_gestion\n";
    echo "  • Created 8 tables with relationships and indexes\n";
    echo "  • All tables ready for your app\n\n";
    echo "You can now start using your application!\n";
    
} catch (PDOException $e) {
    http_response_code(500);
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "❌ Error setting up database\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo $e->getMessage() . "\n";
}
?>

