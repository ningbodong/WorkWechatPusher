<?php
$dbname = "send";
$dbhost = "127.0.0.1";
$dbuser = "root";
$dbpass = "password";

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");