-- create database q_7_db;

-- use q_7_db;


create table customers(
	customer_id bigint primary key,
    name varchar(128) not null
);


create table products(
	pid bigint not null,
    cid bigint not null,
    customer_id bigint ,
    foreign key (customer_id) references customers(customer_id)
);




INSERT INTO customers VALUES
(1,'Alice'),
(2,'Bob'),
(3,'Charlie');



INSERT INTO products VALUES
(101,1,1),  -- Alice bought Electronics
(102,2,1),  -- Alice bought Clothing
(103,3,1),  -- Alice bought Books

(104,1,2),  -- Bob bought Electronics
(105,2,2),  -- Bob bought Clothing

(106,1,3),  -- Charlie bought Electronics
(107,3,3);  -- Charlie bought Books




SELECT c.customer_id, c.name
FROM customers c

WHERE NOT EXISTS (
    
    SELECT p.cid -- selected all cid 1,2,3 
    FROM products p
    GROUP BY p.cid
    
    HAVING NOT EXISTS ( -- here having sort those catgory for which category exist in p2
        SELECT 1
        FROM products p2
        WHERE p2.customer_id = c.customer_id
        AND p2.cid = p.cid
    )
);























