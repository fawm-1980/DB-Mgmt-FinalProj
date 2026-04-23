<?php
include 'nav.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Posts</title>
</head>
<body>
    <h1>Search Blog Posts</h1>
    <form action="post_search.php" method="get">
        <label for="keyword">Search Keyword:</label><br>
        <input type="text" id="keyword" name="keyword" required><br>
        <input type="submit" value="Search">
    </form>
</body>
</html>