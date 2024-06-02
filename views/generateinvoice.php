<?php
session_start();
require_once '../include/classcontroll.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Adjusted path to autoload.php

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $invoiceid = $_GET['id'];

    // Fetch invoice details
    try {
        $stmt = $conn->prepare("SELECT * FROM nw_services_invoices WHERE invoiceid = :invoiceid");
        $stmt->bindParam(':invoiceid', $invoiceid);
        $stmt->execute();
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            echo "Invoice not found.";
            exit;
        }

        // Log the invoice generation
        logInvoiceGenerate($conn, $invoice['invoiceid'], $invoice['serviceid'], $_SESSION['username'], 'Invoice generated');

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }

    // Create a new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Company');
    $pdf->SetTitle('Invoice');
    $pdf->SetSubject('Invoice Details');
    $pdf->SetKeywords('TCPDF, PDF, invoice, test, guide');

    // Add a page
    $pdf->AddPage();

    // Set some content to print
    $html = '<h1>Invoice Details</h1>
             <table border="1" cellpadding="4">
                 <tr>
                     <th>Invoice ID:</th>
                     <td>' . htmlspecialchars($invoice['invoiceid']) . '</td>
                 </tr>
                 <tr>
                     <th>Service ID:</th>
                     <td>' . htmlspecialchars($invoice['serviceid']) . '</td>
                 </tr>
                 <tr>
                     <th>Username:</th>
                     <td>' . htmlspecialchars($invoice['username']) . '</td>
                 </tr>
                 <tr>
                     <th>Amount:</th>
                     <td>' . htmlspecialchars($invoice['amount']) . ' €</td>
                 </tr>
                 <tr>
                     <th>Status:</th>
                     <td>' . ($invoice['paid'] ? '<span style="color:green;">Paid</span>' : '<span style="color:red;">Not Paid</span>') . '</td>
                 </tr>
                 <tr>
                     <th>Creation Date:</th>
                     <td>' . htmlspecialchars($invoice['creationdate']) . '</td>
                 </tr>
             </table>';

    // Print text using writeHTMLCell()
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('invoice_' . $invoiceid . '.pdf', 'I');
} else {
    echo "No invoice ID provided.";
    exit;
}

?>
