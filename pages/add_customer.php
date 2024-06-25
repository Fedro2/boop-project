
<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// nicht admin user haben kein zugriff auf die seite
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../home.php");
    exit();
}

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

<?php


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include("../db_connect.php");
   
    $stmt = $conn->prepare('SELECT * FROM kunden WHERE email = ?');
    $stmt->execute([$_POST['email']]);
    if ($stmt->rowCount() > 0) {
        echo 'This email is already used.';
    } else {
        
        $stmt = $conn->prepare('INSERT INTO kunden (vorname, name, email, geburtstag, geschlecht, kunde_seit, kontaktpermail) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$_POST['vorname'], $_POST['name'], $_POST['email'], $_POST['geburtstag'], $_POST['geschlecht'], date('Y-m-d'), isset($_POST['kontaktpermail']) ? 1 : 0]);
       header('Location: admin.php');
    }
}


?>

<div class="add-customer-container">
    <div class="add-customer-content">
<form action="add_customer.php" method="post">
    <input type="text" id="vorname" name="vorname" placeholder="Vorname: " required><br>
    <input type="text" id="name" name="name" placeholder="Name: " required><br>
    <input type="text" id="email" name="email" placeholder="Email: " required><br>
    <label for="geburtstag">Geburtstag:</label><br>
    <input type="date" id="geburtstag" name="geburtstag" placeholder="Geburtstag: " required><br>
    <label for="geschlecht">Geschlecht: ↴</label><br>
    <select id="geschlecht" name="geschlecht" placeholder="Geschlecht" required>
        <option value="M">M</option>
        <option value="F">F</option>
    </select><br>
    <label for="kontaktpermail">Kontakt per Mail:</label>
    <input type="checkbox" id="kontaktpermail" name="kontaktpermail"><br>
    <input type="submit" value="Submit">
</form>
</div>
</div>
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