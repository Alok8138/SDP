create database q_4_db;

use q_4_db;


create table product_detail(
		product_id bigint primary key auto_increment,
        product_name varchar(256) not null,
        created_at datetime default current_timestamp
);

create table product_price_history(
	id bigint primary key auto_increment,
	product_id bigint,
    product_price decimal(10,2) not null,
    price_updated_at date not null,
    
    foreign key (product_id) references product_detail(product_id)
);



-- ALTER TABLE product_detail
-- MODIFY product_id BIGINT AUTO_INCREMENT;



INSERT INTO product_detail (product_name) VALUES
('Laptop'),
('Smartphone'),
('Headphones'),
('Monitor');



INSERT INTO product_price_history (product_id, product_price, price_updated_at) VALUES
-- Laptop prices
(1, 900.00, '2025-11-15'),
(1, 950.00, '2025-12-20'),
(1, 1000.00, '2026-01-25'),
(1, 1100.00, '2026-02-20'),

-- Smartphone prices
(2, 600.00, '2025-12-01'),
(2, 580.00, '2026-01-10'),
(2, 620.00, '2026-02-15'),

-- Headphones prices
(3, 150.00, '2025-12-10'),
(3, 140.00, '2026-01-18'),
(3, 160.00, '2026-02-25'),

-- Monitor prices
(4, 300.00, '2025-11-20'),
(4, 320.00, '2026-01-05'),
(4, 310.00, '2026-02-10');


INSERT INTO product_detail (product_name) VALUES
('TV');

INSERT INTO product_price_history (product_id, product_price, price_updated_at) VALUES (5, 310.00, '2024-02-10');


select * from product_detail;
select * from product_price_history;


-- with  price_changed_90 as (

-- 	select product_id from product_price_history where price_updated_at >= now() - interval 90 DAY group by product_id
-- )
-- select * from price_changed_90;

WITH price_analysis AS (
    SELECT
        pd.product_id,
        pd.product_name,
        ph.product_price AS current_price,
        ph.price_updated_at,

        LAG(ph.product_price) OVER(
            PARTITION BY ph.product_id
            ORDER BY ph.price_updated_at
        ) AS previous_price,

        LEAD(ph.product_price) OVER(
            PARTITION BY ph.product_id
            ORDER BY ph.price_updated_at
        ) AS next_price

    FROM product_price_history ph
    JOIN product_detail pd
        ON pd.product_id = ph.product_id
)

SELECT
    product_id,
    product_name,
    current_price,
    previous_price,
    next_price,
    price_updated_at,
    ROUND(((current_price - previous_price)/previous_price)*100,2) AS percent_change
FROM price_analysis
WHERE price_updated_at >= CURRENT_DATE - INTERVAL 90 DAY;



