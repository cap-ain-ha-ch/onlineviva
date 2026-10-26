<?php
require_once __DIR__ . '/bootstrap.php';
require_role('teacher');
$pageTitle = 'Manage questions';
$teacherId = (int)$_SESSION['teacher_id'];
$examId = filter_var($_POST['exam_id'] ?? $_GET['exam_id'] ?? null, FILTER_VALIDATE_INT);
if (!$examId || $examId < 1) {
    set_flash('Select a valid exam first.');
    redirect('teacher_dashboard.php');
}
$examStatement = $conn->prepare('SELECT id, title FROM exams WHERE id = ? AND created_by = ? LIMIT 1');
$examStatement->bind_param('ii', $examId, $teacherId);
$examStatement->execute();
$exam = $examStatement->get_result()->fetch_assoc();
if (!$exam) {
    http_response_code(404);
    exit('Exam not found or access denied.');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $questionText = trim((string)($_POST['question_text'] ?? ''));
    $optionA = trim((string)($_POST['option_a'] ?? ''));
    $optionB = trim((string)($_POST['option_b'] ?? ''));
    $optionC = trim((string)($_POST['option_c'] ?? ''));
    $optionD = trim((string)($_POST['option_d'] ?? ''));
    $correctOption = strtoupper(trim((string)($_POST['correct_option'] ?? '')));
    if ($questionText === '') {
        $error = 'Enter the question text.';
    } elseif ($optionA === '' || $optionB === '' || $optionC === '' || $optionD === '') {
        $error = 'Enter all four answer options.';
    } elseif (max(strlen($optionA), strlen($optionB), strlen($optionC), strlen($optionD)) > 255) {
        $error = 'Each answer option must be 255 characters or fewer.';
    } elseif (!in_array($correctOption, ['A', 'B', 'C', 'D'], true)) {
        $error = 'Select the correct option.';
    } else {
        $insert = $conn->prepare('INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $insert->bind_param('issssss', $examId, $questionText, $optionA, $optionB, $optionC, $optionD, $correctOption);
        $insert->execute();
        set_flash('Question added successfully.');
        redirect('add_question.php?exam_id=' . $examId);
    }
}
$countStatement = $conn->prepare('SELECT COUNT(*) AS question_count FROM questions WHERE exam_id = ?');
$countStatement->bind_param('i', $examId);
$countStatement->execute();
$questionCount = (int)$countStatement->get_result()->fetch_assoc()['question_count'];
$listStatement = $conn->prepare('SELECT id, question_text, correct_option FROM questions WHERE exam_id = ? ORDER BY id');
$listStatement->bind_param('i', $examId);
$listStatement->execute();
$questionList = $listStatement->get_result()->fetch_all(MYSQLI_ASSOC);
require __DIR__ . '/partials/header.php';
?>
<section class="page-heading heading-row"><div><p class="eyebrow">QUESTION EDITOR · EXAM ID <?= (int)$examId ?></p><h1><?= h($exam['title']) ?></h1><p>Add one multiple-choice question at a time. The correct answer is stored in MySQL and is not sent to the student exam page.</p></div><a class="button button-secondary" href="teacher_dashboard.php">← Dashboard</a></section>
<section class="dashboard-grid question-layout">
    <div class="form-card">
        <div class="section-heading"><p class="eyebrow">NEW QUESTION</p><h2>Add an MCQ</h2></div>
        <?php if ($error !== ''): ?><div class="notice notice-error" role="alert"><?= h($error) ?></div><?php endif; ?>
        <form method="post" action="add_question.php?exam_id=<?= (int)$examId ?>" class="stacked-form">
            <?= csrf_input() ?>
            <input type="hidden" name="exam_id" value="<?= (int)$examId ?>">
            <label for="question_text">Question</label>
            <textarea id="question_text" name="question_text" rows="4" maxlength="5000" required placeholder="Type the question here"></textarea>
            <div class="option-input-grid">
                <div><label for="option_a">Option A</label><input id="option_a" name="option_a" maxlength="255" required></div>
                <div><label for="option_b">Option B</label><input id="option_b" name="option_b" maxlength="255" required></div>
                <div><label for="option_c">Option C</label><input id="option_c" name="option_c" maxlength="255" required></div>
                <div><label for="option_d">Option D</label><input id="option_d" name="option_d" maxlength="255" required></div>
            </div>
            <label for="correct_option">Correct option</label>
            <select id="correct_option" name="correct_option" required><option value="">Choose the answer key</option><option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option></select>
            <button class="button button-primary button-full" type="submit">Save question</button>
        </form>
    </div>
    <div class="dashboard-tip question-tip"><p class="eyebrow">EXAM CHECK</p><h2><?= $questionCount ?> question<?= $questionCount === 1 ? '' : 's' ?> saved</h2><p class="muted-text">Students can enter this exam when it contains at least one question. Add all questions before sharing the exam ID.</p><a class="button button-secondary button-full" href="view_results.php?exam_id=<?= (int)$examId ?>">View exam results</a></div>
</section>
<section class="section-block"><div class="section-heading"><p class="eyebrow">SAVED CONTENT</p><h2>Question list</h2></div>
    <?php if (!$questionList): ?><div class="empty-state"><h3>No questions yet</h3><p>Add the first question using the form.</p></div><?php else: ?>
        <div class="question-list">
        <?php foreach ($questionList as $index => $question): ?>
            <article class="saved-question"><div class="question-number"><?= $index + 1 ?></div><div class="saved-question-content"><p><?= nl2br(h($question['question_text'])) ?></p><span class="answer-key-chip">Answer key: <?= h($question['correct_option']) ?></span></div></article>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
