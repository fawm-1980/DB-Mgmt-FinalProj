<?php
require_once __DIR__ . '/includes/security.php';
include 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Results</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <h1>Search Results</h1>

<?php
if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
} else {
    $keyword = trim($_GET['keyword'] ?? '');
    $categoryID = (int)($_GET['category_id'] ?? 0);
    $tagID = (int)($_GET['tag_id'] ?? 0);

    echo "<p><a href=\"post_search_form.php?keyword=" . urlencode($keyword) .
        "&category_id=" . urlencode((string)$categoryID) .
        "&tag_id=" . urlencode((string)$tagID) .
        "\">Modify Search</a></p>";

    if ($keyword === '' && $categoryID === 0 && $tagID === 0) {
        echo "<p>Please enter a keyword or choose a category/tag.</p>";
    } else {
        echo "<h2>Active Filters</h2>";
        echo "<ul>";

        if ($keyword !== '') {
            echo "<li><strong>Keyword:</strong> " . escape_html($keyword) . "</li>";
        }

        if ($categoryID > 0) {
            $catName = 'Unknown category';

            $catStmt = $conn->prepare("SELECT CategoryName FROM Categories WHERE CategoryID = ?");
            if ($catStmt) {
                $catStmt->bind_param("i", $categoryID);
                $catStmt->execute();
                $catResult = $catStmt->get_result();

                if ($catRow = $catResult->fetch_assoc()) {
                    $catName = $catRow['CategoryName'];
                }

                $catStmt->close();
            }

            echo "<li><strong>Category:</strong> " . escape_html($catName) . "</li>";
        }

        if ($tagID > 0) {
            $tagName = 'Unknown tag';

            $selectedTagStmt = $conn->prepare("SELECT TagName FROM Tags WHERE TagID = ?");
            if ($selectedTagStmt) {
                $selectedTagStmt->bind_param("i", $tagID);
                $selectedTagStmt->execute();
                $selectedTagResult = $selectedTagStmt->get_result();

                if ($tagRow = $selectedTagResult->fetch_assoc()) {
                    $tagName = $tagRow['TagName'];
                }

                $selectedTagStmt->close();
            }

            echo "<li><strong>Tag:</strong> " . escape_html($tagName) . "</li>";
        }

        echo "</ul>";

        $sql = "
            SELECT DISTINCT
                p.PostID,
                p.Title,
                p.PostContent,
                p.PostCreatedAt,
                u.Username,
                c.CategoryName
            FROM BlogPosts p
            JOIN Users u ON p.UserID = u.UserID
            JOIN Categories c ON p.CategoryID = c.CategoryID
            LEFT JOIN PostTags pt ON p.PostID = pt.PostID
            WHERE 1 = 1
        ";

        $types = "";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (p.Title LIKE ? OR p.PostContent LIKE ?)";
            $searchTerm = "%" . $keyword . "%";
            $types .= "ss";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if ($categoryID > 0) {
            $sql .= " AND p.CategoryID = ?";
            $types .= "i";
            $params[] = $categoryID;
        }

        if ($tagID > 0) {
            $sql .= " AND pt.TagID = ?";
            $types .= "i";
            $params[] = $tagID;
        }

        $sql .= " ORDER BY p.PostID DESC";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

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

            $stmt->execute();
            $result = $stmt->get_result();

            echo "<p><strong>" . $result->num_rows . " result(s) found</strong></p>";

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $formattedDate = $row["PostCreatedAt"]
                        ? date("M d, Y H:i", strtotime($row["PostCreatedAt"]))
                        : "Unknown";

                    echo "<hr>";
                    echo "<h2>" . escape_html($row["Title"]) . "</h2>";
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
                echo "<p>No posts matched those filters. Try broadening your search.</p>";
            }

            if ($tagStmt) {
                $tagStmt->close();
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