<?php
require('../common.php');

# Redirect if already logged in
if (isset($_SESSION['id'])) {
  header('Location: /');
  exit;
}

# Logic for registering new users
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  # Check if username alread taken, otherwise register
  $stmt = $pdo->prepare('SELECT * FROM Users WHERE username = :username');
  $stmt->execute(['username' => $username]);
  $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($existingUser) {
    echo '<script>alert("Username already taken.");</script>';
  } else {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO Users (username, password) VALUES (:username, :password)');
    $stmt->execute(['username' => $username, 'password' => $hashedPassword]);
    echo '<script>
    alert("Registration successful! You will now be redirected.");
    window.location.href = "/login"; // Redirect to login page after success
  </script>';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <title>Register - <?= $app; ?></title>
</head>
<body>
  <h2>Create an account</h2>
  <form id="register-form" method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
  </form>
  <p>Have an account? <a href="/login">Login here</a></p>
</body>
</html>

