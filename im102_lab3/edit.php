<?php
include 'config.php';
include 'navbar.php';

$id = (int) $_GET['id'];

$product = $conn->query("
    SELECT * FROM products WHERE id = $id
")->fetch_assoc();

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$suppliers = $conn->query("SELECT id, name FROM suppliers ORDER BY name");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $category_id = (int) $_POST['category_id'];
    $supplier_id = (int) $_POST['supplier_id'];

    $sql = "
        UPDATE products SET
            name='$name',
            description='$description',
            price=$price,
            stock=$stock,
            category_id=$category_id,
            supplier_id=$supplier_id
        WHERE id=$id
    ";

    mysqli_query($conn, $sql);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">
    <h2>Edit Product</h2>

    <form method="POST" class="form-box">

        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($product['name']) ?>"
            required
        >

        <textarea
            name="description"
            required><?= htmlspecialchars($product['description']) ?></textarea>

        <input
            type="number"
            step="0.01"
            name="price"
            value="<?= $product['price'] ?>"
            required
        >

        <input
            type="number"
            name="stock"
            value="<?= $product['stock'] ?>"
            required
        >

        <select name="category_id" required>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option
                    value="<?= $cat['id'] ?>"
                    <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                    <?= $cat['name'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="supplier_id" required>
            <?php while ($sup = $suppliers->fetch_assoc()): ?>
                <option
                    value="<?= $sup['id'] ?>"
                    <?= $sup['id'] == $product['supplier_id'] ? 'selected' : '' ?>>
                    <?= $sup['name'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Update Product</button>

        <a href="index.php" class="cancel-btn">Cancel</a>

    </form>
</div>

</body>
</html>