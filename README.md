# Online Viva Examination System

A submission-ready academic project for KTU S5 Web Programming Lab. It uses HTML, CSS, JavaScript, PHP 8 and MySQL through XAMPP. No external framework or JavaScript library is needed.

## Syllabus alignment

The project mainly applies the syllabus items in **S5 Web Programming Lab (PCCNL507)**:

- Experiment 4: CSS styling (external stylesheet).
- Experiments 5 and 8: HTML forms and validation.
- Experiment 9: JavaScript event handling.
- Experiment 11: online exam using JavaScript.
- Experiment 12: PHP form processing and validation.
- Experiments 16 and 18: PHP/MySQL connectivity and storing/retrieving records.
- Experiment 17: deploying an HTML/PHP web application locally through XAMPP.

The syllabus lists Laravel as Experiment 19/micro-project, but this version intentionally uses plain PHP and does not require Laravel.

## Features

- Teacher setup and login using PHP password hashing and sessions.
- Teachers create exams with an exam title and student access password.
- Teachers add MCQs with four options and one stored answer key.
- Students enter name, roll number, exam ID and access password.
- Student exam page is responsive for mobile and desktop browsers.
- Server-side score and percentage calculation; correct answers are never selected into the student exam-page query.
- Student answers and results are stored in MySQL.
- A roll number can submit a given exam only once.
- Prepared SQL statements, HTML output escaping, form tokens and role checks are used.
- Exam page blocks common copy/cut/paste, right-click and several keyboard shortcuts; first browser Back attempt warns and the second attempts to submit; refresh/close warnings are shown where supported.

## Important limitation

A webpage cannot completely prevent cheating. Browser JavaScript cannot reliably block Android Circle to Search, operating-system screenshots, another phone/camera, external devices, developer tools in every browser, or a browser/app being force-closed. Mobile browsers also vary in how they handle Back and page-close prompts. The implementation is a demonstration of basic deterrents, not a replacement for a supervised exam or a secure lockdown browser.

## Install on Windows 11 with XAMPP

1. **Back up anything already present.** If `C:\xampp\htdocs\online_viva` already contains files, copy the folder somewhere safe first. If `online_viva` already has data, export it from phpMyAdmin before making schema changes.
2. Extract this project so the files are directly inside `C:\xampp\htdocs\online_viva`. Confirm these paths exist:
   - `C:\xampp\htdocs\online_viva\index.php`
   - `C:\xampp\htdocs\online_viva\setup_teacher.php`
   - `C:\xampp\htdocs\online_viva\style.css`
3. Open the XAMPP Control Panel and start **Apache** and **MySQL**.
4. Visit `http://localhost/phpmyadmin/`. If the `online_viva` database does not exist, create it. Select it, open **Import**, choose `database.sql`, and run the import. The script uses `CREATE TABLE IF NOT EXISTS` and contains no `DROP` statements; it does not delete existing records.
5. If this XAMPP installation uses a MySQL password, edit `DB_PASS` in `db.php`. The default is an empty password for the standard local XAMPP setup.
6. Open `http://localhost/online_viva/`. To create the first teacher, open `http://localhost/online_viva/setup_teacher.php` and create a username/password. The page only allows initial setup when the `teachers` table has no rows.
7. Open Teacher login, sign in, create an exam, and save the exam ID and the student access password.
8. Select **Manage questions** and add at least one question. Add the question text, A/B/C/D options, and select the correct option.
9. Open a private/incognito window or a second browser. Visit Student login, enter a name, roll number, exam ID and student access password. Answer the questions and submit.
10. Confirm that the result page displays the score and percentage. Sign back in as the teacher and select **View results** for that exam.

## Existing `teachers` table warning

Your earlier attempt reported that `teachers` already exists. This project does **not** drop or recreate it. `CREATE TABLE IF NOT EXISTS` does not repair an existing table whose columns are different. If teacher setup/login reports a missing column, open phpMyAdmin → `online_viva` → `teachers` → **Structure**, compare its columns to `database.sql`, and back up the database before making a migration. Do not delete the database just to fix a column mismatch.

## Project files

- `database.sql` — tables and relationships.
- `db.php` — MySQL connection settings.
- `bootstrap.php` — session and shared helper functions.
- `index.php` — home page and role entry points.
- `setup_teacher.php` — one-time first teacher account creation.
- `teacher_login.php` — teacher authentication.
- `teacher_dashboard.php` — create exam form and exam list.
- `create_exam.php` — saves a new exam.
- `add_question.php` — adds and lists exam questions.
- `student_login.php` — validates the student and exam access.
- `exam.php` — displays MCQs without answer keys.
- `quiz.js` — common anti-cheating deterrents and Back handling.
- `submit_exam.php` — validates answers and calculates the score on the server.
- `result.php` — student's result.
- `view_results.php` — teacher's results table.
- `logout.php` — ends the current session.
- `style.css` — responsive layout for phones, tablets and desktops.
- `app.js` — small show/hide-password helper.
- `VIVA_GUIDE.md` — explanation and questions for evaluation.
- `LAB_REPORT.md` — project report draft for the record.

## Test checklist

- [ ] Home page opens from `localhost` (not by double-clicking the PHP file).
- [ ] First teacher account can be created once.
- [ ] Wrong teacher credentials are rejected.
- [ ] Teacher can create an exam and add multiple questions.
- [ ] Student cannot start an exam with a wrong ID/password or with zero questions.
- [ ] Student page source/query does not contain `correct_option` or answer-key values.
- [ ] Correct answers produce the expected score; unanswered/wrong answers do not earn marks.
- [ ] Duplicate submission for the same exam and roll number is rejected.
- [ ] Teacher can view names, roll numbers, scores and percentages.
- [ ] Layout remains usable on a phone in portrait mode.
- [ ] Copy/paste, right-click, Back warning and refresh/close warning are tested in the target browser, with the limitations above understood.

## Troubleshooting

- **404 Not Found:** verify the files are directly in `C:\xampp\htdocs\online_viva\` and that the URL is `http://localhost/online_viva/setup_teacher.php` (not a `file:///` URL and not an extra nested folder path).
- **Database connection failed:** make sure MySQL is running, database `online_viva` exists, and `db.php` has the correct username/password.
- **Unknown column/table error:** the database may have older tables from previous attempts. Back up first and compare table Structure with `database.sql`; do not run `DROP DATABASE`.
- **Blank/500 page:** check `C:\xampp\apache\logs\error.log` and, if present, `C:\xampp\php\logs\php_error_log` for local debugging.
- **Mobile Back or close prompt differs:** browser implementations differ, and some browsers suppress prompts. That is a known browser limitation, not proof that the scoring system is broken.
