
-- Create stored procedures
DELIMITER //

-- Employee Creation Procedure
CREATE PROCEDURE create_employee(
    IN p_username VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_password VARCHAR(255)
)
BEGIN
    INSERT INTO Employee (username, email, password, requires_password_reset) 
    VALUES (p_username, p_email, p_password, TRUE);  -- TRUE for password reset
END //

-- Category Insertion Procedure
CREATE PROCEDURE insert_category(
    IN p_name VARCHAR(100),
    IN p_description TEXT
)
BEGIN
    INSERT INTO Category (name, description) 
    VALUES (p_name, p_description);
END //

-- Product Insertion Procedure
CREATE PROCEDURE insert_product(
    IN p_name VARCHAR(100),
    IN p_description TEXT,
    IN p_price DECIMAL(10, 2),
    IN p_stock_quantity INT,
    IN p_advising_threshold INT,
    IN p_category_id INT
)
BEGIN
    INSERT INTO Product (name, description, price, stock_quantity, advising_threshold, category_id) 
    VALUES (p_name, p_description, p_price, p_stock_quantity, p_advising_threshold, p_category_id);
END //

-- Product Price Update Procedure
CREATE PROCEDURE update_product_price(
    IN p_product_id INT,
    IN p_new_price DECIMAL(10, 2)
)
BEGIN
    UPDATE Product 
    SET price = p_new_price 
    WHERE product_id = p_product_id;
END //

-- Product Restocking Procedure
CREATE PROCEDURE restock_product(
    IN p_product_id INT,
    IN p_quantity INT
)
BEGIN
    UPDATE Product 
    SET stock_quantity = stock_quantity + p_quantity 
    WHERE product_id = p_product_id;
END //

-- Order Insertion Procedure
CREATE PROCEDURE insert_order(
    IN p_customer_id INT,
    IN p_order_status VARCHAR(50),
    IN p_total_order_dollars DECIMAL(10, 2)
)
BEGIN
    INSERT INTO `Order` (customer_id, order_status, total_order_dollars) 
    VALUES (p_customer_id, p_order_status, p_total_order_dollars);
END //

-- Order Item Insertion Procedure
CREATE PROCEDURE insert_order_item(
    IN p_order_id INT,
    IN p_product_id INT,
    IN p_quantity INT,
    IN p_price DECIMAL(10, 2)
)
BEGIN
    INSERT INTO OrderItem (order_id, product_id, quantity, price) 
    VALUES (p_order_id, p_product_id, p_quantity, p_price);
END //

-- Customer Creation Procedure
CREATE PROCEDURE create_customer(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_first_name VARCHAR(50),
    IN p_last_name VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_shipping_address TEXT
)
BEGIN
    INSERT INTO Customer (username, password, first_name, last_name, email, shipping_address) 
    VALUES (p_username, p_password, p_first_name, p_last_name, p_email, p_shipping_address);
END //

-- Add Product to Cart Procedure
CREATE PROCEDURE add_product_to_cart(
    IN p_cart_id INT,
    IN p_product_id INT,
    IN p_quantity INT
)
BEGIN
    INSERT INTO ShoppingCartItem (cart_id, product_id, quantity) 
    VALUES (p_cart_id, p_product_id, p_quantity)
    ON DUPLICATE KEY UPDATE 
        quantity = quantity + p_quantity;  -- Update quantity if item exists
END //

-- Update Cart Item Procedure
CREATE PROCEDURE update_cart_item(
    IN p_cart_id INT,
    IN p_product_id INT,
    IN p_new_quantity INT
)
BEGIN
    UPDATE ShoppingCartItem 
    SET quantity = p_new_quantity 
    WHERE cart_id = p_cart_id AND product_id = p_product_id;
END //

-- Checkout Cart Procedure
CREATE PROCEDURE checkout_cart(
    IN p_cart_id INT
)
BEGIN
    DECLARE v_customer_id INT;
    DECLARE v_total DECIMAL(10, 2) DEFAULT 0;
    DECLARE v_order_id INT;

    -- Get the customer ID from the shopping cart
    SELECT customer_id INTO v_customer_id 
    FROM ShoppingCart 
    WHERE cart_id = p_cart_id;

    -- Calculate the total for the items in the cart
    SELECT SUM(p.price * sci.quantity) INTO v_total
    FROM ShoppingCartItem sci
    JOIN Product p ON sci.product_id = p.product_id
    WHERE sci.cart_id = p_cart_id;

    -- Insert the order into the Order table
    INSERT INTO `Order` (customer_id, order_status, total_order_dollars) 
    VALUES (v_customer_id, 'Completed', v_total);

    -- Get the last inserted order ID
    SET v_order_id = LAST_INSERT_ID();

    -- Insert each item from the shopping cart into the OrderItem table
    INSERT INTO OrderItem (order_id, product_id, quantity, price)
    SELECT v_order_id, sci.product_id, sci.quantity, p.price
    FROM ShoppingCartItem sci
    JOIN Product p ON sci.product_id = p.product_id
    WHERE sci.cart_id = p_cart_id;

    -- Update the product stock quantities after placing the order
    UPDATE Product p
    JOIN ShoppingCartItem sci ON p.product_id = sci.product_id
    SET p.stock_quantity = p.stock_quantity - sci.quantity
    WHERE sci.cart_id = p_cart_id;

    -- Optionally, clear the shopping cart after checkout
    DELETE FROM ShoppingCartItem WHERE cart_id = p_cart_id;
