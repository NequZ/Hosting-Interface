<?php
session_start();
require_once '../include/classcontroll.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['serviceid'])) {
    $id = $_GET['serviceid'];

    try {
        // Fetch service details
        $stmt = $conn->prepare("SELECT * FROM nw_services WHERE serviceid = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$service) {
            echo "Service not found.";
            exit;
        }

        // Fetch service category from nw_services_details
        $detailsStmt = $conn->prepare("SELECT * FROM nw_services_details WHERE serviceid = :serviceid");
        $detailsStmt->bindParam(':serviceid', $service['serviceid']);
        $detailsStmt->execute();
        $serviceDetails = $detailsStmt->fetch(PDO::FETCH_ASSOC);

        if (!$serviceDetails) {
            echo "Service details not found.";
            exit;
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    echo "No service ID provided.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serviceid = $service['serviceid'];
    $category = $serviceDetails['servicecat'];
    $bookedmemory = $_POST['bookedmemory'];
    $bookedcpu = $_POST['bookedcpu'];
    $diskspace = $_POST['diskspace'];
    $imageid = $_POST['imageid'];
    $createdbyadmin = isset($_POST['createdbyadmin']) ? 1 : 0;

    try {
        if ($category === 'gameserver') {
            // Insert into nw_services_advanced_gameserver table
            $stmt = $conn->prepare("INSERT INTO nw_services_advanced_gameserver (serviceid, bookedmemory, bookedcpu, diskspace, imageid, createdbyadmin) VALUES (:serviceid, :bookedmemory, :bookedcpu, :diskspace, :imageid, :createdbyadmin)");
        } elseif ($category === 'kvm') {
            // Insert into nw_services_advanced_kvm table
            $stmt = $conn->prepare("INSERT INTO nw_services_advanced_kvm (serviceid, bookedmemory, bookedcpu, diskspace, imageid, createdbyadmin) VALUES (:serviceid, :bookedmemory, :bookedcpu, :diskspace, :imageid, :createdbyadmin)");
        } elseif ($category === 'storage') {
            // Insert into nw_services_advanced_storage table
            $stmt = $conn->prepare("INSERT INTO nw_services_advanced_storage (serviceid, bookedmemory, bookedcpu, diskspace, imageid, createdbyadmin) VALUES (:serviceid, :bookedmemory, :bookedcpu, :diskspace, :imageid, :createdbyadmin)");
        } else {
            throw new Exception("Unknown service category.");
        }

        $stmt->bindParam(':serviceid', $serviceid);
        $stmt->bindParam(':bookedmemory', $bookedmemory);
        $stmt->bindParam(':bookedcpu', $bookedcpu);
        $stmt->bindParam(':diskspace', $diskspace);
        $stmt->bindParam(':imageid', $imageid);
        $stmt->bindParam(':createdbyadmin', $createdbyadmin);
        $stmt->execute();

        // Update nw_services table to set active to 1
        $updateStmt = $conn->prepare("UPDATE nw_services SET active = 1 WHERE serviceid = :serviceid");
        $updateStmt->bindParam(':serviceid', $serviceid);
        $updateStmt->execute();

        $successMessage = "Advanced details added successfully and service activated!";
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Soft UI Dashboard by Creative Tim</title>
    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome 4.7.0 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.7" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">

<?php include '../include/sidebar.php'; ?>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php include '../include/navbar.php'; ?>
    <!-- End Navbar -->

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Service Details</h6>

                <?php if (isset($successMessage)): ?>
                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                <?php endif; ?>
                <?php if (isset($errorMessage)): ?>
                    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                <?php endif; ?>
                <!-- Advanced Details Form -->
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Advanced Details</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="bookedmemory">Booked Memory (GB)</label>
                                <input type="number" step="0.01" class="form-control" id="bookedmemory" name="bookedmemory" required>
                            </div>
                            <div class="form-group">
                                <label for="bookedcpu">Booked CPU (Cores)</label>
                                <input type="number" step="0.01" class="form-control" id="bookedcpu" name="bookedcpu" required>
                            </div>
                            <div class="form-group">
                                <label for="diskspace">Disk Space (GB)</label>
                                <input type="number" step="0.01" class="form-control" id="diskspace" name="diskspace" required>
                            </div>
                            <div class="form-group">
                                <label for="imageid">Image ID</label>
                                <input type="number" class="form-control" id="imageid" name="imageid" required>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="createdbyadmin" name="createdbyadmin">
                                <label class="form-check-label" for="createdbyadmin">Created by Admin</label>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Add Details</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
