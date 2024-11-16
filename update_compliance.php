<?php
// update_compliance.php

// Database connection
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get compliance standards from the form
    $compliance = isset($_POST['compliance']) ? $_POST['compliance'] : [];
    
    // Convert the compliance array to a comma-separated string
    $compliance_str = implode(',', $compliance);

    // SQL query to update compliance settings
    $query = "UPDATE settings SET compliance_standards = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $compliance_str);

    if ($stmt->execute()) {
        echo "Compliance settings updated successfully.";
    } else {
        echo "Error updating compliance settings: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
