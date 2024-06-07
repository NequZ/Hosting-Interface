<?php
session_start();
require_once '../include/classcontroll.php';

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serviceid = $_POST['serviceid'];
    $hostid = $_POST['hostSystemId'];

    try {
        // Insert new host system service
        $stmt = $conn->prepare("INSERT INTO nw_hostsystems_services (hostid, serviceid) VALUES (:hostid, :serviceid)");
        $stmt->bindParam(':hostid', $hostid);
        $stmt->bindParam(':serviceid', $serviceid);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Service successfully added to host system.";
        } else {
            $_SESSION['error_message'] = "Failed to add service to host system.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }

    header('Location: ../views/services.php');
    exit;
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header('Location: ../views/services.php');
    exit;
}
?>
