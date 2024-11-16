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
    // Retrieve the symptoms from the form submission
    $symptoms = filter_var($_POST['symptoms'], FILTER_SANITIZE_STRING);

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO symptoms (description) VALUES (?)");
    $stmt->bind_param("s", $symptoms);

    // Execute the query
    if ($stmt->execute()) {
        echo "<script>alert('Symptoms submitted successfully!'); window.location.href='patient-dashboard.html';</script>";
    } else {
        echo "<script>alert('Error: Could not submit symptoms.'); window.location.href='patient-dashboard.html';</script>";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
