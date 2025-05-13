<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['path' => '/']);
    session_start();
}
require_once 'config/database.php';
require_once 'controllers/HomeController.php';
$database = new Database();
$db = $database->getConnection();
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare('SELECT points FROM users WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($points);
    if ($stmt->fetch()) {
        $_SESSION['user_points'] = $points;
    }
    $stmt->close();
} else {
    echo '<div style="color:red; font-weight:bold;">Please log in to view your points.</div>';
}

// Initialize controller
$controller = new HomeController($db);

// Route the request
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'createPost':
        $controller->createPost();
        break;
    case 'createComment':
        $controller->createComment();
        break;
    case 'votePost':
        $controller->votePost();
        break;
    case 'editPost':
        $controller->editPost();
        break;
    case 'editComment':
        $controller->editComment();
        break;
    case 'voteComment':
        $controller->voteComment();
        break;
    case 'reportPost':
        $controller->reportPost();
        break;
    case 'reportComment':
        $controller->reportComment();
        break;
    case 'followUser':
        $controller->followUser();
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        $controller->index();
        break;
} 