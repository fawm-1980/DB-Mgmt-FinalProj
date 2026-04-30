<?php
require_once __DIR__ . '/includes/security.php';
require_admin_user();
require __DIR__ . '/db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['new_category'] ?? '');

    if ($name === '') {
        $message = 'Category name is required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO Categories (CategoryName) VALUES (?)");

        if ($stmt) {
            $stmt->bind_param("s", $name);

            if ($stmt->execute()) {
                $message = 'Category added successfully.';
            } else {
                $message = 'Could not add category.';
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
    <title>Manage Categories</title>
</head>
<body>
<?php include 'nav.php'; ?>

<h1>Manage Categories</h1>

<?php if ($message !== ''): ?>
    <p><?php echo escape_html($message); ?></p>
<?php endif; ?>

<h2>Add Category</h2>
<form method="post">
    <?php echo csrf_field(); ?>
    <label for="new_category">Category Name:</label><br>
    <input type="text" id="new_category" name="new_category" required>
    <input type="submit" value="Add Category">
</form>

<h2>Existing Categories</h2>

<?php
if (!$db_connected) {
    echo "<p><em>Server unavailable.</em></p>";
} else {
    $result = $conn->query("SELECT CategoryID, CategoryName FROM Categories ORDER BY CategoryName");

    if ($result && $result->num_rows > 0) {
        echo "<ul>";

        while ($row = $result->fetch_assoc()) {
            echo "<li>" . escape_html($row['CategoryName']) . "</li>";
        }

        echo "</ul>";
    } else {
        echo "<p>No categories found.</p>";
    }

    $conn->close();
}
?>

</body>
</html>