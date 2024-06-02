<?php
session_start(); // Ensure the session is started

require_once '../include/classcontroll.php'; // Adjust the path if needed

// Call the logout function
if (isset($_SESSION['username'])) {
    logout($conn, $_SESSION['username']);
} else {
    header('Location: login.php'); // Redirect to login if no session found
    exit();
}
?>
