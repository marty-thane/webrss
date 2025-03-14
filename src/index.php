<?php
// Ensure that headers are set before any output is made
if (!defined('PHPUNIT_TEST')) {
    header('Content-Type: application/json');
}

// Dummy response
echo json_encode([
    'status' => 'success',
    'message' => 'Hello from Docker PHP!'
]);
?>
