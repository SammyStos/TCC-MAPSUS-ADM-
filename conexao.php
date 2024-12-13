<?php
$servername = "162.241.203.11";
$username = "mapsus45_sammyStos";
$password = "mikaela@20";
$dbname = "mapsus45_db_adm";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
