create database q_6_db;
use q_6_db;



CREATE TABLE categories (
    category_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL
);


CREATE TABLE products (
    product_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(200) NOT NULL,
    category_id BIGINT,
    
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE orders (
    order_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL
);



CREATE TABLE order_items (
    item_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT,
    product_id BIGINT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);








SELECT 
    c.category_name,
    CONCAT('Q', QUARTER(o.order_date), '-', YEAR(o.order_date)) AS quarter,

    COUNT(DISTINCT CASE WHEN o.status = 'COMPLETED' THEN o.order_id END) AS completed_orders,
    
    SUM(CASE 
        WHEN o.status = 'COMPLETED' 
        THEN oi.quantity * oi.price 
        ELSE 0 
    END) AS completed_amount,

    COUNT(DISTINCT CASE WHEN o.status = 'PENDING' THEN o.order_id END) AS pending_orders,
    
    SUM(CASE 
        WHEN o.status = 'PENDING' 
        THEN oi.quantity * oi.price 
        ELSE 0 
    END) AS pending_amount,

    COUNT(DISTINCT CASE WHEN o.status = 'CANCELLED' THEN o.order_id END) AS cancelled_orders,
    
    SUM(CASE 
        WHEN o.status = 'CANCELLED' 
        THEN oi.quantity * oi.price 
        ELSE 0 
    END) AS cancelled_amount

FROM orders o
JOIN order_items oi ON o.order_id = oi.order_id
JOIN products p ON oi.product_id = p.product_id
JOIN categories c ON p.category_id = c.category_id

GROUP BY 
    c.category_name,
    YEAR(o.order_date),
    QUARTER(o.order_date)

ORDER BY 
    c.category_name,
    quarter;