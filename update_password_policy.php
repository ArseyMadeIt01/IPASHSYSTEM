<?php
// update_password_policy.php

// Database connection
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get password policy settings from the form
    $min_length = intval($_POST['min_password_length']);
    $require_special_chars = isset($_POST['require_special_characters']) ? 1 : 0;
    $password_expiration = intval($_POST['password_expiration']);

    // SQL query to update password policy
    $query = "UPDATE settings SET min_password_length = ?, require_special_characters = ?, password_expiration_days = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iii', $min_length, $require_special_chars, $password_expiration);

    if ($stmt->execute()) {
        echo "Password policy updated successfully.";
    } else {
        echo "Error updating password policy: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
