<?php
session_start();
require_once '../include/classcontroll.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $invoiceId = $_GET['id'];

    // Fetch existing invoice details
    try {
        $stmt = $conn->prepare("SELECT * FROM nw_services_invoices WHERE invoiceid = :invoiceid");
        $stmt->bindParam(':invoiceid', $invoiceId);
        $stmt->execute();
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            echo "Invoice not found.";
            exit;
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount = $_POST['amount'];
            $paid = isset($_POST['paid']) ? 1 : 0;

            try {
                // Update invoice details
                $updateStmt = $conn->prepare("UPDATE nw_services_invoices SET amount = :amount, paid = :paid WHERE invoiceid = :invoiceid");
                $updateStmt->bindParam(':amount', $amount);
                $updateStmt->bindParam(':paid', $paid);
                $updateStmt->bindParam(':invoiceid', $invoiceId);
                $updateStmt->execute();

                echo "Invoice updated successfully.";
                header('Location: servermanagement.php');
                exit;
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    echo "No invoice ID provided.";
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
                        <h6>Edit Invoice</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="invoiceid">Invoice ID</label>
                                <input type="text" class="form-control" id="invoiceid" name="invoiceid" value="<?php echo htmlspecialchars($invoice['invoiceid']); ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount (€)</label>
                                <input type="text" class="form-control" id="amount" name="amount" value="<?php echo htmlspecialchars($invoice['amount']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="paid">Paid</label>
                                <input type="checkbox" id="paid" name="paid" <?php echo $invoice['paid'] ? 'checked' : ''; ?>>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
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
