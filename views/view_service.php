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
        $stmt = $conn->prepare("SELECT * FROM nw_services WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$service) {
            echo "Service not found.";
            exit;
        }

        // Fetch invoices related to the service
        $invoiceStmt = $conn->prepare("SELECT * FROM nw_services_invoices WHERE serviceid = :serviceid");
        $invoiceStmt->bindParam(':serviceid', $service['serviceid']);
        $invoiceStmt->execute();
        $invoices = $invoiceStmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    echo "No service ID provided.";
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
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th>Service ID:</th>
                                <td><?php echo htmlspecialchars($service['serviceid']); ?></td>
                            </tr>
                            <tr>
                                <th>Username:</th>
                                <td><?php echo htmlspecialchars($service['username']); ?></td>
                            </tr>
                            <tr>
                                <th>Created:</th>
                                <td><?php echo htmlspecialchars($service['created']); ?></td>
                            </tr>
                            <tr>
                                <th>Active:</th>
                                <td>
                                    <?php if ($service['active']): ?>
                                        <span class="text-success"><i class="fa fa-check-circle"></i> Active</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fa fa-times-circle"></i> Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                        <button class="btn btn-warning" onclick="window.location.href='edit_service.php?id=<?php echo $service['id']; ?>'">Edit</button>
                        <?php if (!$service['active']): ?>
                            <button class="btn btn-primary" onclick="window.location.href='setup_service.php?serviceid=<?php echo $service['serviceid']; ?>'">
                                <i class="fa fa-cogs"></i> Setup Service
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Invoice Details</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($invoices)): ?>
                            <?php foreach ($invoices as $invoice): ?>
                                <table class="table table-striped">
                                    <tr>
                                        <th>Invoice ID:</th>
                                        <td><?php echo htmlspecialchars($invoice['invoiceid']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Invoice Date:</th>
                                        <td><?php echo htmlspecialchars($invoice['creationdate']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Amount:</th>
                                        <td><?php echo htmlspecialchars($invoice['amount']); ?> €</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <?php if ($invoice['paid']): ?>
                                                <span class="text-success"><i class="fa fa-check-circle"></i> Paid</span>
                                            <?php else: ?>
                                                <span class="text-danger"><i class="fa fa-times-circle"></i> Not Paid</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                                <button class="btn btn-success" data-toggle="modal" data-target="#invoiceModal-<?php echo $invoice['invoiceid']; ?>">View</button>

                                <!-- Modal -->
                                <div class="modal fade" id="invoiceModal-<?php echo $invoice['invoiceid']; ?>" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel-<?php echo $invoice['invoiceid']; ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="invoiceModalLabel-<?php echo $invoice['invoiceid']; ?>">Invoice Details</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <table class="table table-striped">
                                                    <tr>
                                                        <th>Invoice ID:</th>
                                                        <td><?php echo htmlspecialchars($invoice['invoiceid']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Invoice Date:</th>
                                                        <td><?php echo htmlspecialchars($invoice['creationdate']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Amount:</th>
                                                        <td><?php echo htmlspecialchars($invoice['amount']); ?> €</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status:</th>
                                                        <td>
                                                            <?php if ($invoice['paid']): ?>
                                                                <span class="text-success"><i class="fa fa-check-circle"></i> Paid</span>
                                                            <?php else: ?>
                                                                <span class="text-danger"><i class="fa fa-times-circle"></i> Not Paid</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary" onclick="window.location.href='edit_invoice.php?id=<?php echo $invoice['invoiceid']; ?>'"><i class="fa fa-edit"></i> Edit</button>
                                                <button type="button" class="btn btn-warning" onclick="window.location.href='generateinvoice.php?id=<?php echo $invoice['invoiceid']; ?>'"><i class="fa fa-file-pdf-o"></i> Generate PDF</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No invoices found for this service!</p>
                            <p>A Invoice is needed for a correct billing process.</p>
                        <button class="btn btn-primary" onclick="window.location.href='add_invoice.php?serviceid=<?php echo $service['serviceid']; ?>'">Add Invoice</button>
                        <?php endif; ?>
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
