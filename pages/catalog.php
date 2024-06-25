<?php
session_start();
//error debugg
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//holt die connection zur datenbank von der db_connect.php datei
include("../db_connect.php");

// definiert variablen für das suchen, sortieren und filtern von büchern
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$search_field = isset($_GET['search_field']) && !empty($_GET['search_field']) ? $_GET['search_field'] : 'kurztitle';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$resultsPerPage = 18;

// initialisiert sql query
$sql = "SELECT * FROM buecher";
$conditions = [];
$params = [];



// Conditions für das suchen, filtern, and sortieren
if (!empty($search)) {
    if ($search_field == 'zustand' && is_numeric($search) && $search >= 1 && $search <= 14) {
        $conditions[] = "zustand = :search";
    } else {
        $conditions[] = "$search_field LIKE :search";
        $params[':search'] = "%$search%";
    }
}

if (!empty($filter) && in_array($filter, ['S', 'M', 'G'])) {
    $conditions[] = "zustand = :filter";
    $params[':filter'] = $filter;
}

if (!empty($filter)) {
    $conditions[] = "autor LIKE :filter";
    $params[':filter'] = "%" . $filter . "%";
}

// sucht mit where wenn es eine condition gibt
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// sortieren von büchern
switch ($sort) {
    case 'alphabetical':
        $sql .= " ORDER BY " . $search_field . " ASC";
        break;
    case 'reverse_alphabetical':
        $sql .= " ORDER BY kurztitle DESC";
        break;
    case 'popularity':
        $sql .= " ORDER BY kaufer DESC";
        break;
    case 'least_popular':
        $sql .= " ORDER BY kaufer ASC";
        break;
    case 'autor_alphabetical':
        $sql .= " ORDER BY autor ASC";
        break;
    case 'autor_reverse_alphabetical':
        $sql .= " ORDER BY autor DESC";
        break;
    default:
        $sql .= " ORDER BY id";
}

// seiten berechnung
$offset = ($page - 1) * $resultsPerPage;
$sql .= " LIMIT $offset, $resultsPerPage";

try {
    // führt den query aus
    $stmt = $conn->prepare($sql);
    foreach ($params as $param => $value) {
        $stmt->bindValue($param, $value);
    }
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
$stmt = $conn->prepare($sql);
foreach ($params as $param => $value) {
    $stmt->bindValue($param, $value);
}

// Log SQL habe ich gemacht damit ich es debugen kann
$logMessage = "SQL Query: " . $sql . "\n";
$logMessage .= "Bound Parameters: " . json_encode($params) . "\n";

// Pfad für das log file
$logFile = __DIR__ . "/sql_debug.log";

// macht die infos in die log datei
file_put_contents($logFile, $logMessage, FILE_APPEND);

// führt das ganze vorbereitete query endlich aus
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // anzahl allen büchern mit den conditions für die pagination
    $totalBooksSql = "SELECT COUNT(*) AS total FROM buecher";
    if (!empty($conditions)) {
        $totalBooksSql .= " WHERE " . implode(" AND ", $conditions);
    }
    $stmt = $conn->prepare($totalBooksSql);
    foreach ($params as $param => $value) {
        $stmt->bindValue($param, $value);
    }
    $stmt->execute();
    $totalBooks = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // total seiten berechnung
    $totalPages = ceil($totalBooks / $resultsPerPage);

    // berechnet die start- und endseite
    $startPage = max(1, $page - 2);
    $endPage = min($totalPages, $startPage + 4);

} catch (PDOException $e) {
    echo "Error executing query: " . $e->getMessage();
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







    <div class="header-main">
        <img class="allbooks" src="../pics/allbooks.png" alt="allbooks">
        <div class="hamburger">
        <div class="hover-dropdown-container">
            <img src="../pics/options.png" alt="options">
            <div class="hover-dropdown catalogdrop">
                <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                    <label for="sort">Sort:</label>
                    <select name="sort">
                        <option value="" selected>--</option>
                        <option value="alphabetical">Alphabetical (A-Z)</option>
                        <option value="reverse_alphabetical">Reverse Alphabetical (Z-A)</option>
                        <option value="popularity">Most popular</option>
                        <option value="least_popular">Least Popular</option>
                        <option value="autor_alphabetical">Autor Alphabetical (A-Z)</option>
                        <option value="autor_reverse_alphabetical">Autor Reverse Alphabetical (Z-A)</option>
                    </select>
                    <input type="submit" value="Apply">
                </form>
            </div>
        </div>
        </div>
        <div class="search-bar">
            <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <input type="text" name="search" placeholder="Search books..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="search_field">
                     <option value="kurztitle" <?php echo $search_field === 'kurztitle' ? 'selected' : ''; ?>>Title</option>
                    <option value="autor" <?php echo $search_field === 'autor' ? 'selected' : ''; ?>>Author</option>
                     <option value="zustand" <?php echo $search_field === 'zustand' ? 'selected' : ''; ?>>Condition (S/M/G)</option>
                </select>
                <input type="submit" value="BOOP IT!">
            </form>
        </div>
    </div>
    <!-- Bücher katalog, printet die bücher auf der page -->
    <div class="book-container">
        <?php if (empty($books)): ?>
            <p class="no-result">No books found with these options.</p>
        <?php else: ?>
            <?php foreach ($books as $book): ?>
                <a href='book_details.php?id=<?php echo htmlspecialchars($book['id']); ?>'>
                    <div class="book-box">
                        <div class="book-box-text">
                            <h2><?php echo htmlspecialchars($book['kurztitle']); ?></h2>
                            <p><?php echo htmlspecialchars($book['autor']); ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <!-- Pagination -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination">
            <a class="page-link" href="?page=1&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>&filter=<?php echo urlencode($filter); ?>">First</a>
            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <?php if ($i <= $totalPages): ?>
                    <a class="page-link <?php echo $i == $page ? 'current-page' : ''; ?>" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>&filter=<?php echo urlencode($filter); ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <a class="page-link" href="?page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>&filter=<?php echo urlencode($filter); ?>">Last</a>
        </div>
    <?php endif; ?>

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
</main>

<script src="../javascript.js"></script>
</body>
</html>
