<?php
// Include database connection
include 'db_connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted form data
    $max_failed_attempts = isset($_POST['max_failed_attempts']) ? (int)$_POST['max_failed_attempts'] : null;
    $lockout_duration = isset($_POST['lockout_duration']) ? (int)$_POST['lockout_duration'] : null;

    // Validate input data
    if ($max_failed_attempts === null || $lockout_duration === null) {
        // Error response if any input is missing
        echo "Error: Please fill in all required fields.";
        exit;
    }

    if ($max_failed_attempts < 3) {
        echo "Error: Max failed login attempts should be at least 3.";
        exit;
    }

    if ($lockout_duration < 10) {
        echo "Error: Lockout duration should be at least 10 minutes.";
        exit;
    }

    // Prepare the SQL query to update the settings in the database
    $sql = "UPDATE security_settings SET max_failed_attempts = ?, lockout_duration = ? WHERE setting_name = 'account_lockout'";

    // Prepare the statement to prevent SQL injection
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters (integer types)
        $stmt->bind_param("ii", $max_failed_attempts, $lockout_duration);

        // Execute the query
        if ($stmt->execute()) {
            echo "Account lockout settings updated successfully!";
        } else {
            echo "Error: Could not update settings.";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error: Could not prepare the statement.";
    }

    // Close the database connection
    $conn->close();
}
?>
