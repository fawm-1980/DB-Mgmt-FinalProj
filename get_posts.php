<?php
require_once __DIR__ . '/includes/security.php';
include 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Blog Posts</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <h1>All Blog Posts</h1>

<?php
if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
} else {
    $sql = "SELECT PostID, Title, Content FROM BlogPosts";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<hr>";
            echo "<h2>" . escape_html($row["Title"]) . "</h2>";
            echo "<p><strong>Post ID:</strong> " . escape_html((string)$row["PostID"]) . "</p>";
            echo "<p>" . nl2br(escape_html($row["Content"])) . "</p>";
        }
    } else {
        echo "<p><em>No posts available.</em></p>";
    }

    $conn->close();
}
?>
</body>
</html>