END //

-- View Orders Procedure
CREATE PROCEDURE view_orders(
    IN p_customer_id INT
)
BEGIN
    SELECT o.order_id, o.order_date, o.order_status, o.total_order_dollars
    FROM `Order` o
    WHERE o.customer_id = p_customer_id
    ORDER BY o.order_date DESC;
END //

-- Login Procedure for Employee
CREATE PROCEDURE login_employee(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    OUT p_success BOOLEAN,
    OUT p_requires_reset BOOLEAN
)
BEGIN
    -- Initialize output variables
    SET p_success = FALSE;
    SET p_requires_reset = FALSE;

    -- Check if the employee exists with the given credentials
    SELECT COUNT(*), MAX(requires_password_reset) 
    INTO @login_count, p_requires_reset
    FROM Employee
    WHERE username = p_username AND password = p_password
    GROUP BY username;  -- Group by username to comply with ONLY_FULL_GROUP_BY

    -- Set p_success based on whether the employee was found
    SET p_success = (@login_count > 0);
END //

-- Login Procedure for Customer
CREATE PROCEDURE login_customer(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    OUT p_success BOOLEAN,
    OUT p_requires_reset BOOLEAN
)
BEGIN
    -- Initialize output variables
    SET p_success = FALSE;
    SET p_requires_reset = FALSE;

    -- Check if the customer exists with the given credentials
    SELECT COUNT(*), MAX(requires_password_reset) 
    INTO @login_count, p_requires_reset
    FROM Customer
    WHERE username = p_username AND password = p_password
    GROUP BY username;  -- Group by username to comply with ONLY_FULL_GROUP_BY

    -- Set p_success based on whether the customer was found
    SET p_success = (@login_count > 0);
END //

DELIMITER ;

-- Product Update Triggers
DROP TRIGGER IF EXISTS after_product_insert;
DROP TRIGGER IF EXISTS after_product_update;
DROP TRIGGER IF EXISTS before_product_update;
DROP TRIGGER IF EXISTS before_product_delete;

DELIMITER //

-- After Insert Trigger
CREATE TRIGGER after_product_insert
AFTER INSERT ON Product
FOR EACH ROW
BEGIN
    INSERT INTO ProductHistory (product_id, action) 
    VALUES (NEW.product_id, 'Inserted');
END //

-- After Update Trigger
CREATE TRIGGER after_product_update
AFTER UPDATE ON Product
FOR EACH ROW
BEGIN
    INSERT INTO ProductHistory (product_id, action) 
    VALUES (NEW.product_id, 'Updated');
END //

-- Before Update Trigger
CREATE TRIGGER before_product_update
BEFORE UPDATE ON Product
FOR EACH ROW
BEGIN
    IF NEW.product_id <> OLD.product_id THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'The product ID is not allowed to be changed';
    END IF;
END //

-- Before Delete Trigger
CREATE TRIGGER before_product_delete
BEFORE DELETE ON Product
FOR EACH ROW
BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Product deletion is not allowed';
END //

DELIMITER ;

DELIMITER //

CREATE TRIGGER after_product_price_update
AFTER UPDATE ON Product
FOR EACH ROW
BEGIN
    -- Check if the price has changed
    IF OLD.price <> NEW.price THEN
        INSERT INTO ProductPriceHistory (product_id, old_price, new_price)
        VALUES (NEW.product_id, OLD.price, NEW.price);
    END IF;
END //

DELIMITER ;

DELIMITER $$


DROP TRIGGER IF EXISTS product_price_history;
DELIMITER $$

CREATE TRIGGER product_price_history
AFTER UPDATE ON Product
FOR EACH ROW
BEGIN
    IF OLD.price <> NEW.price THEN
        INSERT INTO ProductPriceHistory (product_id, old_price, new_price, change_date)
        VALUES (NEW.product_id, OLD.price, NEW.price, NOW());
    END IF;
END $$

ALTER TABLE ProductPriceHistory
ADD COLUMN change_date DATETIME NOT NULL;
DELIMITER ;









