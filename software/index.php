<?php
require_once 'config/database.php';
require_once 'controllers/UserController.php';

$conn = getConnection();
$controller = new UserController($conn);

// Pass a sample user ID (e.g., 1)
$controller->profile(1);
// Check if 'page' exists in the URL, if not default to page 1
$current_page = isset($_GET['page']) ? $_GET['page'] : 1; // Default to page 1 if not set

?>
