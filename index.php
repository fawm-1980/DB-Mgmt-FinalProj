<?php
require_once __DIR__ . '/includes/security.php';
include 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | Home</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    $sql = "
        SELECT
            p.PostID,
            p.Title,
            p.PostContent,
            p.PostCreatedAt,
            u.Username,
            c.CategoryName
        FROM BlogPosts p
        JOIN Users u ON p.UserID = u.UserID
        JOIN Categories c ON p.CategoryID = c.CategoryID
        ORDER BY p.PostID DESC
        LIMIT 10
    ";

    $result = $conn->query($sql);

    $tagStmt = $conn->prepare("
        SELECT t.TagName
        FROM PostTags pt
        JOIN Tags t ON pt.TagID = t.TagID
        WHERE pt.PostID = ?
        ORDER BY t.TagName
    ");

    if ($tagStmt) {
        $postId = 0;
        $tagStmt->bind_param("i", $postId);
    }

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $formattedDate = $row["PostCreatedAt"]
                ? date("M d, Y H:i", strtotime($row["PostCreatedAt"]))
                : "Unknown";

            echo "<hr>";
            echo "<h3>" . escape_html($row["Title"]) . "</h3>";

            echo "<p><strong>By:</strong> " . escape_html($row["Username"]) .
                " | <strong>Category:</strong> " . escape_html($row["CategoryName"]) .
                " | <strong>Posted:</strong> " . escape_html($formattedDate) . "</p>";

            if ($tagStmt) {
                $postId = (int)$row["PostID"];
                $tagStmt->execute();
                $tagResult = $tagStmt->get_result();

                $tags = [];

                while ($tagRow = $tagResult->fetch_assoc()) {
                    $tags[] = escape_html($tagRow["TagName"]);
                }

                if (!empty($tags)) {
                    echo "<p><strong>Tags:</strong> " . implode(", ", $tags) . "</p>";
                }
            }

            echo "<p>" . nl2br(escape_html($row["PostContent"])) . "</p>";
        }
    } else {
        echo "<p><em>No posts available.</em></p>";
    }

    if ($tagStmt) {
        $tagStmt->close();
    }

    $conn->close();
}
?>

</body>
</html>