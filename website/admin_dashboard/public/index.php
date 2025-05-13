<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_set_cookie_params(['path' => '/']);
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: ../../register_and_login/login.php');
    exit;
}
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->conn;
$controller = new AdminController($db);
$action = $_GET['action'] ?? 'index';

switch ($action) {
    default:
        $controller->index();
        break;
} 