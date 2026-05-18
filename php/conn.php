<?php
// $servername = "localhost";
// $dbname = "db_hiking1";
// $username = "root";
// $password = "";


// try {
//     $conn = new PDO("mysql:host=$servername;dbname=$dbname" , $username , $password);

//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die("Connection failed: " . $e->getMessage());
// }

$servername = "sql306.infinityfree.com";
$dbname = "if0_41953633_db_hiking1";
$username = "if0_41953633";
$password = "merwin2004";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>