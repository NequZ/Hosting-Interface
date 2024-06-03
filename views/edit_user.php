<?php
session_start(); // Start the session
require_once '../include/classcontroll.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login if session is not set
    exit;
}

$userId = isset($_GET['id']) ? $_GET['id'] : null;
$user = null;

if ($userId) {
    $user = getUserWithID($conn, $userId);
    $usernamewithid = getUserWithID($conn, $userId);
}
$userprivilegelevel = fetchUsersPrivilegeWithUserID($conn, $userId);

include '../include/head.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>User Overview</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <?php if ($user): ?>
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <tbody>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User ID</th>
                                    <td class="text-sm font-weight-bold mb-0"><?php echo htmlspecialchars($user['id']); ?></td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Username</th>
                                    <td class="text-sm font-weight-bold mb-0"><?php echo htmlspecialchars($user['username']); ?></td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                    <td class="text-sm font-weight-bold mb-0"><?php echo htmlspecialchars($user['email']); ?></td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Verified</th>
                                    <td class="text-sm font-weight-bold mb-0">
                                        <?php if ($user['verified'] == 1): ?>
                                            <i class="fa fa-thumbs-up text-success"></i> Verified
                                        <?php else: ?>
                                            <i class="fa fa-thumbs-down text-danger"></i> Not Verified
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <td class="text-sm font-weight-bold mb-0">
                                        <?php if ($user['login'] == 1): ?>
                                            <i class="fa fa-circle text-success"></i> Online
                                        <?php else: ?>
                                            <i class="fa fa-circle-o text-danger"></i> Offline
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Creation Date</th>
                                    <td class="text-sm font-weight-bold mb-0"><?php echo htmlspecialchars($user['created']); ?></td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Privilege Level</th>
                                    <td class="text-sm font-weight-bold mb-0">Level <?php echo htmlspecialchars($userprivilegelevel['privilege']); ?></td>
                                </tr>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    <td class="text-sm font-weight-bold mb-0">
                                        <a href="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-icon btn-2 btn-primary btn-sm">
                                            <span class="btn-inner--icon"><i class="fa fa-edit"></i></span>
                                            <span class="btn-inner--text">Edit User</span>
                                        </a>
                                        <a href="services.php?id=<?php echo htmlspecialchars($user['username']); ?>" class="btn btn-icon btn-2 btn-info btn-sm">
                                            <span class="btn-inner--icon"><i class="fa fa-cogs"></i></span>
                                            <span class="btn-inner--text">View Services</span>
                                        </a>
                                        <a href="view_invoices.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-icon btn-2 btn-warning btn-sm">
                                            <span class="btn-inner--icon"><i class="fa fa-file-text"></i></span>
                                            <span class="btn-inner--text">View Invoices</span>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">No user found with the given ID.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../include/footer.php'; ?>
</body>
</html>
