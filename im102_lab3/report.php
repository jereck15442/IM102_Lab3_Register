<?php
include 'config.php';
include 'navbar.php';

$summary = $conn->query("
    SELECT
        COUNT(*) AS total_products,
        SUM(stock) AS total_stock,
        SUM(price * stock) AS total_value
    FROM products
")->fetch_assoc();

$categories = $conn->query("
SELECT
    c.name,
    COUNT(p.id) AS products,
    SUM(p.stock) AS total_stock,
    SUM(p.price * p.stock) AS total_value,
    AVG(p.price) AS avg_price
FROM categories c
LEFT JOIN products p
    ON c.id = p.category_id
GROUP BY c.id, c.name
ORDER BY total_value DESC
");

$suppliers = $conn->query("
SELECT
    s.name,
    COUNT(p.id) AS products,
    SUM(p.stock) AS total_stock
FROM suppliers s
LEFT JOIN products p
    ON s.id = p.supplier_id
GROUP BY s.id, s.name
ORDER BY products DESC
");
?>
<link rel="stylesheet" href="style.css">
<div class="report-container">

    <h2>Inventory Report</h2>

    <div class="summary-cards">
        <div class="card">
            <h3>Total Products</h3>
            <p><?= $summary['total_products'] ?></p>
        </div>

        <div class="card">
            <h3>Total Stock</h3>
            <p><?= $summary['total_stock'] ?></p>
        </div>

        <div class="card">
            <h3>Total Value</h3>
            <p>₱<?= number_format($summary['total_value'],2) ?></p>
        </div>
    </div>

    <h2>Category Report</h2>

    <table class="report-table">
        <tr>
            <th>Category</th>
            <th>Products</th>
            <th>Total Stock</th>
            <th>Total Value</th>
            <th>Average Price</th>
        </tr>

        <?php while($row = $categories->fetch_assoc()): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['products'] ?></td>
            <td><?= $row['total_stock'] ?? 0 ?></td>
            <td>₱<?= number_format($row['total_value'] ?? 0,2) ?></td>
            <td>₱<?= number_format($row['avg_price'] ?? 0,2) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>
