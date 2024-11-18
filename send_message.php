<?php
    include_once "db.php";
    session_start();

    $sql = "INSERT INTO messages (sender, recipient, message) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $_POST['sender']);
    $stmt->bindParam(2, $_POST['recipient']);
    $stmt->bindParam(3, $_POST['message']);
    $stmt->execute();
    header("Location: " . $_POST['return']);
?>