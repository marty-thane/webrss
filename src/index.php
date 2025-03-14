<?php
require('common.php');

# Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <title>Home - WebRSS</title>
</head>
<body>
    <div class="w3-container">
        <h1>Hello, <?php echo $_SESSION['username']; ?>!</h1>
        <p>You are successfully logged in.</p>
    </div>
</body>
</html>
