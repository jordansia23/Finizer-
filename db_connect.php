<?php
$servername = "localhost"; 
$username   = "root";      
$password   = "";           
$database   = "finizer";
$port       = 3306;        

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
