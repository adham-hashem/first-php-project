CREATE TABLE images (
    id INT(10) AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255),
    description TEXT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    national_id VARCHAR(14) NOT NULL
);