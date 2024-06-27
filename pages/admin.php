<?php
// Session starten
session_start();

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../db_connect.php");

// Überprüfung, ob Benutzer eingeloggt ist
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Überprüfung, ob Benutzer Adminrechte hat
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../home.php");
    exit();
}

// abrufen der such- und seitenparameter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 18; 
$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

// filter abrufen
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// IInitialisierung der WHERE-Bedingungen und Sortierung
$where = [];
$orderBy = 'kid ASC';

// filterbedingungen setzen
switch ($filter) {
    case 'vorname_asc':
        $orderBy = 'vorname ASC';
        break;
    case 'vorname_desc':
        $orderBy = 'vorname DESC';
        break;
    case 'name_asc':
        $orderBy = 'name ASC';
        break;
    case 'name_desc':
        $orderBy = 'name DESC';
        break;
    case 'geschlecht_m':
        $where[] = 'geschlecht = "M"';
        break;
    case 'geschlecht_f':
        $where[] = 'geschlecht = "F"';
        break;
    case 'kunde_seit_asc':
        $orderBy = 'kunde_seit ASC';
        break;
    case 'kunde_seit_desc':
        $orderBy = 'kunde_seit DESC';
        break;
    case 'geburtstag_asc':
        $orderBy = 'geburtstag ASC';
        break;
    case 'geburtstag_desc':
        $orderBy = 'geburtstag DESC';
        break;
    case 'kontaktpermail_1':
        $where[] = 'kontaktpermail = 1';
        break;
    case 'kontaktpermail_0':
        $where[] = 'kontaktpermail = 0';
        break;
    default:
        break;
}

// Suchbedingungen hinzufügen
if (!empty($search)) {
    $searchWords = explode(' ', $search);
    $searchConditions = [];
    foreach ($searchWords as $word) {
        $searchConditions[] = '(name LIKE :search_' . $word . ' OR vorname LIKE :search_' . $word . ')';
    }
    $where[] = implode(' AND ', $searchConditions);
}

// WHERE-SQL-Bedingung zusammenstellen
$whereSql = '';
if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

// SQL-Abfrage erstellen
$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM kunden $whereSql ORDER BY $orderBy LIMIT {$start}, {$perPage}";
$stmt = $conn->prepare($sql);

// Parameter für die Suchbedingungen setzen
$params = [];
if (!empty($search)) {
    foreach ($searchWords as $word) {
        $params['search_' . $word] = "%$word%";
    }
}

// SQL-Abfrage ausführen
$stmt->execute($params);
$customers = $stmt->fetchAll();

