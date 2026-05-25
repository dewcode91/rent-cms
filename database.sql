-- Rent CMS Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS rent_cms;
USE rent_cms;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    role ENUM('guest', 'tenant', 'admin') DEFAULT 'guest',
    profile_image VARCHAR(255),
    bio TEXT,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
);

-- Categories Table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Deal Types Table
CREATE TABLE deal_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cities Table
CREATE TABLE cities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    state VARCHAR(50),
    country VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Properties Table
CREATE TABLE properties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    address VARCHAR(255) NOT NULL,
    city_id INT NOT NULL,
    category_id INT NOT NULL,
    deal_type_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    price_unit ENUM('per_sqft', 'per_month', 'per_day') DEFAULT 'per_month',
    bedrooms INT,
    bathrooms INT,
    square_feet DECIMAL(10, 2),
    amenities TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    status ENUM('pending', 'approved', 'rejected', 'inactive') DEFAULT 'pending',
    featured BOOLEAN DEFAULT FALSE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (city_id) REFERENCES cities(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (deal_type_id) REFERENCES deal_types(id),
    INDEX idx_city (city_id),
    INDEX idx_category (category_id),
    INDEX idx_deal_type (deal_type_id),
    INDEX idx_status (status),
    INDEX idx_user (user_id),
    FULLTEXT INDEX ft_search (title, description, address)
);

-- Property Images Table
CREATE TABLE property_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    INDEX idx_property (property_id)
);

-- Inquiries Table
CREATE TABLE inquiries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT NOT NULL,
    sender_id INT,
    sender_name VARCHAR(100),
    sender_email VARCHAR(100),
    sender_phone VARCHAR(20),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    replied_at TIMESTAMP NULL,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_property (property_id),
    INDEX idx_status (status)
);

-- Admin Settings Table
CREATE TABLE admin_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert Sample Data
INSERT INTO categories (name, slug, description) VALUES
('Apartment', 'apartment', 'Modern apartments and flats'),
('Villa', 'villa', 'Luxury villas and houses'),
('Penthouse', 'penthouse', 'Premium penthouses'),
('Studio', 'studio', 'Studio apartments');

INSERT INTO deal_types (name, slug, description) VALUES
('Rent', 'rent', 'Properties for rent'),
('Sale', 'sale', 'Properties for sale');

INSERT INTO cities (name, state, country) VALUES
('Tampa', 'FL', 'USA'),
('Palm Beach', 'FL', 'USA'),
('Miami Beach', 'FL', 'USA');

INSERT INTO admin_settings (setting_key, setting_value, description) VALUES
('site_name', 'Rent CMS', 'Website name'),
('site_description', 'Professional Property Rental Management', 'Site description'),
('items_per_page', '12', 'Items displayed per page'),
('require_approval', 'true', 'Require admin approval for listings');
