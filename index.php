<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blog Platform</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <h1>Blog Platform</h1>
    <p>Welcome to the blog platform. Use the links above to navigate.</p>

<h2>10 Most Recent Posts</h2>

<?php
if (!$db_connected) {
    echo "<p><em>Server unavailable. Please try again later.</em></p>";
} else {
    $sql = "SELECT PostID, Title, Content
            FROM BlogPosts
            ORDER BY PostID DESC
            LIMIT 10";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<hr>";
            echo "<h3>" . htmlspecialchars($row["Title"]) . "</h3>";
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