<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "im102_lab3";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>