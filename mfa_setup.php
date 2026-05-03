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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>Multi-Factor Authentication Setup</h1>

<?php
if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
    exit;
}

$userID = (int) $_SESSION['userid'];

$stmt = $conn->prepare("
    SELECT Username, MFASecret
    FROM Users
    WHERE UserID = ?
");

$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<p>User not found.</p>";
    exit;
}

$username = $user['Username'];
$secret = $user['MFASecret'];

// Only generate a new secret if one does not already exist
if (empty($secret)) {
    $secret = generate_base32_secret();

    $stmt = $conn->prepare("
        UPDATE Users
        SET MFASecret = ?
        WHERE UserID = ?
    ");

    if ($stmt) {
        $stmt->bind_param("si", $secret, $userID);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "<p>Unable to prepare MFA setup.</p>";
        exit;
    }
}

$otpauthUri = build_otpauth_uri($username, $secret);

echo "<p>Your MFA secret has been generated.</p>";

echo "<p>Scan this QR code with Google Authenticator:</p>";
echo "<div id=\"qrcode\" style=\"width:200px; height:200px; background:white; padding:10px;\"></div>";

echo "<input type=\"hidden\" id=\"otpauth-uri\" value=\"" . escape_html($otpauthUri) . "\">";

echo "<p>Or enter this setup key manually:</p>";
echo "<p><strong>Secret:</strong> " . escape_html($secret) . "</p>";

echo "<script src=\"js/qrcode.min.js\"></script>";
echo "<script src=\"js/mfa_qr.js\"></script>";

echo "<p><a href=\"mfa_verify.php\">Continue to verification</a></p>";

$conn->close();
?>
</body>
</html>