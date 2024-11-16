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

$sql = "SELECT * FROM appointments ORDER BY appointment_date, appointment_time";
$result = $conn->query($sql);

$appointments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($appointments);
?>
