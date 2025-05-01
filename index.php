<?php

include 'config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: views/login.php');
    exit;
} else {
    header('Location: views/dashboard.php');
    exit;
}