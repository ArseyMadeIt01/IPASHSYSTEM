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

// Prepare and bind parameters
$api_key = $_POST['api_key'] ?? '';
$service_url = $_POST['service_url'] ?? '';
$enabled = isset($_POST['enabled']) ? 1 : 0;

if (empty($api_key) || empty($service_url)) {
    echo json_encode(['status' => 'error', 'message' => 'API Key and Service URL are required.']);
    exit();
}

// Sanitize and validate inputs
$api_key = $conn->real_escape_string($api_key);
$service_url = filter_var($service_url, FILTER_VALIDATE_URL);

if (!$service_url) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Service URL.']);
    exit();
}

// Update or insert integration settings
$sql = "REPLACE INTO integrations (id, api_key, service_url, enabled) VALUES (1, '$api_key', '$service_url', $enabled)";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Integrations updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error updating integrations: ' . $conn->error]);
}

// Close the connection
$conn->close();
?>
