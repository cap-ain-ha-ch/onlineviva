<?php
require_once __DIR__ . '/bootstrap.php';
require_role('student');
$examId = (int)($_SESSION['exam_id'] ?? 0);
$rollNumber = (string)($_SESSION['student_roll'] ?? '');
if ($examId < 1 || $rollNumber === '') {
    set_flash('Enter the exam details to continue.');
    redirect('student_login.php');
}
$existingStatement = $conn->prepare('SELECT id FROM results WHERE exam_id = ? AND roll_number = ? LIMIT 1');
$existingStatement->bind_param('is', $examId, $rollNumber);
$existingStatement->execute();
if ($existingStatement->get_result()->fetch_assoc()) {
    set_flash('This exam has already been submitted for this roll number.');
    redirect('student_login.php');
}
$examStatement = $conn->prepare('SELECT id, title FROM exams WHERE id = ? AND is_active = 1 LIMIT 1');
$examStatement->bind_param('i', $examId);
$examStatement->execute();
$exam = $examStatement->get_result()->fetch_assoc();
if (!$exam) {
    set_flash('This exam is not available. Contact the teacher.');
    redirect('student_login.php');
}
$questionStatement = $conn->prepare('SELECT id, question_text, option_a, option_b, option_c, option_d FROM questions WHERE exam_id = ? ORDER BY id');
$questionStatement->bind_param('i', $examId);
$questionStatement->execute();
$questions = $questionStatement->get_result()->fetch_all(MYSQLI_ASSOC);
if (!$questions) {
    set_flash('This exam has no questions yet. Contact the teacher.');
    redirect('student_login.php');
}
$pageTitle = 'Examination';
$bodyClass = 'exam-body';
require __DIR__ . '/partials/header.php';
?>
<section class="exam-heading">
    <div><p class="eyebrow">ONLINE VIVA · EXAM ID <?= $examId ?></p><h1><?= h($exam['title']) ?></h1><p>Student: <strong><?= h($_SESSION['student_name']) ?></strong> <span class="dot-separator">·</span> Roll no: <strong><?= h($rollNumber) ?></strong></p></div>
    <div class="exam-progress-card"><strong><?= count($questions) ?></strong><span>Questions</span><small>One attempt per roll number</small></div>
</section>
<div class="exam-instruction-bar"><strong>Instructions</strong><span>Select one answer for each question. Do not refresh or leave this page. The second browser Back attempt submits the answers automatically.</span></div>
<div class="notice notice-warning exam-warning"><strong>Exam mode:</strong> copy, cut, paste, right-click and common keyboard shortcuts are restricted. Android Circle to Search, operating-system screenshots and other external tools cannot be reliably blocked by a normal webpage.</div>
<form id="examForm" method="post" action="submit_exam.php" class="exam-form">
    <?= csrf_input() ?>
    <input type="hidden" id="submission_reason" name="submission_reason" value="manual">
    <div class="exam-question-list">
        <?php foreach ($questions as $index => $question): ?>
            <?php $questionId = (int)$question['id']; ?>
            <fieldset class="exam-question">
                <legend><span class="question-number"><?= $index + 1 ?></span><span><?= nl2br(h($question['question_text'])) ?></span></legend>
                <div class="answer-options">
                    <?php foreach (['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'] as $letter => $column): ?>
                        <label class="answer-option"><input type="radio" name="answers[<?= $questionId ?>]" value="<?= $letter ?>" required><span class="option-letter"><?= $letter ?></span><span class="option-text"><?= h($question[$column]) ?></span></label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endforeach; ?>
    </div>
    <div class="submit-bar"><div><strong><?= count($questions) ?> questions</strong><p>Review your choices before the final submission.</p></div><button class="button button-primary submit-exam-button" type="submit" data-manual-submit>Submit examination</button></div>
</form>
<script src="quiz.js" defer></script>
<?php require __DIR__ . '/partials/footer.php'; ?>
