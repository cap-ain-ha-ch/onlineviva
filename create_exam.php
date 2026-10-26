<?php
require_once __DIR__ . '/bootstrap.php';
require_role('teacher');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('teacher_dashboard.php');
}
verify_csrf();
$title = trim((string)($_POST['title'] ?? ''));
$accessPassword = (string)($_POST['access_password'] ?? '');
if ($title === '' || strlen($title) > 120) {
    set_flash('Enter an exam title of 1 to 120 characters.');
    redirect('teacher_dashboard.php');
}
if (strlen($accessPassword) < 4 || strlen($accessPassword) > 100) {
    set_flash('The student access password must contain 4 to 100 characters.');
    redirect('teacher_dashboard.php');
}
$passwordHash = password_hash($accessPassword, PASSWORD_DEFAULT);
$teacherId = (int)$_SESSION['teacher_id'];
$statement = $conn->prepare('INSERT INTO exams (title, access_password_hash, created_by) VALUES (?, ?, ?)');
$statement->bind_param('ssi', $title, $passwordHash, $teacherId);
$statement->execute();
$examId = $conn->insert_id;
set_flash('Exam created. Exam ID: ' . $examId . '. Save this ID and share it with the student access password.');
redirect('teacher_dashboard.php');
