<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // replace with your MySQL password
$dbname = "ipash_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handling the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $provider_specialization = $_POST['provider_specialization'];
    $provider = $_POST['provider'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO appointments (appointment_date, appointment_time, provider_specialization, provider) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $appointment_date, $appointment_time, $provider_specialization, $provider);

    // Execute the query
    if ($stmt->execute()) {
        echo "<script>alert('Appointment booked successfully!'); window.location.href='patient-dashboard.html';</script>";
    } else {
        echo "<script>alert('Error: Could not book the appointment.'); window.location.href='patient-dashboard.html';</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
