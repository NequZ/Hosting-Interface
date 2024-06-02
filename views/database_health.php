<?php
session_start();
require_once '../include/classcontroll.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$configPath = realpath(__DIR__ . '/../config.php');
if ($configPath === false) {
    die('Configuration file not found.');
}

$config = require $configPath; // Include the config.php to use db_name
$databaseHealth = checkDatabaseHealth($conn, $config['db_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Database Health</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Database Health Overview</h2>
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-database"></i> Database Health
            </h5>
            <p class="card-text">
                <strong>
                    <?php echo $databaseHealth['status'] === "Database connection is healthy." ? "<i class='fas fa-check-circle text-success'></i> " : "<i class='fas fa-exclamation-circle text-danger'></i> "; ?>
                    <?php echo $databaseHealth['status']; ?>
                </strong>
            </p>
            <?php if ($databaseHealth['status'] === "Database connection is healthy."): ?>
                <p class="card-text"><i class="fas fa-table"></i> Number of tables: <strong><?php echo $databaseHealth['table_count']; ?></strong></p
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>