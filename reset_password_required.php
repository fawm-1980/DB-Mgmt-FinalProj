<?php
require_once __DIR__ . '/includes/security.php';
require_login();
include 'db_connect.php';

require_post();
verify_csrf();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | Password Reset Result</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
</head>
<body>
<?php include 'nav.php'; ?>

<h1>Password Reset Result</h1>

<?php
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($password === '' || $confirm === '') {
    echo "<p>Password and confirmation are required.</p>";
    exit;
}

if ($password !== $confirm) {
    echo "<p>Passwords do not match.</p>";
    exit;
}

$errors = password_policy_errors($password);

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>" . escape_html($error) . "</p>";
    }
    exit;
}

if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
    exit;
}

$userID = (int) $_SESSION['userid'];
$passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $conn->prepare("
    UPDATE Users
    SET PasswordHash = ?,
        PasswordSet = 1,
        MustResetPassword = 0,
        UpdatedAt = NOW()
    WHERE UserID = ?
");

if ($stmt) {
    $stmt->bind_param("si", $passwordHash, $userID);

    if ($stmt->execute()) {
        $_SESSION['password_set'] = true;

        echo "<p>Password updated successfully.</p>";
        echo "<p><a href=\"mfa_setup.php\">Continue to MFA setup</a></p>";
    } else {
        echo "<p>Error updating password.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Error preparing password update.</p>";
}

$conn->close();
?>
</body>
</html>