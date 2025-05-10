<?php
// Start session to access user data
session_start();

// Include database connection
require_once 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get current user data from session
    $user_id = $_SESSION['user_id'];
    $current_email = $_SESSION['user_email'];
    $current_name = $_SESSION['user_name'] ?? '';
    
    // Get form data - use current values if not provided
    $name = isset($_POST['name']) && !empty(trim($_POST['name'])) ? trim($_POST['name']) : $current_name;
    $email = isset($_POST['email']) ? trim($_POST['email']) : $current_email;
    $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
    
    // If email is empty, keep using the current email
    if (empty($email)) {
        $email = $current_email;
    }
    
    // Email format validation (only if email is being changed and not empty)
    if ($email !== $current_email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.'); window.location.href='userprofile.html';</script>";
        exit();
    }
    
    // Check if the new email already exists for another user (only if email is changed and not empty)
    if ($email !== $current_email && !empty($email)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            echo "<script>alert('Email already in use by another user.'); window.location.href='userprofile.html';</script>";
            exit();
        }
    }
    
    // Determine which fields to update
    $set_fields = array();
    $param_types = "";
    $param_values = array();
    
    // Always add fields to the update query
    $set_fields[] = "name = ?";
    $param_types .= "s";
    $param_values[] = $name;
    
    $set_fields[] = "email = ?";
    $param_types .= "s";
    $param_values[] = $email;
    
    $set_fields[] = "bio = ?";
    $param_types .= "s";
    $param_values[] = $bio;
    
    // Add user_id to parameters
    $param_types .= "i";
    $param_values[] = $user_id;
    
    // Create the SQL query with the fields to update
    $sql = "UPDATE users SET " . implode(", ", $set_fields) . " WHERE id = ?";
    
    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    
    // Dynamically bind parameters
    $bind_params = array($param_types);
    for ($i = 0; $i < count($param_values); $i++) {
        $bind_params[] = &$param_values[$i];
    }
    call_user_func_array(array($stmt, 'bind_param'), $bind_params);
    
    if ($stmt->execute()) {
        // Update session with new data
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        
        echo "<script>alert('Profile updated successfully.'); window.location.href='userprofile.html';</script>";
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "'); window.location.href='userprofile.html';</script>";
    }
} else {
    // Redirect to profile page if accessed directly
    header('Location: userprofile.html');
    exit();
}
?>