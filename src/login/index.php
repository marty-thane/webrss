<?php
require('../common.php');

# Redirect if already logged in
if (isset($_SESSION['id'])) {
  header('Location: /');
  exit;
}

# If trying to log in
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  # Check credentials, login if correct
  $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = :username");
  $stmt->execute(['username' => $username]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['id'] = $user['id'];
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
  <title>Log in - <?= $app; ?></title>
</head>
<body>
  <h2>Log into <?= $app; ?></h2>
  <form id="login-form" method="POST">
    <input type="text" id="username" name="username" placeholder="Username" required><br>
    <input type="password" id="password" name="password" placeholder="Password" required><br>
    <button type="submit">Log in</button>
  </form>
  <p>Don't have an account? <a href="/register" >Register here</a></p>
</body>
</html>
