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

// schaut ob der User ein Admin ist, das macht das nicht admin user die admin seiten nicht zugreifen können
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../home.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("ID not provided");
}

$id = $_GET['id'];

// Infos aus dem Formular werden zu einer variable gespecihert
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kurztitle = $_POST['kurztitle'];
    $kategorie = $_POST['kategorie'];
    $verfasser = $_POST['verfasser'];
    $title = $_POST['title'];
    $sprache = $_POST['sprache'];
    $zustand = $_POST['zustand'];
// Infos aus dem Formular werden in die Datenbank eingefügt
    $sql = "UPDATE buecher SET kurztitle = :kurztitle, kategorie = :kategorie, verfasser = :verfasser, title = :title, sprache = :sprache, zustand = :zustand WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['kurztitle' => $kurztitle, 'kategorie' => $kategorie, 'verfasser' => $verfasser, 'title' => $title, 'sprache' => $sprache, 'zustand' => $zustand, 'id' => $id]);

    header("Location: admin_books.php");
    exit();
}
// holt die daten vom ausgewählten Buch
$sql = "SELECT * FROM buecher WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

if (!$book) {
    die("Book not found");
}

$sql = "SELECT * FROM kategorien";
$kategorienStmt = $conn->query($sql);
$kategorien = $kategorienStmt->fetchAll();
?>


<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200..800&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

</head>
<body>

<main>
<nav>
        <div class="margin">
            <a href="catalog.php">CATALOG</a>
            <a href="about.php">ABOUT</a>
        </div>
        <a class="logo" href="../home.php">BOOP</a>
    
    <div class="admin-login">
        <?php if (isset($_SESSION["username"])): ?>
            <?php if ($_SESSION["admin"] == 1): ?>
            <a href="admin.php">
                <div class="admin">
                    <img src="../pics/admin.png" alt="admin">
                </div>
            </a>
        <?php endif; ?>
            <div class="login">
                <a class="user" href="#"><?= $_SESSION["name"] . " " ?> <?= $_SESSION["surname"]?></a>
                <div class="dropdown">
                    <a href="logout.php">Logout</a>
                    <a href="login.php">Change Password</a>
                </div>
            </div>
        <?php else: ?>
            <div class="login">
                <a href="login.php">LOG IN</a>
            </div>
        <?php endif; ?>
    </div>
    </nav>
<body>
<!-- Formular für das Update -->
<div class="add-customer-container">
    <div class="add-customer-content">
    <form method="post">
        <label for="kurztitle">Kurztitle:</label>
        <input type="text" name="kurztitle" id="kurztitle" value="<?= htmlspecialchars($book['kurztitle']) ?>"><br>

        <label for="kategorie">Kategorie:</label>
        <select name="kategorie" id="kategorie">
            <?php foreach ($kategorien as $kategorie): ?>
                <option value="<?= $kategorie['id'] ?>" <?= $book['kategorie'] == $kategorie['id'] ? 'selected' : '' ?>><?= htmlspecialchars($kategorie['kategorie']) ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="verfasser">Verfasser:</label>
        <input type="text" name="verfasser" id="verfasser" value="<?= htmlspecialchars($book['verfasser']) ?>"><br>

        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($book['title']) ?>"><br>

        <label for="sprache">Sprache:</label>
        <input type="text" name="sprache" id="sprache" value="<?= htmlspecialchars($book['sprache']) ?>"><br>

        <label for="zustand">Zustand:</label>
        <input type="text" name="zustand" id="zustand" value="<?= htmlspecialchars($book['zustand']) ?>"><br>

        <input type="submit" value="Update">
    </form>
    </div>
    </div>
    <button class='goback' onclick='goBack()'>Back</button>
</main>
<script>
function goBack() {
  window.history.back();
}

</script>
<footer>
    <div class="footer-container">
        <div class="left">
            <p>Informatikmittelschule<br>Andreas Heusler-Strasse 41, 4052 Basel</p>
            
        </div>
        <div class="center">
            <p><a href="/pages/impressum.php">Impressum</a></p>
            <p><a href="/pages/feedback.php">Feedback</a></p>
        </div>
        <div class="right">
            <a href="https://www.instagram.com/ww_basel/" target="_blank"><img src="../pics/instagramlogo.png" alt="WW Basel on Instagram"></a>
            <a href="https://www.facebook.com"target="_blank"><img src="../pics/facebooklogo.png" alt="Facebook"></a>
            <a href="https://twitter.com"target="_blank"><img src="../pics/twitterlogo.png" alt="Twitter"></a>
            <a href="https://www.youtube.com"target="_blank"><img src="../pics/youtubelogo.png" alt=">Youtube"></a>
        </div>
    </div>

    </footer>
</body>


</html>
