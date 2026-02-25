<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $asset_id = $_POST['asset_id'];
    $reporter = $_POST['reporter_name'];
    
    // Tangkap data contact baru
    $phone = $_POST['reporter_phone'];
    $email = $_POST['reporter_email'];
    
    $desc = $_POST['description'];

    // Masukkan dalam database (SQL INSERT yang dikemaskini)
    $sql = "INSERT INTO tickets (asset_id, reporter_name, reporter_phone, reporter_email, description) 
            VALUES ('$asset_id', '$reporter', '$phone', '$email', '$desc')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Ticket Submitted! IT Team will contact you at $phone if needed.'); 
                window.location.href='index.php'; // Atau page 'Thank You'
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>