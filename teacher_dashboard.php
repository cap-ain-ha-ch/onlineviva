<?php
require_once __DIR__ . '/bootstrap.php';
require_role('teacher');
$pageTitle = 'Teacher dashboard';
$teacherId = (int)$_SESSION['teacher_id'];
$statement = $conn->prepare(
    'SELECT e.id, e.title, e.is_active, e.created_at,
            (SELECT COUNT(*) FROM questions q WHERE q.exam_id = e.id) AS question_count,
            (SELECT COUNT(*) FROM results r WHERE r.exam_id = e.id) AS result_count
     FROM exams e WHERE e.created_by = ? ORDER BY e.id DESC'
);
$statement->bind_param('i', $teacherId);
$statement->execute();
$exams = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
require __DIR__ . '/partials/header.php';
?>
<section class="page-heading heading-row">
    <div><p class="eyebrow">TEACHER WORKSPACE</p><h1>Dashboard</h1><p>Signed in as <strong><?= h($_SESSION['teacher_username'] ?? 'Teacher') ?></strong>. Create exams, write questions and review submitted scores.</p></div>
    <div class="heading-chip"><span class="status-dot"></span> Teacher access</div>
</section>
<section class="dashboard-grid">
    <div class="form-card dashboard-create-card">
        <div class="card-top-icon">+</div>
        <h2>Create an examination</h2>
        <p class="muted-text">The exam ID and access password are shared with students. Keep the password available for the exam session.</p>
        <form action="create_exam.php" method="post" class="stacked-form">
            <?= csrf_input() ?>
            <label for="title">Exam title</label>
            <input id="title" name="title" type="text" maxlength="120" placeholder="Example: Web Programming Viva" required>
            <label for="access_password">Student access password</label>
            <div class="password-wrap"><input id="access_password" name="access_password" type="password" minlength="4" maxlength="100" autocomplete="new-password" required><button type="button" class="password-toggle" data-toggle-password="#access_password">Show</button></div>
            <p class="field-help">Use at least 4 characters. The password is stored as a hash and cannot be displayed again.</p>
            <button class="button button-primary button-full" type="submit">Create exam</button>
        </form>
    </div>
    <div class="dashboard-tip">
        <p class="eyebrow">QUICK WORKFLOW</p><h2>Ready for an exam?</h2>
        <ol class="workflow-list"><li><span>1</span><div><strong>Create exam</strong><p>Enter a title and student access password.</p></div></li><li><span>2</span><div><strong>Add questions</strong><p>Each MCQ has four options and one correct answer.</p></div></li><li><span>3</span><div><strong>Share access details</strong><p>Give students the exam ID and password.</p></div></li><li><span>4</span><div><strong>Review results</strong><p>Scores are checked by PHP on the server.</p></div></li></ol>
    </div>
</section>
<section class="section-block dashboard-exams">
    <div class="section-heading heading-row"><div><p class="eyebrow">EXAM LIBRARY</p><h2>Your examinations</h2></div><span class="count-pill"><?= count($exams) ?> exam<?= count($exams) === 1 ? '' : 's' ?></span></div>
    <?php if (!$exams): ?>
        <div class="empty-state"><div class="empty-icon">Ex</div><h3>No exams created yet</h3><p>Create the first exam using the form above.</p></div>
    <?php else: ?>
        <div class="exam-list">
            <?php foreach ($exams as $exam): ?>
                <article class="exam-row">
                    <div class="exam-row-main"><span class="exam-id">EXAM ID <?= (int)$exam['id'] ?></span><h3><?= h($exam['title']) ?></h3><p class="muted-text">Created <?= h(date('d M Y, h:i A', strtotime($exam['created_at']))) ?></p></div>
                    <div class="exam-metrics"><div><strong><?= (int)$exam['question_count'] ?></strong><span>Questions</span></div><div><strong><?= (int)$exam['result_count'] ?></strong><span>Submissions</span></div></div>
                    <div class="exam-actions"><a class="button button-small button-primary" href="add_question.php?exam_id=<?= (int)$exam['id'] ?>">Manage questions</a><a class="button button-small button-secondary" href="view_results.php?exam_id=<?= (int)$exam['id'] ?>">View results</a></div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
