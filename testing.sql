-- 1. List all Employees
SELECT * FROM Employee;

-- 2. List all Customers
SELECT * FROM Customer;

-- 3. List all Categories
SELECT * FROM Category;

-- 4. List all Products
SELECT * FROM Product;

-- 5. List Product Price Change History (Ordered by Time)
SELECT * FROM ProductHistory ORDER BY action_time;

-- 6. List all Shopping Carts
SELECT * FROM ShoppingCart;

-- 7. List all Shopping Cart Items
SELECT * FROM ShoppingCartItem;

-- 8. List all Orders
SELECT * FROM `Order`;

-- 9. List Order Items for Each Order
SELECT oi.order_item_id, oi.order_id, oi.product_id, oi.quantity, oi.price, p.name AS product_name
FROM OrderItem oi
JOIN Product p ON oi.product_id = p.product_id
ORDER BY oi.order_id;

-- 10. List Historic Prices for a Specific Product (Example for product_id = 1)
SELECT * FROM ProductHistory
WHERE product_id = 1
ORDER BY action_time;

-- 11. List Highest and Lowest Price within a Time Period (e.g., Jan 1, 2023 to Jun 1, 2023)
SELECT product_id,
       MAX(price) AS highest_price,
       MIN(price) AS lowest_price
FROM ProductHistory
WHERE action_time BETWEEN '2023-01-01' AND '2023-06-01'
GROUP BY product_id;

-- 12. List Quantities Sold for Each Product within a Specific Time Frame (e.g., Jan 1, 2023 to Jun 30, 2023)
SELECT oi.product_id, SUM(oi.quantity) AS total_sold
FROM OrderItem oi
JOIN `Order` o ON oi.order_id = o.order_id
WHERE o.order_date BETWEEN '2023-01-01' AND '2023-06-30'
GROUP BY oi.product_id;

-- 13. List Products Below Restocking Threshold and Quantity Needed to Reach the Threshold
SELECT p.product_id, p.name, p.stock_quantity, p.advising_threshold, 
       (p.advising_threshold - p.stock_quantity) AS quantity_needed
FROM Product p
WHERE p.stock_quantity < p.advising_threshold;

-- Update the price of a product (e.g., product with ID = 1)
UPDATE Product SET price = 799.99 WHERE product_id = 1;

-- Check if the price history has been updated
SELECT * FROM ProductPriceHistory;


-- Insert 3 shopping carts (one for each customer)
INSERT INTO ShoppingCart (customer_id)
VALUES
(1), -- Cart for customer with customer_id = 1 (John Doe)
(2), -- Cart for customer with customer_id = 2 (Alice Smith)
(3); -- Cart for customer with customer_id = 3 (Charlie Brown)


-- Add items to John Doe's shopping cart (cart_id = 1)
INSERT INTO ShoppingCartItem (cart_id, product_id, quantity)
VALUES
(1, 1, 1),  -- 1 Smartphone
(1, 3, 2);  -- 2 T-shirts

-- Add items to Alice Smith's shopping cart (cart_id = 2)
INSERT INTO ShoppingCartItem (cart_id, product_id, quantity)
VALUES
(2, 2, 1),  -- 1 Laptop
(2, 4, 3);  -- 3 Jeans


SELECT * FROM ShoppingCart;
SELECT * FROM ShoppingCartItem;
SELECT sci.cart_item_id, sci.cart_id, sci.product_id, p.name AS product_name, sci.quantity
FROM ShoppingCartItem sci
JOIN Product p ON sci.product_id = p.product_id
WHERE sci.cart_id = 2
ORDER BY sci.cart_id;

#order
SELECT 
    o.order_id AS order_number,
    o.order_date AS time,
    CONCAT(c.first_name, ' ', c.last_name) AS customer,
    GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ' x $', oi.price, ')') ORDER BY oi.order_item_id SEPARATOR ', ') AS order_items,
    SUM(oi.quantity * oi.price) AS total
FROM 
    `Order` o
JOIN 
    Customer c ON o.customer_id = c.customer_id
JOIN 
    OrderItem oi ON o.order_id = oi.order_id
JOIN 
    Product p ON oi.product_id = p.product_id
GROUP BY 
    o.order_id, o.order_date, c.first_name, c.last_name
ORDER BY 
    o.order_date DESC
LIMIT 15;

