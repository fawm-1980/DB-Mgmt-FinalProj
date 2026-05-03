<?php
require_once __DIR__ . '/includes/security.php';
require_password_set();
include 'db_connect.php';
include 'nav.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | MFA Setup</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
</head>
<body>
    <h1>Multi-Factor Authentication Setup</h1>

<?php
if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
    exit;
}

$userID = (int) $_SESSION['userid'];

// Generate a placeholder secret for now
$secret = bin2hex(random_bytes(10));

// Store it (temporary, will replace with real TOTP later)
$stmt = $conn->prepare("
    UPDATE Users
    SET MFASecret = ?
    WHERE UserID = ?
");

if ($stmt) {
    $stmt->bind_param("si", $secret, $userID);
    $stmt->execute();
    $stmt->close();
}

echo "<p>Your MFA secret has been generated.</p>";
echo "<p><strong>Secret:</strong> " . escape_html($secret) . "</p>";

echo "<p>This will later be converted into a QR code for authenticator apps.</p>";

echo "<p><a href=\"mfa_verify.php\">Continue to verification</a></p>";

$conn->close();
?>
</body>
</html>