<?php
require "../db.php";
if (empty($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$error = "";
if (isset($_POST['save'])) {
    $name = trim($_POST['name'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);
    if ($name === '') {
        $error = "Enter a category name.";
    } else {
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE categories SET name=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "si", $name, $id);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO categories (name) VALUES (?)");
            mysqli_stmt_bind_param($stmt, "s", $name);
        }
        mysqli_stmt_execute($stmt);
        header("Location: categories.php");
        exit;
    }
}
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: categories.php");
    exit;
}
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM categories WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header>
        <h1>Manage Categories</h1>
        <nav><a href="dashboard.php">Dashboard</a><a href="logout.php">Logout</a></nav>
    </header>
    <main class="container">
        <div class="panel">
            <h2><?= $edit ? 'Edit' : 'Add' ?> Category</h2>
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
            <form method="post">
                <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
                <label>Category name</label><input name="name" required
                    value="<?= htmlspecialchars($edit['name'] ?? '') ?>">
                <button name="save" value="1">Save Category</button>
            </form>
        </div>
        <div class="panel">
            <h2>Category List</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
                <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                    <tr>
                        <td><?= (int) $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td><a href="?edit=<?= (int) $c['id'] ?>">Edit</a> |
                            <a href="?delete=<?= (int) $c['id'] ?>"
                                onclick="return confirm('Delete this category and its products?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </main>
</body>

</html>