<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

session_unset();
session_destroy();


header('Location: /register_and_login/login.php');
exit;
