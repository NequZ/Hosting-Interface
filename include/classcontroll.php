<?php
require_once '../controllers/DatabaseController.php';
require_once '../controllers/ConfigController.php';
require_once '../controllers/RegisterController.php';
require_once '../controllers/LoginController.php'; // Include the new LoginController

// Initialize ConfigController and apply debug mode settings
$configController = new ConfigController();
$configController->applyDebugMode();

// Initialize DatabaseController and establish database connection
$databaseController = new DatabaseController();
$conn = $databaseController->connect();

// Create instances of RegisterController and LoginController
$registerController = new RegisterController($conn);
$loginController = new LoginController($conn);

// Display debug mode and database connection status if debug mode is enabled
if ($configController->isDebugMode()) {
    echo '<div class="alert alert-danger" role="alert">
            <strong>Danger!</strong> Debug mode is enabled!
          </div>';

    if ($conn) {
        echo '<div class="alert alert-primary" role="alert">
            <strong>Success!</strong> Database connection established!
          </div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">
            <strong>Error!</strong> Database connection failed!
          </div>';
    }
}

// Function to fetch and display users with privileges higher than 0
function fetchUsersWithPrivileges($conn) {
    try {
        // Query to get all users
        $userStmt = $conn->prepare("SELECT id, username, email, created, verified, login FROM nw_users");
        $userStmt->execute();
        $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($users as $user) {
            // Check privilege for each user
            $privilegeStmt = $conn->prepare("SELECT privilege FROM nw_users_privileges WHERE userid = :userid AND privilege > 0");
            $privilegeStmt->bindParam(':userid', $user['id']);
            $privilegeStmt->execute();
            $privilege = $privilegeStmt->fetch(PDO::FETCH_ASSOC);

            if ($privilege) {
                $user['privilege'] = $privilege['privilege'];
                $result[] = $user;
            }
        }
        return $result;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

// Function to fetch user data for verification
function fetchDataForVerification($conn) {
    if (isset($_GET['user_id'])) {
        $userId = $_GET['user_id'];
        try {
            $stmt = $conn->prepare("SELECT id, username, email, created, verified, login FROM nw_users WHERE id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                return $user;
            } else {
                echo "User not found.";
                return null;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    } else {
        return null;
    }
}

function fetchHostSystemData($conn) {
    if (isset($_GET['id'])) {
        $systemId = $_GET['id'];
        try {
            $stmt = $conn->prepare("SELECT id, hostname, ip, systemcat, creationdate, modifydate FROM nw_hostsystems WHERE id = :system_id");
            $stmt->bindParam(':system_id', $systemId);
            $stmt->execute();
            $system = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($system) {
                return $system;
            } else {
                echo "Host system not found.";
                return null;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    } else {
        return null;
    }
}


// Fetch users with privileges
$users = fetchUsersWithPrivileges($conn);

// Fetch user data for verification if user_id is set
$verificationUser = fetchDataForVerification($conn);

if (!function_exists('logLoginLogoutAction')) {
    function logLoginLogoutAction($conn, $username, $action) {
        try {
            $stmt = $conn->prepare("INSERT INTO nw_log_login_logout (username, date, action) VALUES (:username, NOW(), :action)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':action', $action);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

if (!function_exists('logout')) {
    function logout($conn, $username) {
        try {
            // Set login status to 0 in nw_users table
            $stmt = $conn->prepare("UPDATE nw_users SET login = 0 WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            // Log the logout action
            logLoginLogoutAction($conn, $username, 'User logged out');

            // Destroy the session and redirect to login page
            session_unset();
            session_destroy();
            header('Location: login.php');
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
if (!function_exists('getUsersCount')) {
    function getUsersCount($conn) {
        try {
            $stmt = $conn->prepare("SELECT COUNT(*) as user_count FROM nw_users");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['user_count'];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return 0;
        }
    }
}

// Example usage of the getUsersCount function
$userCount = getUsersCount($conn);
if (!function_exists('fetchDataForVerification')) {
    // Function to fetch user data for verification
    function fetchDataForVerification($conn) {
        if (isset($_GET['user_id'])) {
            $userId = $_GET['user_id'];
            try {
                $stmt = $conn->prepare("SELECT id, username, email, created, verified, login FROM nw_users WHERE id = :user_id");
                $stmt->bindParam(':user_id', $userId);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    return $user;
                } else {
                    echo "User not found.";
                    return null;
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return null;
            }
        } else {

            return null;
        }
    }
}
if (!function_exists('logVerificationAction')) {
    function logVerificationAction($conn, $username) {
        try {
            $stmt = $conn->prepare("INSERT INTO nw_log_verification (username, date) VALUES (:username, NOW())");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

if (!function_exists('verifyUserNow')) {
    function verifyUserNow($conn, $userId) {
        try {
            $stmt = $conn->prepare("UPDATE nw_users SET verified = 21 WHERE id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}

function verifyUserNow($conn, $userId) {
    try {
        $stmt = $conn->prepare("UPDATE nw_users SET verified = 21 WHERE id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return true;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}


if (!function_exists('getPhpVersion')) {
    function getPhpVersion() {
        $version = phpversion();
        $shortVersion = implode('.', array_slice(explode('.', $version), 0, 2));
        return $shortVersion;
    }
}

function checkDatabaseHealth($conn, $dbName) {
    try {
        $stmt = $conn->prepare("SELECT 1");
        $stmt->execute();

        $tablesStmt = $conn->prepare("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = :dbName");
        $tablesStmt->bindParam(':dbName', $dbName);
        $tablesStmt->execute();
        $tablesResult = $tablesStmt->fetch(PDO::FETCH_ASSOC);
        $tableCount = $tablesResult['table_count'];


        return [
            "status" => "Database connection is healthy.",
            "table_count" => $tableCount,
        ];
    } catch (PDOException $e) {
        return [
            "status" => "Database connection failed: " . $e->getMessage(),
            "table_count" => 0
        ];
    }
}
function logHostSystemAction($conn, $hostname, $ip, $hostid, $action, $username) {
    try {
        $stmt = $conn->prepare("INSERT INTO nw_log_hostsystem_actions (hostname, ip, hostid, action, username, date) VALUES (:hostname, :ip, :hostid, :action, :username, NOW())");
        $stmt->bindParam(':hostname', $hostname);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':hostid', $hostid);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


function fetchLogTables($conn) {
    try {
        $stmt = $conn->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name LIKE 'nw_log%'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

function generateUUID() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

?>
