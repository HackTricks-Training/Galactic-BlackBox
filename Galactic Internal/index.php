<?php

session_start();
include 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username='$username'";
        $stmt = $conn->query($sql);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Check if password is hashed
            if ($row['is_hashed']) {
                // Calculate the SHA1 hash in PHP and compare
                $hashed_password = sha1($password);
                if ($hashed_password == $row['password']) {
                    $_SESSION['loggedin'] = true;
                    header("Location: calculator.php");
                    exit();
                } else {
                    $error = "Invalid credentials.";
                }
            } else {
                // Directly compare the password
                if ($password == $row['password']) {
                    $_SESSION['loggedin'] = true;
                    header("Location: calculator.php");
                    exit();
                } else {
                    $error = "Invalid credentials.";
                }
            }
        } else {
            $error = "Invalid credentials.";
        }
    } catch (Exception $e) {
        # Remove this to make it more difficult
        $error = "An error occurred: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galactic Internal Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Galactic Internal Login</h2>
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <div class="error"><?php echo $error; ?></div>
    </div>
</body>
</html>
