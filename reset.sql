-- Disable foreign key checks to avoid constraint errors during table drops
SET FOREIGN_KEY_CHECKS = 0;

-- Drop dependent tables in the correct order
DROP TABLE IF EXISTS ShoppingCartItem;
DROP TABLE IF EXISTS ShoppingCart;
DROP TABLE IF EXISTS ProductHistory;
DROP TABLE IF EXISTS OrderItem;
DROP TABLE IF EXISTS `Order`;
DROP TABLE IF EXISTS Product;
DROP TABLE IF EXISTS Category;
DROP TABLE IF EXISTS Customer;
DROP TABLE IF EXISTS Employee;
DROP TABLE IF EXISTS ProductPriceHistory;

-- Drop existing stored procedures
DROP PROCEDURE IF EXISTS create_employee;
DROP PROCEDURE IF EXISTS insert_category;
DROP PROCEDURE IF EXISTS insert_product;
DROP PROCEDURE IF EXISTS update_product_price;
DROP PROCEDURE IF EXISTS restock_product;
DROP PROCEDURE IF EXISTS insert_order;
DROP PROCEDURE IF EXISTS insert_order_item;
DROP PROCEDURE IF EXISTS checkout_cart;
DROP PROCEDURE IF EXISTS view_orders;
DROP PROCEDURE IF EXISTS login_employee;
DROP PROCEDURE IF EXISTS login_customer;
DROP PROCEDURE IF EXISTS create_customer;
DROP PROCEDURE IF EXISTS add_product_to_cart;
DROP PROCEDURE IF EXISTS update_cart_item;

-- Enable foreign key checks again after dropping the tables
SET FOREIGN_KEY_CHECKS = 1;

