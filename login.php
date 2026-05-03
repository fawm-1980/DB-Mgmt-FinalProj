<?php
require_once __DIR__ . '/includes/security.php';
include 'db_connect.php';
require_post();
verify_csrf();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | Login Result</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        SELECT UserID, Username, PasswordHash, PasswordSet, MustResetPassword, MFAEnabled,
            FailedLoginCount, LockedUntil, IsActive, RoleID
        FROM Users
        WHERE Username = ?
    ");

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $user = $result->fetch_assoc()) {
            if (
                !empty($user['LockedUntil']) &&
                strtotime($user['LockedUntil']) > time()
            ) {
                echo "<p>Account is temporarily locked. Please try again later.</p>";
                $stmt->close();
                $conn->close();
                exit;
            }

            if ((int)$user['IsActive'] !== 1) {
                echo "<p>Account is inactive.</p>";
                $stmt->close();
                $conn->close();
                exit;
            }

            if (empty($user['PasswordHash'])) {
                session_regenerate_id(true);
                
                $_SESSION['userid'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['role_id'] = (int)$user['RoleID'];
                $_SESSION['password_set'] = false;
                $_SESSION['mfa_verified'] = false;

                redirect('account_setup_required.php');

            } elseif (password_verify($password, $user['PasswordHash'])) {

                session_regenerate_id(true);

                $resetStmt = $conn->prepare("
                    UPDATE Users
                    SET FailedLoginCount = 0,
                        LockedUntil = NULL
                    WHERE UserID = ?
                ");

                if ($resetStmt) {
                    $resetStmt->bind_param("i", $user['UserID']);
                    $resetStmt->execute();
                    $resetStmt->close();
                }

                $_SESSION['userid'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['role_id'] = (int)$user['RoleID'];
                $_SESSION['password_set'] = ((int)$user['PasswordSet'] === 1);
                $_SESSION['mfa_verified'] = false;

                if ((int)$user['MustResetPassword'] === 1 || (int)$user['PasswordSet'] !== 1) {
                    redirect('reset_password_required_form.php');
                }

                // MFA handling (future)
                if ((int)$user['MFAEnabled'] === 1) {
                    $_SESSION['mfa_verified'] = false;
                    redirect('mfa_verify.php');
                } else {
                    $_SESSION['mfa_verified'] = false;
                    redirect('account_setup_required.php');
                }

            } else {
                $failStmt = $conn->prepare("
                    UPDATE Users
                    SET FailedLoginCount = FailedLoginCount + 1,
                        LockedUntil = CASE
                            WHEN FailedLoginCount + 1 >= 5
                            THEN DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                            ELSE LockedUntil
                        END
                    WHERE UserID = ?
                ");

                if ($failStmt) {
                    $failStmt->bind_param("i", $user['UserID']);
                    $failStmt->execute();
                    $failStmt->close();
                }

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