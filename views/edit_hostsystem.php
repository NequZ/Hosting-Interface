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
        $stmt = $conn->prepare("SELECT id, hostname, ip, systemcat FROM nw_hostsystems WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $hostSystem = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$hostSystem) {
            echo "Host system not found.";
            exit;
        }
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
            logHostSystemAction($conn, $hostname, $ip, $id, 'Edited the Hostsystem', $_SESSION['username']);

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
include '../include/head.php'; ?>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Edit Host System</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="form-group
                            <label for="hostname">Hostname</label>
                            <input type="text" class="form-control" id="hostname" name="hostname" value="<?php echo htmlspecialchars($hostSystem['hostname']); ?>" required>
                        </div>
                        <div class="form-group
                        <label for="ip">IP Address</label>
                        <input type="text" class="form-control" id="ip" name="ip" value="<?php echo htmlspecialchars($hostSystem['ip']); ?>" required>
                    </div>
                    <div class="form-group
                    <label for="systemcat">System Category</label>
                    <select class="form-control" id="systemcat" name="systemcat" required>
                        <option value="gameserver" <?php if ($hostSystem['systemcat'] === 'server') echo 'selected'; ?>>Gameserver</option>
                        <option value="kvm" <?php if ($hostSystem['systemcat'] === 'kvm') echo 'selected'; ?>>KVM</option>
                        <option value="storage" <?php if ($hostSystem['systemcat'] === 'storage') echo 'selected'; ?>>Storage</option>
                        <option value="image" <?php if ($hostSystem['systemcat'] === 'image') echo 'selected'; ?>>Image</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
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

    <?php include '../include/footer.php'; ?>
</body>
</html>