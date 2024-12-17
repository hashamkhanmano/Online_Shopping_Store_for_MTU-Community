<?php
session_start();
$category = $_GET['category'] ?? 'electronics'; // Default to electronics if no category is specified

// You could load products from the database based on the category
$products = [
    'electronics' => [
        ['name' => 'Laptop', 'price' => 1000],
        ['name' => 'Smartphone', 'price' => 800]
    ],
    'clothing' => [
        ['name' => 'T-shirt', 'price' => 20],
        ['name' => 'Jeans', 'price' => 50]
    ],
    'food' => [
        ['name' => 'Pizza', 'price' => 15],
        ['name' => 'Burger', 'price' => 10]
    ]
];

$category_products = $products[$category] ?? [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($category); ?> - Online Store</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2><?php echo ucfirst($category); ?> Products</h2>
    <div class="row">
        <?php foreach ($category_products as $product): ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['name']; ?></h5>
                        <p class="card-text">$<?php echo $product['price']; ?></p>
                        <a href="add_to_cart.php?product=<?php echo urlencode($product['name']); ?>&price=<?php echo $product['price']; ?>" class="btn btn-primary">Add to Cart</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Bootstrap JavaScript and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
