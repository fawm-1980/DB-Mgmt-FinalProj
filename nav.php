<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav>
    <a href="index.php">Home</a> |
    <a href="get_posts.php">View Posts</a> |
    <a href="insert_post_form.php">Create Post</a> |
    <a href="post_search_form.php">Search Posts</a> |
    <?php if (isset($_SESSION['username'])): ?>
        <span>Logged in as <?php echo htmlspecialchars($_SESSION['username']); ?></span> |
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login_form.php">Login</a>
    <?php endif; ?>
</nav>
<hr>