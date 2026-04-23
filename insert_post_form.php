<?php
session_start();
include 'nav.php';

if (!isset($_SESSION['userid'], $_SESSION['username'])) {
    echo "<p>Please log in to create a post.</p>";
    echo "<p><a href='login_form.php'>Go to Login</a></p>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Post</title>
</head>
<body>
    <h1>Add New Blog Post</h1>

    <p><strong>Posting as:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>

    <form action="insert_post.php" method="post">
        <label for="categoryid">Category:</label><br>
        <select id="categoryid" name="categoryid" required>
            <option value="">Select a category</option>
            <option value="1">Technology</option>
            <option value="2">Sports</option>
            <option value="3">News</option>
        </select><br>

        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" placeholder="Enter post title" required><br>

        <label for="content">Content:</label><br>
        <textarea id="content" name="content" required></textarea><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>