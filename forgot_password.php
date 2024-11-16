<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection (make sure you have this setup correctly)
require_once 'db_connect.php'; // Replace with your actual database connection file

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input from request body (JSON)
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'];

    // Validate email input
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email address']);
        exit;
    }

    // Check if the email exists in the database
    $user = getUserByEmail($email);

    if ($user) {
        // Generate a unique token and expiration time (1 hour)
        $token = bin2hex(random_bytes(50));
        $expires = time() + 3600;

        // Save the token and expiration in the database
        savePasswordResetToken($user['id'], $token, $expires);

        // Prepare the reset link
        $resetLink = "https://yourdomain.com/reset-password.html?token=$token";
        
        // Send reset email
        $subject = "Reset Your Password";
        $message = "Click the link below to reset your password:\n\n$resetLink";
        $headers = 'From: no-reply@yourdomain.com' . "\r\n" .
                   'Reply-To: no-reply@yourdomain.com' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();

        if (mail($email, $subject, $message, $headers)) {
            // Respond with success message
            http_response_code(200);
            echo json_encode(['message' => 'Reset link sent to your email.']);
        } else {
            // Handle mail error
            http_response_code(500);
            echo json_encode(['error' => 'Failed to send reset email.']);
        }
    } else {
        // Respond with error if email is not found
        http_response_code(404);
        echo json_encode(['error' => 'Email not found.']);
    }
}

// Function to get a user by email
function getUserByEmail($email) {
    global $pdo; // Ensure $pdo is your database connection

    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to save the password reset token and expiration time
function savePasswordResetToken($userId, $token, $expires) {
    global $pdo;

    // Remove any existing reset tokens for the user
    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Insert new token
    $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $token, $expires]);
}
