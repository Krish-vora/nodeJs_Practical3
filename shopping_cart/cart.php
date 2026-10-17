<?php
require "db.php";
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $stmt = mysqli_prepare($conn, "SELECT id FROM products WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    }
    header("Location: cart.php");
    exit;
}
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int)$_GET['remove']]);
    header("Location: cart.php");
    exit;
}
if (isset($_POST['clear'])) {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;
}
$total = 0;
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>My Cart</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<header><h1>My Shopping Cart</h1><nav><a href="index.php">Home</a><a href="register.php">Register</a><a href="login.php">Login</a></nav></header>
<main class="container"><div class="panel">
<?php if (empty($_SESSION['cart'])): ?>
<p>Your cart is empty.</p>
<?php else: ?>
<table><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th></tr>
<?php foreach ($_SESSION['cart'] as $id => $qty):
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "SELECT name, price FROM products WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$product) continue;
    $subtotal = (float)$product['price'] * (int)$qty;
    $total += $subtotal;
?>
<tr>
<td><?= htmlspecialchars($product['name']) ?></td>
<td>₹<?= number_format((float)$product['price'], 2) ?></td>
<td><?= (int)$qty ?></td>
<td>₹<?= number_format($subtotal, 2) ?></td>
<td><a class="button danger" href="cart.php?remove=<?= $id ?>">Remove</a></td>
</tr>
<?php endforeach; ?>
</table>
<h3>Total: ₹<?= number_format($total, 2) ?></h3>
<form method="post"><button name="clear" value="1">Clear Cart</button></form>
<?php endif; ?>
<a class="button" href="index.php">Continue Shopping</a>
</div></main></body></html>
