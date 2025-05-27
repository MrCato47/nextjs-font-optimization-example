<?php
require_once 'db.php';

try {
    // SQLite schema
    $schema = "
    -- Users table
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        email TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        reset_token TEXT DEFAULT NULL,
        reset_token_expiry TIMESTAMP DEFAULT NULL,
        last_login TIMESTAMP DEFAULT NULL,
        status TEXT CHECK(status IN ('active', 'inactive', 'pending')) DEFAULT 'pending',
        remember_token TEXT DEFAULT NULL,
        email_verification_token TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Login attempts table
    CREATE TABLE IF NOT EXISTS login_attempts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        ip_address TEXT NOT NULL,
        attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        success INTEGER DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    -- Cart items table
    CREATE TABLE IF NOT EXISTS cart_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        product_id INTEGER NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 1,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    -- Create indexes for better performance
    CREATE INDEX IF NOT EXISTS idx_login_attempts_user_id ON login_attempts(user_id);
    CREATE INDEX IF NOT EXISTS idx_login_attempts_ip ON login_attempts(ip_address);
    CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
    CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
    ";

    // Execute each statement separately
    $queries = explode(';', $schema);
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            $conn->exec($query);
            echo "Executed: " . substr($query, 0, 50) . "...\n";
        }
    }

    echo "\nDatabase schema created successfully!\n";

    // Create a test user
    $username = "test_user";
    $email = "test@example.com";
    $password = "Test123!";
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $verification_token = bin2hex(random_bytes(32));

    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, email_verification_token, status) VALUES (?, ?, ?, ?, 'active')");
    if ($stmt->execute([$username, $email, $password_hash, $verification_token])) {
        echo "Test user created successfully!\n";
        echo "Username: test_user\n";
        echo "Password: Test123!\n";
    }

} catch(PDOException $e) {
    die("Database initialization failed: " . $e->getMessage() . "\n");
}
?>
