<?php
require_once __DIR__ . '/includes/security.php';
require_password_set();
include 'nav.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>MFA Verification</title>
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