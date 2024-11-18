<?php
include_once "db.php";
session_start();

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];
$specialization = $_POST['specialization'] ?? null;
$table = $_POST['table'];


//check if existing
$tables = ["patients", "providers", "admins"];
$exist = false;

foreach ($tables as $index => $tb) {
    $stmt = $db->prepare("SELECT * FROM $tb WHERE name = :name");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $exist = true;
        break;
    }
}
if ($exist) {
    echo "Username taken";
    exit();
}
//

$sql = "INSERT INTO $table (name, email, phone, password";
if ($table === 'providers') {
    $sql .= ", specialization";
}
$sql .= ") VALUES (?, ?, ?, ?";
if ($table === 'providers') {
    $sql .= ", ?";
}
$sql .= ")";

$stmt = $db->prepare($sql);

$stmt->bindParam(1, $name);
$stmt->bindParam(2, $email);
$stmt->bindParam(3, $phone);
$stmt->bindParam(4, $password);

if ($table === 'providers') {
    $stmt->bindParam(5, $specialization);
}

if ($stmt->execute()) {
    $_SESSION['user'] = $user;
    $_SESSION['email']= $email;
    $_SESSION['phone'] = $phone;
    $_SESSION['as'] = $table;

    switch ($table) {
        case "patients":
            header("Location: patient_dashboard.php");
            break;
        case "providers":
            header("Location: provider_dashboard.php");
            break;
        case "admins":
            header("Location: admin_dashboard.php");
            break;
    }
} else {
    echo "Registration failed. Please try again.";
}
?>