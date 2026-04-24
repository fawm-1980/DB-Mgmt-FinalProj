<?php
require_once __DIR__ . '/includes/security.php';
include 'db_connect.php';
require_post();
verify_csrf();
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
    if (!isset($_POST['keyword']) || trim($_POST['keyword']) === '') {
        echo "<p>No search keyword provided.</p>";
    } else {
        $keyword = trim($_POST['keyword']);
        $searchTerm = "%" . $keyword . "%";

        $stmt = $conn->prepare("SELECT PostID, Title, Content FROM BlogPosts WHERE Title LIKE ? OR Content LIKE ?");

        if ($stmt) {
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();

            echo "<p>Results for: <strong>" . escape_html($keyword) . "</strong></p>";

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<hr>";
                    echo "<h2>" . escape_html($row["Title"]) . "</h2>"; 
                    echo "<p><strong>Post ID:</strong> " . escape_html((string)$row["PostID"]) . "</p>";
                    echo "<p>" . nl2br(escape_html($row["Content"])) . "</p>";
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