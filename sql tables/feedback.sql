CREATE TABLE feedback (
    id INT(10) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    feedback_content VARCHAR(2000) 
);