# test how to add new product by calling insert function
SELECT * FROM Product;
CALL insert_product('New hasham Laptop', 'Latest model with advanced features', 100.00, 15, 5, 1);  
SELECT * FROM Product; # check whether new product is added or not
# test to update any existing product
SELECT * FROM Product;
CALL update_product_price(10, 150.00);  -- Use an existing product_id
SELECT * FROM Product;
# test to check the producy history is recorded or not
SELECT * FROM ProductHistory;  


#Function for customers
#1) Register as a new customer.
SELECT * FROM Customer;
CALL create_customer('Hasham_Khanborther', SHA2('password', 256), 'New', 'User', 'newddduser@example.com', '7119 Elm St');
SELECT * FROM Customer;


#2) Browse categories and products.
SELECT * FROM Category;
#To browse products in a specific category (for example, category ID 1):
CALL browse_products_by_category(1);
SELECT * FROM Product;

#3) Add products to the shopping cart.
SELECT * FROM ShoppingCartItem WHERE cart_id = 3;
CALL add_product_to_cart(1, 3, 2);  -- Example: Adding 2 of product_id 1 to cart_id 1
CALL add_product_to_cart(1, 3, 6); 
CALL add_product_to_cart(1, 3, 10); 
SELECT * FROM ShoppingCartItem WHERE cart_id = 3;


#4) View and update the shopping cart.
SELECT * FROM ShoppingCart;
SELECT * FROM ShoppingCart WHERE customer_id = 2;  -- Check cart for the customer
SELECT * FROM ShoppingCartItem;
CALL update_cart_item(4, 1, 20);  -- Example: Updating quantity of product_id 1 in cart_id 1 to 3
CALL update_cart_item(5, 2, 16);
CALL update_cart_item(6, 3, 10);
SELECT * FROM ShoppingCartItem WHERE cart_id = 1;
SELECT * FROM ShoppingCartItem;
#5) Checkout.
SELECT * FROM ShoppingCartItem WHERE cart_id = 1;


CALL checkout_cart(2);  -- Replace with your cart ID


#6) View previous orders.
CALL view_orders(2);

# Function for employees
#1)	Insert new category.
select*from Category;
CALL insert_category('copies', 'Various genres of books');
select*from Category;

#2)	Insert new product.
select*from Product;
CALL insert_product('hasham Novel', 'Fictional novel', 115.99, 100, 20, 1);  -- Adjust category_id as needed
select*from Product;

#3)	Update the product information, such as price changes or restocking.
select*from Product;
CALL update_product_price(4, 12111.99);  -- Replace 1 with the actual product_id
select*from Product;

CALL restock_product(1, 5000);  -- Replace 1 with the actual product_id
select*from Product;



#Quantities Sold for Each Product

SELECT product_id, SUM(quantity) AS Total_Sold 
FROM OrderItem 
JOIN `Order` ON OrderItem.order_id = `Order`.order_id 
WHERE `Order`.order_date BETWEEN '2023-01-01' AND '2024-10-17' 
GROUP BY product_id;


#Products Below Restocking Threshold
SELECT name, stock_quantity, advising_threshold 
FROM Product;
SELECT product_id, name, stock_quantity, advising_threshold 
FROM Product;

#testing 
CALL insert_category('New Category', 'Description of new category');
CALL insert_product('New Product', 'Description of new product', 29.99, 100, 20, 1);
CALL update_product_price(1, 19.99);  -- Update price of product with ID 1
CALL restock_product(1, 50);  -- Restock product with ID 1 by 50 units

UPDATE Product 
SET stock_quantity = 30  -- New stock quantity
WHERE product_id = 3;  -- Replace with the product_id needing restock



##  restocking data threshold

SELECT name, stock_quantity, advising_threshold, 
       (advising_threshold - stock_quantity) AS Quantity_Needed 
FROM Product 
WHERE stock_quantity < advising_threshold;

select*from Employee;
select*from Customer;
select*from Category;
select*from Product;
select*from ProductHistory;
select*from ShoppingCart;
select*from ShoppingCartItem;
select*from `Order`;


CALL create_employee('nehaswuserhasham', 'newusdder@example.com', SHA2('tempPassword', 256));



# Example of Calling Employee Login
CALL login_employee('mstone', SHA2('inputPassword', 256), @loginSuccess, @requiresReset);
SELECT @loginSuccess AS success, @requiresReset AS reset_required;

# Example of Calling Customer Login
CALL login_customer('bear', SHA2('inputPassword', 256), @loginSuccess, @requiresReset);
SELECT @loginSuccess AS success, @requiresReset AS reset_required;

-- Assume 'orders' is the name of the table storing orders
-- Assume 'customer_id' is a foreign key in the 'orders' table referring to the customer

SELECT * FROM Customer;


