<?php
session_start();
require_once '../include/classcontroll.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

function fetchServices($conn) {
    try {
        $stmt = $conn->prepare("SELECT id, serviceid, username, created, active FROM nw_services");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

$services = fetchServices($conn);
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
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>Services</h6>
                    </div>
                    <button class="btn btn-icon btn-3 btn-primary btn-sm" type="button" onclick="window.location.href='create_service.php'">
                        <span class="btn-inner--icon"><i class="fa fa-plus"></i></span>
                        <span class="btn-inner--text">New Service</span>
                    </button>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Service ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Username</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Active</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($services)): ?>
                                    <?php foreach ($services as $service): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($service['serviceid']); ?></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($service['username']); ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($service['created']); ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs text-secondary mb-0">
                                                    <?php if ($service['active']): ?>
                                                        <span class="text-success"><i class="fa fa-check-circle"></i> Active</span>
                                                    <?php else: ?>
                                                        <span class="text-danger"><i class="fa fa-times-circle"></i> Inactive</span>
                                                    <?php endif; ?>
                                                </p>
                                            </td>
                                            <td class="align-middle">
                                                <button class="btn btn-icon btn-3 btn-success btn-sm" type="button" data-toggle="tooltip" data-original-title="View service" onclick="window.location.href='view_service.php?id=<?php echo htmlspecialchars($service['id']); ?>'">
                                                    <span class="btn-inner--icon"><i class="fa fa-eye"></i></span>
                                                    <span class="btn-inner--text">View</span>
                                                </button>
                                                <button class="btn btn-icon btn-3 btn-danger btn-sm" type="button" data-toggle="tooltip" data-original-title="Delete service" onclick="if(confirm('Are you sure you want to delete this service?')) window.location.href='deleteservice.php?id=<?php echo htmlspecialchars($service['id']); ?>'">
                                                    <span class="btn-inner--icon"><i class="fa fa-trash"></i></span>
                                                    <span class="btn-inner--text">Delete</span>
                                                </button>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">No Services found.</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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
