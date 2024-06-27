
<?php
session_start();

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
        <a class="logo" href="../index.php">BOOP</a>
    
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
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../db_connect.php");


// nimmt den ID vom geclickten Buch
$book_id = isset($_GET['id']) ? $_GET['id'] : '';

// holt die bücher details vom gewählten buch
$stmt = $conn->prepare("SELECT * FROM buecher WHERE id = :id");
$stmt->execute([':id' => $book_id]);
$book = $stmt->fetch();


echo "<div class='book-details-container'>";
echo "<img src='../pics/bookcover.png' alt='bookcover' class='bookcover-details'>";
echo "<div class='book-details'>";
echo "<div class='books-main'>";
echo "<p>" . htmlspecialchars($book['autor']) . "</p>";
echo "<h1>" . htmlspecialchars($book['kurztitle']) . "</h1>";
echo "</div>";
echo "<div class='book-second'>";
echo "<p> Catalog: " . htmlspecialchars($book['katalog']) . "</p>";
echo "<p> Category: " . htmlspecialchars($book['kategorie']) . "</p>";
echo "<p> Sold: " . htmlspecialchars($book['kaufer']) . "</p>";
echo "<p> Writers: " . htmlspecialchars($book['verfasser']) . "</p>";
echo "<p> Condition: " . htmlspecialchars($book['zustand']) . "</p>";
echo "</div>";
echo "</div>";
echo "</div>";

echo "<button class='goback' onclick='goBack()'>Back</button>";

echo "<script>
function goBack() {
  window.history.back();
}
</script>";

?>
</main>
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
