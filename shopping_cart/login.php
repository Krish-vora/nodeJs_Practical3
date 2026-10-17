<?php
require "db.php";
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = mysqli_prepare($conn, "SELECT id, name, password FROM users WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: index.php");
        exit;
    }
    $error = "Invalid email or password.";
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>User Login</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<header><h1>User Login</h1><nav><a href="index.php">Home</a><a href="register.php">Register</a></nav></header>
<main class="container"><div class="panel">
<h2>Login</h2>
<?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post">
<label>Email</label><input type="email" name="email" required>
<label>Password</label><input type="password" name="password" required>
<button type="submit">Login</button>
</form></div></main></body></html>
