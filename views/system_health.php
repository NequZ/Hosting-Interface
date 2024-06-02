<?php
session_start();
require_once '../include/classcontroll.php';

$configPath = realpath(__DIR__ . '/../config.php');
if ($configPath === false) {
    die('Configuration file not found.');
}

$config = require $configPath;

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

function getServerLoad() {
    $load = sys_getloadavg();
    return $load[0];
}

function getMemoryUsage() {
    $free = shell_exec('free');
    $free = (string)trim($free);
    $free_arr = explode("\n", $free);
    $memory = explode(" ", $free_arr[1]);
    $memory = array_filter($memory);
    $memory = array_merge($memory);
    $used_memory = $memory[2];
    $total_memory = $memory[1];
    $memory_usage = round($used_memory / $total_memory * 100, 2);
    return $memory_usage;
}

function getDiskUsage() {
    $disk_total_space = disk_total_space("/");
    $disk_free_space = disk_free_space("/");
    $disk_used_space = $disk_total_space - $disk_free_space;
    $disk_usage = round($disk_used_space / $disk_total_space * 100, 2);
    return $disk_usage;
}

function getPhpVersion() {
    $version = phpversion();
    $shortVersion = implode('.', array_slice(explode('.', $version), 0, 2));
    return $shortVersion;
}

// Establish the database connection
$databaseController = new DatabaseController();
$conn = $databaseController->connect();

// Get the user count
$userCount = getUsersCount($conn);

$currentPhpVersion = getPhpVersion();
$requiredPhpVersion = $config['requiredphpversion'];
$isPhpVersionSufficient = version_compare($currentPhpVersion, $requiredPhpVersion, '>=');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>
        System Health
    </title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
</head>

<body class="bg-light">
<div class="container mt-5">
    <h2>System Health Overview</h2>
    <p>If Close don't work Press ESC</p>
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-server"></i> Server Load
            </h5>
            <p class="card-text">The current server load is: <strong><?php echo getServerLoad(); ?></strong></p>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-memory"></i> Memory Usage
            </h5>
            <p class="card-text">The current memory usage is: <strong><?php echo getMemoryUsage(); ?>%</strong></p>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-hdd"></i> Disk Usage
            </h5>
            <p class="card-text">The current disk usage is: <strong><?php echo getDiskUsage(); ?>%</strong></p>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-users"></i> Registered Users
            </h5>
            <p class="card-text">The current number of registered users is: <strong><?php echo $userCount; ?></strong></p>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-code"></i> PHP Version
            </h5>
            <p class="card-text">
                The current PHP version is: <strong><?php echo $currentPhpVersion; ?></strong>
                <?php if (!$isPhpVersionSufficient): ?>
                    <span class="text-warning">(Required: <?php echo $requiredPhpVersion; ?>)
                    <p>PHP version is not sufficient. Please upgrade to PHP <?php echo $requiredPhpVersion; ?> or higher.</p>
                    </span>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
