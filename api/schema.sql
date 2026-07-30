-- MySQL Schema for WearVault Project
CREATE DATABASE IF NOT EXISTS `wearvault` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `wearvault`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `fname` VARCHAR(100) NOT NULL,
    `lname` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `age` INT NOT NULL,
    `notify` VARCHAR(20) DEFAULT 'email',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Admins Table
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `fname` VARCHAR(100) NOT NULL,
    `lname` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(50) DEFAULT 'Administrator',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Products Table
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `stock` INT NOT NULL DEFAULT 10,
    `image_url` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Orders Table
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `customer_name` VARCHAR(150) NOT NULL,
    `user_email` VARCHAR(150) NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('Pending', 'Completed', 'Cancelled') DEFAULT 'Pending',
    `payment_method` VARCHAR(50) DEFAULT 'COD',
    `items_json` JSON NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Digital Wardrobe / Closet Items Table
CREATE TABLE IF NOT EXISTS `custom_closet` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_email` VARCHAR(150) NOT NULL,
    `item_name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `is_favorite` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Saved Outfit Looks Table
CREATE TABLE IF NOT EXISTS `saved_looks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_email` VARCHAR(150) NOT NULL,
    `look_name` VARCHAR(150) NOT NULL,
    `items_json` JSON NOT NULL,
    `is_favorite` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
