<?php
require_once __DIR__ . '/bootstrap.php';
require_role('student');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('exam.php');
}
verify_csrf();
$examId = (int)($_SESSION['exam_id'] ?? 0);
$studentName = trim((string)($_SESSION['student_name'] ?? ''));
$rollNumber = trim((string)($_SESSION['student_roll'] ?? ''));
if ($examId < 1 || $studentName === '' || $rollNumber === '') {
    set_flash('Your exam session has expired. Sign in again.');
    redirect('student_login.php');
}

$questionStatement = $conn->prepare('SELECT id, correct_option FROM questions WHERE exam_id = ? ORDER BY id');
$questionStatement->bind_param('i', $examId);
$questionStatement->execute();
$questionRows = $questionStatement->get_result()->fetch_all(MYSQLI_ASSOC);
if (!$questionRows) {
    set_flash('No questions were found for this exam.');
    redirect('student_login.php');
}

$submittedAnswers = $_POST['answers'] ?? [];
if (!is_array($submittedAnswers)) {
    $submittedAnswers = [];
}
$totalQuestions = count($questionRows);
$score = 0;
$cleanAnswers = [];
foreach ($questionRows as $question) {
    $questionId = (int)$question['id'];
    $chosen = $submittedAnswers[$questionId] ?? null;
    if (!is_string($chosen) || !in_array($chosen, ['A', 'B', 'C', 'D'], true)) {
        $chosen = null;
    }
    $isCorrect = ($chosen !== null && $chosen === $question['correct_option']) ? 1 : 0;
    $score += $isCorrect;
    $cleanAnswers[] = [
        'question_id' => $questionId,
        'selected_option' => $chosen,
        'is_correct' => $isCorrect,
    ];
}
$percentage = round(($score / $totalQuestions) * 100, 2);

try {
    $conn->begin_transaction();
    $resultStatement = $conn->prepare('INSERT INTO results (exam_id, student_name, roll_number, score, total_questions, percentage) VALUES (?, ?, ?, ?, ?, ?)');
    $resultStatement->bind_param('issiid', $examId, $studentName, $rollNumber, $score, $totalQuestions, $percentage);
    $resultStatement->execute();
    $resultId = (int)$conn->insert_id;

    $answerStatement = $conn->prepare('INSERT INTO student_answers (result_id, question_id, selected_option, is_correct) VALUES (?, ?, ?, ?)');
    foreach ($cleanAnswers as $answer) {
        $questionId = (int)$answer['question_id'];
        $selectedOption = $answer['selected_option'];
        $isCorrect = (int)$answer['is_correct'];
        $answerStatement->bind_param('iisi', $resultId, $questionId, $selectedOption, $isCorrect);
        $answerStatement->execute();
    }
    $conn->commit();
    $_SESSION['last_result_id'] = $resultId;
    $_SESSION['last_result_exam_id'] = $examId;
    redirect('result.php');
} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    // If this same logged-in student double-submits, show the already-saved result.
    $existingStatement = $conn->prepare('SELECT id FROM results WHERE exam_id = ? AND roll_number = ? LIMIT 1');
    $existingStatement->bind_param('is', $examId, $rollNumber);
    $existingStatement->execute();
    $existing = $existingStatement->get_result()->fetch_assoc();
    if ($existing) {
        $_SESSION['last_result_id'] = (int)$existing['id'];
        $_SESSION['last_result_exam_id'] = $examId;
        redirect('result.php');
    }
    set_flash('The answers could not be saved. Check the database schema and try again.');
    redirect('exam.php');
}
