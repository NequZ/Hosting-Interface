<?php
session_start();
require_once '../include/classcontroll.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}


function fetchHostSystems($conn) {
    try {
        $stmt = $conn->prepare("SELECT id, hostname, ip, systemcat, creationdate, modifydate FROM nw_hostsystems");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

$hostSystems = fetchHostSystems($conn);
include '../include/head.php'; ?>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Hostsystems</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hostname</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">IP</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">System Category</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Creation Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Modify Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($hostSystems)): ?>
                                    <?php foreach ($hostSystems as $system): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($system['hostname']); ?></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($system['ip']); ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($system['systemcat']); ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($system['creationdate']); ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($system['modifydate']); ?></p>
                                            </td>
                                            <td class="align-middle">
                                                <button class="btn btn-icon btn-3 btn-success btn-sm" type="button" data-toggle="tooltip" data-original-title="View host system" onclick="window.location.href='view_hostsystem.php?id=<?php echo htmlspecialchars($system['id']); ?>'">
                                                    <span class="btn-inner--icon"><i class="fa fa-eye"></i></span>
                                                    <span class="btn-inner--text">View</span>
                                                </button>
                                                <button class="btn btn-icon btn-3 btn-danger btn-sm" type="button" data-toggle="tooltip" data-original-title="Delete host system" onclick="if(confirm('Are you sure you want to delete this host system?')) window.location.href='deletehostsystem.php?id=<?php echo htmlspecialchars($system['id']); ?>'">
                                                    <span class="btn-inner--icon"><i class="fa fa-trash"></i></span>
                                                    <span class="btn-inner--text">Delete</span>
                                                </button>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6">No host systems found.</td>
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
