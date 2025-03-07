USE car_customization;

-- Table for regular users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL
);

-- Table for admins
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_username VARCHAR(50) NOT NULL,
    admin_password VARCHAR(255) NOT NULL
);

-- Insert a sample admin (for testing)
INSERT INTO admins (admin_username, admin_password) VALUES ('Devansh', 'Sonakshi01');
USE car_customization;

-- Table for orders
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('credit_card', 'debit_card', 'paypal', 'upi', 'cash_on_delivery') NOT NULL,
    card_number VARCHAR(16),
    expiry_date VARCHAR(5),
    cvv VARCHAR(3),
    upi_id VARCHAR(50),
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);