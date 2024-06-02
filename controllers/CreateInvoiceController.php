<?php
session_start();
require_once '../include/classcontroll.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceid = $_POST['serviceid'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $paid = isset($_POST['paid']) ? 1 : 0;
    $username = $_SESSION['username'];

    $invoiceid = generateUUID();

    try {
        $stmt = $conn->prepare("INSERT INTO nw_services_invoices (invoiceid, serviceid, username, amount, creationdate, paid) VALUES (:invoiceid, :serviceid, :username, :amount, :date, :paid)");
        $stmt->bindParam(':invoiceid', $invoiceid);
        $stmt->bindParam(':serviceid', $serviceid);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':paid', $paid);
        $stmt->execute();

        // Log the invoice generation
        logInvoiceGenerate($conn, $invoiceid, $serviceid, $username, 'Invoice generated');

        // Redirect to the dashboard
        header('Location: ../views/dashboard.php');
        exit;
    } catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        exit;
    }
} else {
    echo "Invalid request method.";
    exit;
}
?>
