<?php
require "../db.php";
if (empty($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$error = "";
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

if (isset($_POST['save'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $image = $_POST['old_image'] ?? '';

    $check = mysqli_prepare($conn, "SELECT id FROM categories WHERE id=?");
    mysqli_stmt_bind_param($check, "i", $category_id);
    mysqli_stmt_execute($check);
    $categoryExists = mysqli_fetch_assoc(mysqli_stmt_get_result($check));

    if ($name === '' || $price < 0 || !$categoryExists) {
        $error = "Enter a product name, valid price and category.";
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $error = "Image upload failed.";
            } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $error = "Image must be smaller than 2 MB.";
            } else {
                $tmp = $_FILES['image']['tmp_name'];
                $info = @getimagesize($tmp);
                if (!$info || !in_array($info['mime'], ['image/jpeg', 'image/png'])) {
                    $error = "Please upload a JPG or PNG image.";
                } elseif (!function_exists('imagecreatetruecolor')) {
                    $error = "Enable the GD extension in php.ini and restart Apache.";
                } else {
                    $src = ($info['mime'] === 'image/png')
                        ? @imagecreatefrompng($tmp)
                        : @imagecreatefromjpeg($tmp);

                    if (!$src) {
                        $error = "Could not read the image.";
                    } else {
                        $w = imagesx($src);
                        $h = imagesy($src);
                        $scale = min(1, 300 / max($w, $h));
                        $nw = max(1, (int) ($w * $scale));
                        $nh = max(1, (int) ($h * $scale));
                        $dst = imagecreatetruecolor($nw, $nh);
                        $white = imagecolorallocate($dst, 255, 255, 255);
                        imagefill($dst, 0, 0, $white);
                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

                        if (!is_dir("../uploads")) {
                            mkdir("../uploads", 0755, true);
                        }
                        $newImage = uniqid("product_", true) . ".jpg";
                        if (imagejpeg($dst, "../uploads/" . $newImage, 85)) {
                            if ($image !== '' && is_file("../uploads/" . basename($image))) {
                                @unlink("../uploads/" . basename($image));
                            }
                            $image = $newImage;
                        } else {
                            $error = "Could not save the resized image.";
                        }
                        imagedestroy($src);
                        imagedestroy($dst);
                    }
                }
            }
        }

        if ($error === "") {
            if ($id > 0) {
                $stmt = mysqli_prepare($conn, "UPDATE products SET category_id=?, name=?, price=?, image=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "isdsi", $category_id, $name, $price, $image, $id);
            } else {
                $stmt = mysqli_prepare($conn, "INSERT INTO products (category_id, name, price, image) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "isds", $category_id, $name, $price, $image);
            }
            if (mysqli_stmt_execute($stmt)) {
                header("Location: products.php");
                exit;
            } else {
                $error = "Could not save product.";
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "SELECT image FROM products WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $old = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    if ($old && $old['image'] !== '' && is_file("../uploads/" . basename($old['image']))) {
        @unlink("../uploads/" . basename($old['image']));
    }
    header("Location: products.php");
    exit;
}
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
$products = mysqli_query($conn, "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.id DESC");
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Manage Products</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header>
        <h1>Manage Products</h1>
        <nav><a href="dashboard.php">Dashboard</a><a href="categories.php">Categories</a><a href="logout.php">Logout</a>
        </nav>
    </header>
    <main class="container">
        <div class="panel">
            <h2><?= $edit ? 'Edit' : 'Add' ?> Product</h2>
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
                <input type="hidden" name="old_image" value="<?= htmlspecialchars($edit['image'] ?? '') ?>">
                <label>Category</label><select name="category_id" required>
                    <option value="">Choose category</option>
                    <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= ($edit && (int) $edit['category_id'] === (int) $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endwhile; ?>
                </select>
                <label>Product name</label><input name="name" required
                    value="<?= htmlspecialchars($edit['name'] ?? '') ?>">
                <label>Price (₹)</label><input type="number" name="price" min="0" step="0.01" required
                    value="<?= htmlspecialchars((string) ($edit['price'] ?? '')) ?>">
                <label>Product picture (JPG or PNG, max 2 MB)</label><input type="file" name="image"
                    accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                <?php if ($edit && $edit['image'] !== ''): ?>
                    <p>Current picture:</p><img class="small-image" src="../uploads/<?= rawurlencode($edit['image']) ?>"
                        alt="Current product"><?php endif; ?>
                <p class="muted">Pictures are resized to fit within 300 × 300 pixels.</p>
                <button name="save" value="1">Save Product</button>
            </form>
        </div>
        <div class="panel">
            <h2>Product List</h2>
            <table>
                <tr>
                    <th>Picture</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
                <?php while ($p = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td><?php if ($p['image'] !== ''): ?><img class="small-image"
                                    src="../uploads/<?= rawurlencode($p['image']) ?>" alt=""><?php else: ?>No
                                image<?php endif; ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['category_name'] ?? '') ?></td>
                        <td>₹<?= number_format((float) $p['price'], 2) ?></td>
                        <td><a href="?edit=<?= (int) $p['id'] ?>">Edit</a> | <a href="?delete=<?= (int) $p['id'] ?>"
                                onclick="return confirm('Delete this product?')">Delete</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </main>
</body>

</html>