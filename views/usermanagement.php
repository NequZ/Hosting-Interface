<?php
session_start(); // Start the session
require_once '../include/classcontroll.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login if session is not set
    exit;
}

// Fetch users
$users = fetchUsers($conn);

include '../include/head.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between">
                    <h6>Users</h6>
                </div>
                <button class="btn btn-icon btn-3 btn-primary btn-sm" type="button" onclick="window.location.href='create_service.php'">
                    <span class="btn-inner--icon"><i class="fa fa-plus"></i></span>
                    <span class="btn-inner--text">Create User</span>
                </button>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User UUID</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Username</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Verified</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Creation Date</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($user['id']); ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?php echo htmlspecialchars($user['username']); ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">
                                                <?php if ($user['verified'] == 1): ?>
                                                    <i class="fa fa-thumbs-up text-success"></i> Verified
                                                <?php else: ?>
                                                    <i class="fa fa-thumbs-down text-danger"></i> Not Verified
                                                <?php endif; ?>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">
                                                <?php if ($user['login'] == 1): ?>
                                                    <i class="fa fa-circle text-success"></i> Online
                                                <?php else: ?>
                                                    <i class="fa fa-circle-o text-danger"></i> Offline
                                                <?php endif; ?>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?php echo htmlspecialchars($user['created']); ?></p>
                                        </td>
                                        <td>
                                            <button class="btn btn-icon btn-2 btn-primary btn-sm" type="button" onclick="window.location.href='edit_user.php?id=<?php echo $user['id']; ?>'">
                                                <span class="btn-inner--icon"><i class="fa fa-edit"></i></span>
                                            </button>
                                            <button class="btn btn-icon btn-2 btn-danger btn-sm" type="button" onclick="window.location.href='delete_user.php?id=<?php echo $user['id']; ?>'">
                                                <span class="btn-inner--icon"><i class="fa fa-trash"></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">
                                        <p class="text-center">No users found.</p>
                                    </td>
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

<?php include '../include/footer.php'; ?>
</body>
