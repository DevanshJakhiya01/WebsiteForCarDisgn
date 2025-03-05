CREATE DATABASE car_customization;

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