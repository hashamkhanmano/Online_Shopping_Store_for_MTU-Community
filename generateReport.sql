-- 1. Report: List the historic prices for a given product.
select*from Product;
-- Update prices for products to create price history records
UPDATE Product SET price = 749.99 WHERE product_id = 1;  -- Update price for product 1
UPDATE Product SET price = 849.99 WHERE product_id = 2;  -- Update price for product 2
UPDATE Product SET price = 29.99 WHERE product_id = 3;  -- Update price for product 3
UPDATE Product SET price = 49.99 WHERE product_id = 4;  -- Update price for product 4
UPDATE Product SET price = 599.99 WHERE product_id = 5;  -- Update price for product 5

UPDATE Product SET price = 149.99 WHERE product_id = 6;  -- Update price for product 6
UPDATE Product SET price = 429.99 WHERE product_id = 7;  -- Update price for product 7
UPDATE Product SET price = 229.99 WHERE product_id = 8;  -- Update price for product 8
UPDATE Product SET price = 439.99 WHERE product_id = 9;  -- Update price for product 9
UPDATE Product SET price = 5399.99 WHERE product_id = 10;  -- Update price for product 10
UPDATE Product SET price = 1599.99 WHERE product_id = 11;  -- Update price for product 11
-- Update again to simulate another price change
UPDATE Product SET price = 799.99 WHERE product_id = 1;  -- Update price for product 1
UPDATE Product SET price = 899.99 WHERE product_id = 2;  -- Update price for product 2
UPDATE Product SET price = 34.99 WHERE product_id = 3;  -- Update price for product 3
UPDATE Product SET price = 59.99 WHERE product_id = 4;  -- Update price for product 4
UPDATE Product SET price = 649.99 WHERE product_id = 5;  -- Update price for product 5
UPDATE Product SET price = 1149.99 WHERE product_id = 6;  -- Update price for product 6
UPDATE Product SET price = 4229.99 WHERE product_id = 7;  -- Update price for product 7
UPDATE Product SET price = 2229.99 WHERE product_id = 8;  -- Update price for product 8
UPDATE Product SET price = 4339.99 WHERE product_id = 9;  -- Update price for product 9
UPDATE Product SET price = 53949.99 WHERE product_id = 10;  -- Update price for product 10
UPDATE Product SET price = 15959.99 WHERE product_id = 11;  -- Update price for product 11

SELECT * FROM ProductPriceHistory;
# 1 historic price of product
SELECT 
    ph.history_id, 
    ph.product_id, 
    ph.old_price AS 'Old Price', 
    ph.new_price AS 'New Price', 
    ph.change_date AS 'Change Date'
FROM 
    ProductPriceHistory ph
WHERE 
    ph.product_id = 2
ORDER BY 
    ph.change_date DESC;

# with name and category
SELECT 
    ph.history_id, 
    ph.product_id AS 'Product ID',
    p.name AS 'Product Name',
    c.name AS 'Category Name',
    ph.old_price AS 'Old Price', 
    ph.new_price AS 'New Price', 
    ph.change_date AS 'Change Date'
FROM 
    ProductPriceHistory ph
JOIN 
    Product p ON ph.product_id = p.product_id
JOIN 
    Category c ON p.category_id = c.category_id
WHERE 
    ph.product_id = 2
ORDER BY 
    ph.change_date DESC;


# b) List the highest and lowest price within a given period (start time and end time) for all products.

SELECT 
    p.product_id,
    p.name AS 'Product Name',
    MAX(pph.new_price) AS 'Highest Price',
    MIN(pph.new_price) AS 'Lowest Price'
FROM 
    ProductPriceHistory pph
JOIN 
    Product p ON pph.product_id = p.product_id
WHERE 
    pph.change_date BETWEEN '2023-10-01' AND '2024-12-31'  -- Replace with your desired start and end dates
GROUP BY 
    p.product_id, p.name
ORDER BY 
    p.product_id;

# c) List how many qualities sold for each product within a specified time frame. You may ignore the ones that have not be sold.
# shopping 
-- Assuming you have already inserted 11 products and 3 customers into the respective tables

