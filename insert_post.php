<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Post Result</title>
</head>
<body>
    <nav>
        <a href="index.php">Home</a> |
        <a href="login.html">Login</a> |
        <a href="get_posts.php">View Posts</a> |
        <a href="insert_post.html">Create Post</a> |
        <a href="post_search.html">Search Posts</a>
    </nav>
    <hr>

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

    if (isset($_SESSION['userid']) && ctype_digit((string) $_SESSION['userid'])) {
        $userid = (int) $_SESSION['userid'];
    } elseif (isset($_POST['userid']) && ctype_digit($_POST['userid'])) {
        $userid = (int) $_POST['userid'];
    } else {
        $userid = null;
    }

    if ($userid === null) {
        echo "<p>A valid user ID is required.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO BlogPosts (UserID, Title, Content, CategoryID) VALUES (?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("issi", $userid, $title, $content, $categoryid);

            if ($stmt->execute()) {
                echo "<p>New post created successfully.</p>";
                echo "<p><strong>Title:</strong> " . htmlspecialchars($title) . "</p>";
                echo "<p><strong>User ID:</strong> " . htmlspecialchars((string) $userid) . "</p>";
            } else {
                echo "<p>Error creating post.</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Error preparing request.</p>";
        }
    }

    $conn->close();
}
?>
</body>
</html>