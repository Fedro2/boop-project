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

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../home.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("ID not provided");
}

$id = $_GET['id'];

$sql = "DELETE FROM buecher WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $id]);

header("Location: admin_books.php");
exit();
?>
