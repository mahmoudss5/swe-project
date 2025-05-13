<?php
session_set_cookie_params(['path' => '/']);
session_start(); 

include_once "../home_page/config/database.php";
include_once "../useclass.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $database = new Database();
    $user = new User($database->getConnection());

    if ($user->login($email, $password)) {
        if ($_SESSION['is_admin']) {
            echo "<script>alert('Login successful!'); window.location.href='../admin_dashboard/public/index.php';</script>";
        } else {
            echo "<script>alert('Login successful!'); window.location.href='../home_page/index.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password'); window.location.href='login.php';</script>";
    }
}
?>
