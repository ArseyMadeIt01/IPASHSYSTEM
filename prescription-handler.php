<?php
// prescription-handler.php

// Database configuration
$host = 'localhost';
$dbname = 'ipash_system';
$username = 'root';
$password = '';

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Get the JSON payload
$data = json_decode(file_get_contents('php://input'), true);

// Check if all necessary data is provided
if (!isset($data['medication_name'], $data['medication_dose'], $data['medication_frequency'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit();
}

$medicationName = $data['medication_name'];
$medicationDose = $data['medication_dose'];
$medicationFrequency = $data['medication_frequency'];

try {
    // Insert prescription data into the database
    $stmt = $pdo->prepare("INSERT INTO prescriptions (medication_name, medication_dose, medication_frequency, created_at) VALUES (:medication_name, :medication_dose, :medication_frequency, NOW())");
    
    $stmt->bindParam(':medication_name', $medicationName);
    $stmt->bindParam(':medication_dose', $medicationDose);
    $stmt->bindParam(':medication_frequency', $medicationFrequency);
    
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Prescription added successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error adding prescription: ' . $e->getMessage()]);
}
?>
