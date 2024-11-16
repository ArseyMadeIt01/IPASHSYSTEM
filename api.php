<?php
header('Content-Type: application/json');
require 'vendor/autoload.php'; // Load Composer dependencies

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;

// Database connection setup
$servername = "localhost";
$username = "root";
$password = ""; // Adjust as needed
$dbname = "ipash_system";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Twilio setup
$twilioSid = 'your_twilio_sid';
$twilioToken = 'your_twilio_auth_token';
$twilioFromNumber = 'your_twilio_phone_number';
$twilioClient = new Client($twilioSid, $twilioToken);

// PHPMailer setup
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.example.com'; // Replace with your SMTP server
$mail->SMTPAuth = true;
$mail->Username = 'your_email@example.com';
$mail->Password = 'your_email_password';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

// Get incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Check the action type
if (isset($data['action'])) {
    $action = $data['action'];
    $patientName = $data['patientName'];
    $reason = isset($data['reason']) ? $data['reason'] : '';

    // Fetch patient's email and phone number
    $stmt = $conn->prepare("SELECT email, phone FROM patients WHERE name = ?");
    $stmt->bind_param("s", $patientName);
    $stmt->execute();
    $stmt->bind_result($patientEmail, $patientPhone);
    $stmt->fetch();
    $stmt->close();

    if (!$patientEmail || !$patientPhone) {
        echo json_encode(["success" => false, "message" => "Patient contact info not found"]);
        exit;
    }

    // Prepare notification messages
    $smsMessage = "";
    $emailSubject = "";
    $emailBody = "";

    switch ($action) {
        case 'acceptAppointment':
            $smsMessage = "Your appointment has been accepted.";
            $emailSubject = "Appointment Accepted";
            $emailBody = "Dear $patientName,\n\nYour appointment has been accepted. We look forward to seeing you!";
            break;

        case 'reschedule':
            $smsMessage = "Your appointment has been rescheduled. Reason: $reason";
            $emailSubject = "Appointment Rescheduled";
            $emailBody = "Dear $patientName,\n\nYour appointment has been rescheduled for the following reason:\n$reason.";
            break;

        case 'cancel':
            $smsMessage = "Your appointment has been canceled. Reason: $reason";
            $emailSubject = "Appointment Canceled";
            $emailBody = "Dear $patientName,\n\nUnfortunately, your appointment has been canceled for the following reason:\n$reason.";
            break;

        default:
            echo json_encode(["success" => false, "message" => "Invalid action"]);
            exit;
    }

    // Send SMS
    try {
        $twilioClient->messages->create(
            $patientPhone,
            [
                'from' => $twilioFromNumber,
                'body' => $smsMessage
            ]
        );
        $smsSuccess = true;
    } catch (Exception $e) {
        $smsSuccess = false;
        error_log("Twilio Error: " . $e->getMessage());
    }

    // Send Email
    try {
        $mail->setFrom('your_email@example.com', 'Health Provider');
        $mail->addAddress($patientEmail, $patientName);
        $mail->Subject = $emailSubject;
        $mail->Body = $emailBody;
        $mail->send();
        $emailSuccess = true;
    } catch (Exception $e) {
        $emailSuccess = false;
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
    }

    // Response back to client
    if ($emailSuccess && $smsSuccess) {
        echo json_encode(["success" => true, "message" => "Patient notified via email and SMS."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to notify patient via one or more channels."]);
    }

} else {
    echo json_encode(["success" => false, "message" => "No action specified"]);
}

$conn->close();
?>
