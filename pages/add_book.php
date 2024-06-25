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

// schaut ob user admin ist, das macht das nicht admin user die admin seiten nicht zugreifen können
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../home.php");
    exit();
}
// die informationen aus dem formular werden zu einer variable gespeichert
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kurztitle = $_POST['kurztitle'];
    $kategorie = $_POST['kategorie'];
    $verfasser = (int)$_POST['verfasser']; 
    $title = $_POST['title'];
    $autor = $_POST['autor'];
    $zustand = $_POST['zustand'];
    $katalog = rand(1, 20); 
    $nummer = rand(1, 100); 
    $verkauft = 0; 
    $kaufer = rand(1, 9999); 
    $sprache = 'Deutsch'; 
    $foto = 'book.jpg'; 

    // die infos aus dem formular werden in die datenbank eingefügt
    $sql = "INSERT INTO buecher (kurztitle, kategorie, verfasser, title, autor, zustand, katalog, nummer, verkauft, kaufer, sprache, foto) VALUES (:kurztitle, :kategorie, :verfasser, :title, :autor, :zustand, :katalog, :nummer, :verkauft, :kaufer, :sprache, :foto)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'kurztitle' => $kurztitle,
        'kategorie' => $kategorie,
        'verfasser' => $verfasser,
        'title' => $title,
        'autor' => $autor,
        'zustand' => $zustand,
        'katalog' => $katalog,
        'nummer' => $nummer,
        'verkauft' => $verkauft,
        'kaufer' => $kaufer,
        'sprache' => $sprache,
        'foto' => $foto
    ]);
    // der user wird zurück zur admin_books seite geleitet nachdem er die informationen eingefügt hat
    header("Location: admin_books.php");
    exit();
}


// holt daten aus kategorien column und speicher es in $kategorien variable
$sql = "SELECT * FROM kategorien";
$kategorienStmt = $conn->query($sql);
$kategorien = $kategorienStmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
</head>
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
 <!-- wenn der user eingeloggt ist, wird der name des users angezeigt und man kann durch hovern sich ausloggen oder passwort ändern, wenn nicht, wird der user auf die login seite geleitet -->
    <div class="admin-login">
        <?php if (isset($_SESSION["username"])): ?>
            <?php if ($_SESSION["admin"] == 1): ?>
            <a href="admin_books.php">
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
<!-- das formular für das hinzufügen eines neuen buches -->
<div class="add-customer-container">
    <div class="add-customer-content">
    <form method="post">
        <label for="kurztitle">Kurztitle:</label>
        <input type="text" name="kurztitle" id="kurztitle" required><br>

        <label for="kategorie">Kategorie:</label>
        <select name="kategorie" id="kategorie" required>
            <?php foreach ($kategorien as $kategorie): ?>
                <option value="<?= $kategorie['id'] ?>"><?= htmlspecialchars($kategorie['kategorie']) ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="verfasser">Verfasser:</label>
        <input type="number" name="verfasser" id="verfasser" required><br>

        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required><br>

        <label for="autor">Autor:</label>
        <input type="text" name="autor" id="autor" required><br>

        <label for="zustand">Zustand:</label>
        <select name="zustand" id="zustand" required>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="G">G</option>
        </select><br>

        <input type="submit" value="Add">
    </form>
    </div>
</main>
</body>
</html>
