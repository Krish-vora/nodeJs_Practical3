<?php
require "db.php";
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Simple Shopping Cart</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Simple Shopping Cart</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
        <a href="login.php">User Login</a>
        <a href="cart.php">My Cart (<?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
        <a href="admin/login.php">Admin</a>
    </nav>
</header>
<main class="container">
    <h2>Shop by Category</h2>
    <div class="grid">
        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
            <div class="card">
                <h3><?= htmlspecialchars($cat['name']) ?></h3>
                <p class="muted">View products in this category.</p>
                <a class="button" href="products.php?category=<?= (int)$cat['id'] ?>">View Products</a>
            </div>
        <?php endwhile; ?>
    </div>
</main>
<footer>PHP and MySQL Shopping Cart Practical</footer>
</body>
</html>
