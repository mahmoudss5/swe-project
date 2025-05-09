<?php
include_once "database.php";
include_once "User.php";

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

    $user = new User($conn, $fullname, $email, $password, $dob);

    if ($user->emailExists()) {
        echo "<script>alert('Email already exists.'); window.location.href='register.php';</script>";
    } else {
        if ($user->save()) {
            echo "<script>alert('Account created successfully.'); window.location.href='register.php';</script>";
        } else {
            echo "<script>alert('Error creating account.'); window.location.href='register.php';</script>";
        }
    }
}
?>
