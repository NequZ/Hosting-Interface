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
include '../include/head.php'; ?>

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
                            <label for="amount" class="form-label">Amount <i class="fas fa-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Please enter a valid amount like 10.00, 1, or 10.98"></i></label>
                            <input type="text" class="form-control" id="amount" name="amount" required pattern="^\d+(\.\d{1,2})?$" title="Please enter a valid amount like 10.00, 1, or 10.98">
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

<script>
    // Enable Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Input validation function (optional, since pattern attribute handles it)
    document.getElementById('amount').addEventListener('input', function (e) {
        var value = e.target.value;
        var regex = /^\d+(\.\d{1,2})?$/;
        if (!regex.test(value)) {
            e.target.setCustomValidity("Please enter a valid amount like 10.00, 1, or 10.98");
        } else {
            e.target.setCustomValidity("");
        }
    });
</script>

</body>
</html>

