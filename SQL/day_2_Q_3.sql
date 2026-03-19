create database 02_q_3;

use 02_q_3;


create table departments (
    department_id int primary key auto_increment,
    department_name varchar(100)
); 

create table employees (
    employee_id int primary key auto_increment,
    employee_name varchar(100),
    department_id int,
    foreign key (department_id) references departments(department_id)
);

create table performance (
    performance_id int primary key auto_increment,
    employee_id int,
    sales decimal(10,2),
    target decimal(10,2),
    rating decimal(3,2),
    foreign key (employee_id) references employees(employee_id)
);



select
    e.employee_id,
    e.employee_name,
    d.department_name,
    p.sales,
    p.target,

    (p.sales / p.target) * 100 as achievement_percentage,

    avg(p.sales) over(partition by e.department_id) as department_average,

    percent_rank() over(
        partition by e.department_id
        order by p.sales
    ) as percentile_rank,

    ntile(4) over(
        partition by e.department_id
        order by p.sales desc
    ) as quartile

from performance p
join employees e 
on p.employee_id = e.employee_id
join departments d 
on e.department_id = d.department_id;