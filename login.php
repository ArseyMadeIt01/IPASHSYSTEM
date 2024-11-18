<?php
include_once "db.php";
session_start();

$user = $_POST['username'] ?? null;
$pass = $_POST['password'] ?? null;

if (empty($user) || empty($pass)) {
    header("Location: login.html");
    exit;
}

$tables = ["patients", "providers"];//, "admins"];
$loginAs = -1;

foreach ($tables as $index => $table) {
    $stmt = $db->prepare("SELECT * FROM $table WHERE name = :name AND password = :password");
    $stmt->bindParam(':name', $user);
    $stmt->bindParam(':password', $pass);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $loginAs = $index;
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user'] = $user;
        $_SESSION['email']= $row['email'];
        $_SESSION['phone'] = $row['phone'];
        $_SESSION['as'] = $table;
        break;
    }
}

if ($loginAs >= 0) {
    switch ($loginAs) {
        case 0:
            header("Location: patient_dashboard.php");
            break;
        case 1:
            header("Location: provider_dashboard.php");
            break;
        // case 2:
        //     header("Location: admin_dashboard.php");
        //     break;
    }
} else {
    echo "Login failed. Please try again.";
}