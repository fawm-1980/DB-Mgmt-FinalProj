<?php
require_once __DIR__ . '/includes/security.php';
require_fully_verified_user();
include 'db_connect.php';
require_post();
verify_csrf();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | Post Result</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    $tagIds = $_POST['tag_ids'] ?? [];

    if (!is_array($tagIds)) {
        $tagIds = [];
    }

    $cleanTagIds = [];

    foreach ($tagIds as $tagId) {
        if (ctype_digit((string)$tagId)) {
            $cleanTagIds[] = (int)$tagId;
        }
    }
    $cleanTagIds = array_unique($cleanTagIds);

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            INSERT INTO BlogPosts (UserID, Title, PostContent, CategoryID)
            VALUES (?, ?, ?, ?)
        ");

        if (!$stmt) {
            throw new Exception("Error preparing post insert.");
        }

        $stmt->bind_param("issi", $userid, $title, $content, $categoryid);

        if (!$stmt->execute()) {
            throw new Exception("Error creating post.");
        }

        $postId = $conn->insert_id;
        $stmt->close();

        if (!empty($cleanTagIds)) {
            $tagStmt = $conn->prepare("
                INSERT INTO PostTags (PostID, TagID)
                VALUES (?, ?)
            ");

            if (!$tagStmt) {
                throw new Exception("Error preparing tag insert.");
            }

            foreach ($cleanTagIds as $tagId) {
                $tagStmt->bind_param("ii", $postId, $tagId);

                if (!$tagStmt->execute()) {
                    throw new Exception("Error adding tag to post.");
                }
            }

            $tagStmt->close();
        }

        $conn->commit();

        echo "<div id='toast' class='toast'>Post created successfully</div>";
        echo "<p><strong>Title:</strong> " . escape_html($title) . "</p>";
        echo "<p><strong>Posted by:</strong> " . escape_html($_SESSION['username']) . "</p>";

    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        echo "<p>Error creating post.</p>";
    }

    $conn->close();
}
?>
<script src="scripts.js"></script>
</body>
</html>