<?php
session_start();
require_once '../include/classcontroll.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $result = verifyUserNow($conn, $userId);

    if ($result) {
        // Fetch username for logging
        try {
            $stmt = $conn->prepare("SELECT username FROM nw_users WHERE id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                logVerificationAction($conn, $user['username']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch user details for logging.']);
            exit;
        }

        echo json_encode(['status' => 'success', 'message' => 'User verified successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to verify user.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No user ID provided.']);
}
?>
