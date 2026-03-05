create database Q_2;

use Q_2;

-- product table 

-- create table products(
-- 	product_id bigint primary key auto_increment,
--     category_id int not null,
--     product_name varchar(64) not null,
--     revenue bigint not null
-- );


-- insert data 

-- INSERT INTO products (category_id, product_name, revenue) VALUES
-- (5, 'Running Shoes', 4000),
-- (1, 'Laptop Pro 15', 85000),
-- (12, 'Notebook Pack', 300),
-- (3, 'Smartphone Ultra', 95000),
-- (8, 'Air Cooler', 9000),
-- (2, 'Mechanical Keyboard', 4500),
-- (14, 'Bed Sheet Set', 2200),
-- (7, 'LED TV 55 inch', 45000),
-- (10, 'Coffee Maker', 4500),
-- (6, 'Microwave Oven', 12000),
-- (11, 'Travel Trolley', 6500),
-- (4, 'Office Chair', 7000),
-- (9, 'Protein Powder 1kg', 2200),
-- (15, 'Face Wash', 250),
-- (1, 'Gaming Laptop X', 120000),
-- (13, 'Smart Watch Pro', 15000),
-- (16, 'Bike Helmet', 1800),
-- (3, 'Smartphone Mini', 55000),
-- (2, 'Wireless Mouse', 1200),
-- (17, 'Toy Car Remote', 1200),
-- (6, 'Refrigerator 300L', 32000),
-- (8, 'Ceiling Fan', 2500),
-- (4, 'Wooden Wardrobe', 25000),
-- (7, 'Home Theatre', 22000),
-- (5, 'Formal Shoes', 3500),
-- (10, 'Electric Kettle', 1500),
-- (11, 'Backpack 30L', 1800),
-- (14, 'Blanket King Size', 3500),
-- (3, 'Smartphone Max', 75000),
-- (9, 'Vitamin Tablets', 800),
-- (15, 'Perfume 100ml', 1800),
-- (12, 'Ball Pen Set', 200),
-- (1, 'Laptop Air 13', 65000),
-- (2, 'Bluetooth Speaker', 3500),
-- (16, 'Car Seat Cover', 3500),
-- (5, 'Sports Jacket', 2800),
-- (7, 'LED TV 43 inch', 28000),
-- (4, 'Study Table', 9000),
-- (13, 'Analog Watch', 1800),
-- (6, 'Washing Machine 7kg', 27000),
-- (17, 'Building Blocks Set', 2800),
-- (8, 'Air Conditioner 1.5 Ton', 38000),
-- (10, 'Mixer Grinder', 3500),
-- (14, 'Pillow Memory Foam', 1500),
-- (9, 'Gym Gloves', 600),
-- (11, 'Handbag Leather', 4800),
-- (12, 'Office Files', 500),
-- (15, 'Shampoo 500ml', 450),
-- (16, 'Car Vacuum Cleaner', 2200),
-- (13, 'Digital Watch', 2500);


-- queries 


select * from products;
 
 
-- category wise revenue 
-- select category_id , sum(revenue) from products group by(category_id);




with rank_tbl as ( 
	select *, dense_rank() over(partition by category_id order by revenue desc) as rank_category  from products
)

select * from rank_tbl;

-- select count(*) from rank_tbl;

select * from rank_tbl where rank_category <= 3;



