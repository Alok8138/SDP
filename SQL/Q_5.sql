create database q_5_db;
use q_5_db;

create table orders(
	id bigint primary key auto_increment,
	order_id bigint not null
);

create table order_iems(
	item_id bigint primary key auto_increment,
    order_id bigint,
    product_name varchar(128) not null,
    product_quantity int not null,
    foreign key (order_id) references orders(id)
);

INSERT INTO orders (order_id) VALUES
(1001),
(1002),
(1003),
(1004),
(1005),
(1006),
(1007),
(1008),
(1009),
(1010),
(1011),
(1012);



INSERT INTO order_iems (order_id, product_name, product_quantity) VALUES
(1,'Laptop',1),
(1,'Mouse',1),

(2,'Laptop',1),
(2,'Mouse',1),

(3,'Laptop',1),
(3,'Mouse',1),

(4,'Laptop',1),
(4,'Mouse',1),

(5,'Laptop',1),
(5,'Mouse',1),

(6,'Laptop',1),
(6,'Mouse',1),

(7,'Laptop',1),
(7,'Mouse',1),

(8,'Laptop',1),
(8,'Mouse',1),

(9,'Laptop',1),
(9,'Mouse',1),

(10,'Laptop',1),
(10,'Mouse',1),

(11,'Laptop',1),
(11,'Mouse',1),

(12,'Laptop',1),
(12,'Mouse',1),

-- Other combinations
(1,'Keyboard',1),
(2,'Keyboard',1),
(3,'Keyboard',1),

(4,'Headphones',1),
(5,'Headphones',1),
(6,'Headphones',1);


select * from order_iems;



WITH product_pairs AS (
    SELECT 
        o1.product_name AS product_1,
        o2.product_name AS product_2,
        o1.order_id
    FROM order_iems o1
    JOIN order_iems o2 
        ON o1.order_id = o2.order_id
       AND o1.product_name < o2.product_name
),

pair_counts AS (
    SELECT 
        product_1,
        product_2,
        COUNT(DISTINCT order_id) AS times_bought
    FROM product_pairs
    GROUP BY product_1, product_2
)

SELECT 
    product_1,
    product_2,
    times_bought,
    ROUND(
        times_bought * 100.0 / (SELECT COUNT(*) FROM orders),
        2
    ) AS percentage_of_orders
FROM pair_counts
WHERE times_bought > 10;


