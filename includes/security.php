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
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline';");

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

function generate_base32_secret(int $length = 32): string {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';

    for ($i = 0; $i < $length; $i++) {
        $secret .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }

    return $secret;
}

function base32_decode_custom(string $base32): string {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $base32 = strtoupper(str_replace('=', '', $base32));
    $bits = '';

    for ($i = 0; $i < strlen($base32); $i++) {
        $value = strpos($alphabet, $base32[$i]);

        if ($value === false) {
            continue;
        }

        $bits .= str_pad(decbin($value), 5, '0', STR_PAD_LEFT);
    }

    $binary = '';

    for ($i = 0; $i + 8 <= strlen($bits); $i += 8) {
        $binary .= chr(bindec(substr($bits, $i, 8)));
    }

    return $binary;
}

function generate_totp_code(string $secret, ?int $timeSlice = null): string {
    if ($timeSlice === null) {
        $timeSlice = (int) floor(time() / 30);
    }

    $secretKey = base32_decode_custom($secret);
    $time = pack('N*', 0) . pack('N*', $timeSlice);

    $hash = hash_hmac('sha1', $time, $secretKey, true);
    $offset = ord(substr($hash, -1)) & 0x0F;

    $binary =
        ((ord($hash[$offset]) & 0x7F) << 24) |
        ((ord($hash[$offset + 1]) & 0xFF) << 16) |
        ((ord($hash[$offset + 2]) & 0xFF) << 8) |
        (ord($hash[$offset + 3]) & 0xFF);

    return str_pad((string)($binary % 1000000), 6, '0', STR_PAD_LEFT);
}

function verify_totp_code(string $secret, string $code): bool {
    if (!preg_match('/^\d{6}$/', $code)) {
        return false;
    }

    $currentSlice = (int) floor(time() / 30);

    for ($i = -1; $i <= 1; $i++) {
        if (hash_equals(generate_totp_code($secret, $currentSlice + $i), $code)) {
            return true;
        }
    }

    return false;
}

function require_password_set(): void {
    require_login();

    if (empty($_SESSION['password_set'])) {
        redirect('reset_password_required_form.php');
    }
}

function is_admin(): bool {
    return isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 2;
}

function require_admin_user(): void {
    require_fully_verified_user();

    if (!is_admin()) {
        http_response_code(403);
        exit('Access denied.');
    }
}

function build_otpauth_uri(string $accountName, string $secret, string $issuer = 'Galactic Blog Terminal'): string {
    $label = rawurlencode($issuer . ':' . $accountName);
    $issuerEncoded = rawurlencode($issuer);

    return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuerEncoded}&algorithm=SHA1&digits=6&period=30";
}