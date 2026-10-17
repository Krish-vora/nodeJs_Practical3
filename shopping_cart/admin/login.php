<?php
require "../db.php";
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        ($_POST['username'] ?? '') === 'admin' &&
        ($_POST['password'] ?? '') === 'admin123'
    ) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    }
    $error = "Incorrect admin username or password.";
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header>
        <h1>Admin Login</h1>
        <nav><a href="../index.php">Store Home</a></nav>
    </header>
    <main class="container">
        <div class="panel">
            <h2>Admin Login</h2>
            <p class="muted">Practical demo credentials: admin / admin123</p>
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
            <form method="post">
                <label>Username</label><input name="username" required>
                <label>Password</label><input type="password" name="password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </main>
</body>

</html>