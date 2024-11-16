<?php
// update_encryption_settings.php

// Database connection
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get encryption settings from the form
    $enable_encryption = isset($_POST['enable_encryption']) ? 1 : 0;
    $encryption_algorithm = $_POST['encryption_algorithm'];

    // SQL query to update encryption settings
    $query = "UPDATE settings SET encryption_enabled = ?, encryption_algorithm = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('is', $enable_encryption, $encryption_algorithm);

    if ($stmt->execute()) {
        echo "Encryption settings updated successfully.";
    } else {
        echo "Error updating encryption settings: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
