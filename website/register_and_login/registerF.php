<?php
session_start();
include_once "../home_page/config/database.php";
include_once "../useclass.php";

if (!isset($_POST["email"]) || empty($_POST["email"])) {
    die("Email is required");
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid request");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $dob = $_POST['dob'];

    // Validate date format
    if (!DateTime::createFromFormat('Y-m-d', $dob)) {
        echo "<script>alert('Invalid date format.'); window.location.href='register.php';</script>";
        exit;
    }
    $database = new Database();
    $user = new User($database->getConnection(), $fullname, $email, $password, $dob);

    if ($user->emailExists()) {
        echo "<script>alert('Email already exists.'); window.location.href='register.php';</script>";
    } else {
        if ($user->save()) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            echo "<script>alert('Account created successfully.'); window.location.href='../home_page/index.php';</script>";
        } else {
            echo "<script>alert('Error creating account.'); window.location.href='register.php';</script>";
        }
    }
}
?>