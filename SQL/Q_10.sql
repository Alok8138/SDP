create database q_10_db;
use q_10_db;


CREATE TABLE system1_inventory (
    product_id BIGINT,
    product_name TEXT,
    stock INT
);
CREATE TABLE system2_inventory (
    product_id BIGINT,
    product_name TEXT,
    stock INT
);


INSERT INTO system1_inventory VALUES
(101,'Laptop',10),
(102,'Mouse',50),
(103,'Keyboard',20),
(105,'Headphones',30);


INSERT INTO system2_inventory VALUES
(101,'Laptop',10),
(102,'Mouse',45),
(104,'Monitor',15),
(105,'Headphones',30);


SELECT
    COALESCE(s1.product_id, s2.product_id) AS product_id,
    COALESCE(s1.product_name, s2.product_name) AS product_name,
    s1.stock AS system1_stock,
    s2.stock AS system2_stock,

    CASE
        WHEN s1.product_id IS NULL THEN 'MISSING_IN_SYSTEM1'
        WHEN s2.product_id IS NULL THEN 'MISSING_IN_SYSTEM2'
        WHEN s1.stock = s2.stock THEN 'MATCH'
        ELSE 'STOCK_MISMATCH'
    END AS status

FROM system1_inventory s1
FULL OUTER JOIN system2_inventory s2
ON s1.product_id = s2.product_id
ORDER BY product_id;


