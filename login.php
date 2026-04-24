<?php
require_once __DIR__ . '/includes/security.php';
include 'db_connect.php';
require_post();
verify_csrf();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Result</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <h1>Login Result</h1>

<?php
if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
} elseif (
    !isset($_POST['username'], $_POST['password']) ||
    trim($_POST['username']) === '' ||
    trim($_POST['password']) === ''
) {
    echo "<p>Username and password are required.</p>";
} else {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("
        SELECT UserID, Username, PasswordHash, MFAEnabled
        FROM Users
        WHERE Username = ?
    ");

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $user = $result->fetch_assoc()) {
        
            if (empty($user['PasswordHash'])) {
                session_regenerate_id(true);
                
                $_SESSION['userid'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['password_set'] = false;
                $_SESSION['mfa_verified'] = false;

                redirect('account_setup_required.php');

            } elseif (password_verify($password, $user['PasswordHash'])) {

                session_regenerate_id(true);

                $_SESSION['userid'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['password_set'] = true;

                // MFA handling (future)
                if ((int)$user['MFAEnabled'] === 1) {
                    $_SESSION['mfa_verified'] = false;
                    redirect('mfa_verify.php');
                } else {
                    $_SESSION['mfa_verified'] = false;
                    redirect('account_setup_required.php');
                }

            } else {
                echo "<p>Invalid username or password.</p>";
            }

        } else {
            echo "<p>Invalid username or password.</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Error preparing login request.</p>";
    }

    $conn->close();
}
?>
</body>
</html>