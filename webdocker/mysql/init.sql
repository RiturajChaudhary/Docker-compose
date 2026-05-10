CREATE DATABASE IF NOT EXISTS crudapp;
USE crudapp;

CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    department VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO employees (name, email, department, position, salary) VALUES
('Alice Johnson', 'alice@example.com', 'Engineering', 'Senior Developer', 85000.00),
('Bob Smith', 'bob@example.com', 'Marketing', 'Marketing Manager', 72000.00),
('Carol White', 'carol@example.com', 'HR', 'HR Specialist', 60000.00);
