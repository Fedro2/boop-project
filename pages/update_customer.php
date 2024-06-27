
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
include("../db_connect.php");


// speichert die id des kunden
$id = $_GET['id'];

// holt die infos von dem ausgewählten kunden
$stmt = $conn->prepare('SELECT * FROM kunden WHERE kid = ?');
$stmt->execute([$id]);
$customer = $stmt->fetch();

// die daten vom ausgewählten Kunden werden geändert
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare('UPDATE kunden SET vorname = ?, name = ?, email = ?, geburtstag = ?, kunde_seit = ?, kontaktpermail = ?, geschlecht = ? WHERE kid = ?');
    $stmt->execute([$_POST['vorname'], $_POST['name'], $_POST['email'], $_POST['geburtstag'], $_POST['kunde_seit'], intval(isset($_POST['kontaktpermail'])), $_POST['geschlecht'], $id]);
    header('Location: admin.php');
}
?>
 
 <div class="add-customer-container">
    <div class="add-customer-content">
<form id="editForm" method="post">
<form id="registrationForm" method="post">
    <div id="step1">
    <label for="kid">Kid:</label><br>
    <input type="text" id="kid" name="kid" value="<?php echo htmlspecialchars($customer['kid']); ?>" readonly><br>
    <label for="vorname">Vorname:</label><br>
    <input type="text" id="vorname" name="vorname" value="<?php echo htmlspecialchars($customer['vorname']); ?>"><br>
    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>"><br>
    <label for="email">Email:</label><br>
    <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>"><br>
    <button type="button" onclick="nextStep()">Next</button>
    </div>
    <div id="step2" style="display: none;">
        <button type="button" onclick="previousStep()">Back</button>
    <label for="geburtstag">Geburtstag:</label><br>
    <input type="date" id="geburtstag" name="geburtstag" value="<?php echo htmlspecialchars($customer['geburtstag']); ?>"><br>
    <label for="geschlecht">Geschlecht:</label><br>
    <select id="geschlecht" name="geschlecht">
    <option value="M" <?php echo $customer['geschlecht'] == 'M' ? 'selected' : ''; ?>>M</option>
    <option value="F" <?php echo $customer['geschlecht'] == 'F' ? 'selected' : ''; ?>>F</option>
    </select><br>
    <label for="kunde_seit">Kunde Seit:</label><br>
    <input type="date" id="kunde_seit" name="kunde_seit" value="<?php echo htmlspecialchars($customer['kunde_seit']); ?>"><br>
    <label for="kontaktpermail">Kontakt per Mail:</label>
    <input type="checkbox" id="kontaktpermail" name="kontaktpermail" <?php echo $customer['kontaktpermail'] ? 'checked' : ''; ?>><br>
    <input type="submit" value="Submit">
    </div>
</form>
</div>
</div>
<button class='goback' onclick='goBack()'>Back</button>
</main>
<script>
function nextStep() {
    // next step script ( aus dem Internet kopiert)
    var vorname = document.getElementById('vorname').value;
    var name = document.getElementById('name').value;
    var email = document.getElementById('email').value;

    if (vorname === '' || name === '' || email === '') {
        alert('Please fill out all fields before proceeding.');
    } else {
        document.getElementById('step1').style.display = 'none';
        document.getElementById('step2').style.display = 'block';
    }
}

function previousStep() {
        // previous step script ( aus dem Internet kopiert)
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';
}

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
