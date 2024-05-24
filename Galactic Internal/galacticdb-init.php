<?php
include 'config.php';

$conn->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        password TEXT NOT NULL,
        is_hashed BOOLEAN DEFAULT 0
    );
");

// Insert initial data
$conn->exec("
    INSERT INTO users (username, password, is_hashed)
    VALUES ('admin', 'password', 1);
");

$conn->exec("
    INSERT INTO users (username, password, is_hashed)
    VALUES ('user', 'passwordwtf', 1);
");

$conn->exec("
    INSERT INTO users (username, password, is_hashed)
    VALUES ('user2', 'sha1thismate', 1);
");

// Verify the data insertion
$result = $conn->query("SELECT * FROM users");
foreach ($result as $row) {
    print_r($row);
}
?>
