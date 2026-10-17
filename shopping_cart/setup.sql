CREATE DATABASE IF NOT EXISTS shopping_cart;
USE shopping_cart;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT '',
    FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

INSERT INTO categories (name)
SELECT 'Garments' WHERE NOT EXISTS
(SELECT 1 FROM categories WHERE name='Garments');

INSERT INTO categories (name)
SELECT 'Mobiles' WHERE NOT EXISTS
(SELECT 1 FROM categories WHERE name='Mobiles');

INSERT INTO categories (name)
SELECT 'Laptops' WHERE NOT EXISTS
(SELECT 1 FROM categories WHERE name='Laptops');