// Gesamtanzahl der Kunden abrufen
$total = $conn->query("SELECT FOUND_ROWS() as total")->fetch()['total'];
$pages = ceil($total / $perPage);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kundenkatalog</title>
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
    <div class="admin-content">
        <div class="left">
            <div class="up">
                <div class="up-first">
                <a href="admin.php" class="button-current">KUNDEN</a><br>
                </div>
                <a href="add_customer.php">
                    <img class="add" src="../pics/add.png"/>
                </a>
                <div class="hover-dropdown-container">
                    <img class="add add-second" src="../pics/options.png"/>
                    <div class="hover-dropdown">
                        <form action="admin.php" method="get">
                            <label for="filter">Filter:</label>
                            <select id="filter" name="filter">
                                <option value="default">--</option>
                                <option value="vorname_asc">Vorname A-Z</option>
                                <option value="vorname_desc">Vorname Z-A</option>
                                <option value="name_asc">Name A-Z</option>
                                <option value="name_desc">Name Z-A</option>
                                <option value="geschlecht_m">Geschlecht M</option>
                                <option value="geschlecht_f">Geschlecht F</option>
                                <option value="kunde_seit_asc">ältester Kunde</option>
                                <option value="kunde_seit_desc">jüngster Kunde</option>
                                <option value="geburtstag_asc">ältester Kunde (Alter)</option>
                                <option value="geburtstag_desc">jüngster Kunde (Alter)</option>
                                <option value="kontaktpermail_1">Kontakt per Mail Ja</option>
                                <option value="kontaktpermail_0">Kontakt per Mail Nein</option>
                            </select>
                            <input type="submit" value="Filter anwenden">
                        </form>
                    </div>
                </div>
                <div class="hover-image">
                    <img class="add lupead" src="../pics/lupe.png">
                    <div class="search-container">
                        <form action="admin.php" method="get" class="search-bar">
                            <input type="search" name="search" placeholder="Suchen..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit">Suchen</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="down">
                <a href="admin_books.php" class="button-other">BÜCHER</a>
                <a id="second" href="admin_users.php" class="button-other">BENUTZER</a>
            </div>
        </div>
        <div class="customer-grid">
            <?php if (!empty($customers)): ?>
                <?php foreach ($customers as $customer): ?>
                    <div class="customer-column"> 
                        <p class="kid"><?= htmlspecialchars($customer['kid']) . " ";?></p>
                        <p><?= htmlspecialchars($customer['vorname']) . " ";?><?= htmlspecialchars($customer['name']) . " | "; ?><?= htmlspecialchars($customer['geschlecht']) . " | "; ?></p>
                        <p class="emailcus"><?= htmlspecialchars($customer['email']); ?></p>
                        <?php
                            // Alter des Kunden berechnen
                            $geburtstag = new DateTime(htmlspecialchars($customer['geburtstag']));
                            $now = new DateTime();
                            $intervalGeburtstag = $now->diff($geburtstag);
                            $alter = $intervalGeburtstag->y;

                            // Anzahl der Tage von kunde
                            $kundeSeit = new DateTime(htmlspecialchars($customer['kunde_seit']));
                            $intervalKundeSeit = $now->diff($kundeSeit);
                            $tageKunde = $intervalKundeSeit->days;
                        ?>
                        <p>  |   Geb. am:  <?= htmlspecialchars($customer['geburtstag']) . " (" . $alter . ")   |   Kunde seit: "; ?><?= htmlspecialchars($customer['kunde_seit']) . " (" . $tageKunde . ")";?></p>
                        <a href="update_customer.php?id=<?php echo $customer['kid']; ?>">
                            <img class="edit" src="../pics/admin.png"/>
                        </a>
                        <a href="deletecustomer.php?id=<?php echo $customer['kid']; ?>" onclick="return confirm('Sind Sie sicher, dass Sie diesen Kunden löschen möchten?');">
                            <img class="delete" src="../pics/delete.png"/>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Keine Kunden gefunden.</p>
            <?php endif; ?>

            <?php
            // Paginierung erstellen
            $range = 5; 
            $startPage = max(1, $page - floor($range / 2));
            $endPage = min($pages, $startPage + $range - 1);

            if ($pages > 1): ?>
                <div class="pagination">
                    <a class="page-link" href="?page=1&filter=<?php echo $filter; ?>&search=<?php echo $search; ?>">Erste</a>
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a class="page-link <?php echo $i == $page ? 'current-page' : ''; ?>" href="?page=<?php echo $i; ?>&filter=<?php echo $filter; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <a class="page-link" href="?page=<?php echo $pages; ?>&filter=<?php echo $filter; ?>&search=<?php echo $search; ?>">Letzte</a>
                </div>
            <?php endif; ?>
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
            <a href="https://www.instagram.com/ww_basel/" target="_blank"><img src="../pics/instagramlogo.png" alt="WW Basel auf Instagram"></a>
            <a href="https://www.facebook.com" target="_blank"><img src="../pics/facebooklogo.png" alt="Facebook"></a>
            <a href="https://twitter.com" target="_blank"><img src="../pics/twitterlogo.png" alt="Twitter"></a>
            <a href="https://www.youtube.com" target="_blank"><img src="../pics/youtubelogo.png" alt="YouTube"></a>
        </div>
    </div>
</footer>   
</body>
</html>
