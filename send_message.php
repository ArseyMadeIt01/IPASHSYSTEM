<?php
require 'vendor/autoload.php'; // Load the Composer dependencies for Twilio and SendGrid

use Twilio\Rest\Client;
use SendGrid\Mail\Mail;

// Enable CORS and set content type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Twilio Configuration (SMS)
$twilioSid = "your_twilio_account_sid";
$twilioAuthToken = "your_twilio_auth_token";
$twilioPhoneNumber = "your_twilio_phone_number";

// SendGrid Configuration (Email)
$sendGridApiKey = "your_sendgrid_api_key";

// Retrieve POST data
$inputData = json_decode(file_get_contents('php://input'), true);

// Get the action (sendEmail or sendSMS)
$action = $inputData['action'] ?? null;
$recipient = $inputData['recipient'] ?? null;
$message = $inputData['message'] ?? null;

if (!$action || !$recipient || !$message) {
    echo json_encode(['error' => 'Invalid input. Action, recipient, and message are required.']);
    exit();
}

// Perform the action
if ($action === 'sendSMS') {
    sendSmsNotification($recipient, $message);
} elseif ($action === 'sendEmail') {
    sendEmailNotification($recipient, "New Message", $message);
} else {
    echo json_encode(['error' => 'Invalid action. Use "sendSMS" or "sendEmail".']);
}

// Function to send SMS via Twilio
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
        echo json_encode(['message' => 'SMS sent successfully']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to send SMS: ' . $e->getMessage()]);
    }
}

// Function to send Email via SendGrid
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
        
        if ($response->statusCode() == 202) {
            echo json_encode(['message' => 'Email sent successfully']);
        } else {
            echo json_encode(['error' => 'Failed to send email. Status code: ' . $response->statusCode()]);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to send email: ' . $e->getMessage()]);
    }
}
