-- DROP DATABASE IF EXISTS
DROP DATABASE IF EXISTS language_platform;

-- CREATE DATABASE
CREATE DATABASE language_platform;
USE language_platform;

-- 1. USERS TABLE
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(30) NOT NULL,
    last_name VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student') NOT NULL DEFAULT 'student',
    profile_picture VARCHAR(255) DEFAULT 'default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. ACTIVE SESSIONS TABLE
CREATE TABLE active_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    device_name VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. LANGUAGES TABLE
CREATE TABLE languages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    language_code VARCHAR(10) NOT NULL UNIQUE,
    language_name VARCHAR(50) NOT NULL UNIQUE
);

-- 4. SUBJECTS TABLE (Mavzular jadvali)
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    subject_name VARCHAR(100) NOT NULL UNIQUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. DICTIONARY TABLE (Lug‘at jadvali)
CREATE TABLE dictionary (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    subject_id INT NOT NULL,
    language_from_id INT NOT NULL,
    language_to_id INT NOT NULL,
    word VARCHAR(100) NOT NULL,
    translation VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (language_from_id) REFERENCES languages(id) ON DELETE CASCADE,
    FOREIGN KEY (language_to_id) REFERENCES languages(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==============================  
-- 📥 DATA INSERTION (COMPLETE)  
-- ==============================  
-- DEFAULT PASSWORD: "IQBOLSHOH" (HASHED FOR SECURITY)  
-- ==============================  

-- INSERTING USERS
INSERT INTO users (first_name, last_name, email, username, password, role, profile_picture) VALUES 
('Iqbolshoh', 'Ilhomjonov', 'iilhomjonov777@gmail.com', 'iqbolshoh', '52be5ff91284c65bac56f280df55f797a5c505f7ef66317ff358e34791507027', 'student', '790d5772254c72bf5c01d43920d8e6a6.jpeg'),
('student', 'student', 'student@iqbolshoh.uz', 'student',  '52be5ff91284c65bac56f280df55f797a5c505f7ef66317ff358e34791507027',  'student', 'default.png');

-- INSERTING LANGUAGES
INSERT INTO languages (language_code, language_name) VALUES 
('en', 'English'),
('ru', 'Russian'),
('tj', 'Tajik'),
('uz', 'Uzbek');

-- INSERTING SUBJECTS
INSERT INTO subjects (user_id, subject_name) VALUES 
(1, 'Common Words'),
(1, 'Programming Terms'),
(2, 'Basic Phrases'),
(2, 'Travel Vocabulary');

-- INSERTING DICTIONARY
INSERT INTO dictionary (user_id, subject_id, language_from_id, language_to_id, word, translation) VALUES 
(1, 1, 1, 2, 'Hello', 'Здравствуйте'),
(1, 1, 1, 3, 'Hello', 'Салом'),
(1, 2, 1, 3, 'Function', 'Функсия'),
(2, 3, 1, 4, 'Good morning', 'Xayrli tong'),
(2, 4, 1, 4, 'Airport', 'Aeroport');
