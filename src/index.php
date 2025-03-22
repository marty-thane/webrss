<?php
require('common.php');

# Redirect if not logged in
if (!isset($_SESSION['id'])) {
  header('Location: /login');
  exit();
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <title>Home - <?= $app; ?></title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    function fetchFeed(url) {
      // Show the loading indicator
      $('#loading-indicator').show();

      $.ajax({
        url: 'feed.php', // PHP file that will return the RSS feed data
        method: 'GET',
        data: { url: url },
        success: function(response) {
          // Hide the loading indicator and display the feed
          $('#loading-indicator').hide();
          $('#rss-feed').html(response); // Insert the transformed HTML
        },
        error: function() {
          // Hide the loading indicator and show an error message
          $('#loading-indicator').hide();
          alert("Error fetching feed!");
        }
      });
    }
  </script>
</head>
<body>
  <h1>Hello, <?= $_SESSION['username']; ?>!</h1>
  <p>See what's going on in the world.</p>
  
  <h3>Your Subscribed Feeds:</h3>
  <ul>
    <?php foreach ($feeds as $feed): ?>
      <li><a href="javascript:void(0);" onclick="fetchFeed('<?= htmlspecialchars($feed['url']); ?>')"><?= htmlspecialchars($feed['url']); ?></a></li>
    <?php endforeach; ?>
  </ul>

  <div id="loading-indicator" style="display: none;">
    <p>Loading RSS feed...</p>
    <img src="https://cdnjs.cloudflare.com/ajax/libs/jquery-loading/1.1.0/loading.svg" alt="Loading" />
  </div>

  <div id="rss-feed">
    <!-- RSS feed content will be displayed here -->
  </div>

  <form id="login-form" method="POST">
    <input type="text" id="url" name="url" placeholder="Link" required><br>
    <button type="submit">Add</button>
  </form>
</body>
</html>
