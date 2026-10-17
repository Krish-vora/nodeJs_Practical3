<?php
require "db.php";
unset($_SESSION['user_id'], $_SESSION['user_name']);
header("Location: index.php");
exit;
?>
