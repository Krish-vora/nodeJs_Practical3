<?php
require "db.php";

if (!isset($_SESSION['captcha_a'], $_SESSION['captcha_b'])) {
    $_SESSION['captcha_a'] = random_int(1, 9);
    $_SESSION['captcha_b'] = random_int(1, 9);
}
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $answer = (int)($_POST['captcha'] ?? -1);

    if ($answer !== $_SESSION['captcha_a'] + $_SESSION['captcha_b']) {
        $error = "Incorrect CAPTCHA. Try again.";
        unset($_SESSION['captcha_a'], $_SESSION['captcha_b']);
    } elseif ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 4) {
        $error = "Enter a valid name/email and a password of at least 4 characters.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hash);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Registration successful. You can now log in.";
            unset($_SESSION['captcha_a'], $_SESSION['captcha_b']);
        } else {
            $error = "Registration failed. This email may already be registered.";
        }
    }
}
if (!isset($_SESSION['captcha_a'], $_SESSION['captcha_b'])) {
    $_SESSION['captcha_a'] = random_int(1, 9);
    $_SESSION['captcha_b'] = random_int(1, 9);
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Register</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<header><h1>User Registration</h1><nav><a href="index.php">Home</a><a href="login.php">Login</a></nav></header>
<main class="container"><div class="panel">
<h2>Create Account</h2>
<?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<?php if ($success): ?><p class="message"><?= htmlspecialchars($success) ?></p><?php endif; ?>
<form method="post">
<label>Name</label><input name="name" required>
<label>Email</label><input type="email" name="email" required>
<label>Password (minimum 4 characters)</label><input type="password" name="password" minlength="4" required>
<label>CAPTCHA: <?= (int)$_SESSION['captcha_a'] ?> + <?= (int)$_SESSION['captcha_b'] ?> = ?</label>
<input type="number" name="captcha" required>
<button type="submit">Register</button>
</form></div></main></body></html>
