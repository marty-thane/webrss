<?php
require('../common.php');

# Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

# Connect to database
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

# If trying to log in
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    # Check credentials, login if correct
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];

        echo "<script>window.location.href = '/';</script>";
    } else {
        echo "<script>alert('Invalid username or password.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>Login - WebRSS</title>
</head>
<body>
    <div class="login-container w3-card-4 w3-padding-16">
        <h2>Login to RSS Reader</h2>
        <form id="login-form" method="POST">
            <input type="text" id="username" name="username" class="w3-input w3-border" placeholder="Username" required><br>
            <input type="password" id="password" name="password" class="w3-input w3-border" placeholder="Password" required><br>
            <button type="submit" class="w3-button w3-green">Login</button>
        </form>
        <div class="w3-container">
            <p>Don't have an account? <a href="/register" class="w3-text-blue">Register here</a></p>
        </div>
    </div>
</body>
</html>
