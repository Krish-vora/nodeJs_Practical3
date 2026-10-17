<?php
require "../db.php";
unset($_SESSION['admin']);
header("Location: login.php");
exit;
?>
