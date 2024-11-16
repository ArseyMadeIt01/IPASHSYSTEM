<?php
header("Content-Type: application/json");

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Add your MySQL password if needed
$dbname = "ipash_system";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve POST data
    $inputData = json_decode(file_get_contents("php://input"), true);
    $rating = $inputData['rating'] ?? null;
    $message = $inputData['message'] ?? null;

    // Validate input
    if (!$rating || !$message) {
        echo json_encode(["success" => false, "message" => "Invalid input."]);
        exit();
    }

    // Insert feedback into database
    $stmt = $conn->prepare("INSERT INTO feedback (rating, message) VALUES (:rating, :message)");
    $stmt->bindParam(":rating", $rating);
    $stmt->bindParam(":message", $message);
    $stmt->execute();

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
