CREATE TABLE degrees (
    id INT(6) AUTO_INCREMENT PRIMARY KEY,
    user_national_id VARCHAR(14) NOT NULL,
    student_national_id VARCHAR(14) NOT NULL,
    arabic INT(3),
    english INT(3),
    mathematics INT(3),
    chemistry INT(3),
    physics INT(3),
    FOREIGN KEY (user_national_id) REFERENCES users(national_id),
    FOREIGN KEY (student_national_id) REFERENCES students(national_id)
);