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
    window.location.href = "/login";
  </script>';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/w3.css"> 
  <title>Register - <?= $app; ?></title>
</head>
<body>
  <div class="w3-panel w3-card w3-display-topmiddle" style="width:360px;">
    <h2>Create an account</h2>
    <form id="register-form" method="POST">
      <input class="w3-input" type="text" name="username" placeholder="Username" required><br>
      <input class="w3-input" type="password" name="password" placeholder="Password" required><br>
      <button class="w3-button w3-teal w3-block" type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="/login">Login here</a></p>
  </div>
</body>
</html>
