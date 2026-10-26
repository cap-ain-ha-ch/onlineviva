# Project Report: Online Viva Examination System

## Abstract

The Online Viva Examination System is a web application for conducting multiple-choice viva examinations. A teacher can create an examination, define an access password, add questions with four options and a correct answer, and review student scores. Students enter their name, roll number and exam credentials, select answers and submit. PHP verifies answers using the correct options stored in MySQL, calculates the score and percentage, and saves the result. HTML, CSS and JavaScript provide the user interface and basic browser-event restrictions. The page layout adapts to desktop and mobile screens.

## Objectives

1. To create a simple online examination interface using HTML, CSS and JavaScript.
2. To process teacher and student forms using PHP and validate form input.
3. To store teacher accounts, examinations, questions and results in MySQL.
4. To calculate marks on the server and display the result.
5. To demonstrate basic browser-based deterrents against ordinary copy/paste and accidental navigation.

## Software requirements

- Windows 11
- XAMPP with Apache, MySQL/MariaDB and PHP 8
- Web browser (desktop or mobile)
- Code editor such as Visual Studio Code

## System design

The application has two roles:

**Teacher:** one-time account setup, login, examination creation, question entry and result viewing.

**Student:** exam access, MCQ answering, submission and result viewing.

The browser sends form data to PHP. PHP validates the data, uses MySQL prepared statements to read or update the database, and returns the relevant HTML page. The correct answers remain on the server until the submission handler scores the answers.

## Database design

| Table | Purpose |
|---|---|
| `teachers` | Teacher usernames and password hashes |
| `exams` | Exam title, access-password hash and teacher owner |
| `questions` | Question text, four options and correct option |
| `results` | Student identity, score, percentage and submission time |
| `student_answers` | Submitted option and correctness for each question |

The result table has a unique key on exam ID and roll number to prevent duplicate attempts for the same roll number.

## Algorithm

1. Start Apache and MySQL and connect PHP to the `online_viva` database.
2. Teacher signs in after password verification.
3. Teacher creates an exam and adds MCQs.
4. Student submits identity and exam access details.
5. PHP verifies the exam and password and checks for an earlier result for the roll number.
6. PHP loads question text and options without exposing correct answers.
7. Student submits chosen option letters.
8. PHP loads the answer key, compares each submitted choice, counts correct answers and computes percentage.
9. PHP saves the result and selected answers in a transaction.
10. The result page displays the saved score and percentage; the teacher can view the class results.

## Validation and security measures

- Required HTML fields and server-side PHP validation.
- Prepared MySQL statements for values from forms.
- `password_hash()` and `password_verify()` for account and exam passwords.
- PHP sessions and role checks for protected pages.
- CSRF tokens on POST forms.
- `htmlspecialchars()` for output escaping.
- Server-side score calculation and a unique key preventing repeat results.
- Basic JavaScript restrictions for common copying/navigation actions.

These measures reduce common errors and casual copying, but a browser-only system cannot guarantee prevention of all cheating.

## Test cases

| Test | Expected result |
|---|---|
| Correct teacher username/password | Teacher dashboard opens |
| Incorrect teacher credentials | Login is rejected |
| Create exam with valid title/password | Exam ID is created and displayed |
| Add a question with all four options | Question is saved and shown in the teacher list |
| Wrong exam ID or password | Student cannot start the exam |
| Exam with no questions | Student is asked to contact the teacher |
| All selected answers correct | Score equals number of questions |
| Some answers wrong or unanswered | Only correct selections earn marks |
| Submit the same exam/roll number again | Second attempt is rejected |
| Teacher opens results | Student names, roll numbers, scores and percentages display |
| Open on a narrow mobile screen | Forms and answer options fit the viewport |

## Result

The application demonstrates an end-to-end viva examination workflow: the teacher creates an exam and questions, students submit answers, PHP calculates the scores and MySQL stores the results. A teacher can view submitted results through the dashboard. The layout is responsive for desktop and mobile browsers.

## Conclusion

The project applies HTML, CSS, JavaScript, PHP and MySQL concepts from the Web Programming Lab syllabus to a practical examination system. It demonstrates form processing, validation, client/server scripts, database connectivity and data retrieval. Anti-cheating features provide basic deterrence but should not be treated as a complete secure-examination solution.
