<?php
$servername = "localhost"; 
$username   = "root";       // your MySQL username (default: root)
$password   = "";           // your MySQL password (default: empty)
$database   = "finizer";    // your database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
