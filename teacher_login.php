<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Teacher login';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    try {
        $statement = $conn->prepare('SELECT id, username, password_hash FROM teachers WHERE username = ? LIMIT 1');
        $statement->bind_param('s', $username);
        $statement->execute();
        $teacher = $statement->get_result()->fetch_assoc();
        if ($teacher && password_verify($password, $teacher['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['role'] = 'teacher';
            $_SESSION['teacher_id'] = (int)$teacher['id'];
            $_SESSION['teacher_username'] = $teacher['username'];
            unset($_SESSION['student_name'], $_SESSION['student_roll'], $_SESSION['exam_id'], $_SESSION['last_result_id']);
            redirect('teacher_dashboard.php');
        }
        $error = 'Username or password is incorrect.';
    } catch (mysqli_sql_exception $exception) {
        $error = 'Login could not be completed. Check the teachers table structure and database connection.';
    }
}
require __DIR__ . '/partials/header.php';
?>
<section class="form-layout compact-layout">
    <div class="form-intro">
        <p class="eyebrow">STAFF WORKSPACE</p>
        <h1>Teacher login</h1>
        <p>Manage examinations from one place. After signing in, create an exam and add its multiple-choice questions.</p>
        <div class="mini-stat-row"><div><strong>01</strong><span>Create an exam</span></div><div><strong>02</strong><span>Add questions</span></div><div><strong>03</strong><span>View results</span></div></div>
    </div>
    <div class="form-card">
        <h2>Welcome back</h2>
        <?php if ($error !== ''): ?><div class="notice notice-error" role="alert"><?= h($error) ?></div><?php endif; ?>
        <form method="post" action="teacher_login.php" class="stacked-form">
            <?= csrf_input() ?>
            <label for="username">Username</label>
            <input id="username" name="username" type="text" autocomplete="username" required>
            <label for="password">Password</label>
            <div class="password-wrap"><input id="password" name="password" type="password" autocomplete="current-password" required><button type="button" class="password-toggle" data-toggle-password="#password">Show</button></div>
            <button class="button button-primary button-full" type="submit">Sign in</button>
        </form>
        <p class="form-footnote">First use? <a href="setup_teacher.php">Create the first teacher account</a></p>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
