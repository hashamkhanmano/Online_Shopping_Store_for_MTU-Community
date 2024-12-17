<?php
require_once 'db.php';

function fetchCategories() {
    $query = "SELECT * FROM Category";
    return executeQuery($query)->fetchAll();
}

function fetchProductsByCategory($categoryId) {
    $query = "SELECT * FROM Product WHERE category_id = :categoryId";
    return executeQuery($query, ['categoryId' => $categoryId])->fetchAll();
}

function fetchCartItems($cartId) {
    $query = "SELECT p.name, p.price, sci.quantity 
              FROM ShoppingCartItem sci 
              JOIN Product p ON sci.product_id = p.product_id 
              WHERE sci.cart_id = :cartId";
    return executeQuery($query, ['cartId' => $cartId])->fetchAll();
}

function addProductToCart($cartId, $productId, $quantity) {
    $query = "INSERT INTO ShoppingCartItem (cart_id, product_id, quantity) 
              VALUES (:cartId, :productId, :quantity)
              ON DUPLICATE KEY UPDATE quantity = quantity + :quantity";
    executeQuery($query, [
        'cartId' => $cartId,
        'productId' => $productId,
        'quantity' => $quantity
    ]);
}

function checkout($cartId, $customerId) {
    $query = "SELECT SUM(p.price * sci.quantity) AS total 
              FROM ShoppingCartItem sci 
              JOIN Product p ON sci.product_id = p.product_id 
              WHERE sci.cart_id = :cartId";
    $total = executeQuery($query, ['cartId' => $cartId])->fetchColumn();

    $query = "INSERT INTO `Order` (customer_id, total_order_dollars) 
              VALUES (:customerId, :total)";
    executeQuery($query, [
        'customerId' => $customerId,
        'total' => $total
    ]);

    $orderId = connectToDB()->lastInsertId();

    $query = "INSERT INTO OrderItem (order_id, product_id, quantity, price)
              SELECT :orderId, sci.product_id, sci.quantity, p.price 
              FROM ShoppingCartItem sci 
              JOIN Product p ON sci.product_id = p.product_id 
              WHERE sci.cart_id = :cartId";
    executeQuery($query, [
        'orderId' => $orderId,
        'cartId' => $cartId
    ]);

    $query = "UPDATE Product p 
              JOIN ShoppingCartItem sci ON p.product_id = sci.product_id 
              SET p.stock_quantity = p.stock_quantity - sci.quantity 
              WHERE sci.cart_id = :cartId";
    executeQuery($query, ['cartId' => $cartId]);

    $query = "DELETE FROM ShoppingCartItem WHERE cart_id = :cartId";
    executeQuery($query, ['cartId' => $cartId]);
}
?>
