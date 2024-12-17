<?php
session_start();  // Start the session

// Check if the remove_index is passed and if the item exists in the cart
if (isset($_POST['remove_index']) && isset($_SESSION['cart'][$_POST['remove_index']])) {
    // Remove the item from the cart
    unset($_SESSION['cart'][$_POST['remove_index']]);

    // Reindex the array to ensure that indexes are correct after removal
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

// Redirect to the cart page after removal
header('Location: cart.php');
exit();
?>
