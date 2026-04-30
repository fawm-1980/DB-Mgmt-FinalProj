<?php
require_once __DIR__ . '/includes/security.php';
?>
<nav>
    <a href="index.php">Home</a> |
    <a href="get_posts.php">View Posts</a> |
    <a href="insert_post_form.php">Create Post</a> |
    <a href="post_search_form.php">Search Posts</a> |
    <a href="register_form.php">Register</a> |

    <?php if (is_admin()): ?>
        <a href="manage_categories.php">Manage Categories</a> |
        <a href="manage_tags.php">Manage Tags</a> |
    <?php endif; ?>

    <?php if (isset($_SESSION['username'])): ?>
        <span>Logged in as <?php echo escape_html($_SESSION['username']); ?></span> |
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login_form.php">Login</a>
    <?php endif; ?>
</nav>
<hr>