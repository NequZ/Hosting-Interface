<?php
session_start();
require_once '../include/classcontroll.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['daemonId'])) {
    $daemonId = $_POST['daemonId'];

    // Generate the bash command to install prerequisites
    $bashCommand = "sudo apt update && sudo apt upgrade -y"; // Replace with actual prerequisites

    // Return the command as a JSON response
    echo json_encode(['success' => 'Daemon setup command generated for daemon ID: ' . $daemonId, 'command' => $bashCommand]);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
