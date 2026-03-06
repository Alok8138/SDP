create database q_9_db;
use q_9_db;


CREATE TABLE customers (
    customer_id BIGINT PRIMARY KEY,
    name TEXT,
    email TEXT
);

CREATE TABLE products (
    product_id BIGINT PRIMARY KEY,
    product_name TEXT,
    price NUMERIC
);

CREATE TABLE orders (
    order_id BIGINT PRIMARY KEY,
    customer_id BIGINT,
    order_date DATE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);



CREATE TABLE order_items (
    order_item_id BIGINT PRIMARY KEY,
    order_id BIGINT,
    product_id BIGINT,
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);




INSERT INTO customers VALUES
(1,'Alice','alice@email.com'),
(2,'Bob','bob@email.com'),
(3,'Charlie','charlie@email.com');


INSERT INTO products VALUES
(101,'Laptop',800),
(102,'Mouse',20),
(103,'Keyboard',50);

INSERT INTO orders VALUES
(1001,1,'2025-01-10'),
(1002,1,'2025-02-05'),
(1003,2,'2025-03-01');


INSERT INTO order_items VALUES
(1,1001,101,1),
(2,1001,102,2),
(3,1002,103,1),
(4,1003,102,3);


SELECT JSON_ARRAYAGG(customer_json)
FROM (
    SELECT 
        JSON_OBJECT(
            'customer_id', c.customer_id,
            'name', c.name,
            'orders', JSON_ARRAYAGG(
                JSON_OBJECT(
                    'order_id', o.order_id,
                    'order_date', o.order_date,
                    'items', (
                        SELECT JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'product_id', p.product_id,
                                'product_name', p.product_name,
                                'price', p.price,
                                'quantity', oi.quantity
                            )
                        )
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.product_id
                        WHERE oi.order_id = o.order_id
                    )
                )
            )
        ) AS customer_json
    FROM customers c
    JOIN orders o ON c.customer_id = o.customer_id
    GROUP BY c.customer_id, c.name
) AS customer_data;