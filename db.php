<?php
declare(strict_types=1);

// Default settings for a standard XAMPP installation. Change DB_PASS if needed.
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'online_viva';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    http_response_code(500);
    exit(
        'Database connection failed. Start MySQL in XAMPP, check db.php settings, '
        . 'and import database.sql into the online_viva database.'
    );
}
