<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Blog Posts</title>
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
            echo "<h2>" . htmlspecialchars($row["Title"]) . "</h2>";
            echo "<p><strong>Post ID:</strong> " . htmlspecialchars($row["PostID"]) . "</p>";
            echo "<p>" . nl2br(htmlspecialchars($row["Content"])) . "</p>";
        }
    } else {
        echo "<p><em>No posts available.</em></p>";
    }

    $conn->close();
}
?>
</body>
</html>