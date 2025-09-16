CREATE DATABASE IF NOT EXISTS pc_webstore;
USE pc_webstore;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    stock INT NOT NULL DEFAULT 10, -- stock control added (default 10)
    image VARCHAR(255),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (category)
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(product_id) REFERENCES products(id)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2),
    payment_method VARCHAR(40), 
    full_name VARCHAR(150),
    email VARCHAR(150),
    phone VARCHAR(50),
    address TEXT,
    city VARCHAR(100),
    zip VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `products` (`id`, `name`, `price`, `description`, `category`, `image`) VALUES
(1, 'Lenovo ThinkPad P14s Gen 2 Core i5 Business Laptop', 93000.00, 'Core i5 business-class productivity laptop', 'Laptops', 'assets/images/products/thinkpad-p14s-gen-2-01-500x500.jpg'),
(2, 'MSI Modern 15 F13MG Core i3 13th Gen 15.6" FHD Laptop Platinum Gray', 56500.00, '13th Gen Core i3 slim 15.6" Laptops', 'Laptops', 'assets/images/products/modern-15-f13mg-003-500x500.jpg'),
(3, 'MSI Modern 15 B7M Ryzen 7 7730U 15.6" FHD Laptop', 79000.00, 'Ryzen 7 thin 15.6" productivity notebook', 'Laptops', 'assets/images/products/modern-15-b7m-classic-black-07-500x500.jpeg.jpg'),
(4, 'ASUS Vivobook Go 15 L510KA Celeron N4500 15.6" FHD Laptop Star Black', 82000.00, 'Celeron-powered lightweight 15.6" notebook', 'Laptops', 'assets/images/products/vivobook-go-15-l510ka-star-black-01-500x500.jpeg.jpg'),
(5, 'SUS Vivobook 15 F1504ZA Core i3 12th Gen 15.6" FHD Laptop Cool Silver', 52000.00, '12th Gen Core i3 everyday 15.6" Laptops', 'Laptops', 'assets/images/products/vivobook-15-f1504za-cool-silver-001-500x500.jpeg.jpg'),
(6, 'Gigasonic RB-G19S-300C 19" HD LED Monitor', 5800.00, '19" HD LED monitor for basic use', 'Monitors', 'assets/images/products/rb-g19s-300c-01-500x500.jpeg.jpg'),
(7, 'Gigasonic GS-21.5FHD500S8 21.5" 100Hz FHD IPS Gaming Monitor', 4600.00, '21.5" 100Hz IPS budget gaming display', 'Monitors', 'assets/images/products/gs-21-5fhd500s8-500x500.jpeg'),
(8, 'Xiaomi Redmi V22FAB-RA 21.45" FHD Monitor', 9500.00, '21.45" slim Full HD productivity monitor', 'Monitors', 'assets/images/products/v22fab-ra-001-500x500.jpeg.jpg'),
(9, 'Viewsonic VA2025-H 20” WSXGA 60Hz LED Monitor', 9500.00, '20" WSXGA energy-efficient office monitor', 'Monitors', 'assets/images/products/va2025-h-01-500x500.jpeg.jpg'),
(10, 'Teclast P30 Tablet', 24560.00, '10.1" Android tablet with 6000mAh battery', 'Tablets', 'assets/images/products/p30-01-500x500.jpeg.jpg'),
(11, 'HONOR Pad X7', 23000.00, 'Compact 8.7" tablet for study and media', 'Tablets', 'assets/images/products/pad-x7-01-500x500.jpeg.jpg'),
(12, 'Samsung Galaxy Tab A9 LTE (Wi-Fi + SIM)', 17000.00, '8.7" LTE tablet with expandable storage', 'Tablets', 'assets/images/products/galaxy-tab-a9-graphite-02-500x500.jpeg'),
(13, 'HONOR Pad X8a', 21000.00, '11" Full HD+ tablet with long battery life', 'Tablets', 'assets/images/products/pad-x8a-01-500x500.jpeg.jpg'),
(14, 'TwinMOS EzeeHUB-23P 4-Port USB Hub', 580.00, 'Compact 3x USB 2.0 + 1x USB 3.0 hub', 'Accessories', 'assets/images/products/ezeehub-23p-01-500x500.jpeg.jpg'),
(15, 'Ugreen US133 Micro USB Male to USB Female OTG Cable #10396', 120.00, 'Micro USB OTG adapter cable for peripherals', 'Accessories', 'assets/images/products/us133-01-500x500.jpeg.jpg'),
(16, 'Havit HV-MS753 Optical Mouse', 260.00, 'Basic wired optical mouse for daily use', 'Accessories', 'assets/images/products/ms753-1-500x500.jpg'),
(17, 'Phantom Edge Vortex CH25BB Gaming Chair', 19000.00, 'Ergonomic gaming chair with lumbar support', 'Chairs', 'assets/images/products/vortex-ch25bb-01-500x500.jpeg.jpg'),
(18, 'GIGABYTE AORUS AGC310 Gaming Chair', 26500.00, 'Adjustable steel-frame performance gaming chair', 'Chairs', 'assets/images/products/aorus-agc310-1-500x500.jpg'),
(19, 'Phantom Edge Vortex CH25BG Gaming Chair', 19700.00, 'Adjustable gaming chair with 3D armrests', 'Chairs', 'assets/images/products/vortex-ch25bg-01-500x500.jpeg.jpg');
