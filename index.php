<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = 'Home';
require __DIR__ . '/partials/header.php';
?>
<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">WEB PROGRAMMING LAB PROJECT</p>
        <h1>Online Viva<br><span>Examination System</span></h1>
        <p class="hero-lead">A simple online multiple-choice viva platform for teachers and students, built using the technologies covered in the Web Programming Lab syllabus.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="student_login.php">Enter student exam</a>
            <a class="button button-secondary" href="teacher_login.php">Teacher sign in</a>
        </div>
        <p class="helper-text">For local college demonstration, open this site through XAMPP at <strong>http://localhost/online_viva/</strong>.</p>
    </div>
    <div class="hero-panel" aria-label="Project features">
        <div class="panel-topline"><span class="status-dot"></span> EXAM PORTAL</div>
        <div class="feature-line"><span class="feature-number">01</span><span><strong>Teacher workspace</strong><small>Create exams and MCQ questions</small></span></div>
        <div class="feature-line"><span class="feature-number">02</span><span><strong>Student examination</strong><small>Responsive questions and answer selection</small></span></div>
        <div class="feature-line"><span class="feature-number">03</span><span><strong>Server-side scoring</strong><small>Results calculated using MySQL answer keys</small></span></div>
        <div class="hero-panel-footer">Desktop · Tablet · Mobile</div>
    </div>
</section>
<section class="section-block">
    <div class="section-heading"><p class="eyebrow">CHOOSE A WORKSPACE</p><h2>Continue to the portal</h2></div>
    <div class="card-grid two-columns">
        <article class="workspace-card">
            <div class="card-icon" aria-hidden="true">T</div>
            <h3>Teacher</h3>
            <p>Sign in to create an exam, add four-option questions and check submitted scores.</p>
            <a class="text-link" href="teacher_login.php">Open teacher login →</a>
            <a class="subtle-link" href="setup_teacher.php">First-time teacher setup</a>
        </article>
        <article class="workspace-card">
            <div class="card-icon student-icon" aria-hidden="true">S</div>
            <h3>Student</h3>
            <p>Enter a name, roll number, exam ID and the access password supplied by the teacher.</p>
            <a class="text-link" href="student_login.php">Open student login →</a>
        </article>
    </div>
</section>
<section class="small-print-box">
    <strong>Academic project note</strong>
    <p>Copy, cut, paste and ordinary right-click actions are restricted on the exam page. Browser controls cannot block every method of cheating, including another device or operating-system screenshots.</p>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
