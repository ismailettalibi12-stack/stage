<?php
/**
 * Database Setup Script
 * 
 * This script creates the database schema for the MVC Gestion application.
 * Run this once after deploying to Railway to set up the database.
 * 
 * Usage:
 * 1. Deploy your app to Railway
 * 2. SSH into the Railway container or run this via HTTP endpoint
 * 3. The script will create all necessary tables
 */

// Get database connection info from environment
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

echo "Setting up MVC Gestion database...\n";
echo "Host: $host:$port\n";
echo "User: $user\n";

try {
    // Connect to MySQL without specifying database (to create it)
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute schema file
    $schema_path = __DIR__ . '/schema.sql';
    if (!file_exists($schema_path)) {
        die("Error: schema.sql not found at $schema_path\n");
    }
    
    $sql = file_get_contents($schema_path);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            echo "Executing: " . substr($statement, 0, 60) . "...\n";
            $pdo->exec($statement);
        }
    }
    
    echo "\n✅ Database setup completed successfully!\n";
    echo "All tables have been created in the mvc_gestion database.\n";
    
} catch (PDOException $e) {
    echo "❌ Error setting up database:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
?>

