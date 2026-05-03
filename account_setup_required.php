<?php
require_once __DIR__ . '/includes/security.php';
require_login();
include 'nav.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Account Setup Required</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
</head>
<body>
    <h1>Account Setup Required</h1>

    <p>Your account must have a password and multi-factor authentication enabled before you can create posts or access account features.</p>

    <ul>
        <li><a href="reset_password_required_form.php">Set or reset your password</a></li>
        <li><a href="mfa_setup.php">Set up multi-factor authentication</a></li>
    </ul>
</body>
</html>