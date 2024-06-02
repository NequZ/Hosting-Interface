<?php
session_start();
require_once '../include/classcontroll.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $conn->prepare("SELECT hostname, ip FROM nw_hostsystems WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $system = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($system) {
            $hostname = $system['hostname'];
            $ip = $system['ip'];

            $stmt = $conn->prepare("DELETE FROM nw_hostsystems WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            logHostSystemAction($conn, $hostname, $ip, $id, 'Host was deleted', $_SESSION['username']);

            echo "Host system deleted successfully.";
        } else {
            echo "Host system not found.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
