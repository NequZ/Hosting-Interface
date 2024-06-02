<?php
session_start(); // Start the session
require_once '../include/classcontroll.php'; // Use require_once to ensure it's included only once

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login if session is not set
    exit;
}

// Fetch user data for verification
$verificationUser = fetchDataForVerification($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>
        Account Verification
    </title>
    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
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
                        <h6>Account Verification</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <?php if ($verificationUser): ?>
                                <table class="table align-items-center mb-0">
                                    <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Field</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td class="text-xs font-weight-bold mb-0">Username</td>
                                        <td class="text-xs mb-0"><?php echo htmlspecialchars($verificationUser['username']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-xs font-weight-bold mb-0">Email</td>
                                        <td class="text-xs mb-0"><?php echo htmlspecialchars($verificationUser['email']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-xs font-weight-bold mb-0">Account Created</td>
                                        <td class="text-xs mb-0"><?php echo htmlspecialchars($verificationUser['created']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-xs font-weight-bold mb-0">Verified</td>
                                        <td class="text-xs mb-0"><?php echo $verificationUser['verified'] ? 'Yes' : 'No'; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-xs font-weight-bold mb-0">Status</td>
                                        <td class="text-xs mb-0"><?php echo $verificationUser['login'] ? 'Online' : 'Offline'; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-xs font-weight-bold mb-0">Actions</td>
                                        <td class="text-xs mb-0">
                                            <button class="btn btn-sm btn-success" onclick="verifyNow('<?php echo $verificationUser['id']; ?>')">Verify Now</button>
                                            <button class="btn btn-sm btn-primary" onclick="sendVerificationEmail('<?php echo $verificationUser['email']; ?>')">Send Email for Verification</button>
                                            <button class="btn btn-sm btn-info" onclick="verifyOnDiscord('<?php echo $verificationUser['id']; ?>')">Verify on Discord</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No user found for verification.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../include/footer.php'; ?>

<script>
    function verifyNow(userId) {
        if (confirm("Are you sure you want to verify this user now?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "verify_now.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send("user_id=" + userId);

        }
    }

    function sendVerificationEmail(email) {
        // Implement your "Send Email for Verification" logic here
        alert("Verification email sent to: " + email);
    }

    function verifyOnDiscord(userId) {
        // Implement your "Verify on Discord" logic here
        alert("Verification on Discord initiated for user ID: " + userId);
    }
</script>

</body>
</html>
