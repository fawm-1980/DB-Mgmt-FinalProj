<?php
require_once __DIR__ . '/includes/security.php';
session_unset();
session_destroy();

header("Location: index.php");
exit();
?>