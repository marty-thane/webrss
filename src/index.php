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
  $url = htmlspecialchars($_POST['url']);
  
  if (validateRSS($url, 'rss.xsd')) {
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

# Query the user's subscribed feeds
$stmt = $pdo->prepare('
  SELECT f.id, f.url 
  FROM Feeds f
  JOIN UsersFeeds uf ON uf.feed_id = f.id
  WHERE uf.user_id = :user_id
');
$stmt->execute(['user_id' => $_SESSION['id']]);
$feeds = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/w3.css"> 
  <title>Home - <?= $app; ?></title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    function fetchFeed(url) {
      $('#welcome').hide();
      $('#feed').hide();
      $('#loading-indicator').show();
      $.ajax({
        url: 'feed.php',
        method: 'GET',
        data: { url: url },
        success: function(response) {
          $('#loading-indicator').hide();
          $('#feed').show();
          $('#feed').html(response);
        },
        error: function() {
          $('#loading-indicator').hide();
          alert("Error fetching feed!");
        }
      });
    }
  </script>
  <style>
    #loading-indicator {
      border: 3px solid #000;
      border-top: 3px solid #FFF;
      border-radius: 100%;
      width: 39px;
      height: 39px;
      animation: spin 1.5s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <div class="w3-flex">
    <div class="w3-panel" style="width:320px;">
      <form class="w3-flex" id="login-form" method="POST">
          <input class="w3-input" type="text" id="url" name="url" placeholder="Enter feed URL..." required><br>
        <button class="w3-button w3-teal" type="submit">Add</button>
      </form>
      <ul class="w3-ul w3-hoverable">
        <?php foreach ($feeds as $feed): ?>
          <li onclick="fetchFeed('<?= $feed['url']; ?>')"><?= $feed['url']; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="w3-container" style="width:850px;">
      <div id="welcome">
        <h1>Welcome, <?= $_SESSION['username']; ?>!</h1>
        <p>
          You can click on the links on the left to view the feeds you're subscribed to.
          If you don't have any subscriptions yet, simply use the input field in the
          upper left corner to subscribe to your favorite feeds.
        </p>
        <p>
          If you encounter any issues, feel free to open an issue on our
          <a href="https://github.com/marty-thane/webrss/issues" target="_blank">GitHub page</a>.
        </p>
      </div>

      <span class="w3-panel w3-display-topmiddle" id="loading-indicator" style="display: none;"></span>

      <div id="feed"></div>
    </div>
  </div>
</body>
</html>
