<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/db.php';

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function set_flash(string $message): void
{
    $_SESSION['flash_message'] = $message;
}

function take_flash(): ?string
{
    if (!isset($_SESSION['flash_message'])) {
        return null;
    }
    $message = (string)$_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
    return $message;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    if (!is_string($submitted) || !hash_equals(csrf_token(), $submitted)) {
        http_response_code(403);
        exit('Invalid form token. Please return to the page and try again.');
    }
}

function require_role(string $role): void
{
    if (($_SESSION['role'] ?? '') !== $role) {
        set_flash('Please sign in to continue.');
        redirect($role === 'teacher' ? 'teacher_login.php' : 'student_login.php');
    }
}
