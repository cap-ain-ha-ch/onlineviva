<?php
require_once __DIR__ . '/bootstrap.php';
require_role('student');
$resultId = (int)($_SESSION['last_result_id'] ?? 0);
$examId = (int)($_SESSION['last_result_exam_id'] ?? 0);
$rollNumber = (string)($_SESSION['student_roll'] ?? '');
if ($resultId < 1 || $examId < 1 || $rollNumber === '') {
    set_flash('No result is available in this session.');
    redirect('student_login.php');
}
$statement = $conn->prepare('SELECT r.student_name, r.roll_number, r.score, r.total_questions, r.percentage, r.submitted_at, e.title FROM results r INNER JOIN exams e ON e.id = r.exam_id WHERE r.id = ? AND r.exam_id = ? AND r.roll_number = ? LIMIT 1');
$statement->bind_param('iis', $resultId, $examId, $rollNumber);
$statement->execute();
$result = $statement->get_result()->fetch_assoc();
if (!$result) {
    set_flash('The requested result could not be found.');
    redirect('student_login.php');
}
$pageTitle = 'Examination result';
require __DIR__ . '/partials/header.php';
?>
<section class="result-screen">
    <div class="result-topline"><span class="result-check">✓</span><p class="eyebrow">SUBMISSION RECEIVED</p><h1>Examination completed</h1><p>Your answers have been checked by the server and the result has been saved.</p></div>
    <article class="result-card">
        <p class="eyebrow">FINAL RESULT</p><h2><?= h($result['title']) ?></h2>
        <div class="score-display"><strong><?= (int)$result['score'] ?><span> / <?= (int)$result['total_questions'] ?></span></strong><small>Correct answers</small></div>
        <div class="percentage-track"><div class="percentage-fill" style="width: <?= max(0, min(100, (float)$result['percentage'])) ?>%"></div></div>
        <div class="result-metrics"><div><span>Percentage</span><strong><?= number_format((float)$result['percentage'], 2) ?>%</strong></div><div><span>Student</span><strong><?= h($result['student_name']) ?></strong></div><div><span>Roll number</span><strong><?= h($result['roll_number']) ?></strong></div><div><span>Submitted</span><strong><?= h(date('d M Y, h:i A', strtotime($result['submitted_at']))) ?></strong></div></div>
        <div class="notice notice-info result-note">The result is final for this roll number and exam. The system does not allow a second submission.</div>
        <form id="resultLogoutForm" method="post" action="logout.php">
            <?= csrf_input() ?>
            <button class="button button-primary button-full" type="submit">Finish</button>
        </form>
    </article>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
