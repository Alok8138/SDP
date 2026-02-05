-- Database Schema for EasyCart (PostgreSQL)
-- Senior Backend Architect Design

-- 1. USER TABLE: customer_entity
CREATE TABLE IF NOT EXISTS customer_entity (
    entity_id SERIAL PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. PRODUCT TABLES: catalog_product_entity
CREATE TABLE IF NOT EXISTS catalog_product_entity (
    entity_id SERIAL PRIMARY KEY,
    sku VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    price NUMERIC(10,2) NOT NULL,
    old_price NUMERIC(10,2),
    description TEXT,
    brand VARCHAR(100),
    delivery_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. PRODUCT ATTRIBUTES: catalog_product_attribute (size, color, features, etc.)
CREATE TABLE IF NOT EXISTS catalog_product_attribute (
    id SERIAL PRIMARY KEY,
    product_id INT REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    attribute_name  VARCHAR(100) NOT NULL,
    attribute_value VARCHAR(255) NOT NULL
);

-- 4. PRODUCT IMAGES: catalog_product_image (gallery)
CREATE TABLE IF NOT EXISTS catalog_product_image (
    id SERIAL PRIMARY KEY,
    product_id INT REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    image_path TEXT NOT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0
);

-- 5. CATEGORY TABLES: catalog_category_entity
CREATE TABLE IF NOT EXISTS catalog_category_entity (
    entity_id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL
);

-- 6. CATEGORY ATTRIBUTES: catalog_category_attribute
CREATE TABLE IF NOT EXISTS catalog_category_attribute (
    id SERIAL PRIMARY KEY,
    category_id INT REFERENCES catalog_category_entity(entity_id) ON DELETE CASCADE,
    attribute_name VARCHAR(100) NOT NULL,
    attribute_value VARCHAR(255) NOT NULL
);

-- 7. PRODUCT-CATEGORY MAPPING: catalog_category_products
CREATE TABLE IF NOT EXISTS catalog_category_products (
    id SERIAL PRIMARY KEY,
    category_id INT REFERENCES catalog_category_entity(entity_id) ON DELETE CASCADE,
    product_id INT REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE
);

-- 8. CART TABLES: sales_cart
CREATE TABLE IF NOT EXISTS sales_cart (
    entity_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES customer_entity(entity_id) ON DELETE SET NULL,
    session_id VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 9. CART PRODUCTS: sales_cart_product
CREATE TABLE IF NOT EXISTS sales_cart_product (
    id SERIAL PRIMARY KEY,
    cart_id INT REFERENCES sales_cart(entity_id) ON DELETE CASCADE,
    product_id INT REFERENCES catalog_product_entity(entity_id) ON DELETE RESTRICT,
    quantity INT NOT NULL CHECK (quantity > 0)
);

-- 10. CART ADDRESS: cart_address
CREATE TABLE IF NOT EXISTS cart_address (
    id SERIAL PRIMARY KEY,
    cart_id INT REFERENCES sales_cart(entity_id) ON DELETE CASCADE,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    pincode VARCHAR(20) NOT NULL
);

-- 11. ORDER TABLES: sales_order
CREATE TABLE IF NOT EXISTS sales_order (
    entity_id SERIAL PRIMARY KEY,
    user_id INT REFERENCES customer_entity(entity_id) ON DELETE SET NULL,
    total_amount NUMERIC(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 12. ORDER PRODUCTS: sales_order_product (snapshot of products at order time)
CREATE TABLE IF NOT EXISTS sales_order_product (
    id SERIAL PRIMARY KEY,
    order_id INT REFERENCES sales_order(entity_id) ON DELETE CASCADE,
    product_id INT REFERENCES catalog_product_entity(entity_id) ON DELETE RESTRICT,
    quantity INT NOT NULL CHECK (quantity > 0),
    price NUMERIC(10,2) NOT NULL -- Price at time of purchase
);

-- 13. ORDER ADDRESS: order_address (snapshot of shipping info)
CREATE TABLE IF NOT EXISTS order_address (
    id SERIAL PRIMARY KEY,
    order_id INT REFERENCES sales_order(entity_id) ON DELETE CASCADE,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    pincode VARCHAR(20) NOT NULL
);

CREATE TABLE sales_order_price (
    id SERIAL PRIMARY KEY,

    order_id INT NOT NULL
        REFERENCES sales_order(entity_id)
        ON DELETE CASCADE,

    -- price breakdown
    subtotal_amount   NUMERIC(10,2) NOT NULL,
    shipping_amount   NUMERIC(10,2) NOT NULL,
    tax_amount        NUMERIC(10,2) NOT NULL,
    discount_amount  NUMERIC(10,2) DEFAULT 0,

    -- derived but stored for history integrity
    final_amount      NUMERIC(10,2) NOT NULL,

    -- meta info
    shipping_type     VARCHAR(30) NOT NULL,     -- standard | express | freight | white_glove
    tax_rate          NUMERIC(5,2) NOT NULL,     -- e.g. 10.00
    currency          VARCHAR(10) DEFAULT 'USD',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-----------------------------------------------------------
-- SAMPLE DATA INSERTION (Based on PHP Arrays)
-----------------------------------------------------------

-- 1. Insert Categories
INSERT INTO catalog_category_entity (name) VALUES 
('Electronics'), ('Fashion'), ('Accessories'), ('Gadgets'), ('Laptops');

-- 2. Insert Products
INSERT INTO catalog_product_entity (sku, name, price, old_price, description, brand, delivery_type) VALUES
('sku-001', 'Wireless Headphones', 99.00, 129.00, 'Premium wireless headphones with noise cancellation.', 'Sony', 'Express'),
('sku-002', 'Smart Watch', 79.00, 99.00, 'Track fitness and notifications with style.', 'Samsung', 'Express'),
('sku-003', 'Smart Phone Pro', 149.00, 199.00, 'Powerful smartphone with amazing camera.', 'Apple', 'Express'),
('sku-004', 'Asus Gaming Phone', 1499.00, 199.00, 'Powerful gaming phone.', 'Asus', 'Freight'),
('sku-005', 'HP Workstation Phone', 1499.00, 199.00, 'Business oriented phone.', 'HP', 'Freight'),
('sku-006', 'Lenovo IdeaPhone', 499.00, 199.00, 'Solid performance phone.', 'Lenovo', 'Freight');

-- 3. Insert Product Images (Main & Gallery)
INSERT INTO catalog_product_image (product_id, image_path, is_main, sort_order) VALUES
(1, 'assets/images/headphone.jpg', TRUE, 0),
(1, 'assets/images/phone.jpg', FALSE, 1),
(1, 'assets/images/laptop.jpg', FALSE, 2),
(2, 'assets/images/watch.jpg', TRUE, 0),
(3, 'assets/images/phone.jpg', TRUE, 0);

-- 4. Insert Product Attributes (Features)
INSERT INTO catalog_product_attribute (product_id, attribute_name, attribute_value) VALUES
(1, 'Feature', 'Active Noise Cancellation'),
(1, 'Feature', '30 hours battery life'),
(1, 'Feature', 'Bluetooth 5.2'),
(2, 'Feature', 'Heart rate monitoring'),
(2, 'Feature', 'Sleep tracking'),
(3, 'Feature', 'High resolution camera');

-- 5. Map Products to Categories
INSERT INTO catalog_category_products (category_id, product_id) VALUES
(1, 1), -- Headphones -> Electronics
(1, 3), -- Phone -> Electronics
(4, 2), -- Watch -> Gadgets
(1, 4), -- Asus -> Electronics
(1, 5); -- HP -> Electronics

-- 6. Sample Customer
INSERT INTO customer_entity (firstname, lastname, email, password_hash, phone) VALUES
('John', 'Doe', 'john@example.com', '$2y$10$abcdefghijklmnopqrstuv', '9876543210');
