<?php
require_once __DIR__ . '/controllers/ProfileController.php';
$controller = new ProfileController();
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'update':
        $controller->update();
        break;
    case 'changePassword':
        $controller->changePassword();
        break;
    default:
        $controller->index();
        break;
} 