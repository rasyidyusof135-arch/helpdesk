<?php
session_start();
include 'db_connect.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['ticket_id'];
    $status = $_POST['status'];
    $remarks = mysqli_real_escape_string($conn, $_POST['admin_remarks']);

    // Update database
    $sql = "UPDATE tickets SET status='$status', admin_remarks='$remarks' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        // Balik ke dashboard selepas berjaya
        header("Location: dashboard.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>