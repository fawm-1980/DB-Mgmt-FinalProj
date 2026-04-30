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
    <title>Add Comment Result</title>
</head>
<body>
<?php include 'nav.php'; ?>

<h1>Add Comment Result</h1>

<?php
if (empty($_SESSION['userid'])) {
    echo "<p>You must be logged in to comment.</p>";
    exit;
}

if (empty($_SESSION['mfa_verified'])) {
    echo "<p>You must complete MFA verification before commenting.</p>";
    exit;
}

$postID = (int)($_POST['post_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($postID <= 0 || $comment === '') {
    echo "<p>Post and comment are required.</p>";
    exit;
}

if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
    exit;
}

$userID = (int)$_SESSION['userid'];

$stmt = $conn->prepare("
    INSERT INTO Comments (
        UserID,
        PostID,
        CommentContent
    )
    VALUES (?, ?, ?)
");

if ($stmt) {
    $stmt->bind_param("iis", $userID, $postID, $comment);

    if ($stmt->execute()) {
        echo "<p>Comment added successfully.</p>";
        echo "<p><a href=\"get_posts.php\">Return to posts</a></p>";
    } else {
        echo "<p>Error adding comment.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Error preparing comment request.</p>";
}

$conn->close();
?>
</body>
</html>