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

// nicht admin user können die Seit nicht zugreifen
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../home.php");
    exit();
}

$id = $_GET['id'];

// deletet der ausgewählte Kunde aus der Datenbank
$stmt = $conn->prepare('DELETE FROM kunden WHERE kid = ?');
$stmt->execute([$id]);

header('Location: admin.php');
exit;
?>