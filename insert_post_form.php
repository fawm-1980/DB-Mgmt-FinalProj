<?php
require_once __DIR__ . '/includes/security.php';
require_fully_verified_user();
require __DIR__ . '/db_connect.php';
include 'nav.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | Add Post</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>Add New Blog Post</h1>

    <p><strong>Posting as:</strong> <?php echo escape_html($_SESSION['username']); ?></p>

    <?php if (!$db_connected): ?>
        <p><em>Server unavailable.</em></p>
    <?php else: ?>
        <form action="insert_post.php" method="post">
            <?php echo csrf_field(); ?>

            <label for="categoryid">Category:</label><br>
            <select id="categoryid" name="categoryid" required>
                <option value="">Select a category</option>
                <?php
                $categoryResult = $conn->query("SELECT CategoryID, CategoryName FROM Categories ORDER BY CategoryName");

                while ($category = $categoryResult->fetch_assoc()) {
                    echo "<option value=\"" . escape_html((string)$category["CategoryID"]) . "\">" .
                        escape_html($category["CategoryName"]) .
                        "</option>";
                }
                ?>
            </select><br><br>

            <label for="tag_ids">Tags:</label><br>
            <select id="tag_ids" name="tag_ids[]" multiple size="6">
                <?php
                $tagResult = $conn->query("SELECT TagID, TagName FROM Tags ORDER BY TagName");

                while ($tag = $tagResult->fetch_assoc()) {
                    echo "<option value=\"" . escape_html((string)$tag["TagID"]) . "\">" .
                        escape_html($tag["TagName"]) .
                        "</option>";
                }
                ?>
            </select>
            <p><small>Hold Ctrl on Windows or Command on Mac to select multiple tags.</small></p>
            <br>

            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" placeholder="Enter post title" required><br><br>

            <label for="content">Content:</label><br>
            <textarea id="content" name="content" required></textarea><br><br>

            <input type="submit" value="Submit">
        </form>
    <?php endif; ?>

<?php
if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
    $conn->close();
}
?>

</body>
</html>