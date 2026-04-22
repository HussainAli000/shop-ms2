<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'shopdb');
if ($conn->connect_error) {
    die('Database connection failed.');
}

$conn->set_charset('utf8mb4');
?>
