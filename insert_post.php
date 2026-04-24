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
    <title>Add New Post Result</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <h1>Add New Post Result</h1>

<?php
if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
} elseif (
    !isset($_POST['categoryid'], $_POST['title'], $_POST['content']) ||
    trim($_POST['categoryid']) === '' ||
    trim($_POST['title']) === '' ||
    trim($_POST['content']) === ''
) {
    echo "<p>Category, title, and content are required.</p>";
} elseif (!ctype_digit($_POST['categoryid'])) {
    echo "<p>Category ID must be numeric.</p>";
} else {
    $categoryid = (int) $_POST['categoryid'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $userid = (int) $_SESSION['userid'];

    $stmt = $conn->prepare("INSERT INTO BlogPosts (UserID, Title, Content, CategoryID) VALUES (?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("issi", $userid, $title, $content, $categoryid);

        if ($stmt->execute()) {
            echo "<p>New post created successfully.</p>";
            echo "<p><strong>Title:</strong> " . escape_html($title) . "</p>";
            echo "<p><strong>Posted by:</strong> " . escape_html($_SESSION['username']) . "</p>";
        } else {
            echo "<p>Error creating post.</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Error preparing request.</p>";
    }

    $conn->close();
}
?>
</body>
</html>