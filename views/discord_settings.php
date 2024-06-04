<?php
session_start(); // Start the session
require_once '../include/classcontroll.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login if session is not set
    exit;
}



$config = include('../config.php');

$discordEnabled = $config['discord']['enabled'];

if (!$discordEnabled) {
    echo '<br>
    <div class="alert alert-danger" role="alert">
        <span class="alert-icon"><i class="fas fa-thumbs-down"></i></span>
        <strong>Danger!</strong> Discord Module is disabled. Please enable it in the config.php file.
    </div>';
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <?php include '../include/head.php'; ?>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </head>
    <body>
    <!-- Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Discord Bot Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Status message will be inserted here -->
                    <p id="statusMessage">Checking status...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-cog"></i> General Discord Settings</h6>
                    </div>
                    <div class="card-body">
                        <a href="discord_security_settings.php" class="btn btn-primary"><i class="fas fa-shield-alt"></i>
                            Discord Security Settings
                        </a>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#statusModal">
                            <i class="fas fa-check"></i> Check Discord Status
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Section 1 -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-user"></i> User Management</h6>
                    </div>
                    <div class="card-body">
                        <!-- Add user management settings or details here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Section 2 -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-shield-alt"></i> Security Settings</h6>
                    </div>
                    <div class="card-body">
                        <!-- Add security settings or details here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Section 3 -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-bell"></i> Notification Settings</h6>
                    </div>
                    <div class="card-body">
                        <!-- Add notification settings or details here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#statusModal').on('show.bs.modal', function (event) {
                $.post('../controllers/CheckDiscordStatusController.php', function(data) {
                    $('#statusMessage').html(data);
                });
            });
        });
    </script>
    </body>
    </html>
    <?php
}
?>
