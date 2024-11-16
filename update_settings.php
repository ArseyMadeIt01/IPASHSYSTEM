<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // replace with your database username
$password = ""; // replace with your database password
$dbname = "admin_dashboard"; // replace with your database name

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and sanitize inputs from the form
$site_name = $_POST['site_name'] ?? '';
$site_url = $_POST['site_url'] ?? '';
$timezone = $_POST['timezone'] ?? '';
$email = $_POST['email'] ?? '';

if (empty($site_name) || empty($site_url) || empty($timezone) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit();
}

// Sanitize inputs
$site_name = $conn->real_escape_string($site_name);
$site_url = filter_var($site_url, FILTER_VALIDATE_URL);
$timezone = $conn->real_escape_string($timezone);
$email = filter_var($email, FILTER_VALIDATE_EMAIL);

if (!$site_url) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Site URL.']);
    exit();
}

if (!$email) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Email Address.']);
    exit();
}

// Update settings in the database
$sql = "UPDATE settings SET site_name = '$site_name', site_url = '$site_url', timezone = '$timezone', email = '$email' WHERE id = 1";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Settings updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error updating settings: ' . $conn->error]);
}

// Close the connection
$conn->close();
?>
