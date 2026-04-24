<?php
declare(strict_types=1);

ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline';");

function escape_html(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): void {
    header("Location: $path");
    exit;
}

function is_logged_in(): bool {
    return !empty($_SESSION['userid']);
}

function require_login(): void {
    if (!is_logged_in()) {
        redirect('login_form.php');
    }
}

function require_fully_verified_user(): void {
    require_login();

    if (
        empty($_SESSION['password_set']) ||
        empty($_SESSION['mfa_verified'])
    ) {
        redirect('account_setup_required.php');
    }
}

function password_policy_errors(string $password): array {
    $errors = [];

    if (strlen($password) < 12) {
        $errors[] = 'Password must be at least 12 characters.';
    }

    $classes = 0;
    $classes += preg_match('/[A-Z]/', $password) ? 1 : 0;
    $classes += preg_match('/[a-z]/', $password) ? 1 : 0;
    $classes += preg_match('/[0-9]/', $password) ? 1 : 0;
    $classes += preg_match('/[^A-Za-z0-9]/', $password) ? 1 : 0;

    if ($classes < 3) {
        $errors[] = 'Password must include at least 3 of these: uppercase, lowercase, number, symbol.';
    }

    return $errors;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . escape_html(csrf_token()) . '">';
}

function verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(403);
        exit('Invalid request.');
    }
}

function require_post(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Method not allowed.');
    }
}

function generate_backup_code(): string {
    return strtoupper(bin2hex(random_bytes(5)));
}

function require_password_set(): void {
    require_login();

    if (empty($_SESSION['password_set'])) {
        redirect('reset_password_required_form.php');
    }
}