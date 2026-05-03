<?php
require_once __DIR__ . '/includes/security.php';
include 'nav.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | User Login</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
</head>
<body>
    <h1>User Login</h1>
    <form action="login.php" method="post">
        <?php echo csrf_field(); ?>
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>