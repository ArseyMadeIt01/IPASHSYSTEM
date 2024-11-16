<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ipash_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$appointmentId = $data['appointmentId'];
$status = $data['status'];
$reason = isset($data['reason']) ? $data['reason'] : null;

// Prepare and bind the SQL statement
if ($reason) {
    $stmt = $conn->prepare("UPDATE appointments SET status = ?, reason = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $reason, $appointmentId);
} else {
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $appointmentId);
}

// Execute the query
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
?>
