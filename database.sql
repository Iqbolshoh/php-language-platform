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

-- 4. USER LANGUAGES TABLE
CREATE TABLE user_languages (
    user_id INT NOT NULL,
    language_id INT NOT NULL,
    PRIMARY KEY (user_id, language_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
);

-- 5. SUBJECTS TABLE
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    language_from_id INT NOT NULL,
    language_to_id INT NOT NULL,
    subject_name VARCHAR(100) NOT NULL UNIQUE,
    FOREIGN KEY (language_from_id) REFERENCES languages(id) ON DELETE CASCADE,
    FOREIGN KEY (language_to_id) REFERENCES languages(id) ON DELETE CASCADE
);

-- 6. DICTIONARY TABLE
CREATE TABLE dictionary (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_id INT NOT NULL,
    language_from_id INT NOT NULL,
    language_to_id INT NOT NULL,
    word VARCHAR(100) NOT NULL,
    translation VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (language_from_id) REFERENCES languages(id) ON DELETE CASCADE,
    FOREIGN KEY (language_to_id) REFERENCES languages(id) ON DELETE CASCADE
);

-- ==============================  
-- 📥 DATA INSERTION (COMPLETE)  
-- ==============================  
-- DEFAULT PASSWORD: "IQBOLSHOH" (HASHED FOR SECURITY)  
-- ==============================

-- INSERTING USERS
INSERT INTO users (first_name, last_name, email, username, password, role, profile_picture) VALUES 
('Iqbolshoh', 'Ilhomjonov', 'iilhomjonov777@gmail.com', 'iqbolshoh', '52be5ff91284c65bac56f280df55f797a5c505f7ef66317ff358e34791507027', 'student', '790d5772254c72bf5c01d43920d8e6a6.jpeg'),
('Student', 'Example', 'student@iqbolshoh.uz', 'student',  '52be5ff91284c65bac56f280df55f797a5c505f7ef66317ff358e34791507027',  'student', 'default.png');

-- INSERTING LANGUAGES
INSERT INTO languages (language_code, language_name) VALUES 
('en', 'English'),
('ru', 'Russian'),
('tj', 'Tajik'),
('uz', 'Uzbek'),
('ar', 'Arabic'),
('fa', 'Persian'),
('zh', 'Chinese'),
('es', 'Spanish'),
('fr', 'French'),
('de', 'German'),
('it', 'Italian'),
('tr', 'Turkish'),
('hi', 'Hindi'),
('pt', 'Portuguese'),
('ja', 'Japanese'),
('ko', 'Korean'),
('nl', 'Dutch'),
('pl', 'Polish'),
('sv', 'Swedish'),
('el', 'Greek'),
('th', 'Thai'),
('he', 'Hebrew'),
('id', 'Indonesian'),
('ms', 'Malay'),
('ro', 'Romanian'),
('vi', 'Vietnamese'),
('cs', 'Czech'),
('hu', 'Hungarian'),
('da', 'Danish'),
('fi', 'Finnish'),
('no', 'Norwegian'),
('bg', 'Bulgarian'),
('sr', 'Serbian'),
('uk', 'Ukrainian'),
('sk', 'Slovak'),
('hr', 'Croatian'),
('sl', 'Slovenian'),
('lv', 'Latvian'),
('lt', 'Lithuanian'),
('et', 'Estonian'),
('ka', 'Georgian'),
('hy', 'Armenian'),
('az', 'Azerbaijani'),
('kk', 'Kazakh'),
('tk', 'Turkmen'),
('ky', 'Kyrgyz'),
('mn', 'Mongolian'),
('sw', 'Swahili'),
('bn', 'Bengali'),
('ml', 'Malayalam'),
('ta', 'Tamil'),
('te', 'Telugu'),
('mr', 'Marathi'),
('ur', 'Urdu'),
('pa', 'Punjabi'),
('my', 'Burmese'),
('si', 'Sinhala'),
('km', 'Khmer'),
('lo', 'Lao'),
('ne', 'Nepali'),
('ps', 'Pashto'),
('so', 'Somali'),
('af', 'Afrikaans'),
('xh', 'Xhosa'),
('zu', 'Zulu'),
('st', 'Sesotho'),
('sn', 'Shona'),
('yo', 'Yoruba'),
('ig', 'Igbo'),
('ha', 'Hausa'),
('am', 'Amharic'),
('ti', 'Tigrinya'),
('om', 'Oromo'),
('rw', 'Kinyarwanda'),
('ny', 'Chichewa'),
('ts', 'Tsonga'),
('tn', 'Tswana'),
('ss', 'Swazi'),
('ve', 'Venda'),
('bm', 'Bambara'),
('wo', 'Wolof'),
('dz', 'Dzongkha'),
('bo', 'Tibetan'),
('iu', 'Inuktitut'),
('sm', 'Samoan'),
('to', 'Tongan'),
('fj', 'Fijian'),
('mi', 'Maori'),
('haw', 'Hawaiian'),
('chr', 'Cherokee'),
('nv', 'Navajo'),
('oj', 'Ojibwe'),
('moh', 'Mohawk'),
('kl', 'Greenlandic'),
('ay', 'Aymara'),
('qu', 'Quechua'),
('gn', 'Guarani'),
('tt', 'Tatar'),
('ba', 'Bashkir'),
('cv', 'Chuvash'),
('sah', 'Yakut'),
('mdf', 'Moksha'),
('myv', 'Erzya');

-- INSERTING USER LANGUAGES
INSERT INTO user_languages (user_id, language_id) VALUES 
(1, 1), -- Iqbolshoh -> English
(1, 2), -- Iqbolshoh -> Russian
(1, 3), -- Iqbolshoh -> Tajik
(1, 4), -- Iqbolshoh -> Uzbek
(1, 5), -- Iqbolshoh -> Arabic
(1, 6), -- Iqbolshoh -> Persian
(2, 1), -- Student -> English
(2, 3); -- Student -> Tajik

-- INSERTING SUBJECTS
INSERT INTO subjects (language_from_id, language_to_id, subject_name) VALUES 
(1, 2, 'English-Russian Vocabulary'),
(3, 1, 'Tajik-English Phrases'),
(4, 1, 'Uzbek-English Words'),
(5, 6, 'Arabic-Persian Words');

-- INSERTING DICTIONARY WORDS
INSERT INTO dictionary (subject_id, language_from_id, language_to_id, word, translation) VALUES 
(1, 1, 2, 'Hello', 'Здравствуйте'), 
(1, 1, 2, 'Goodbye', 'До свидания'), 
(2, 3, 1, 'Салом', 'Hello'), 
(2, 3, 1, 'Хайр', 'Goodbye'), 
(3, 4, 1, 'Rahmat', 'Thank you'), 
(3, 4, 1, 'Salom', 'Hello'),
(4, 5, 6, 'مرحبا', 'سلام'), 
(4, 5, 6, 'شكرا', 'سپاس'); 