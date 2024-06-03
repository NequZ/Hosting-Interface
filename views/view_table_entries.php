<?php
session_start();
require_once '../include/classcontroll.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['table'])) {
    $tableName = $_GET['table'];

    try {
        $stmt = $conn->prepare("SELECT * FROM " . $tableName);
        $stmt->execute();
        $tableEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    echo "No table name provided.";
    exit;
}
include '../include/head.php'; ?>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Table Entries: <?php echo htmlspecialchars($tableName); ?></h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <?php if (!empty($tableEntries)): ?>
                                    <?php foreach (array_keys($tableEntries[0]) as $columnName): ?>
                                        <th><?php echo htmlspecialchars($columnName); ?></th>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($tableEntries)): ?>
                                <?php foreach ($tableEntries as $entry): ?>
                                    <tr>
                                        <?php foreach ($entry as $value): ?>
                                            <td><?php echo htmlspecialchars($value); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="100%">No entries found for this table.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
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
