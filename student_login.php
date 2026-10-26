<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Student exam access';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $studentName = trim((string)($_POST['student_name'] ?? ''));
    $rollNumber = trim((string)($_POST['roll_number'] ?? ''));
    $examId = filter_var($_POST['exam_id'] ?? null, FILTER_VALIDATE_INT);
    $accessPassword = (string)($_POST['access_password'] ?? '');
    if ($studentName === '' || strlen($studentName) > 100) {
        $error = 'Enter a student name of 1 to 100 characters.';
    } elseif ($rollNumber === '' || strlen($rollNumber) > 50) {
        $error = 'Enter a roll number of 1 to 50 characters.';
    } elseif (!$examId || $examId < 1) {
        $error = 'Enter a valid numeric exam ID.';
    } else {
        $statement = $conn->prepare('SELECT id, title, access_password_hash, is_active FROM exams WHERE id = ? LIMIT 1');
        $statement->bind_param('i', $examId);
        $statement->execute();
        $exam = $statement->get_result()->fetch_assoc();
        if (!$exam || (int)$exam['is_active'] !== 1 || !password_verify($accessPassword, $exam['access_password_hash'])) {
            $error = 'Exam ID or access password is incorrect, or the exam is unavailable.';
        } else {
            $questionCountStatement = $conn->prepare('SELECT COUNT(*) AS question_count FROM questions WHERE exam_id = ?');
            $questionCountStatement->bind_param('i', $examId);
            $questionCountStatement->execute();
            $questionCount = (int)$questionCountStatement->get_result()->fetch_assoc()['question_count'];
            if ($questionCount < 1) {
                $error = 'This exam does not contain questions yet. Contact the teacher.';
            } else {
                $existingStatement = $conn->prepare('SELECT id FROM results WHERE exam_id = ? AND roll_number = ? LIMIT 1');
                $existingStatement->bind_param('is', $examId, $rollNumber);
                $existingStatement->execute();
                $existingResult = $existingStatement->get_result()->fetch_assoc();
                if ($existingResult) {
                    $error = 'This roll number has already submitted this exam. A second attempt is not allowed.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['role'] = 'student';
                    $_SESSION['student_name'] = $studentName;
                    $_SESSION['student_roll'] = $rollNumber;
                    $_SESSION['exam_id'] = (int)$exam['id'];
                    $_SESSION['exam_title'] = $exam['title'];
                    unset($_SESSION['teacher_id'], $_SESSION['teacher_username'], $_SESSION['last_result_id']);
                    redirect('exam.php');
                }
            }
        }
    }
}
require __DIR__ . '/partials/header.php';
?>
<section class="form-layout">
    <div class="form-intro">
        <p class="eyebrow">STUDENT WORKSPACE</p>
        <h1>Enter your viva exam</h1>
        <p>Use the exam ID and access password supplied by the teacher. Check the name and roll number before starting because each roll number gets one submission.</p>
        <div class="info-list"><p><strong>Before you begin</strong></p><p>• Use a stable connection and keep this tab open.</p><p>• Answer all questions, then submit.</p><p>• Your score is calculated after the submission reaches the server.</p></div>
    </div>
    <div class="form-card">
        <h2>Exam access</h2>
        <?php if ($error !== ''): ?><div class="notice notice-error" role="alert"><?= h($error) ?></div><?php endif; ?>
        <form method="post" action="student_login.php" class="stacked-form" id="studentLoginForm">
            <?= csrf_input() ?>
            <label for="student_name">Full name</label>
            <input id="student_name" name="student_name" type="text" maxlength="100" autocomplete="name" required value="<?= h($_POST['student_name'] ?? '') ?>">
            <label for="roll_number">Roll number</label>
            <input id="roll_number" name="roll_number" type="text" maxlength="50" autocomplete="off" required value="<?= h($_POST['roll_number'] ?? '') ?>">
            <label for="exam_id">Exam ID</label>
            <input id="exam_id" name="exam_id" type="number" min="1" step="1" required value="<?= h($_POST['exam_id'] ?? '') ?>">
            <label for="access_password">Student access password</label>
            <div class="password-wrap"><input id="access_password" name="access_password" type="password" autocomplete="off" required><button type="button" class="password-toggle" data-toggle-password="#access_password">Show</button></div>
            <button class="button button-primary button-full" type="submit">Verify and start exam</button>
        </form>
        <p class="form-footnote"><a href="index.php">← Back to home</a></p>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
