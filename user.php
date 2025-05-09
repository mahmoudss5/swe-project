<?php
class User {
    private $conn;
    public $id;
    public $name;
    public $email;
    private $password;
    public $dob;
    public $bio;

    public function __construct($db, $name = "", $email = "", $password = "", $dob = "") {
        $this->conn = $db;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->dob = $dob;
    }

    public function emailExists() {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function save() {
        $stmt = $this->conn->prepare(
            "INSERT INTO users (name, email, password, date_of_birth) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $this->name, $this->email, $this->password, $this->dob);
        return $stmt->execute();
    }

    public function login($email, $password): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        $stmt = $this->conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
    
        // Declare variables first to avoid Intelephense undefined variable warnings
        $id = $name = $storedPassword = null;
        $stmt->bind_result($id, $name, $storedPassword);
    
        if ($stmt->fetch() && $password == $storedPassword) {
            $this->id = $id;
            $this->name = $name;
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            
            // Redirect to profile page after successful login
            header('Location: userprofile.html');
            exit();
            return true;
        }
    
        return false;
    }
    

    public function logout(): void {
        session_start();
        session_unset();
        session_destroy();
        // Redirect to login page after logout
        header('Location: login.php');
        exit();
    }

    public function resetPassword($email): bool {
        return $this->emailExists();
    }

    public function updateProfile($name, $email, $bio): bool {
        if ($email !== $this->email) {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $this->id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                return false;
            }
        }

        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, bio = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $bio, $this->id);
        
        if ($stmt->execute()) {
            $this->name = $name;
            $this->email = $email;
            $this->bio = $bio;
            return true;
        }
        return false;
    }

    public function updatePassword($currentPassword, $newPassword): bool {
        // First get the stored password
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if (!$row) {
            return false;
        }

        $storedPassword = $row['password'];

        // Direct comparison of passwords
        if ($currentPassword === $storedPassword) {
            // Update with new password directly
            $updateStmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->bind_param("si", $newPassword, $this->id);
            return $updateStmt->execute();
        }
        
        return false;
    }
}
?>
