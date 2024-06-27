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

// Check if user is an admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../home.php");
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 18; // number of books per page
$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

// Determine filter and order by conditions
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$where = [];
$orderBy = 'b.id ASC';

switch ($filter) {
    case 'title_asc':
        $orderBy = 'b.kurztitle ASC';
        break;
    case 'title_desc':
        $orderBy = 'b.kurztitle DESC';
        break;
    case 'autor_asc':
        $orderBy = 'b.autor ASC';
        break;
    case 'autor_desc':
        $orderBy = 'b.autor DESC';
        break;
    default:
        break;
}

if (!empty($search)) {
    $searchWords = explode(' ', $search);
    $searchConditions = [];
    foreach ($searchWords as $word) {
        $searchConditions[] = '(b.kurztitle LIKE :search_' . $word . ' OR b.autor LIKE :search_' . $word . ')';
    }
    $where[] = implode(' AND ', $searchConditions);
}

$whereSql = '';
if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

$sql = "SELECT SQL_CALC_FOUND_ROWS b.*, k.kategorie AS kategorie_name 
        FROM buecher b
        JOIN kategorien k ON b.kategorie = k.id
        $whereSql
        ORDER BY $orderBy 
        LIMIT {$start}, {$perPage}";
$stmt = $conn->prepare($sql);

$params = [];
if (!empty($search)) {
    foreach ($searchWords as $word) {
        $params['search_' . $word] = "%$word%";
    }
}

$stmt->execute($params);
$books = $stmt->fetchAll();

// get the total number of books
$total = $conn->query("SELECT FOUND_ROWS() as total")->fetch()['total'];
$pages = ceil($total / $perPage);
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
    <div class="admin-content">
        <div class="left">
            <div class="up">
                <div class="up-first">
                <a href="admin.php" class="button-other">KUNDEN</a><br>
                </div>
                <a href="add_book.php">
                    <img class="add" src="../pics/add.png"/>
                </a>
                <div class="hover-dropdown-container">
                    <img class="add add-second" src="../pics/options.png"/>
                    <div class="hover-dropdown">
                        <form action="admin_books.php" method="get">
                            <label for="filter">Filter:</label>
                            <select id="filter" name="filter">
                                <option value="default">--</option>
                                <option value="title_asc">Title A-Z</option>
                                <option value="title_desc">Title Z-A</option>
                                <option value="autor_asc">Autor A-Z</option>
                                <option value="autor_desc">Autor Z-A</option>
                            </select>
                            <input type="submit" value="Apply Filter">
                        </form>
                    </div>
                </div>
                <div class="hover-image">
                    <img class="add lupead" src="../pics/lupe.png">
                    <div class="search-container">
                        <form action="admin_books.php" method="get" class="search-bar">
                            <input type="search" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit">Search</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="down">
                <a href="admin_books.php" class="button-current">BOOKS</a>
                <a id="second" href="admin_users.php" class="button-other">USERS</a>
            </div>
        </div>
        <div class="customer-grid">
            <?php if (!empty($books)): ?>
                <?php foreach ($books as $book): ?>
                    <div class="customer-column"> 
                        <p class="kid"><?= htmlspecialchars($book['id']) . " ";?></p>
                        <p>
                            <?= htmlspecialchars(mb_strimwidth($book['kurztitle'], 0, 25, '...')) . " | ";?>
                            <?= htmlspecialchars($book['autor']) . " | ";?>
                            <?= htmlspecialchars($book['kategorie_name']) . " | "; ?>
                            <?= htmlspecialchars($book['zustand']);?>
                        </p>
                        <a href="update_book.php?id=<?= $book['id']; ?>">
                            <img class="edit" src="../pics/admin.png"/>
                        </a>
                        <a href="deletebook.php?id=<?= $book['id']; ?>" onclick="return confirm('Are you sure you want to delete this book?');">
                            <img class="delete" src="../pics/delete.png"/>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No books found.</p>
            <?php endif; ?>

            <?php
            $range = 5; 
            $startPage = max(1, $page - floor($range / 2));
            $endPage = min($pages, $startPage + $range - 1);

            if ($pages > 1): ?>
                <div class="pagination">
                    <a class="page-link" href="?page=1&filter=<?= $filter; ?>&search=<?= $search; ?>">First</a>
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a class="page-link <?= $i == $page ? 'current-page' : ''; ?>" href="?page=<?= $i; ?>&filter=<?= $filter; ?>&search=<?= $search; ?>"><?= $i; ?></a>
                    <?php endfor; ?>
                    <a class="page-link" href="?page=<?= $pages; ?>&filter=<?= $filter; ?>&search=<?= $search; ?>">Last</a>
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
            <a href="https://www.instagram.com/ww_basel/" target="_blank"><img src="../pics/instagramlogo.png" alt="WW Basel on Instagram"></a>
            <a href="https://www.facebook.com"target="_blank"><img src="../pics/facebooklogo.png" alt="Facebook"></a>
            <a href="https://twitter.com"target="_blank"><img src="../pics/twitterlogo.png" alt="Twitter"></a>
            <a href="https://www.youtube.com"target="_blank"><img src="../pics/youtubelogo.png" alt=">Youtube"></a>
        </div>
    </div>
</footer>   
</body>
</html>
