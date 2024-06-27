<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../db_connect.php");


// Speichert die Daten des Formulars in Variablen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vorname = $_POST['vorname'];
    $name = $_POST['name'];
    $spitzname = $_POST['spitzname'];
    $email = $_POST['email'];
    $passwort = password_hash($_POST['passwort'], PASSWORD_DEFAULT);

    // Überprüfen ob Benutzername oder Email bereits existieren
    $stmt = $conn->prepare('SELECT COUNT(*) FROM benutzer WHERE benutzername = ? OR email = ?');
    $stmt->execute([$spitzname, $email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Benutzername oder Email bereits vorhanden
        $error_message = "Benutzername oder Email sind bereits vorhanden.";
    } else { 
        // Daten einfügen
        $stmt = $conn->prepare('INSERT INTO benutzer (benutzername, name, vorname, passwort, email) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$spitzname, $name, $vorname, $passwort, $email]);

        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Catalog</title>
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

<main>  
<div class="login-container">
<div class="login-content">
<img class="regpic" src="../pics/registration.png" alt="registration">  
<form id="registrationForm" method="post">
    <div id="step1">
        <input type="text" id="vorname" placeholder="Vorname: " name="vorname" required>
        <input type="text" id="name" placeholder="Name:" name="name" required>
        <input type="text" id="spitzname" placeholder="Username: " name="spitzname" required maxlength="45">  
        <button type="button" onclick="nextStep()">Next</button>
        <?php if (!empty($error_message)): ?>
            <p><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
    <div id="step2" style="display: none;">
        <button type="button" onclick="previousStep()">Back</button>
        <input type="email" id="email" placeholder="Email:" name="email" required>
        <input type="password" id="passwort" placeholder="Passwort:" name="passwort" required minlength="8">
        <div class="adcheck">
        </div>
        <input type="submit" value="Create account">
    </div>
</form>
</div>
</div>
</main>

<script>
function nextStep() {
    var vorname = document.getElementById('vorname').value;
    var name = document.getElementById('name').value;
    var spitzname = document.getElementById('spitzname').value;

    if (vorname === '' || name === '' || spitzname === '') {
        alert('Please fill out all fields before proceeding.');
    } else {
        document.getElementById('step1').style.display = 'none';
        document.getElementById('step2').style.display = 'block';
    }
}

function previousStep() {
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';
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
            <a href="https://www.facebook.com" target="_blank"><img src="../pics/facebooklogo.png" alt="Facebook"></a>
            <a href="https://twitter.com" target="_blank"><img src="../pics/twitterlogo.png" alt="Twitter"></a>
            <a href="https://www.youtube.com" target="_blank"><img src="../pics/youtubelogo.png" alt="YouTube"></a>
        </div>
    </div>
</footer>   
</body>
</html>
