<?php
session_start();
require_once '../include/classcontroll.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch existing host system details
    try {
        $stmt = $conn->prepare("SELECT id, hostname, ip, systemcat, creationdate, modifydate FROM nw_hostsystems WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $hostSystem = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$hostSystem) {
            echo "Host system not found.";
            exit;
        }

        // Fetch daemon details for the host system
        $daemonStmt = $conn->prepare("SELECT daemonid, daemonstatus FROM nw_hostsystems_daemon WHERE hostid = :hostid");
        $daemonStmt->bindParam(':hostid', $id);
        $daemonStmt->execute();
        $daemons = $daemonStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch service details for the host system
        $serviceStmt = $conn->prepare("SELECT id, serviceid FROM nw_hostsystems_services WHERE hostid = :hostid");
        $serviceStmt->bindParam(':hostid', $id);
        $serviceStmt->execute();
        $services = $serviceStmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $hostname = $_POST['hostname'];
        $ip = $_POST['ip'];
        $systemcat = $_POST['systemcat'];

        try {
            // Update host system details
            $stmt = $conn->prepare("UPDATE nw_hostsystems SET hostname = :hostname, ip = :ip, systemcat = :systemcat, modifydate = NOW() WHERE id = :id");
            $stmt->bindParam(':hostname', $hostname);
            $stmt->bindParam(':ip', $ip);
            $stmt->bindParam(':systemcat', $systemcat);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Log the edit action
            logHostSystemAction($conn, $hostname, $ip, $id, 'Host was edited', $_SESSION['username']);

            echo "Host system updated successfully.";
            header('Location: servermanagement.php');
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    echo "No host system ID provided.";
    exit;
}

function getSystemCategory($category) {
    switch ($category) {
        case 'gameserver':
            return 'Gameserver';
        case 'kvm':
            return 'KVM';
        case 'storage':
            return 'Storage';
        case 'image':
            return 'Image';
        default:
            return $category;
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
                        <h6>Host System Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Overview</h6>
                                <table class="table table-striped">
                                    <tr>
                                        <th>Hostname:</th>
                                        <td><?php echo htmlspecialchars($hostSystem['hostname']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>IP Address:</th>
                                        <td><?php echo htmlspecialchars($hostSystem['ip']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>System Category:</th>
                                        <td><?php echo htmlspecialchars(getSystemCategory($hostSystem['systemcat'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Creation Date:</th>
                                        <td><?php echo htmlspecialchars($hostSystem['creationdate']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Modify Date:</th>
                                        <td><?php echo htmlspecialchars($hostSystem['modifydate']); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Edit Details</h6>
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="hostname">Hostname</label>
                                        <input type="text" class="form-control" id="hostname" name="hostname" value="<?php echo htmlspecialchars($hostSystem['hostname']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="ip">IP Address</label>
                                        <input type="text" class="form-control" id="ip" name="ip" value="<?php echo htmlspecialchars($hostSystem['ip']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="systemcat">System Category</label>
                                        <select class="form-control" id="systemcat" name="systemcat" required>
                                            <option value="gameserver" <?php if ($hostSystem['systemcat'] === 'gameserver') echo 'selected'; ?>>Gameserver</option>
                                            <option value="kvm" <?php if ($hostSystem['systemcat'] === 'kvm') echo 'selected'; ?>>KVM</option>
                                            <option value="storage" <?php if ($hostSystem['systemcat'] === 'storage') echo 'selected'; ?>>Storage</option>
                                            <option value="image" <?php if ($hostSystem['systemcat'] === 'image') echo 'selected'; ?>>Image</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Daemons</h6>
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Daemon ID</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($daemons)): ?>
                                        <?php foreach ($daemons as $daemon): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($daemon['daemonid']); ?></td>
                                                <td style="color: <?php echo $daemon['daemonstatus'] ? 'green' : 'red'; ?>">
                                                    <?php echo $daemon['daemonstatus'] ? '<i class="fa fa-check-circle"></i> Active' : '<i class="fa fa-times-circle"></i> Inactive'; ?>
                                                </td>
                                                <td>
                                                    <?php if (!$daemon['daemonstatus']): ?>
                                                        <button class="btn btn-success btn-sm" onclick="setupDaemon('<?php echo htmlspecialchars($daemon['daemonid']); ?>')"><i class="fa fa-cogs"></i> Setup</button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3">No daemons found for this host system.</td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Services</h6>
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Service ID</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($services)): ?>
                                        <?php foreach ($services as $service): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($service['serviceid']); ?></td>
                                                <td>
                                                    <a href="view_service.php?id=<?php echo htmlspecialchars($service['id']); ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2">No services found for this host system.</td>
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
    </div>
</main>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    function setupDaemon(daemonId) {
        if (confirm('Are you sure you want to setup this daemon?')) {
            // Implement your setup daemon logic here
            alert('Daemon setup initiated for daemon ID: ' + daemonId);
        }
    }
</script>

<?php include '../include/footer.php'; ?>
</body>
</html>
