<?php
$app = 'WebRSS';

# Connect to database
$host = 'webrss-postgres';
$db = 'postgres';
$user = 'postgres';
$pass = getenv('POSTGRES_PASSWORD');
try {
  $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die('Could not connect to the database: ' . $e->getMessage());
}


# Session management
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
