<?php
require "db.php";
$category = max(0, (int)($_GET['category'] ?? 0));

if (isset($_GET['ajax'])) {
    header("Content-Type: text/html; charset=UTF-8");
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = 4;
    $offset = ($page - 1) * $limit;

    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM products WHERE category_id=?");
    mysqli_stmt_bind_param($stmt, "i", $category);
    mysqli_stmt_execute($stmt);
    $total = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

    $stmt = mysqli_prepare($conn, "SELECT id, name, price, image FROM products WHERE category_id=? ORDER BY id DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "iii", $category, $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        echo "<p>No products found in this category.</p>";
    }

    while ($p = mysqli_fetch_assoc($result)) {
        echo '<div class="card product">';
        if ($p['image'] !== '') {
            echo '<img src="uploads/' . rawurlencode($p['image']) . '" alt="Product">';
        } else {
            echo '<div class="muted">No picture available</div>';
        }
        echo '<h3>' . htmlspecialchars($p['name']) . '</h3>';
        echo '<p>Price: ₹' . number_format((float)$p['price'], 2) . '</p>';
        echo '<a class="button" href="cart.php?add=' . (int)$p['id'] . '">Add to Cart</a>';
        echo '</div>';
    }

    $pages = (int)ceil($total / $limit);
    if ($pages > 1) {
        echo '<div class="panel">';
        for ($i = 1; $i <= $pages; $i++) {
            echo '<button type="button" onclick="loadPage(' . $i . ')">' . $i . '</button>';
        }
        echo '</div>';
    }
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT name FROM categories WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $category);
mysqli_stmt_execute($stmt);
$cat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$cat) {
    http_response_code(404);
    $catName = "Category not found";
} else {
    $catName = $cat['name'];
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Products</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1><?= htmlspecialchars($catName) ?></h1>
    <nav><a href="index.php">Home</a><a href="cart.php">My Cart</a><a href="login.php">User Login</a></nav>
</header>
<main class="container">
    <h2>Products</h2>
    <div id="product-list" class="grid">Loading products...</div>
    <p><a class="button" href="index.php">Back to Categories</a></p>
</main>
<script>
function loadPage(page) {
    fetch('products.php?ajax=1&category=<?= $category ?>&page=' + page)
        .then(response => {
            if (!response.ok) throw new Error('Could not load products');
            return response.text();
        })
        .then(html => document.getElementById('product-list').innerHTML = html)
        .catch(() => document.getElementById('product-list').textContent = 'Unable to load products.');
}
loadPage(1);
</script>
</body>
</html>
