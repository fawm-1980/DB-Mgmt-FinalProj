<?php
require_once __DIR__ . '/includes/security.php';
require_password_set();
include 'nav.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | MFA Verification</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
</head>
<body>
    <h1>Verify Multi-Factor Authentication</h1>

    <form action="mfa_verify_process.php" method="post">
        <?php echo csrf_field(); ?>

        <label for="code">Enter 6-digit code:</label><br>
        <input type="text" id="code" name="code" required><br>

        <input type="submit" value="Verify">
    </form>
</body>
</html>