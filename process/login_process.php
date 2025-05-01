<?php

include "../config/config.php";

// Hanya izinkan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => true, 'message' => 'Method not allowed']);
    exit;
}

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

$query = "SELECT * FROM users WHERE username='$username' LIMIT 1";
$result = mysqli_query($conn, $query);

if ($data = mysqli_fetch_assoc($result)) {
    if (password_verify($password, $data['password'])) {
        // Login berhasil
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['user_name'] = $data['name'];
        $_SESSION['user_username'] = $data['username'];
        header("Location: ../views/dashboard.php");
        exit;
    } else {
        // Password salah
        header("Location: ../views/login.php?error=Password salah");
        exit;
    }
} else {
    // Username tidak ditemukan
    header("Location: ../views/login.php?error=Username tidak ditemukan");
    exit;
}

?>