-- Insert 3 orders for 3 customers with items they purchased
-- Customer 1 (Bear) buys a Smartphone, Laptop, and T-shirt
INSERT INTO `Order` (customer_id, order_status, total_order_dollars, order_date) 
VALUES (1, 'Completed', 739.97, '2024-10-05');

-- Add order items for Customer 1
INSERT INTO OrderItem (order_id, product_id, quantity, price) 
VALUES (LAST_INSERT_ID(), 1, 1, 699.99),    -- Smartphone
       (LAST_INSERT_ID(), 2, 1, 999.99),    -- Laptop
       (LAST_INSERT_ID(), 3, 1, 19.99);     -- T-shirt

-- Customer 2 (Alice) buys a Sofa, Coffee, and Jeans
INSERT INTO `Order` (customer_id, order_status, total_order_dollars, order_date) 
VALUES (2, 'Completed', 629.97, '2024-10-10');

-- Add order items for Customer 2
INSERT INTO OrderItem (order_id, product_id, quantity, price) 
VALUES (LAST_INSERT_ID(), 5, 1, 499.99),    -- Sofa
       (LAST_INSERT_ID(), 8, 2, 9.99),      -- Coffee (2 items)
       (LAST_INSERT_ID(), 4, 1, 39.99);     -- Jeans

-- Customer 3 (Charlie) buys a Chair, Pasta, and Laptop
INSERT INTO `Order` (customer_id, order_status, total_order_dollars, order_date) 
VALUES (3, 'Completed', 1189.97, '2024-10-15');

-- Add order items for Customer 3
INSERT INTO OrderItem (order_id, product_id, quantity, price) 
VALUES (LAST_INSERT_ID(), 6, 2, 89.99),    -- Chair (2 items)
       (LAST_INSERT_ID(), 7, 5, 2.99),      -- Pasta (5 items)
       (LAST_INSERT_ID(), 2, 1, 999.99);    -- Laptop
       
-- Show the orders placed by customers (for verification)
SELECT o.order_id, o.customer_id, o.order_date, o.total_order_dollars, oi.product_id, oi.quantity, oi.price
FROM `Order` o
JOIN OrderItem oi ON o.order_id = oi.order_id
ORDER BY o.order_id;

-- Update product stock after purchases (ensure stock is reduced)
UPDATE Product SET stock_quantity = stock_quantity - 1 WHERE product_id IN (1, 2, 3, 5, 8);
UPDATE Product SET stock_quantity = stock_quantity - 2 WHERE product_id IN (6);
UPDATE Product SET stock_quantity = stock_quantity - 5 WHERE product_id = 7;


# c) List how many qualities sold for each product within a specified time frame. You may ignore the ones that have not be sold.

-- Verify if there are any order items with quantity > 0
SELECT oi.order_id, oi.product_id, oi.quantity, oi.price
FROM OrderItem oi
JOIN `Order` o ON oi.order_id = o.order_id
WHERE oi.quantity > 0 AND o.order_date BETWEEN '2023-10-01' AND '2024-11-30';

SELECT 
    p.product_id, 
    p.name AS 'Product Name', 
    SUM(oi.quantity) AS 'Total Quantity Sold'
FROM 
    OrderItem oi
JOIN 
    Product p ON oi.product_id = p.product_id
JOIN 
    `Order` o ON oi.order_id = o.order_id
WHERE 
    o.order_date BETWEEN '2023-10-01' AND '2024-11-30'  -- Correct date range
    AND oi.quantity > 0  -- Ignore products with quantity = 0 (i.e., not sold)
GROUP BY 
    p.product_id, p.name
HAVING 
    SUM(oi.quantity) > 0  -- Only include products with total quantity sold > 0
ORDER BY 
    p.product_id
LIMIT 0, 1000;

# d) d) List products below the restocking threshold and the quantity needed to reach the threshold.
select*from Product;

SELECT 
    p.product_id, 
    p.name AS 'Product Name', 
    p.stock_quantity AS 'Current Stock Quantity', 
    p.advising_threshold AS 'Restocking Threshold', 
    (p.advising_threshold - p.stock_quantity) AS 'Quantity Needed to Reach Threshold'
FROM 
    Product p
WHERE 
    p.stock_quantity < p.advising_threshold  -- Only products with stock below the threshold
ORDER BY 
    p.product_id;
