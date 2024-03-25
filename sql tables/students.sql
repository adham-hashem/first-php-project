CREATE TABLE students (
    id INT(6) AUTO_INCREMENT PRIMARY KEY,
    national_id VARCHAR(14) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    profile_image VARCHAR(255)
);