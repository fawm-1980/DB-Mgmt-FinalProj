<?php
session_start();
include 'db_connect.php';
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

    $stmt = $conn->prepare("SELECT UserID, Username, Password FROM Users WHERE Username = ?");

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (!empty($user['Password']) && password_verify($password, $user['Password'])) {
                session_regenerate_id(true);
                $_SESSION['userid'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];

                echo "<p>Login successful.</p>";
                echo "<p>Welcome, <strong>" . htmlspecialchars($user['Username']) . "</strong>.</p>";
                echo "<p><a href=\"insert_post.php\">Create a new post</a></p>";
                echo "<p><a href=\"index.php\">Return to home page</a></p>";
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