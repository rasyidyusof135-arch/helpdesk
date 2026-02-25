<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Prevent SQL Injection (Basic)
    $user = mysqli_real_escape_string($conn, $user);
    $pass = mysqli_real_escape_string($conn, $pass);

    // Query to check user
    // Note: This checks for plain text passwords as inserted in Phase 1
    $sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Login Success
        $row = $result->fetch_assoc();
        
        // Save user info in Session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        // Redirect to Dashboard
        header("Location: dashboard.php");
    } else {
        // Login Failed
        $_SESSION['error'] = "Invalid Username or Password";
        header("Location: login.php");
    }
}
?>