# Viva Guide — Online Viva Examination System

## 1. Project summary

The Online Viva Examination System lets a teacher create an examination, add multiple-choice questions, and view student scores. Students enter their name, roll number, exam ID and access password, answer questions in a browser, and submit the exam. PHP verifies answers using the answer key in MySQL and saves the result.

## 2. Technologies and why they are used

- **HTML** structures forms, question cards, navigation and tables.
- **CSS** provides the page layout, colours and responsive rules for mobile and desktop.
- **JavaScript** handles password visibility and browser events such as copy/paste restrictions, Back handling and the refresh/close prompt.
- **PHP** processes forms, checks login sessions, saves data, verifies answers and calculates marks on the server.
- **MySQL** stores teachers, exams, questions, student results and individual student answers.
- **XAMPP** runs Apache and MySQL locally. PHP must be opened through `http://localhost/`, not by double-clicking the PHP file.

## 3. Main workflow

1. Initial setup creates one teacher account. `password_hash()` stores a hash rather than the original password.
2. Teacher logs in. PHP checks the stored hash with `password_verify()` and starts a session.
3. Teacher creates an exam. The system generates an integer exam ID; it stores a hash of the student access password.
4. Teacher adds MCQs. Each question stores its four options and the correct letter in the `questions` table.
5. Student submits name, roll number, exam ID and access password. PHP verifies the exam/password and checks whether this roll number has already submitted.
6. `exam.php` reads the question text and A–D options only. It does not select the `correct_option` column.
7. `submit_exam.php` receives selected option letters and compares each one with `correct_option` obtained from MySQL. It calculates score and percentage in PHP, then stores the result and answers.
8. `result.php` reads the current student's saved result. `view_results.php` lets the owning teacher review all submissions for an exam.

## 4. Database tables

- **teachers:** teacher username and password hash.
- **exams:** exam title, student password hash, owner teacher and active status.
- **questions:** exam ID, question text, four options and correct option.
- **results:** student identity, score, total questions, percentage and submission time. A unique key on `(exam_id, roll_number)` prevents a second submission.
- **student_answers:** selected answer and correctness for each question in a submitted result.

Foreign keys connect each question and result to its exam. Prepared statements are used when user input is included in SQL queries.

## 5. Important code concepts

**Prepared statement:** SQL is prepared first and input values are bound separately. This helps prevent SQL injection.

**Password hashing:** `password_hash()` converts a password into a one-way hash. `password_verify()` checks a submitted password against that hash. The original password is not stored.

**Session:** PHP sessions remember whether the visitor is a teacher or a student and store the active exam/student details between page requests. `require_role()` blocks pages if the expected role is not logged in.

**Server-side validation:** JavaScript/HTML checks improve usability, but PHP checks submitted values again because browser checks can be bypassed.

**Server-side scoring:** The browser submits the selected answer letter only. PHP loads the correct answers from MySQL and computes the mark, so a modified browser page cannot choose its own score.

**CSRF form token:** The form includes a random session token, and PHP checks it on POST requests to make cross-site form submissions harder.

**Output escaping:** `htmlspecialchars()` is used when displaying text entered by a user, reducing the risk of HTML/script injection in displayed content.

**Responsive design:** CSS media queries change column layouts at smaller screen widths. The viewport meta tag lets the page match a mobile screen width.

## 6. Anti-cheating features and limitations

- JavaScript prevents ordinary right-click, copy, cut, paste, drag and text-selection events on the exam page.
- Common keyboard shortcuts such as Ctrl/Cmd+C, V, X, U and F12 are blocked where the browser allows the page to handle them.
- The first Back event displays a warning. A second event attempts to submit the exam.
- `beforeunload` requests a browser confirmation before leaving or refreshing, where supported.
- Correct answers are not sent in the HTML rendered for students; PHP checks them after submission.
- These are deterrents, not a guarantee. A normal web page cannot reliably block Android Circle to Search, operating-system screenshots, a second device, camera photos, external tools or every browser's developer features. Back/close behaviour also varies on mobile browsers. Supervised examination is still needed for high-stakes tests.

## 7. Likely evaluation questions

**Q1. Why did you use PHP?**  
PHP handles server-side login, form processing, database operations and score calculation.

**Q2. Why is MySQL used?**  
It stores the teacher, exam, question and result data so the records remain available after a page closes.

**Q3. Why are both JavaScript and PHP validation used?**  
JavaScript gives quick feedback in the browser. PHP repeats important validation because client-side validation can be bypassed.

**Q4. What is the purpose of a session?**  
A session keeps login status and the current student/exam identity between separate HTTP requests.

**Q5. Why use `password_hash()`?**  
It avoids saving a readable password in the database. `password_verify()` checks the password during login.

**Q6. How does the system calculate marks?**  
After submission, PHP reads the correct option for each question from MySQL, compares it with the submitted option, counts correct answers and calculates percentage as `score / total_questions * 100`.

**Q7. Can a student see the answers before submission?**  
The student question query selects only question text and the A–D options. The correct-option column is queried only in the server-side submission handler.

**Q8. What is a prepared statement?**  
A prepared SQL query whose input values are bound separately from the SQL command. It is safer than joining user text directly into SQL.

**Q9. Why is a unique key used in the results table?**  
The unique key on exam ID and roll number prevents more than one stored result for the same student roll number in that exam.

**Q10. Why can anti-cheating controls not be perfect?**  
The webpage runs inside a browser and does not control the whole operating system or other devices. Browser support also differs, especially on mobile.

**Q11. How is the project mobile responsive?**  
The viewport meta tag and CSS media queries change columns, font sizes and buttons to fit narrow screens. Radio options are stacked on phones and displayed in two columns on wider screens.

**Q12. How is this project connected to the syllabus?**  
It combines the online-exam JavaScript experiment, HTML form and validation experiments, PHP form processing, PHP/MySQL connectivity and web-app deployment experiments listed in the S5 Web Programming Lab syllabus.

## 8. Demonstration order

1. Open the site home page.
2. Sign in as a teacher.
3. Create an exam and note its ID/password.
4. Add two or three MCQs with known answer keys.
5. Open a second browser or private window and sign in as a student.
6. Submit a mix of correct and wrong answers and explain the calculated score.
7. Log back in as teacher and show the results table.
8. Show one anti-cheating event and explain the browser limitations honestly.
