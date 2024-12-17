<?php
session_start();  // Start the session
require_once 'db.php';  // Include the DB connection file

// Check if the user is logged in
if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];

    // Fetch the user's cart items from the database
    $query = "SELECT c.cart_id, c.product_id, c.quantity, p.name AS product_name, p.price, p.image
          FROM Cart c
          JOIN Product p ON c.product_id = p.product_id
          WHERE c.customer_id = :customer_id";
    $stmt = executeQuery($query, ['customer_id' => $customer_id]);

    // Fetch cart items
    $db_cart_items = $stmt->fetchAll();

    // If the user has a session cart, merge it with the database cart
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Synchronize session cart with the database cart
        foreach ($_SESSION['cart'] as $session_cart_item) {
            // If the session cart has items not in the database, merge them
            $found = false;
            foreach ($db_cart_items as &$db_cart_item) {
                if ($db_cart_item['product_id'] == $session_cart_item['product_id']) {
                    // Update quantity if product is already in the cart
                    $db_cart_item['quantity'] += $session_cart_item['quantity'];
                    $found = true;
                    break;
                }
            }
            // If item not found in the database, add it to the db_cart_items
            if (!$found) {
                $db_cart_items[] = $session_cart_item;
            }
        }
    }

} else {
    // If not logged in, fall back to session-based cart only
    $db_cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

// Handle removal of item from the cart
if (isset($_GET['remove_product_id'])) {
    $product_id = $_GET['remove_product_id'];

    // Remove the product from the session cart
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($cart_item) use ($product_id) {
        return $cart_item['product_id'] !== $product_id;
    });

    // Re-index the array after removing an item
    $_SESSION['cart'] = array_values($_SESSION['cart']);

    // Also remove from the database
    if (isset($_SESSION['customer_id'])) {
        $query = "DELETE FROM Cart WHERE product_id = :product_id AND customer_id = :customer_id";
        executeQuery($query, ['product_id' => $product_id, 'customer_id' => $_SESSION['customer_id']]);
    }

    // Redirect to cart page after removal
    header('Location: cart.php');
    exit();
}

// Handle quantity update (submitted form)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = $_POST['quantity'];

    // Loop through the cart (session or database) and update quantity
    if (isset($_SESSION['customer_id'])) {
        // Update the cart in the database
        $query = "UPDATE Cart SET quantity = :quantity WHERE product_id = :product_id AND customer_id = :customer_id";
        executeQuery($query, ['quantity' => $new_quantity, 'product_id' => $product_id, 'customer_id' => $_SESSION['customer_id']]);
    }

    // Update the session cart as well
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            $cart_item['quantity'] = $new_quantity;
            break;
        }
    }

    // Redirect back to cart page to reflect changes
    header('Location: cart.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'header.php';  // Include the header ?>

<div class="container mt-5">
    <h2>Your Shopping Cart</h2>

    <?php if (empty($db_cart_items)): ?>
        <p>Your cart is empty.</p>
        <a href="cust_main.php" class="btn btn-primary">Continue Shopping</a>
    <?php else: ?>
        <form action="cart.php" method="POST">
            <table class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_price = 0;
                    foreach ($db_cart_items as $index => $cart_item):
                        $total_item_price = $cart_item['price'] * $cart_item['quantity'];
                        $total_price += $total_item_price;
                    ?>
                    <tr>
                        <td>
                            <img src="https://classdb.it.mtu.edu/~hashamk/images/<?php echo $cart_item['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($cart_item['product_name']); ?>" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        </td>
                        <td><?php echo htmlspecialchars($cart_item['product_name']); ?></td>
                        <td>$<?php echo number_format($cart_item['price'], 2); ?></td>
                        <td>
                            <!-- Quantity input field -->
                            <input type="number" class="form-control" name="quantity" value="<?php echo $cart_item['quantity']; ?>" min="1" style="width: 80px;">
                        </td>
                        <td>$<?php echo number_format($total_item_price, 2); ?></td>
                        <td>
                            <!-- Remove button -->
                            <a href="cart.php?remove_product_id=<?php echo $cart_item['product_id']; ?>" class="btn btn-danger">Remove</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <input type="hidden" name="product_id" value="<?php echo $cart_item['product_id']; ?>">
            <button type="submit" class="btn btn-primary" name="update_quantity">Update Quantity</button>
        </form>

        <div class="mt-3">
            <h4>Total Price: $<span id="total-price"><?php echo number_format($total_price, 2); ?></span></h4>
            <!-- Button to open the checkout modal -->
            <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
            <a href="cust_main.php" class="btn btn-secondary">Continue Shopping</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
