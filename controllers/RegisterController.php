<?php
class RegisterController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function register($name, $email, $password) {
        // Validate form data
        if (empty($name) || empty($email) || empty($password)) {
            return "All fields are required!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format!";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Generate UUID in PHP
            $userid = $this->generateUuid();

            // Start a transaction
            $this->conn->beginTransaction();

            try {
                // Insert user into database
                $stmt = $this->conn->prepare("INSERT INTO nw_users (id, username, email, password, verified, login, created, lastlogin) VALUES (:id, :name, :email, :password, 0, 0, current_timestamp(1), '0000-00-00 00:00:00.0')");
                $stmt->bindParam(':id', $userid);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashed_password);

                if ($stmt->execute()) {
                    // Insert default privilege into the nw_users_privileges table
                    $privilegeStmt = $this->conn->prepare("INSERT INTO nw_users_privileges (userid, privilege) VALUES (:userid, 0)");
                    $privilegeStmt->bindParam(':userid', $userid);

                    if ($privilegeStmt->execute()) {
                        // Commit the transaction
                        $this->conn->commit();
                        return "Registration successful! You can now <a href='login.php'>login</a>.";
                    } else {
                        // Roll back the transaction if privilege insertion fails
                        $this->conn->rollBack();
                        return "Error: Could not insert user privileges.";
                    }
                } else {
                    // Roll back the transaction if user insertion fails
                    $this->conn->rollBack();
                    return "Error: Could not execute the query.";
                }
            } catch (Exception $e) {
                // Roll back the transaction in case of any exception
                $this->conn->rollBack();
                return "Error: " . $e->getMessage();
            }
        }
    }

    private function generateUuid() {
        // Generate a UUID
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
?>
