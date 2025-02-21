-- Drop existing tables if they exist (in correct order due to foreign keys)
DROP TABLE IF EXISTS service_requests;
DROP TABLE IF EXISTS providers;
DROP TABLE IF EXISTS beneficiaries;
DROP TABLE IF EXISTS users;

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS maqsad_db;
USE maqsad_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('provider', 'beneficiary') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Providers table
CREATE TABLE IF NOT EXISTS providers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    company_name VARCHAR(100) NOT NULL,
    company_email VARCHAR(100) NOT NULL,
    service_name VARCHAR(100) NOT NULL,
    service_description TEXT NOT NULL,
    services JSON DEFAULT '[]',
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Beneficiaries table
CREATE TABLE IF NOT EXISTS beneficiaries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    company_name VARCHAR(100),
    business_industry VARCHAR(100),
    preferred_provider VARCHAR(100),
    service_interests JSON DEFAULT '[]',
    other_service VARCHAR(100),
    needs TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Services table for tracking matches and requests
CREATE TABLE IF NOT EXISTS service_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    beneficiary_id INT NOT NULL,
    provider_id INT,
    service_type VARCHAR(50) NOT NULL,
    status ENUM('pending', 'matched', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE SET NULL
);
