<?php
// upload-lab-result.php

// Database configuration
$host = 'localhost';
$dbname = 'ipash_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['lab_result'])) {
    $file = $_FILES['lab_result'];
    
    // Set the upload directory
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
    }

    // File path
    $filePath = $uploadDir . basename($file['name']);
    
    // Move the uploaded file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Insert the file path into the database
        $stmt = $pdo->prepare("INSERT INTO lab_results (file_path, uploaded_at) VALUES (:file_path, NOW())");
        $stmt->bindParam(':file_path', $filePath);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'File uploaded successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database insertion failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'File upload failed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
}
?>
