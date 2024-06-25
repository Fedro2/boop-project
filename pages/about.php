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
<div class="about-container">
<img src="../pics/about.png" alt="about" class="about-img">
    <div class="about-text">
        
Willkommen bei BOOP – Ihrer ultimativen Anlaufstelle für alles rund um Bücher! Als ausschliessliches Online-Buchgeschäft möchten wir Ihr Buchkauf-Erlebnis so mühelos und angenehm wie möglich gestalten.<br><br>

Bei BOOP sind wir der Meinung, dass das Eintauchen in die Welt der Bücher ein Kinderspiel sein sollte. Deshalb bieten wir eine grosse Auswahl an Titeln aus allen erdenklichen Genres. Egal, ob Sie ein eingefleischter Fan von Fiktion, ein Geschichtsinteressierter oder ein Liebhaber von Ratgebern sind – wir haben für jeden etwas dabei. Mit nur wenigen Klicks tauchen Sie in fesselnde Geschichten ein, erkunden faszinierende Sachbücher und entdecken neue Autoren und Ideen.<br><br>

Was BOOP auszeichnet, ist unser Engagement für Einfachheit und Bequemlichkeit. Unsere benutzerfreundliche Oberfläche und intuitive Suchfunktionen machen es einfach, genau das zu finden, wonach Sie suchen, sei es der neueste Bestseller oder ein verborgenes Juwel aus vergangenen Jahren. Kein endloses Scrollen oder fruchtlose Suchen mehr – bei BOOP ist Ihr nächstes grossartiges Leseerlebnis nur einen Klick entfernt.<br><br>

Aber BOOP ist mehr als nur ein Buchgeschäft – es ist eine Gemeinschaft von Bücherliebhabern wie Ihnen. Wir sind hier, um die Freude am Lesen zu feiern, unsere Lieblings-Empfehlungen zu teilen und uns mit anderen Bücherwürmern aus der ganzen Welt zu verbinden. Also, egal ob Sie nach Inspiration, Entspannung oder einfach nur einer guten Geschichte suchen, schliessen Sie sich uns bei BOOP an und lassen Sie das Abenteuer beginnen!

Viel Spass beim Lesen,
Das BOOP-Team

    </div>
    <img src="../pics/team.png" alt="about" class="team-img">
    <img src="../pics/user_icon.png" alt="about" class="icon-img">
    <div class="about-text">
        Pedro Ferrari | CEO & Founder<br>
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