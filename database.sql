-- Online Viva Examination System
-- Safe for an existing database: this script does not DROP tables or delete data.
CREATE DATABASE IF NOT EXISTS online_viva
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE online_viva;

CREATE TABLE IF NOT EXISTS teachers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS exams (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    access_password_hash VARCHAR(255) NOT NULL,
    created_by INT UNSIGNED NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_exams_teacher FOREIGN KEY (created_by)
        REFERENCES teachers(id) ON DELETE RESTRICT,
    INDEX idx_exams_teacher (created_by)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS questions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    exam_id INT UNSIGNED NOT NULL,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option CHAR(1) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_questions_exam FOREIGN KEY (exam_id)
        REFERENCES exams(id) ON DELETE CASCADE,
    INDEX idx_questions_exam (exam_id),
    CONSTRAINT chk_correct_option CHECK (correct_option IN ('A','B','C','D'))
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS results (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    exam_id INT UNSIGNED NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    roll_number VARCHAR(50) NOT NULL,
    score INT UNSIGNED NOT NULL DEFAULT 0,
    total_questions INT UNSIGNED NOT NULL DEFAULT 0,
    percentage DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_results_exam FOREIGN KEY (exam_id)
        REFERENCES exams(id) ON DELETE CASCADE,
    UNIQUE KEY uq_exam_student (exam_id, roll_number),
    INDEX idx_results_exam (exam_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS student_answers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    result_id INT UNSIGNED NOT NULL,
    question_id INT UNSIGNED NOT NULL,
    selected_option CHAR(1) NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_answers_result FOREIGN KEY (result_id)
        REFERENCES results(id) ON DELETE CASCADE,
    CONSTRAINT fk_answers_question FOREIGN KEY (question_id)
        REFERENCES questions(id) ON DELETE CASCADE,
    UNIQUE KEY uq_result_question (result_id, question_id)
) ENGINE=InnoDB;
