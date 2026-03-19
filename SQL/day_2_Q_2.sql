create database 02_q_2;
use 02_q_2;




create table products (
    product_id bigint primary key auto_increment,
    product_name varchar(100) not null
);

create table inventory_levels (
    inventory_id bigint primary key auto_increment,
    product_id bigint,
    current_stock int not null check (current_stock >= 0),
    daily_consumption_rate decimal(10, 2) default 0.00, 
    last_updated datetime default current_timestamp on update current_timestamp,
    foreign key (product_id) references products(product_id)
);




create table reorder_points (
    reorder_id bigint primary key auto_increment,
    product_id bigint,
    threshold_level int not null, 
    lead_time int not null, 
    foreign key (product_id) references products(product_id)
);


select p.product_id as item,p.product_name as item_name,r.threshold_level as denger_zone,round(i.current_stock/i.daily_consumption_rate) as days_left,
	   r.lead_time as days_to_arrive from products p join inventory_levels i on p.product_id = i.product_id join reorder_points r on p.product_id = r.product_id  
       where i.current_stock < r.threshold_level order by days_left; 

