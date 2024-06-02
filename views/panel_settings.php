<?php
session_start();
require_once '../include/classcontroll.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
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
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.7" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">
<!-- Modal -->
<div class="modal fade" id="systemHealthModal" tabindex="-1" aria-labelledby="systemHealthModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Use .modal-xl for extra-large modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="systemHealthModalLabel">System Health</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content for system health -->
                <iframe id="systemHealthFrame" src="" width="100%" height="600px" frameborder="0"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="databaseHealthModal" tabindex="-1" aria-labelledby="databaseHealthModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Use .modal-xl for extra-large modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="databaseHealthModalLabel">Database Health</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content for database health -->
                <iframe id="databaseHealthFrame" src="" width="100%" height="600px" frameborder="0"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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
                        <h6>Admin Accounts</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Verified</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Privilege</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($user['username']); ?></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <p class="text-xs text-secondary mb-0">
                                                        <?php if ($user['verified']): ?>
                                                            Yes
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-warning" onclick="startVerification('<?php echo $user['id']; ?>')">Start Verification</button>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <p class="text-xs text-secondary mb-0">
                                                        <?php if ($user['login']): ?>
                                                            <i class="fas fa-check text-success"></i> Online
                                                        <?php else: ?>
                                                            <i class="fas fa-times text-danger"></i> Offline
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($user['privilege']); ?></p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">No users found with privilege higher than 0.</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add buttons for system and database health check -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>System</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-around">
                            <button class="btn btn-outline-primary" onclick="checkSystemHealth()">
                                <i class="fas fa-heartbeat"></i> Check System Health
                            </button>
                            <button class="btn btn-outline-primary" onclick="checkDatabaseHealth()">
                                <i class="fas fa-database"></i> Check Database Health
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function startVerification(userId) {
        // Redirect to the verification process page with the user ID
        window.location.href = 'start_verification.php?user_id=' + userId;
    }

    function checkSystemHealth() {
        $('#systemHealthFrame').attr('src', 'system_health.php');
        $('#systemHealthModal').modal('show');
    }

    function checkDatabaseHealth() {
        $('#databaseHealthFrame').attr('src', 'database_health.php');
        $('#databaseHealthModal').modal('show');
    }
</script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include '../include/footer.php'; ?>
</body>
</html>
