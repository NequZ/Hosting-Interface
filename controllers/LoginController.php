<?php
class LoginController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function login($email, $password) {
        // Validate form data
        if (empty($email) || empty($password)) {
            return "All fields are required!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format!";
        } else {
            // Check user credentials
            $stmt = $this->conn->prepare("SELECT * FROM nw_users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Update login status to 1 and set lastlogin to current timestamp
                $updateStmt = $this->conn->prepare("UPDATE nw_users SET login = 1, lastlogin = NOW() WHERE id = :id");
                $updateStmt->bindParam(':id', $user['id']);
                $updateStmt->execute();

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Log the login action
                logLoginLogoutAction($this->conn, $user['username'], 'User logged in');

                return "Login successful!";
            } else {
                return "Invalid email or password!";
            }
        }
    }
}
?>
