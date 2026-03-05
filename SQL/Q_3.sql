use q_3_db;

select * from customers;
select * from order_items;

select * from orders;



WITH last_30_days AS (
    SELECT *
    FROM orders
    WHERE order_date >= NOW() - INTERVAL 30 DAY
),

customer_spending AS (
    SELECT 
        customer_id,
        COUNT(order_id) AS purchase_count,
        SUM(total_amount) AS total_spending
    FROM last_30_days
    GROUP BY customer_id
),

overall_avg AS (
    SELECT AVG(total_spending) AS avg_spending
    FROM customer_spending
)

-- select * from overall_avg;

SELECT 
    c.customer_id,
    c.name,
    cs.purchase_count,
    cs.total_spending,
    (cs.total_spending - oa.avg_spending) AS amount_above_average,
    oa.avg_spending
FROM customer_spending cs
JOIN customers c ON cs.customer_id = c.customer_id
CROSS JOIN overall_avg oa
WHERE cs.total_spending > oa.avg_spending 
ORDER BY amount_above_average DESC;






