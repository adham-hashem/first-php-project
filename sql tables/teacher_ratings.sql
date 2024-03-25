CREATE TABLE teacher_ratings (
    id INT(10) PRIMARY KEY AUTO_INCREMENT,
    student_national_id VARCHAR(14) NOT NULL,
    teacher_national_id VARCHAR(14) NOT NULL,
    rate DECIMAL(10, 2) NOT NULL DEFAULT 0,
    opinion VARCHAR(2000),
    FOREIGN KEY (student_national_id) REFERENCES students(national_id),
    FOREIGN KEY (teacher_national_id) REFERENCES teachers(national_id)
);