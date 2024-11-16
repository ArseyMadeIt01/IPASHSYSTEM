<?php
// Database connection
require_once 'db_connection.php'; // Adjust with actual database connection file

// Retrieve POST data
$data = json_decode(file_get_contents("php://input"), true);
$feedbackId = $data['feedbackId'] ?? null;

if ($feedbackId) {
    // Fetch feedback details from the database
    $stmt = $pdo->prepare("SELECT email, name, feedback FROM feedback WHERE id = :id");
    $stmt->execute(['id' => $feedbackId]);
    $feedback = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($feedback) {
        // Send an email to notify the user
        $to = $feedback['email'];
        $subject = "Your Feedback Has Been Resolved";
        $message = "Dear {$feedback['name']},\n\nThank you for your feedback:\n\n\"{$feedback['feedback']}\"\n\nWe have reviewed and resolved it. We appreciate your contribution to improving our services.\n\nBest regards,\nSupport Team";
        $headers = "From: no-reply@yourwebsite.com";

        if (mail($to, $subject, $message, $headers)) {
            // Update feedback status to "resolved" if needed
            $updateStmt = $pdo->prepare("UPDATE feedback SET status = 'resolved' WHERE id = :id");
            $updateStmt->execute(['id' => $feedbackId]);

            // Send JSON response to the frontend
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Feedback not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid feedback ID.']);
}
?>
