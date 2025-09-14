-- CREATE TABLE order_items (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     order_id INT,
--     product_id INT,
--     quantity INT,
--     price DECIMAL(10,2),
--     FOREIGN KEY(order_id) REFERENCES orders(id),
--     FOREIGN KEY(product_id) REFERENCES products(id)
-- );
-- SQL schema for pc_webstore
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
    payment_method VARCHAR(40), -- e.g. 'cod' (Cash On Delivery); add via: ALTER TABLE orders ADD payment_method VARCHAR(40) AFTER total;
    full_name VARCHAR(150),
    email VARCHAR(150),
    phone VARCHAR(50),
    address TEXT,
    city VARCHAR(100),
    zip VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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


INSERT INTO products (name, description, price, image, category) VALUES
-- Laptops
('Gaming Laptop Pro 15', 'High-performance gaming laptop with RTX 4070 graphics and 240Hz display', 1899.00, 'https://www.startech.com.bd/image/cache/catalog/laptop/asus/tuf-gaming-a15-fa506nf/tuf-gaming-a15-fa506nf-front-228x228.webp&fit=crop', 'Laptops'),
('Ultrabook Air 13', 'Ultra-light productivity laptop with long battery life', 1099.00, 'https://www.startech.com.bd/image/cache/catalog/laptop/hp-laptop/15-fb1053ax/15-fb1053ax-01-228x228.webp&fit=crop', 'Laptops'),
('Developer Workstation 16', 'Powerful 16-inch laptop for developers and creators', 2199.00, 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&fit=crop', 'Laptops'),
('Business Laptop 14', 'Secure 14-inch business notebook with fingerprint reader', 1299.00, 'https://images.unsplash.com/photo-1559163499-413811fb2344?w=800&fit=crop', 'Laptops'),
('Student Laptop 15', 'Affordable 15-inch laptop ideal for coursework and streaming', 699.00, 'https://images.unsplash.com/photo-1516387938699-a93567ec168e?w=800&fit=crop', 'Laptops'),
('Convertible 2-in-1 13', '360° hinge touchscreen convertible with pen support', 1149.00, 'https://images.unsplash.com/photo-1587831990711-23ca6441447b?w=800&fit=crop', 'Laptops'),
('Creator Laptop 17', 'Large 17-inch laptop with color-accurate display for creators', 2499.00, 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?w=800&fit=crop', 'Laptops'),
('Budget Laptop 15', 'Entry-level dual-core laptop for everyday tasks', 459.00, 'https://images.unsplash.com/photo-1602080858428-57174f9431cf?w=800&fit=crop', 'Laptops'),
('Chromebook Flex 12', 'Lightweight 12-inch convertible Chromebook', 349.00, 'https://images.unsplash.com/photo-1555617981-dac3880feec8?w=800&fit=crop', 'Laptops'),
('Rugged Field Laptop 14', 'Durable MIL-STD rated laptop for field engineers', 1890.00, 'https://images.unsplash.com/photo-1587614382346-4ec70e388b28?w=800&fit=crop', 'Laptops'),

-- Accessories
('Mechanical Keyboard RGB', '96-key hotswap mechanical keyboard with per-key RGB', 129.00, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&fit=crop', 'Accessories'),
('Ergonomic Wireless Mouse', 'Low-latency wireless mouse with adjustable DPI', 59.00, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&fit=crop', 'Accessories'),
('USB-C Docking Hub', '8-in-1 USB-C hub with HDMI and PD charging', 79.00, 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=800&fit=crop', 'Accessories'),
('Portable NVMe SSD 1TB', 'High-speed USB 3.2 Gen2 external SSD', 159.00, 'https://images.unsplash.com/photo-1510552776732-13bbe512e6db?w=800&fit=crop', 'Accessories'),
('Studio Headset ANC', 'Active noise cancellation over-ear headset', 199.00, 'https://images.unsplash.com/photo-1580906855280-95e85f776b51?w=800&fit=crop', 'Accessories'),
('Wireless Earbuds Pro', 'Noise-isolating true wireless earbuds with charging case', 129.00, 'https://images.unsplash.com/photo-1585386959984-a4155222cd05?w=800&fit=crop', 'Accessories'),
('Laptop Cooling Pad', 'Adjustable cooling pad with dual silent fans', 45.00, 'https://images.unsplash.com/photo-1603791452878-001d51b08f5d?w=800&fit=crop', 'Accessories'),
('USB-C to HDMI Adapter', 'Minimal 4K 60Hz aluminum adapter', 24.00, 'https://images.unsplash.com/photo-1555617117-08e2b2a6f281?w=800&fit=crop', 'Accessories'),
('Portable Power Bank 20K', '20000mAh fast-charging power bank with USB-C PD', 69.00, 'https://images.unsplash.com/photo-1603354350317-6f7aaa5911c5?w=800&fit=crop', 'Accessories'),
('Multi-Device Charging Station', '6-port desktop charging organizer', 55.00, 'https://images.unsplash.com/photo-1583431034034-478f24578951?w=800&fit=crop', 'Accessories'),

-- Monitors
('4K UHD Monitor 27"', 'Crisp 4K IPS monitor with HDR support', 429.00, 'https://images.unsplash.com/photo-1587614203976-365c74645e83?w=800&fit=crop', 'Monitors'),
('UltraWide QHD 34"', '34-inch ultrawide monitor ideal for multitasking', 599.00, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&fit=crop', 'Monitors'),
('165Hz Gaming Monitor 27"', 'High refresh rate IPS panel with adaptive sync', 379.00, 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&fit=crop', 'Monitors'),
('1440p IPS Monitor 32"', '32-inch productivity monitor with ergonomic stand', 469.00, 'https://images.unsplash.com/photo-1587614382346-4ec70e388b28?w=800&fit=crop', 'Monitors'),
('Dual Monitor 24" Pack', 'Bundle of two 1080p slim bezel productivity displays', 329.00, 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=800&fit=crop', 'Monitors'),
('Color-Accurate 4K Monitor 32"', '99% Adobe RGB panel for design professionals', 999.00, 'https://images.unsplash.com/photo-1587438428500-135f57265189?w=800&fit=crop', 'Monitors'),
('Curved Gaming Monitor 32"', '1500R curved VA panel with immersive contrast', 549.00, 'https://images.unsplash.com/photo-1610878180933-123728745d22?w=800&fit=crop', 'Monitors'),
('Portable USB-C Monitor 15.6"', 'Slim travel monitor powered by USB-C', 229.00, 'https://images.unsplash.com/photo-1626908013351-800ddd734b14?w=800&fit=crop', 'Monitors'),
('5K Creative Monitor 27"', 'High-resolution panel with wide color gamut', 1299.00, 'https://images.unsplash.com/photo-1587614382346-4ec70e388b28?w=800&fit=crop', 'Monitors'),
('Budget 1080p Monitor 24"', 'Entry-level 75Hz LED monitor', 129.00, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&fit=crop', 'Monitors'),

-- Printers
('All-in-One Inkjet', 'Compact wireless print/scan/copy device', 189.00, 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&fit=crop', 'Printers'),
('Laser Printer Mono Compact', 'Fast monochrome laser printer for home office', 149.00, 'https://images.unsplash.com/photo-1586528116265-7a3585b5305b?w=800&fit=crop', 'Printers'),
('Color LaserJet Pro', 'Color laser printer with duplex printing', 329.00, 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&fit=crop', 'Printers'),
('Photo Printer 6-Ink', 'High fidelity photo printer for enthusiasts', 259.00, 'https://images.unsplash.com/photo-1547815749-8380bc7e4851?w=800&fit=crop', 'Printers'),
('EcoTank Inkjet XL', 'Cartridge-free ink tank printer with ultra-low cost/page', 399.00, 'https://images.unsplash.com/photo-1586528116265-7a3585b5305b?w=800&fit=crop', 'Printers'),
('Portable Mobile Printer', 'Battery-powered compact travel printer', 229.00, 'https://images.unsplash.com/photo-1555617981-dac3880feec8?w=800&fit=crop', 'Printers'),
('Wide-Format Printer A3', 'Print up to A3+ borderless graphics', 479.00, 'https://images.unsplash.com/photo-1555617981-dac3880feec8?w=800&fit=crop', 'Printers'),
('3D Printer Entry-Level', 'Beginner-friendly FDM 3D printer with heated bed', 249.00, 'https://images.unsplash.com/photo-1580719134756-b241b6dc5336?w=800&fit=crop', 'Printers'),
('Label Printer Thermal', 'USB thermal label printer for shipping and barcodes', 119.00, 'https://images.unsplash.com/photo-1603354350317-6f7aaa5911c5?w=800&fit=crop', 'Printers'),
('High-Speed Document Scanner', 'Duplex auto-feed scanner for bulk archiving', 369.00, 'https://images.unsplash.com/photo-1593642634315-48f5414c3ad9?w=800&fit=crop', 'Printers'),

-- Networking
('WiFi 6 Performance Router', 'Tri-band WiFi 6 router with advanced QoS', 249.00, 'https://images.unsplash.com/photo-1618498082410-b4aa22193b38?w=800&fit=crop', 'Networking'),
('Gigabit 8-Port Switch', 'Fanless unmanaged 8-port gigabit switch', 49.00, 'https://images.unsplash.com/photo-1592997576610-be0d3637e06d?w=800&fit=crop', 'Networking'),
('WiFi 6E Mesh Router (2-Pack)', 'Whole-home coverage with 6GHz backhaul', 499.00, 'https://images.unsplash.com/photo-1618498082410-b4aa22193b38?w=800&fit=crop', 'Networking'),
('2.5G Managed Switch 12-Port', 'Layer 2 smart managed switch with VLAN support', 289.00, 'https://images.unsplash.com/photo-1555617117-08e2b2a6f281?w=800&fit=crop', 'Networking'),
('USB-C 2.5G Ethernet Adapter', 'Aluminum portable multi-gig network adapter', 39.00, 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=800&fit=crop', 'Networking'),
('Tri-Band Gaming Router', 'Optimized QoS and low-latency acceleration', 329.00, 'https://images.unsplash.com/photo-1603791452878-001d51b08f5d?w=800&fit=crop', 'Networking'),
('Powerline Kit AV2000', 'High-speed powerline networking kit with passthrough', 139.00, 'https://images.unsplash.com/photo-1603791452906-bb3fcef20874?w=800&fit=crop', 'Networking'),
('Network Attached Storage 4-Bay', '4-bay NAS enclosure with RAID support', 549.00, 'https://images.unsplash.com/photo-1583431034034-478f24578951?w=800&fit=crop', 'Networking'),
('SFP+ 10G Network Card', 'PCIe 10GbE adapter with SFP+ cage', 159.00, 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&fit=crop', 'Networking'),
('Firewall Security Appliance', 'Small business firewall with VPN support', 399.00, 'https://images.unsplash.com/photo-1603791452878-001d51b08f5d?w=800&fit=crop', 'Networking'),

-- Chairs
('Ergo Gaming Chair X', 'Ergonomic reclining gaming chair with lumbar support', 279.00, 'https://images.unsplash.com/photo-1621939514649-280e2ee25f46?w=800&fit=crop', 'Chairs'),
('Mesh Office Chair Pro', 'Breathable mesh chair with adjustable headrest', 349.00, 'https://images.unsplash.com/photo-1598301257983-b149773f0f31?w=800&fit=crop', 'Chairs'),
('Executive Leather Chair', 'High-back leather chair with tilt and memory foam', 429.00, 'https://images.unsplash.com/photo-1600566753151-384129f77004?w=800&fit=crop', 'Chairs'),
('Standing Desk Stool', 'Active leaning stool for sit-stand desks', 189.00, 'https://images.unsplash.com/photo-1616628182507-fbadeb4f3665?w=800&fit=crop', 'Chairs'),
('Kneeling Ergonomic Chair', 'Promotes upright posture and spinal alignment', 159.00, 'https://images.unsplash.com/photo-1562183241-b937e95585b6?w=800&fit=crop', 'Chairs'),
('Fabric Task Chair Adjustable', 'Task chair with height and arm adjustments', 219.00, 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&fit=crop', 'Chairs'),
('Racing Style Gaming Chair S', 'Sport-inspired design with tilt lock', 259.00, 'https://images.unsplash.com/photo-1600566752359-35792bedcfea?w=800&fit=crop', 'Chairs'),
('Reclining Office Chair Footrest', 'Integrated footrest and multi-angle recline', 299.00, 'https://images.unsplash.com/photo-1598301257983-b149773f0f31?w=800&fit=crop', 'Chairs'),
('Active Balance Ball Chair', 'Core-engaging seating for improved posture', 139.00, 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=800&fit=crop', 'Chairs'),
('High-Back Lumbar Chair', 'Adjustable lumbar system and breathable back', 259.00, 'https://images.unsplash.com/photo-1600566753151-384129f77004?w=800&fit=crop', 'Chairs'),

-- Tablets
('10" Media Tablet', '10-inch entertainment tablet with stereo speakers', 329.00, 'https://images.unsplash.com/photo-1510552776732-13bbe512e6db?w=800&fit=crop', 'Tablets'),
('Pro Drawing Tablet 12"', 'Stylus-supported tablet for digital artists', 499.00, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&fit=crop', 'Tablets'),
('Kids Tablet 8" Rugged', 'Durable child-friendly tablet with parental controls', 139.00, 'https://images.unsplash.com/photo-1600783245893-84d3f5f2c87b?w=800&fit=crop', 'Tablets'),
('eReader Tablet 7" Paper', 'E-ink style display with adjustable warm light', 169.00, 'https://images.unsplash.com/photo-1557825835-68b546086317?w=800&fit=crop', 'Tablets'),
('Android Productivity Tablet 11"', '11-inch multitasking tablet with keyboard support', 399.00, 'https://images.unsplash.com/photo-1557825835-68b546086317?w=800&fit=crop', 'Tablets'),
('Windows 2-in-1 Tablet 13"', 'Detachable performance tablet with desktop OS', 1199.00, 'https://images.unsplash.com/photo-1510552776732-13bbe512e6db?w=800&fit=crop', 'Tablets'),
('Mini Tablet 8.3"', 'Compact 8.3-inch tablet for portability', 349.00, 'https://images.unsplash.com/photo-1600783245893-84d3f5f2c87b?w=800&fit=crop', 'Tablets'),
('OLED Entertainment Tablet 11"', 'Vivid OLED display with quad speakers', 549.00, 'https://images.unsplash.com/photo-1600566753151-384129f77004?w=800&fit=crop', 'Tablets'),
('Education Tablet 10" Stylus', 'Designed for classrooms with managed OS features', 289.00, 'https://images.unsplash.com/photo-1600566753151-384129f77004?w=800&fit=crop', 'Tablets'),
('Budget Tablet 10"', 'Affordable 10-inch tablet for basic media and browsing', 179.00, 'https://images.unsplash.com/photo-1510552776732-13bbe512e6db?w=800&fit=crop', 'Tablets');
