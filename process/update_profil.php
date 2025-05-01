<?php

include '../config/config.php';

// Hanya izinkan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = 'Method tidak diizinkan.';
    header('Location: profile.php');
    exit;
}

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_error'] = 'Silakan login terlebih dahulu.';
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$name = $_POST['name'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirmation'] ?? '';

$errors = [];

if (empty($name)) {
    $errors[] = 'Nama wajib diisi.';
}

if (empty($username)) {
    $errors[] = 'Username wajib diisi.';
}

if (!empty($password)) {
    if (strlen($password) < 8) {
        $errors[] = 'Password minimal 8 karakter.';
    } elseif ($password !== $password_confirm) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }
}

// Jika ada error validasi
if (!empty($errors)) {
    $_SESSION['flash_error'] = implode('<br>', $errors);
    header('Location: ../views/dashboard.php');
    exit;
}

// Ambil user lama
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $userId");
$user = mysqli_fetch_assoc($user_query);

if (!$user) {
    $_SESSION['flash_error'] = 'Pengguna tidak ditemukan.';
    header('Location: ../views/dashboard.php');
    exit;
}

// Update data
$update_query = "UPDATE users SET name = '$name'";

// Update username jika berubah
if ($user['username'] != $username) {
    $update_query .= ", username = '$username'";
}

// Update password jika ada input
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $update_query .= ", password = '$hashedPassword'";
}

$update_query .= " WHERE id = $userId";

if (mysqli_query($conn, $update_query)) {
    header('Location: logout.php');
    exit;
} else {
    $_SESSION['flash_error'] = 'Gagal memperbarui data pengguna.';
}

header('Location: ../views/dashboard.php');
exit;
