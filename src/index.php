<?php
require('common.php');

# Redirect if not logged in
if (!isset($_SESSION['id'])) {
  header('Location: /login');
  exit();
}

# Function for validating submitted RSS files
function validateRSS($url, $xsdFile) {
  $dom = new DOMDocument;
  libxml_use_internal_errors(true);
  if ($dom->load($url)) {
    if ($dom->schemaValidate($xsdFile)) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }
}

# If trying to subscribe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $url = $_POST['url'];
  
  if (validateRSS($url, 'rss-2_0_1-rev9.xsd')) {
    $stmt = $pdo->prepare('SELECT id FROM Feeds WHERE url = :url');
    $stmt->execute(['url' => $url]);
    $feed = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$feed) {
      $stmt = $pdo->prepare('INSERT INTO Feeds (url) VALUES (:url) RETURNING id');
      $stmt->execute(['url' => $url]);
      $feedId = $stmt->fetchColumn();
    } else {
      $feedId = $feed['id'];
    }

    $stmt = $pdo->prepare('INSERT INTO UsersFeeds (user_id, feed_id) VALUES (:user_id, :feed_id)');
    $stmt->execute(['user_id' => $_SESSION['id'], 'feed_id' => $feedId]);
  } else {
    echo '<script>alert("Invalid RSS feed or failed validation!");</script>';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <title>Home - <?= $app; ?></title>
</head>
<body>
  <h1>Hello, <?= $_SESSION['username']; ?>!</h1>
  <p>See what's going on in the world.</p>
  <form id="login-form" method="POST">
    <input type="text" id="url" name="url" placeholder="Link" required><br>
    <button type="submit">Add</button>
  </form>
</body>
</html>
