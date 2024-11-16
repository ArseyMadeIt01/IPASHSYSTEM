<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Add your MySQL password if needed
$dbname = "ipash_system";

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is posted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $call_date = $_POST['call_date'];
    $call_time = $_POST['call_time'];
    $recipient = filter_var($_POST['recipient'], FILTER_SANITIZE_STRING);

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO video_calls (call_date, call_time, recipient) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $call_date, $call_time, $recipient);

    // Execute the query
    if ($stmt->execute()) {
        echo "<script>alert('Video call scheduled successfully!'); window.location.href='patient-dashboard.html';</script>";
    } else {
        echo "<script>alert('Error: Could not schedule the video call.'); window.location.href='patient-dashboard.html';</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
