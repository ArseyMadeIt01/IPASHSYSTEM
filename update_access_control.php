<?php
// update_access_control.php

// Database connection
include('db_connection.php'); // Assuming you have a database connection script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected role and permissions from the form
    $role = $_POST['role'];
    $permissions = isset($_POST['permission']) ? $_POST['permission'] : [];

    // Convert permissions array into a comma-separated string
    $permissions_str = implode(',', $permissions);

    // SQL query to update permissions for the selected role
    $query = "UPDATE roles SET permissions = ? WHERE role_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $permissions_str, $role);

    if ($stmt->execute()) {
        echo "Access control updated successfully for role: " . htmlspecialchars($role);
    } else {
        echo "Error updating access control: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
