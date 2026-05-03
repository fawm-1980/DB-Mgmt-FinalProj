<?php
require_once __DIR__ . '/includes/security.php';
require_admin_user();
require __DIR__ . '/db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['new_tag'] ?? '');

    if ($name === '') {
        $message = 'Tag name is required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO Tags (TagName) VALUES (?)");

        if ($stmt) {
            $stmt->bind_param("s", $name);

            if ($stmt->execute()) {
                $message = 'Tag added successfully.';
            } else {
                $message = 'Could not add tag.';
            }

            $stmt->close();
        } else {
            $message = 'Error preparing request.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Galactic Blog Terminal | Manage Tags</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="/favicon.ico">
</head>
<body>
<?php include 'nav.php'; ?>

<h1>Manage Tags</h1>

<?php if ($message !== ''): ?>
    <p><?php echo escape_html($message); ?></p>
<?php endif; ?>

<h2>Add Tag</h2>
<form method="post">
    <?php echo csrf_field(); ?>
    <label for="new_tag">Tag Name:</label><br>
    <input type="text" id="new_tag" name="new_tag" required>
    <input type="submit" value="Add Tag">
</form>

<h2>Existing Tags</h2>

<?php
if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
} else {
    $result = $conn->query("SELECT TagID, TagName FROM Tags ORDER BY TagName");

    if ($result && $result->num_rows > 0) {
        echo "<ul>";

        while ($row = $result->fetch_assoc()) {
            echo "<li>" . escape_html($row['TagName']) . "</li>";
        }

        echo "</ul>";
    } else {
        echo "<p>No tags found.</p>";
    }

    $conn->close();
}
?>

</body>
</html>