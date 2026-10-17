<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "krish123", "shopping_cart");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
