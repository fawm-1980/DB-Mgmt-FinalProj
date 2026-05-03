<?php
require_once __DIR__ . '/includes/security.php';
include 'nav.php';
require __DIR__ . '/db_connect.php';

$keyword = trim($_GET['keyword'] ?? '');
$selectedCategoryID = (int)($_GET['category_id'] ?? 0);
$selectedTagID = (int)($_GET['tag_id'] ?? 0);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | Search</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <h1>Search Blog Posts</h1>

    <form action="post_search.php" method="get">
        <label for="keyword">Search Keyword:</label><br>
        <input type="text" id="keyword" name="keyword" value="<?php echo escape_html($keyword); ?>"><br><br>

        <label for="category">Category:</label><br>
        <select id="category" name="category_id">
            <option value="">-- Any Category --</option>
            <?php
            if ($db_connected) {
                $catResult = $conn->query("SELECT CategoryID, CategoryName FROM Categories ORDER BY CategoryName");

                while ($cat = $catResult->fetch_assoc()) {
                    $categoryID = (int)$cat["CategoryID"];
                    $selected = ($categoryID === $selectedCategoryID) ? " selected" : "";

                    echo "<option value=\"" . $categoryID . "\"" . $selected . ">" .
                        escape_html($cat["CategoryName"]) . "</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="tag">Tag:</label><br>
        <select id="tag" name="tag_id">
            <option value="">-- Any Tag --</option>
            <?php
            if ($db_connected) {
                $tagResult = $conn->query("SELECT TagID, TagName FROM Tags ORDER BY TagName");

                while ($tag = $tagResult->fetch_assoc()) {
                    $tagID = (int)$tag["TagID"];
                    $selected = ($tagID === $selectedTagID) ? " selected" : "";

                    echo "<option value=\"" . $tagID . "\"" . $selected . ">" .
                        escape_html($tag["TagName"]) . "</option>";
                }
            }
            ?>
        </select><br><br>

        <input type="submit" value="Search">
    </form>

<?php
if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
    $conn->close();
}
?>

</body>
</html>