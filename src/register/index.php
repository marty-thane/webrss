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

# Logic for registering new users
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    # Check if username alread taken, otherwise register
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        echo "<script>alert('Username already taken.');</script>";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->execute(['username' => $username, 'password' => $hashedPassword]);
        echo "<script>
        alert('Registration successful! You will now be redirected.');
        window.location.href = '/login'; // Redirect to login page after success
    </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>Register - WebRSS</title>
</head>
<body>
    <div class="register-container w3-card-4 w3-padding-16">
        <h2>Create an Account</h2>
        <form id="register-form" method="POST">
            <input type="text" name="username" class="w3-input w3-border" placeholder="Username" required><br>
            <input type="password" name="password" class="w3-input w3-border" placeholder="Password" required><br>
            <button type="submit" class="w3-button w3-green">Register</button>
        </form>
        <div class="w3-container">
            <p>Already have an account? <a href="/login" class="w3-text-blue">Login here</a></p>
        </div>
    </div>
</body>
</html>

