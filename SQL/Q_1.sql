-- create database Q_1;


-- use Q_1;


CREATE TABLE employees (
    id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    designation VARCHAR(100),
    manager_id INT,
    salary DECIMAL(10,2),

    CONSTRAINT fk_manager
        FOREIGN KEY (manager_id)
        REFERENCES employees(id)
        ON DELETE SET NULL
);

select  * from employees;



-- recursive query 

-- with recursive employe_h as (
-- 	
--     select e.id,e.name,e.position,e.manager_id from employees e where e.manager_id = null
--     
--      union all
--      
-- 	select a.id,a.name,a.position,a.manager_id  from employees a 
--     join employe_h on a.manager_id = e,id
-- )

-- select * from employe_h;


WITH RECURSIVE org_hierarchy AS (
    
    -- Base Case (CEO)
    SELECT 
        id,
        name,
        designation,
        manager_id,
        0 AS depth,
        name as path
        -- CAST(name AS CHAR(500)) AS path
    FROM employees
    WHERE manager_id IS NULL
    
    UNION ALL
    
    -- Recursive Case
    SELECT 
        e.id,
        e.name,
        e.designation,
        e.manager_id,
        oh.depth + 1 AS depth,
        CONCAT(oh.path, ' -> ', e.name) AS path
    FROM employees e
    JOIN org_hierarchy oh
        ON e.manager_id = oh.id
)

SELECT * FROM org_hierarchy
ORDER BY path;




INSERT INTO employees VALUES (1, 'CEO', 'Chief Executive Officer', NULL, 200000);

INSERT INTO employees VALUES (2, 'Manager A', 'Engineering Manager', 1, 150000);
INSERT INTO employees VALUES (3, 'Manager B', 'Sales Manager', 1, 140000);

INSERT INTO employees VALUES (4, 'Dev 1', 'Software Engineer', 2, 90000);
INSERT INTO employees VALUES (5, 'Dev 2', 'Software Engineer', 2, 85000);

INSERT INTO employees VALUES (6, 'Sales 1', 'Sales Executive', 3, 70000);










