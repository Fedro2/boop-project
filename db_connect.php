
<?php
//verbindet die datenbank
try {
    $conn = new PDO("mysql:host=localhost;dbname=book", "root", "root");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>



