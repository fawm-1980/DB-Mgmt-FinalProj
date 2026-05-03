<?php
require_once __DIR__ . '/includes/security.php';
include 'db_connect.php';

require_post();
verify_csrf();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | Create Account Result</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<?php include 'nav.php'; ?>

<h1>Create Account Result</h1>

<?php
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($username === '' || $email === '' || $password === '' || $confirm === '') {
    echo "<p>All fields are required.</p>";
    exit;
}

if ($password !== $confirm) {
    echo "<p>Passwords do not match.</p>";
    exit;
}

// Password policy check
$errors = password_policy_errors($password);
if (!empty($errors)) {
    foreach ($errors as $e) {
        echo "<p>" . escape_html($e) . "</p>";
    }
    exit;
}

if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
    exit;
}

// Hash password (bcrypt)
$passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $conn->prepare("
    INSERT INTO Users
    (Username, Email, PasswordHash, PasswordSet, MustResetPassword, IsActive, MFAEnabled, RoleID)
    VALUES (?, ?, ?, 1, 0, 1, 0, 1)
");

if ($stmt) {
    $stmt->bind_param("sss", $username, $email, $passwordHash);

    if ($stmt->execute()) {
        echo "<p>Account created successfully.</p>";
    } else {
        echo "<p>Error creating account.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Error preparing request.</p>";
}

$conn->close();
?>
</body>
</html>