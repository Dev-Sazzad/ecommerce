<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Log out user
logout();

// Redirect to home page
header('Location: index.php');
exit();
?>


