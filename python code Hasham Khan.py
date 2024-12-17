import mysql.connector                  
import pandas as pd                     
import matplotlib.pyplot as plt         

#  Connect to the MySQL database
db_connection = mysql.connector.connect(
    host="classdb.it.mtu.edu",  
    user="hashamk",       
    password="Huawei@786",  
    database="hashamk"  
)

# Function to execute a query and return data as a pandas DataFrame
def fetch_data(query):
    df = pd.read_sql(query, db_connection)
    return df

# 1. Product Price Distribution (Bar Chart)
def plot_product_prices():
    query = "SELECT name, price FROM Product"
    df = fetch_data(query)
    
    # Plotting product prices as a bar chart
    plt.figure(figsize=(10, 6))
    plt.bar(df['name'], df['price'], color='skyblue')
    plt.xlabel('Product Name')
    plt.ylabel('Price ($)')
    plt.title('Product Price Distribution')
    plt.xticks(rotation=90)
    plt.tight_layout()
    plt.show()

# 2. Product Stock Quantity Distribution (Bar Chart)
def plot_product_stock():
    query = "SELECT name, stock_quantity FROM Product"
    df = fetch_data(query)

    # Plotting stock quantities as a bar chart
    plt.figure(figsize=(10, 6))
    plt.bar(df['name'], df['stock_quantity'], color='lightgreen')
    plt.xlabel('Product Name')
    plt.ylabel('Stock Quantity')
    plt.title('Product Stock Quantity')
    plt.xticks(rotation=90)
    plt.tight_layout()
    plt.show()

# 3. Sales by Category (Pie Chart)
def plot_sales_by_category():
    query = """
    SELECT c.name AS category_name, SUM(oi.quantity * oi.price) AS total_sales
    FROM OrderItem oi
    JOIN Product p ON oi.product_id = p.product_id
    JOIN Category c ON p.category_id = c.category_id
    GROUP BY c.name
    """
    df = fetch_data(query)

    # Plotting sales by category as a pie chart
    plt.figure(figsize=(8, 8))
    plt.pie(df['total_sales'], labels=df['category_name'], autopct='%1.1f%%', startangle=140, colors=plt.cm.Paired.colors)
    plt.title('Sales Distribution by Category')
    plt.axis('equal')
    plt.show()

# 4. Total Orders per Customer (Bar Chart)
def plot_orders_per_customer():
    query = """
    SELECT cu.username, COUNT(o.order_id) AS total_orders
    FROM Orders o
    JOIN Customer cu ON o.customer_id = cu.customer_id
    GROUP BY cu.username
    ORDER BY total_orders DESC
    LIMIT 10
    """
    df = fetch_data(query)

    # Plotting total orders per customer as a bar chart
    plt.figure(figsize=(10, 6))
    plt.bar(df['username'], df['total_orders'], color='salmon')
    plt.xlabel('Customer Username')
    plt.ylabel('Total Orders')
    plt.title('Top 10 Customers by Order Count')
    plt.xticks(rotation=45)
    plt.tight_layout()
    plt.show()

# Example of running the functions to generate visualizations
plot_product_prices()          # Bar chart of product prices
plot_product_stock()           # Bar chart of product stock
plot_sales_by_category()       # Pie chart of sales by category
plot_orders_per_customer()    # Bar chart of orders by customer
