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
$perPage = 18; // number of users per page
$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

// Determine filter and order by conditions
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$where = [];
$orderBy = 'ID ASC';

switch ($filter) {
    case 'username_asc':
        $orderBy = 'benutzername ASC';
        break;
    case 'username_desc':
        $orderBy = 'benutzername DESC';
        break;
    case 'name_asc':
        $orderBy = 'name ASC';
        break;
    case 'name_desc':
        $orderBy = 'name DESC';
        break;
    default:
        break;
}

if (!empty($search)) {
    $searchWords = explode(' ', $search);
    $searchConditions = [];
    foreach ($searchWords as $word) {
        $searchConditions[] = '(benutzername LIKE :search_' . $word . ' OR name LIKE :search_' . $word . ' OR vorname LIKE :search_' . $word . ' OR email LIKE :search_' . $word . ')';
    }
    $where[] = implode(' AND ', $searchConditions);
}

$whereSql = '';
if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM Benutzer $whereSql ORDER BY $orderBy LIMIT {$start}, {$perPage}";
$stmt = $conn->prepare($sql);

$params = [];
if (!empty($search)) {
    foreach ($searchWords as $word) {
        $params['search_' . $word] = "%$word%";
    }
}

$stmt->execute($params);
$users = $stmt->fetchAll();

// get the total number of users
$total = $conn->query("SELECT FOUND_ROWS() as total")->fetch()['total'];
$pages = ceil($total / $perPage);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($users as $user) {
        $id = $user['ID'];
        $adminStatus = isset($_POST['admin_status'][$id]) ? 1 : 0;
        $updateStmt = $conn->prepare("UPDATE Benutzer SET admin = :admin WHERE ID = :id");
        $updateStmt->execute(['admin' => $adminStatus, 'id' => $id]);
    }
    header("Location: admin_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Administration</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200..800&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
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
    <div class="admin-content">
        <div class="left">
            <div class="up">
                <div class="up-first">
                    <a href="admin.php" class="button-other">KUNDEN</a><br>
                </div>
                
                <div class="hover-dropdown-container">
                    <img class="add add-second" src="../pics/options.png"/>
                    <div class="hover-dropdown">
                        <form action="admin_users.php" method="get">
                            <label for="filter">Filter:</label>
                            <select id="filter" name="filter">
                                <option value="default">--</option>
                                <option value="username_asc">Username A-Z</option>
                                <option value="username_desc">Username Z-A</option>
                                <option value="name_asc">Name A-Z</option>
                                <option value="name_desc">Name Z-A</option>
                            </select>
                            <input type="submit" value="Apply Filter">
                        </form>
                    </div>
                </div>
                <div class="hover-image">
                    <img class="add lupead" src="../pics/lupe.png">
                    <div class="search-container">
                        <form action="admin_users.php" method="get" class="search-bar">
                            <input type="search" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit">Search</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="down">
                <a href="admin_books.php" class="button-other">BOOKS</a>
                <a id="second" href="admin_users.php" class="button-current">USERS</a>
            </div>
        </div>
        <div class="customer-grid">
            <form method="post" action="admin_users.php">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <div class="customer-column"> 
                            <p class="kid"><?= htmlspecialchars($user['ID']) . " ";?></p>
                            <p><?= htmlspecialchars($user['benutzername']) . " | ";?>
                               <?= htmlspecialchars($user['name']) . " | ";?>
                               <?= htmlspecialchars($user['vorname']) . " | ";?>
                               <?= htmlspecialchars($user['email']);?>
                            </p>
                            <label>
                                <p>Admin:</p>
                                <input type="checkbox" name="admin_status[<?= $user['ID'] ?>]" <?= $user['admin'] ? 'checked' : '' ?>>
                            </label>
                            <a href="deleteuser.php?id=<?= $user['ID']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">
                                <img class="delete" src="../pics/delete.png"/>
                            </a>
                        </div>
                    <?php endforeach; ?>
                    <div class="save-button">
                        <input type="submit" value="Save Changes">
                    </div>
                <?php else: ?>
                    <p>No users found.</p>
                <?php endif; ?>
            </form>

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
