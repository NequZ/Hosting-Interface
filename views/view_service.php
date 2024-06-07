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

        // Check if there's an entry in nw_hostsystems_services table with the serviceid
        $hostSystemStmt = $conn->prepare("SELECT * FROM nw_hostsystems_services WHERE serviceid = :serviceid");
        $hostSystemStmt->bindParam(':serviceid', $service['serviceid']);
        $hostSystemStmt->execute();
        $hostSystemService = $hostSystemStmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    echo "No service ID provided.";
    exit;
}

include '../include/head.php'; ?>

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

                    <?php if (!$hostSystemService): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <span class="alert-icon"><i class="fas fa-thumbs-down"></i></span>
                            <span class="alert-text"><strong>Warning!</strong> This service is not assigned to any host system. Please assign it to a host system.</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <button class="btn btn-info" data-toggle="modal" data-target="#hostSystemModal">Add to Host System</button>
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
                                            <button class="btn btn-icon btn-3 btn-primary" type="button" data-dismiss="modal" aria-label="Close">
                                                <span class="btn-inner--icon"><i class="ni ni-button-play"></i></span>
                                                <span class="btn-inner--text">Close</span>
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
                        <div class="alert alert-danger" role="alert">
                            <span class="alert-icon"><i class="fas fa-thumbs-down"></i></span>
                            <strong>No invoices found for this service!</strong> A Invoice is needed for a correct billing process
                        </div>
                        <button class="btn btn-primary" onclick="window.location.href='add_invoice.php?serviceid=<?php echo $service['serviceid']; ?>'">Add Invoice</button>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
</main>

<!-- Add to Host System Modal -->
<div class="modal fade" id="hostSystemModal" tabindex="-1" role="dialog" aria-labelledby="hostSystemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hostSystemModalLabel">Add to Host System</h5>
                <button class="btn btn-icon btn-3 btn-primary" type="button" data-dismiss="modal" aria-label="Close">
                    <span class="btn-inner--icon"><i class="ni ni-button-play"></i></span>
                    <span class="btn-inner--text">Close</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="../controllers/add_host_system_service_controller.php">
                    <input type="hidden" name="serviceid" value="<?php echo htmlspecialchars($service['serviceid']); ?>">
                    <div class="form-group">
                        <label for="hostSystemId">Host System ID</label>
                        <input type="text" class="form-control" id="hostSystemId" name="hostSystemId" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
