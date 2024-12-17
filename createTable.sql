-- Create Employee table
select*from Employee;
CREATE TABLE Employee (
    employee_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
ALTER TABLE Employee ADD password_reset BOOLEAN DEFAULT FALSE;

select*from Employee;
-- Create Category table
CREATE TABLE Category (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Create Product table
CREATE TABLE Product (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL,
    advising_threshold INT NOT NULL,
    category_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id)
        REFERENCES Category(category_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
select*from Customer;

describe Product;
describe Category;
-- Create Customer table
CREATE TABLE Customer (
    customer_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    shipping_address TEXT NOT NULL
);

select*from Product;
-- Create ShoppingCart table
CREATE TABLE ShoppingCart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id)
        REFERENCES Customer(customer_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Create Order table
CREATE TABLE `Order` (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    order_status VARCHAR(50) DEFAULT 'Pending',
    total_order_dollars DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (customer_id)
        REFERENCES Customer(customer_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

DESCRIBE Product;
-- Create OrderItem table
select*from OrderItem;
CREATE TABLE OrderItem (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT CHECK (quantity > 0),
    price DECIMAL(10, 2) NOT NULL CHECK (price >= 0),
    FOREIGN KEY (order_id)
        REFERENCES `Order`(order_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (product_id)
        REFERENCES Product(product_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
ALTER TABLE Product
ADD COLUMN image VARCHAR(255);


-- Create ProductHistory table
CREATE TABLE ProductHistory (
    history_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    action VARCHAR(50),
    action_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id)
        REFERENCES Product(product_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Create ShoppingCartItem table
CREATE TABLE ShoppingCartItem (
    cart_item_id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT,
    product_id INT,
    quantity INT CHECK (quantity > 0),
    FOREIGN KEY (cart_id)
        REFERENCES ShoppingCart(cart_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (product_id)
        REFERENCES Product(product_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Create ProductPriceHistory table
CREATE TABLE ProductPriceHistory (
    history_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    old_price DECIMAL(10, 2),
    new_price DECIMAL(10, 2),
    change_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id)
        REFERENCES Product(product_id)
        ON DELETE CASCADE
);


-- Create Orders table

select*from `Orders`;
CREATE TABLE `Orders` (
    `order_id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT NOT NULL,
    `total_price` DECIMAL(10, 2) NOT NULL,
    `order_date` DATETIME NOT NULL,
    FOREIGN KEY (`customer_id`) REFERENCES `Customer`(`customer_id`)
);

-- Create OrderDetails table
CREATE TABLE `OrderDetails` (
    `order_detail_id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `Orders`(`order_id`),
    FOREIGN KEY (`product_id`) REFERENCES `Product`(`product_id`)
);

ALTER TABLE Orders
ADD COLUMN order_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE Orders
MODIFY COLUMN order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE Orders
ADD COLUMN shipping_address VARCHAR(255) NOT NULL,
ADD COLUMN shipping_phone VARCHAR(20) NOT NULL,
ADD COLUMN payment_method VARCHAR(50) NOT NULL;


select*from Product;

UPDATE Product
SET image = '5.jpg'
WHERE product_id = 12;

select*from Category;
CREATE TABLE `Categories` (
    `category_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT
);
ALTER TABLE `Categories` ADD COLUMN image_path VARCHAR(255) NULL;
DELETE FROM Product WHERE product_id = 12;

INSERT INTO `Categories` (`name`, `description`) 
VALUES 
('Electronics', 'Devices like phones, laptops, and gadgets'),
('Clothing', 'Apparel and fashion items'),
('Books', 'Printed and digital books'),
('Home Appliances', 'Items for home use like washing machines, fridges, etc.');


-- Modify change_time to have a default value of CURRENT_TIMESTAMP
ALTER TABLE ProductPriceHistory
MODIFY change_time DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Add foreign key constraint for product_id to reference Product(product_id)
ALTER TABLE ProductPriceHistory
ADD CONSTRAINT fk_product_id
FOREIGN KEY (product_id) REFERENCES Product(product_id)
ON DELETE CASCADE;
DESCRIBE ProductPriceHistory;

SELECT * FROM Product ORDER BY price DESC;
select*from Employee;
select*from Product;
ALTER TABLE Product ADD COLUMN change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE ProductPriceHistory ADD COLUMN change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

SELECT * FROM OrderItem WHERE order_id = 21;
SELECT * FROM OrderDetails WHERE order_id = 24;


CREATE TABLE StockHistory (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    old_stock_quantity INT,
    new_stock_quantity INT,
    change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    change_reason VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (product_id) REFERENCES Product(product_id)
);
ALTER TABLE ProductPriceHistory
ADD COLUMN change_reason VARCHAR(255) DEFAULT NULL;





###############
-- CREATE TABLE Sales (
-- sale_id INT AUTO_INCREMENT PRIMARY KEY,
-- product_id INT,
-- quantity INT,
-- total_amount DECIMAL(10, 2),
-- sale_date DATETIME
-- );
-- DESCRIBE Sales;
-- select*from Sales;
-- DESCRIBE Sales;
-- SELECT * FROM Sales LIMIT 10;


-- INSERT INTO Sales (product_id, quantity, total_amount, sale_date) 
-- VALUES 
-- (1, 5, 100.00, '2024-12-01 10:00:00'),
-- (2, 3, 75.00, '2024-12-01 11:00:00'),
-- (1, 2, 40.00, '2024-12-02 12:00:00');

-- SELECT sale_date, SUM(total_amount) as daily_sales
-- FROM Sales
-- GROUP BY sale_date
-- ORDER BY sale_date DESC;


-- CREATE TABLE Sales (
--     sale_id INT PRIMARY KEY AUTO_INCREMENT,
--     sale_date DATETIME DEFAULT CURRENT_TIMESTAMP,
--     total_sales DECIMAL(10, 2) NOT NULL
-- );
-- CREATE TABLE SalesDetail (
--     sales_detail_id INT PRIMARY KEY AUTO_INCREMENT,
--     sale_id INT,
--     product_id INT,
--     quantity INT CHECK (quantity > 0),
--     total_sales DECIMAL(10, 2) NOT NULL,
--     FOREIGN KEY (sale_id) REFERENCES Sales(sale_id)
--     ON DELETE CASCADE
--     ON UPDATE CASCADE,
--     FOREIGN KEY (product_id) REFERENCES Product(product_id)
--     ON DELETE CASCADE
--     ON UPDATE CASCADE
-- );
-- INSERT INTO Sales (sale_date, total_sales)
-- VALUES (NOW(), 500.00);

-- INSERT INTO SalesDetail (sale_id, product_id, quantity, total_sales)
-- VALUES (LAST_INSERT_ID(), 1, 3, 150.00);  -- 3 units of product with product_id = 1 sold for 150.00
-- INSERT INTO SalesDetail (sale_id, product_id, quantity, total_sales)
-- VALUES (LAST_INSERT_ID(), 2, 2, 350.00);  -- 2 units of product with product_id = 2 sold for 350.00

-- SELECT 
--     DATE(sale_date) AS sale_date, 
--     SUM(total_sales) AS daily_sales
-- FROM Sales
-- GROUP BY DATE(sale_date)
-- ORDER BY sale_date ASC;
