<?php
include 'config.php';
include 'navbar.php';

$id = (int)$_GET['id'];

$product = $conn->query("
    SELECT p.*, c.name AS category
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.id = $id
")->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $conn->query("
        DELETE FROM products
        WHERE id = $id
    ");

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="delete-container">

<div class="delete-box">
    <h2>Delete Product</h2>

    <div class="info">
        <strong>Name:</strong> <?= $product['name'] ?>
    </div>

    <div class="info">
        <strong>Category:</strong> <?= $product['category'] ?>
    </div>

    <div class="info">
        <strong>Price:</strong> ₱<?= number_format($product['price'], 2) ?>
    </div>

    <div class="info">
        <strong>Stock:</strong> <?= $product['stock'] ?>
    </div>

    <div class="warning">
        Are you sure you want to delete this product?
    </div>

    <form method="POST">
        <div class="buttons">
            <button type="submit" class="delete-btn">
                Yes, Delete
            </button>

            <a href="index.php" class="cancel-btn">
                Cancel
            </a>
        </div>
    </form>
</div>
</div>


</body>
</html>