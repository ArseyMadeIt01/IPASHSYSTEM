<?php
    $db;
    $dsn = "mysql:host=localhost; dbname=ipash_system";
    $db_user = "root";
    $db_pass = "";
    try {
        $db = new PDO($dsn, $db_user, $db_pass);
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $ex) {}
?>