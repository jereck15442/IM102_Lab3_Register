<?php
include 'navbar.php';
include 'config.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "
SELECT
    p.id,
    p.name,
    p.description,
    p.price,
    p.stock,
    c.name AS category,
    s.name AS supplier,
    p.created_at
FROM products p
JOIN categories c ON p.category_id = c.id
JOIN suppliers s ON p.supplier_id = s.id
WHERE 1=1
";

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);

    $sql .= "
    AND (
        p.name LIKE '%$search%'
        OR p.description LIKE '%$search%'
    )
    ";
}

if (!empty($category)) {
    $category = mysqli_real_escape_string($conn, $category);
    $sql .= " AND c.name = '$category'";
}

$sql .= " ORDER BY p.id";

$result = mysqli_query($conn, $sql);

$statsSql = "
SELECT
    COUNT(*) AS total_products,
    SUM(stock) AS total_stock,
    SUM(price * stock) AS total_value,
    SUM(CASE WHEN stock < 20 THEN 1 ELSE 0 END) AS low_stock
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE 1=1
";

if (!empty($search)) {
    $statsSql .= "
    AND (
        p.name LIKE '%$search%'
        OR p.description LIKE '%$search%'
    )
    ";
}

if (!empty($category)) {
    $statsSql .= " AND c.name = '$category'";
}

$stats = mysqli_fetch_assoc(mysqli_query($conn, $statsSql));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <!-- <h1 class="Product_Inventory">Product Inventory</h1> -->

    <form method="GET">

        <input
            type="text"
            name="search"
            class="search-input"
            placeholder="Search products..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <select name="category" class="category-select">
            <option value="">All Categories</option>

            <?php
            $categories = mysqli_query(
                $conn,
                "SELECT DISTINCT name FROM categories ORDER BY name"
            );

            while ($c = mysqli_fetch_assoc($categories)) {
            ?>
                <option
                    value="<?= $c['name'] ?>"
                    <?= $category == $c['name'] ? 'selected' : '' ?>
                >
                    <?= $c['name'] ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit" class="filter-btn">Filter</button>

        <!-- <a href="add.php" class="add-btn">+ Add Product</a>

        <a href="report.php" class="add-btn">View Reports</a> -->

    </form>

    <br>

    <div class="stats-box">

    <div class="stat-card">
        <h3>Total Products</h3>
        <p><?= $stats['total_products'] ?></p>
    </div>

    <div class="stat-card">
        <h3>Total Stock</h3>
        <p><?= $stats['total_stock'] ?></p>
    </div>

    <div class="stat-card">
        <h3>Inventory Value</h3>
        <p>₱<?= number_format($stats['total_value'], 2) ?></p>
    </div>

    <div class="stat-card">
        <h3>Low Stock Items</h3>
        <p><?= $stats['low_stock'] ?></p>
    </div>

</div>

    <br>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Category</th>
            <th>Supplier</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

        <tr <?= $row['stock'] < 20 ? 'style="background:#ffcccc;"' : '' ?>>

            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['description'] ?></td>
            <td>₱<?= number_format($row['price'], 2) ?></td>
            <td><?= $row['stock'] ?></td>
            <td><?= $row['category'] ?></td>
            <td><?= $row['supplier'] ?></td>
            <td><?= $row['created_at'] ?></td>

            <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="edit-btn">
                    Edit
                </a>

                <a
                    href="delete.php?id=<?= $row['id'] ?>"
                    class="delete-btn"
                    onclick="return confirm('Are you sure you want to delete this product?')">
                    Delete
                </a>
            </td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>