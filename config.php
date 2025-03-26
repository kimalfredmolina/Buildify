<?php
$host = "localhost";
$user = "root";
$password = "@kimalfred22"; //@kimalfred22
$dbname = "buildify";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
