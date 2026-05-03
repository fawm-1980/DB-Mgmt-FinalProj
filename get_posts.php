<?php
require_once __DIR__ . '/includes/security.php';
include 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | All Posts</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
</head>
<body>
    <?php include 'nav.php'; ?>

    <h1>All Blog Posts</h1>

<?php
if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
} else {
    $sql = "SELECT 
        p.PostID,
        p.Title,
        p.PostContent,
        p.PostCreatedAt,
        u.Username,
        c.CategoryName
    FROM BlogPosts p
    JOIN Users u ON p.UserID = u.UserID
    JOIN Categories c ON p.CategoryID = c.CategoryID
    ORDER BY p.PostID DESC";
    $result = $conn->query($sql);

    $tagStmt = $conn->prepare("
        SELECT t.TagName
        FROM PostTags pt
        JOIN Tags t ON pt.TagID = t.TagID
        WHERE pt.PostID = ?
        ORDER BY t.TagName
    ");

    $commentStmt = $conn->prepare("
        SELECT 
            c.CommentContent, 
            u.Username, 
            c.CommentCreatedAt
        FROM Comments c
        JOIN Users u ON c.UserID = u.UserID
        WHERE c.PostID = ?
        ORDER BY c.CommentID ASC
    ");

    if ($tagStmt) {
        $postId = 0;                // declare variable once
        $tagStmt->bind_param("i", $postId);  // bind ONCE
    }

    if ($commentStmt) {
        $commentStmt->bind_param("i", $postId);
    }

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $formattedDate = $row["PostCreatedAt"]
            ? date("M d, Y H:i", strtotime($row["PostCreatedAt"]))
            : "Unknown";

            echo "<div class='post-card'>";
            echo "<h2>" . escape_html($row["Title"]) . "</h2>";
            echo "<p><strong>By:</strong> " . escape_html($row["Username"]) . 
                " | <strong>Category:</strong> " . escape_html($row["CategoryName"]) . 
                " | <strong>Posted:</strong> " . escape_html($formattedDate) . "</p>";

            if ($tagStmt) {
                $postId = $row["PostID"];  // update value
                $tagStmt->execute();
                $tagResult = $tagStmt->get_result();

                $tags = [];
                while ($tagRow = $tagResult->fetch_assoc()) {
                    $tags[] = escape_html($tagRow["TagName"]);
                }

                if (!empty($tags)) {
                    echo "<div class='tags'>";
                    foreach ($tags as $tag) {
                        echo "<span class='tag-badge'>" . $tag . "</span> ";
                    }
                    echo "</div>";
                }
            }
            echo "<p>" . nl2br(escape_html($row["PostContent"])) . "</p>";

            if ($commentStmt) {
                $postId = $row["PostID"];
                $commentStmt->execute();
                $commentResult = $commentStmt->get_result();

                if ($commentResult && $commentResult->num_rows > 0) {
                    echo "<h3>Comments</h3>";

                    while ($commentRow = $commentResult->fetch_assoc()) {
                        $commentDate = $commentRow["CommentCreatedAt"]
                            ? date("M d, Y H:i", strtotime($commentRow["CommentCreatedAt"]))
                            : "Unknown";

                        echo "<p><strong>" . escape_html($commentRow["Username"]) . "</strong>";
                        echo " <em>" . escape_html($commentDate) . "</em><br>";
                        echo nl2br(escape_html($commentRow["CommentContent"])) . "</p>";
                    }
                }
            }
            
            if (isset($_SESSION['userid']) && !empty($_SESSION['mfa_verified'])) {
                echo "<form action=\"insert_comment.php\" method=\"post\">";
                echo csrf_field();
                echo "<input type=\"hidden\" name=\"post_id\" value=\"" . escape_html((string)$row["PostID"]) . "\">";
                echo "<textarea name=\"comment\" required></textarea><br>";
                echo "<input type=\"submit\" value=\"Add Comment\">";
                echo "</form>";
            }
            echo "</div>";
        }
    } else {
        echo "<p><em>No posts available.</em></p>";
    }

    if ($tagStmt) {
        $tagStmt->close();
    }

    if ($commentStmt) {
        $commentStmt->close();
    }

    $conn->close();
}
?>
</body>
</html>