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
        $stmt = $conn->prepare("SELECT * FROM nw_services_invoices WHERE serviceid = :serviceid");
        $stmt->execute(array(':serviceid' => $id));
        $row = $stmt->fetch();

        if ($row) {
            // Fetch invoices related to the service
            $invoiceStmt = $conn->prepare("SELECT * FROM nw_services_invoices WHERE serviceid = :serviceid");
            $invoiceStmt->bindParam(':serviceid', $id);
            $invoiceStmt->execute();
            $invoices = $invoiceStmt->fetchAll(PDO::FETCH_ASSOC);
            $invoiceFound = true;
        } else {
            $invoiceNotFound = true;
        }
    } catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        exit;
    }
} else {
    $noIdFound = true;
    exit;
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
    <!-- Font Awesome 5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
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
                <?php if (isset($noIdFound)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Danger!</strong> No service ID provided.
                    </div>
                <?php elseif (isset($invoiceNotFound)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <span class="alert-icon"><i class="fas fa-thumbs-down"></i></span>
                        <strong>Danger!</strong> No invoice found with the provided ID. Please create a new one below.
                    </div>
                    <!-- Form to create a new invoice -->
                    <form action="/controllers/CreateInvoiceController.php" method="post">
                        <div class="mb-3">
                            <label for="serviceid" class="form-label">Service ID</label>
                            <input type="text" class="form-control" id="serviceid" name="serviceid" value="<?php echo htmlspecialchars($id); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="text" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="paid" name="paid">
                                <label class="form-check-label" for="paid">
                                    Paid
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Invoice</button>
                    </form>
                <?php elseif (isset($invoiceFound)) : ?>
                    <div class="alert alert-success" role="alert">
                        <strong>Success!</strong> An invoice was found for this service.
                    </div>
                    <a href="javascript:history.back()" class="btn btn-secondary">Return to Previous Page</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

</body>
</html>
