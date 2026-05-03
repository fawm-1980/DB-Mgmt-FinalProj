<?php
require_once __DIR__ . '/includes/security.php';
require_login();
include 'nav.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | Set Password</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>Set Your Password</h1>

    <form action="reset_password_required.php" method="post">
        <?php echo csrf_field(); ?>

        <label for="password">New Password:</label><br>
        <input type="password" id="password" name="password" required><br>

        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br>

        <input type="submit" value="Set Password">
    </form>
</body>
</html>