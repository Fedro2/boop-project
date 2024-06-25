<?php
session_start();

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




<main>  

<p class="impressum">Impressum<br><br>

Verantwortlich für den Inhalt dieser Website gemäß Art. 3 Abs. 1 des Schweizer Bundesgesetzes über den unlauteren Wettbewerb (UWG):<br><br>

Boop Buchhandel GmbH<br>
Musterstraße 123<br>
8000 Zürich<br>
Schweiz<br><br>

Vertreten durch:<br>
Geschäftsführer: Max Mustermann<br><br>

Kontakt:<br>
Telefon: +41 12 345 67 89<br>
E-Mail: info@boopbooks.ch<br><br>

Handelsregister:<br>
Handelsregisteramt des Kantons Zürich<br><br>

UID-Nummer:<br>
CHE-123.456.789 MWST<br><br>

Online-Streitbeilegung gemäß Art. 14 Abs. 1 ODR-VO:<br>
Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit, die Sie unter http://ec.europa.eu/consumers/odr/ finden.
</p>
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