<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'First-time teacher setup';
$error = '';
$teacherExists = false;

try {
    $countResult = $conn->query('SELECT COUNT(*) AS teacher_count FROM teachers');
    $teacherExists = (int)$countResult->fetch_assoc()['teacher_count'] > 0;
} catch (mysqli_sql_exception $exception) {
    http_response_code(500);
    exit('The teachers table could not be read. Import database.sql and check the table structure without deleting existing data.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if ($teacherExists) {
        $error = 'A teacher account already exists. Sign in with that account instead.';
    } else {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');
        if (strlen($username) < 3 || strlen($username) > 50) {
            $error = 'Username must contain between 3 and 50 characters.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must contain at least 8 characters.';
        } elseif ($password !== $confirmPassword) {
            $error = 'The two passwords do not match.';
        } else {
            try {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $statement = $conn->prepare('INSERT INTO teachers (username, password_hash) VALUES (?, ?)');
                $statement->bind_param('ss', $username, $passwordHash);
                $statement->execute();
                set_flash('Teacher account created. Sign in to create an examination.');
                redirect('teacher_login.php');
            } catch (mysqli_sql_exception $exception) {
                $error = 'The account could not be created. The username may already exist, or the teachers table may have a different structure.';
            }
        }
    }
}
require __DIR__ . '/partials/header.php';
?>
<section class="form-layout">
    <div class="form-intro">
        <p class="eyebrow">TEACHER ACCESS</p>
        <h1>Set up the first teacher</h1>
        <p>This page creates the initial teacher account only when the teachers table contains no teacher records. The password is stored as a hash.</p>
        <div class="info-list"><p><strong>Setup checklist</strong></p><p>1. Start Apache and MySQL in XAMPP.</p><p>2. Import <code>database.sql</code> into phpMyAdmin.</p><p>3. Create this account once, then use Teacher login.</p></div>
    </div>
    <div class="form-card">
        <?php if ($teacherExists): ?>
            <div class="notice notice-warning">A teacher account is already recorded. This page will not create another one.</div>
            <a class="button button-primary button-full" href="teacher_login.php">Go to teacher login</a>
        <?php else: ?>
            <h2>First-time setup</h2>
            <?php if ($error !== ''): ?><div class="notice notice-error" role="alert"><?= h($error) ?></div><?php endif; ?>
            <form method="post" action="setup_teacher.php" class="stacked-form">
                <?= csrf_input() ?>
                <label for="username">Teacher username</label>
                <input id="username" name="username" type="text" minlength="3" maxlength="50" autocomplete="username" required>
                <label for="password">Password</label>
                <div class="password-wrap"><input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required><button type="button" class="password-toggle" data-toggle-password="#password">Show</button></div>
                <label for="confirm_password">Confirm password</label>
                <input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required>
                <button class="button button-primary button-full" type="submit">Create teacher account</button>
            </form>
        <?php endif; ?>
        <p class="form-footnote"><a href="index.php">← Back to home</a></p>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
