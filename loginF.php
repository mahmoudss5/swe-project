<?php
include_once "database.php";
include_once "User.php";
session_start();
include_once "database.php";
include_once "User.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $user = new User($conn); // Create instance with DB connection only

    if ($user->login($email, $password)) {
        echo "<script>alert('Login successful!'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Invalid email or password'); window.location.href='login.php';</script>";
    }
}
?>
