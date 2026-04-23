<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Results</title>
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

    <h1>Search Results</h1>

<?php
if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
} else {
    if (!isset($_GET['keyword']) || trim($_GET['keyword']) === '') {
        echo "<p>No search keyword provided.</p>";
    } else {
        $keyword = trim($_GET['keyword']);
        $searchTerm = "%" . $keyword . "%";

        $stmt = $conn->prepare("SELECT PostID, Title, Content FROM BlogPosts WHERE Title LIKE ? OR Content LIKE ?");

        if ($stmt) {
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();

            echo "<p>Results for: <strong>" . htmlspecialchars($keyword) . "</strong></p>";

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<hr>";
                    echo "<h2>" . htmlspecialchars($row["Title"]) . "</h2>";
                    echo "<p><strong>Post ID:</strong> " . htmlspecialchars($row["PostID"]) . "</p>";
                    echo "<p>" . nl2br(htmlspecialchars($row["Content"])) . "</p>";
                }
            } else {
                echo "<p>No results found.</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Error preparing search request.</p>";
        }
    }

    $conn->close();
}
?>

</body>
</html>