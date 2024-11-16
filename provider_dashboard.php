<?php
require 'vendor/autoload.php'; // Load the Composer dependencies for Twilio and SendGrid

use Twilio\Rest\Client;
use SendGrid\Mail\Mail;

// Enable CORS and JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Twilio Configuration (SMS)
$twilioSid = "your_twilio_account_sid";
$twilioAuthToken = "your_twilio_auth_token";
$twilioPhoneNumber = "your_twilio_phone_number";

// SendGrid Configuration (Email)
$sendGridApiKey = "your_sendgrid_api_key";

// Appointment and feedback data
$appointments = [];
$feedback = [];

// Route the request based on action
$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

if ($requestMethod === 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);

    switch ($action) {
        case 'submitFeedback':
            handleFeedbackSubmission($inputData);
            break;
        
        case 'acceptAppointment':
            handleAppointmentAcceptance($inputData);
            break;

        default:
            echo json_encode(['message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['message' => 'Only POST requests are supported']);
}

// Function to handle feedback submission
function handleFeedbackSubmission($data) {
    global $feedback;

    $rating = $data['rating'] ?? null;
    $message = $data['message'] ?? null;

    if ($rating && $message) {
        // Store feedback (in real use case, you'd save to a database)
        $feedback[] = ['rating' => $rating, 'message' => $message];

        echo json_encode(['message' => 'Feedback submitted successfully']);
    } else {
        echo json_encode(['message' => 'Invalid feedback data']);
    }
}

// Function to handle appointment acceptance and send notifications
function handleAppointmentAcceptance($data) {
    global $appointments;

    $patientId = $data['patientId'] ?? null;
    $patientPhoneNumber = $data['patientPhoneNumber'] ?? null; // Add patient phone number for SMS
    $patientEmail = $data['patientEmail'] ?? null; // Add patient email for email notifications

    if ($patientId) {
        // Mark appointment as accepted
        $appointments[] = ['patientId' => $patientId, 'status' => 'accepted'];

        // Send notification (SMS and Email)
        sendNotificationToPatient($patientId, $patientPhoneNumber, $patientEmail);

        echo json_encode(['message' => 'Appointment accepted and notification sent']);
    } else {
        echo json_encode(['message' => 'Invalid patient ID']);
    }
}

// Function to send SMS and Email notifications
function sendNotificationToPatient($patientId, $phoneNumber, $email) {
    // SMS Notification via Twilio
    if ($phoneNumber) {
        sendSmsNotification($phoneNumber, "Your appointment has been accepted. Patient ID: $patientId.");
    }

    // Email Notification via SendGrid
    if ($email) {
        sendEmailNotification($email, "Appointment Accepted", "Your appointment with ID $patientId has been accepted.");
    }
}

// Send SMS using Twilio
function sendSmsNotification($phoneNumber, $message) {
    global $twilioSid, $twilioAuthToken, $twilioPhoneNumber;

    try {
        $client = new Client($twilioSid, $twilioAuthToken);
        $client->messages->create(
            $phoneNumber, // Recipient's phone number
            [
                'from' => $twilioPhoneNumber, // Your Twilio phone number
                'body' => $message // The SMS body
            ]
        );
        error_log("SMS sent to $phoneNumber");
    } catch (Exception $e) {
        error_log("Failed to send SMS: " . $e->getMessage());
    }
}

// Send Email using SendGrid
function sendEmailNotification($toEmail, $subject, $content) {
    global $sendGridApiKey;

    try {
        $email = new Mail();
        $email->setFrom("no-reply@yourdomain.com", "Your Company");
        $email->setSubject($subject);
        $email->addTo($toEmail);
        $email->addContent("text/plain", $content);

        $sendgrid = new \SendGrid($sendGridApiKey);
        $response = $sendgrid->send($email);
        error_log("Email sent to $toEmail. Status code: " . $response->statusCode());
    } catch (Exception $e) {
        error_log("Failed to send email: " . $e->getMessage());
    }
}
