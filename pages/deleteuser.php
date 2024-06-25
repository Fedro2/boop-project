<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../db_connect.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if user is an admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../home.php");
    exit();
}

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    // Only admin users can delete users
    header("Location: ../home.php");
    exit();
}

if (isset($_GET['id'])) {
    $userId = (int)$_GET['id'];

    // Delete the user with the given ID
    $stmt = $conn->prepare("DELETE FROM Benutzer WHERE id = :id");
    $stmt->execute(['id' => $userId]);

    header("Location: admin_users.php");
    exit();
} else {
    // If no ID is provided, redirect back to the admin users page
    header("Location: admin_users.php");
    exit();
}
?>
