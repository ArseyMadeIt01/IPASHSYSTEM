<?php
session_start();
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $provider_specialization = $_POST['provider_specialization'];
    $provider = $_POST['provider'];

    $sql = "INSERT INTO appointments (appointment_date, appointment_time, provider_specialization, provider, patient) VALUES (?, ?, ?, ?, ?)";

    $stmt = $db->prepare($sql);
    $stmt->bindparam(1, $appointment_date);
    $stmt->bindparam(2, $appointment_time);
    $stmt->bindparam(3, $provider_specialization);
    $stmt->bindparam(4, $provider);
    $stmt->bindparam(5, $_SESSION['user']);

    if ($stmt->execute()) {
        header("Location: patient_dashboard.php");
    } else {
        echo "Error: Could not book the appointment.";
    }
}
?>