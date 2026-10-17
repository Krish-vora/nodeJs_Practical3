<?php
require "../db.php";
if (empty($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav><a href="../index.php">View Store</a><a href="logout.php">Logout</a></nav>
    </header>
    <main class="container">
        <div class="grid">
            <div class="card">
                <h2>Categories</h2>
                <p>Add, edit or delete categories.</p><a class="button" href="categories.php">Manage Categories</a>
            </div>
            <div class="card">
                <h2>Products</h2>
                <p>Add products and upload pictures.</p><a class="button" href="products.php">Manage Products</a>
            </div>
        </div>
    </main>
</body>

</html>