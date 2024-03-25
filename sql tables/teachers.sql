CREATE TABLE teachers (
    id INT(6) AUTO_INCREMENT PRIMARY KEY,
    national_id VARCHAR(14) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    rate DECIMAL(10, 2) NOT NULL DEFAULT 0,
    opinion VARCHAR(2000),
    profile_image VARCHAR(255)
);