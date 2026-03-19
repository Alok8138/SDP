-- create database Q_1;


-- use Q_1;

-- created table employees with columns id, name, designation, manager_id and salary.
-- here manager_id is a foreign key referencing the id of the employees table itself to establish the hierarchy.




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



-- so here we are using a recursive common table expression (CTE) named org_hierarchy to traverse the employee hierarchy starting from the CEO (the employee with no manager or manager_id IS NULL). 
-- The CTE consists of two parts: the base case, which selects the CEO, and the recursive case, which joins the employees with their managers to build the hierarchy. 
-- The final SELECT statement retrieves all employees along with their depth in the hierarchy and the path from the CEO to each employee.


--below comments explain how this query works:

-- 1: The CTE starts with the base case, which selects the CEO (the employee with no manager). It initializes the depth to 0 and sets the path to the CEO's name.


WITH RECURSIVE org_hierarchy AS (
    
    -- Base Case (CEO)
    -- select an all employee whoes manager_id is null
    SELECT 
        id,
        name,
        designation,
        manager_id,
        0 AS depth,
        name as path
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










