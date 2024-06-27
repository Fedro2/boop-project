<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php

include("../db_connect.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /* Debug 
    print_r($_POST); 
    echo $_SESSION["username"];*/

// wenn es email und passwort gibt, dann wird der user eingeloggt
    if (isset($_POST["email"]) && isset($_POST["passwort"])) {
    $email = $_POST["email"];
    $password = $_POST["passwort"];
   

 

    
    $stmt = $conn->prepare("SELECT * FROM benutzer WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    
    // logged der user ein und erstellt variablen für den user
    if ($user && password_verify($password, $user['passwort'])) {
        $_SESSION["username"] = $email; 
        $_SESSION["name"] = $user["name"];
        $_SESSION["surname"] = $user["vorname"];
        $_SESSION["admin"] = $user["admin"];
        header("Location: ../home.php"); 
        exit;
    } else {
        session_destroy();
        echo "Invalid email or password";
        
       
    }

}

// schaut ob das passwort übereinstimmt
function verify_password($username, $password) {
    include("../db_connect.php");
    $stmt = $conn->prepare('SELECT passwort FROM benutzer WHERE email = ?');
    $stmt->execute([$username]);

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the username exists in the database
    if ($result !== false) {
        // Verify the password
        $isPasswordCorrect = password_verify(trim($password), trim($result['passwort']));

        /* Debug 
        echo "Password entered by user: $password<br>";
        echo "Hashed password from database: " . $result['passwort'] . "<br>";
        echo "Result of password_verify: " . ($isPasswordCorrect ? "true" : "false") . "<br>";
        */
        
        return $isPasswordCorrect;
    } else {
       
        return false;
    }
}
    

// updated das passwort
function update_password($username, $hashed_password) {
    include("../db_connect.php");

    $stmt = $conn->prepare('UPDATE benutzer SET passwort = ? WHERE email = ?');
    $stmt->execute([$hashed_password, $username]);
}
    
    if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
    
        // schaut ob die eingegebenen passwörtern übereinstimmen
        if ($new_password !== $confirm_password) {
            echo "New passwords do not match.";
        } else {
           // ruft die function zur passwort überprüfung auf
            if (verify_password($_SESSION["username"], $current_password)) {
                // ruft die function zu passwort ändern auf
                update_password($_SESSION["username"], password_hash($new_password, PASSWORD_DEFAULT));
                echo "Password updated successfully.";
                header("Location: ../home.php");
            } else {
                echo "Current password is incorrect.";
            }
        }
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

<div class="login-container">
    <div class="login-content">
        <?php if (isset($_SESSION["username"])): ?>
        <img class="change" src="../pics/changepassword.png" alt="changepassword">
        <form method="POST" action="login.php">   
        <input type="password" id="current_password" placeholder="Current Password: " name="current_password">     
        <input type="password" id="new_password" placeholder="New Password: " name="new_password">        
        <input type="password" id="confirm_password" placeholder="Confirm Password: " name="confirm_password">
        <input type="submit" value="Change Password">
    </form>
        <?php else: ?>
        <img class="logpic" src="../pics/login.png" alt="login">    
        <form method="POST" action="login.php">
            
            <input type="email" id="email" placeholder="Email: " name="email"><br>
            
            <input type="password" id="passwort" placeholder="Passwort: " name="passwort"><br>
            <input type="submit" value="Submit">
        </form>
        <a class="register" href="register.php">Make an account</a>
        <?php endif; ?>
    </div>
</div>


</main>
</body>
</html>
