<?php
require_once __DIR__ . '/bootstrap.php';
require_role('teacher');
$pageTitle = 'Exam results';
$teacherId = (int)$_SESSION['teacher_id'];
$examId = filter_var($_GET['exam_id'] ?? null, FILTER_VALIDATE_INT);
if (!$examId || $examId < 1) {
    set_flash('Select a valid exam.');
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
$resultStatement = $conn->prepare('SELECT student_name, roll_number, score, total_questions, percentage, submitted_at FROM results WHERE exam_id = ? ORDER BY submitted_at DESC');
$resultStatement->bind_param('i', $examId);
$resultStatement->execute();
$results = $resultStatement->get_result()->fetch_all(MYSQLI_ASSOC);
require __DIR__ . '/partials/header.php';
?>
<section class="page-heading heading-row"><div><p class="eyebrow">RESULTS · EXAM ID <?= (int)$examId ?></p><h1><?= h($exam['title']) ?></h1><p>Student results are loaded from the results table after server-side scoring.</p></div><a class="button button-secondary" href="teacher_dashboard.php">← Dashboard</a></section>
<section class="section-block results-section">
    <div class="section-heading heading-row"><div><h2>Submitted results</h2><p class="muted-text">Only submitted attempts are displayed.</p></div><span class="count-pill"><?= count($results) ?> submission<?= count($results) === 1 ? '' : 's' ?></span></div>
    <?php if (!$results): ?>
        <div class="empty-state"><h3>No submissions yet</h3><p>Results will appear here after students submit the exam.</p></div>
    <?php else: ?>
        <div class="table-wrapper"><table class="data-table"><thead><tr><th>#</th><th>Student name</th><th>Roll number</th><th>Score</th><th>Percentage</th><th>Submitted at</th></tr></thead><tbody>
        <?php foreach ($results as $index => $result): ?><tr><td><?= $index + 1 ?></td><td><?= h($result['student_name']) ?></td><td><?= h($result['roll_number']) ?></td><td><?= (int)$result['score'] ?> / <?= (int)$result['total_questions'] ?></td><td><span class="score-pill"><?= number_format((float)$result['percentage'], 2) ?>%</span></td><td><?= h(date('d M Y, h:i A', strtotime($result['submitted_at']))) ?></td></tr><?php endforeach; ?>
        </tbody></table></div>
    <?php endif; ?>
    <div class="results-footnote"><strong>Scoring method:</strong> PHP compares each submitted option against the correct option stored in the questions table. The browser does not send the answer key.</div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
