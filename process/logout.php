<?php

// Hanya izinkan method GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => true, 'message' => 'Method not allowed']);
    exit;
}

session_start();
session_destroy();
header("Location: ../views/login.php");
exit;

?>
