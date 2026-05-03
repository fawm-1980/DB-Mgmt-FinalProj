<?php
require_once __DIR__ . '/includes/security.php';
require_password_set();
include 'db_connect.php';

require_post();
verify_csrf();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | MFA Result</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
</head>
<body>
<?php include 'nav.php'; ?>

<h1>MFA Verification Result</h1>

<?php
$code = trim($_POST['code'] ?? '');

if ($code === '') {
    echo "<p>MFA code is required.</p>";
    exit;
}

if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
    exit;
}

/*
    Placeholder verification:
    Real TOTP verification will be added later once we confirm Composer/library support.
    For now, use 000000 as the temporary test code.
*/

if ($code === '000000') {
    $userID = (int) $_SESSION['userid'];

    $stmt = $conn->prepare("
        UPDATE Users
        SET MFAEnabled = 1,
            UpdatedAt = NOW()
        WHERE UserID = ?
    ");

    if ($stmt) {
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $stmt->close();
    }

    $_SESSION['mfa_verified'] = true;

    echo "<p>MFA verified successfully.</p>";
    echo "<p><a href=\"insert_post_form.php\">Continue to create a post</a></p>";
} else {
    echo "<p>Invalid MFA code.</p>";
}

$conn->close();
?>
</body>
</html>