-- Insert sample data for Employee table
INSERT INTO Employee (username, email, password) 
VALUES 
('mstone', 'mstone@example.com', SHA2('tempPassword', 256)),
('jdoe', 'jdoe@example.com', SHA2('tempPassword', 256)),
('admin', 'admin@example.com', SHA2('adminPassword', 256));

-- Insert categories
INSERT INTO Category (name, description) VALUES 
('Electronics', 'Devices and gadgets'), 
('Clothing', 'Apparel and accessories'), 
('Home', 'Home goods and furniture'), 
('Food', 'Groceries and consumables');

-- Insert products
INSERT INTO Product (name, description, price, stock_quantity, advising_threshold, category_id) VALUES 
('Smartphone', 'Latest model smartphone', 699.99, 50, 20, 1),
('Laptop', 'High performance laptop', 999.99, 30, 10, 1),
('T-shirt', 'Cotton t-shirt', 19.99, 100, 50, 2),
('Jeans', 'Blue denim jeans', 39.99, 70, 20, 2),
('Sofa', 'Comfortable sofa', 499.99, 20, 5, 3),
('Chair', 'Dining chair', 89.99, 40, 15, 3),
('Pasta', 'Organic pasta', 2.99, 200, 50, 4),
('Coffee', 'Ground coffee', 9.99, 150, 30, 4),
('Tablet', 'Latest model tablet with high resolution display', 349.99, 50, 20, 1),
('Jacket', 'Stylish winter jacket', 89.99, 40, 15, 2),
('Lamp', 'Modern LED lamp with adjustable brightness', 29.99, 60, 25, 3);

-- Insert customers
INSERT INTO Customer (username, password, first_name, last_name, email, shipping_address) VALUES 
('bear', SHA2('password', 256), 'John', 'Doe', 'john@example.com', '123 Main St'),
('alice', SHA2('password', 256), 'Alice', 'Smith', 'alice@example.com', '456 Oak St'),
('charlie', SHA2('password', 256), 'Charlie', 'Brown', 'charlie@example.com', '789 Pine St');

-- Insert shopping carts for customers
INSERT INTO ShoppingCart (customer_id) VALUES 
(1), 
(2), 
(3);

-- Insert orders
INSERT INTO `Order` (customer_id, order_status, total_order_dollars) VALUES 
(1, 'Completed', 735.97),
(2, 'Pending', 39.99),
(3, 'Completed', 19.99);

-- Insert order items
INSERT INTO OrderItem (order_id, product_id, quantity, price) VALUES 
(1, 1, 1, 699.99),
(1, 4, 1, 39.99),
(1, 7, 5, 2.99),
(2, 3, 2, 19.99),
(3, 5, 1, 499.99);

-- Insert shopping cart items
-- Cart 1: 1 Smartphone, 1 T-shirt, 3 Pasta
INSERT INTO ShoppingCartItem (cart_id, product_id, quantity) VALUES 
(1, 1, 1),  -- Smartphone
(1, 3, 1),  -- T-shirt
(1, 7, 3);  -- Pasta

-- Cart 2: 2 Jeans, 1 Laptop
INSERT INTO ShoppingCartItem (cart_id, product_id, quantity) VALUES 
(2, 4, 2),  -- Jeans
(2, 2, 1);  -- Laptop

-- Cart 3: 1 Sofa, 2 Coffee
INSERT INTO ShoppingCartItem (cart_id, product_id, quantity) VALUES 
(3, 5, 1),  -- Sofa
(3, 8, 2);  -- Coffee

-- Example update after Order 1
UPDATE Product
SET stock_quantity = stock_quantity - (SELECT quantity FROM OrderItem WHERE order_id = 1 AND product_id = Product.product_id)
WHERE product_id IN (SELECT product_id FROM OrderItem WHERE order_id = 1);

-- Example update after Order 2
UPDATE Product
SET stock_quantity = stock_quantity - (SELECT quantity FROM OrderItem WHERE order_id = 2 AND product_id = Product.product_id)
WHERE product_id IN (SELECT product_id FROM OrderItem WHERE order_id = 2);

-- Example update after Order 3
UPDATE Product
SET stock_quantity = stock_quantity - (SELECT quantity FROM OrderItem WHERE order_id = 3 AND product_id = Product.product_id)
WHERE product_id IN (SELECT product_id FROM OrderItem WHERE order_id = 3);

-- Show the tables for validation
SELECT * FROM Employee;
SELECT * FROM Category;
SELECT * FROM Product;
SELECT * FROM Customer;
SELECT * FROM ShoppingCart;
SELECT * FROM `Order`;
SELECT * FROM OrderItem;
SELECT * FROM ShoppingCartItem;
SELECT * FROM ProductPriceHistory;

select*from Product;
UPDATE Product
SET image = 'Smartphone.jpg'
WHERE product_id = 1;
select*from Product;
UPDATE Product
SET image = 'Laptop.jpg'
WHERE product_id = 2;

select*from Product;

UPDATE Product
SET image = 'Lamp.jpg'
WHERE product_id = 11;
select*from Product;



