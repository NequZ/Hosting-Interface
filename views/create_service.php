<?php
session_start();
require_once '../include/classcontroll.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serviceid = generateUUID();
    $username = $_POST['username'];
    $created = date('Y-m-d H:i:s'); // Use current timestamp for creation date
    $active = isset($_POST['active']) ? 1 : 0;
    $servicecat = $_POST['servicecat'];
    $linked_invoice = ''; // You can set default value or fetch the appropriate value as needed
    $blocked = isset($_POST['blocked']) ? 1 : 0;

    try {
        // Begin transaction
        $conn->beginTransaction();

        // Insert into nw_services table
        $stmt = $conn->prepare("INSERT INTO nw_services (serviceid, username, created, active) VALUES (:serviceid, :username, :created, :active)");
        $stmt->bindParam(':serviceid', $serviceid);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':created', $created);
        $stmt->bindParam(':active', $active);
        $stmt->execute();

        // Insert into nw_services_details table
        $stmt = $conn->prepare("INSERT INTO nw_services_details (serviceid, servicecat, linked_invoice, blocked) VALUES (:serviceid, :servicecat, :linked_invoice, :blocked)");
        $stmt->bindParam(':serviceid', $serviceid);
        $stmt->bindParam(':servicecat', $servicecat);
        $stmt->bindParam(':linked_invoice', $linked_invoice);
        $stmt->bindParam(':blocked', $blocked);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        $successMessage = "Service created successfully!";
    } catch (PDOException $e) {
        // Rollback transaction in case of error
        $conn->rollBack();
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
                        <h6>Services</h6>
                <?php if (isset($successMessage)): ?>
                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                <?php endif; ?>
                <?php if (isset($errorMessage)): ?>
                    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                <?php endif; ?>
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Create New Service</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="servicecat">Service Category</label>
                                <select class="form-control" id="servicecat" name="servicecat" required>
                                    <option value="kvm">KVM</option>
                                    <option value="gameserver">Game Server</option>
                                    <option value="storage">Storage</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="linked_invoice">Linked Invoice</label>
                                <input type="text" class="form-control" id="linked_invoice" name="linked_invoice" value="" disabled>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="active" name="active">
                                <label class="form-check-label" for="active">Active</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="blocked" name="blocked">
                                <label class="form-check-label" for="blocked">Blocked</label>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Create Service</button>
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

<?php include '../include/footer.php'; ?>
</body>
</html>
