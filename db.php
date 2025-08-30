<?php
// db.php
$host = "127.0.0.1";
$user = "root"; // change if your MySQL user is different
$pass = "";     // change if you have password set
$db   = "med_platform";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}
?>
