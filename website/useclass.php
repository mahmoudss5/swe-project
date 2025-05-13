<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);class User {
    protected $conn;
    public $id;
    public $name;
    public $email;
    private $password;
    public $dob;
    public $bio;

    // Constructor للـ front-end
    public function __construct($db, $name = "", $email = "", $password = "", $dob = "") {
        $this->conn = $db;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->dob = $dob;
    }

    // ========== 1. التسجيل ==========
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
        $result = $stmt->execute();
        if ($result) {
            $this->id = $this->conn->insert_id;
            $this->name = $this->name; 
        }
        return $result;
    }

    // ========== 2. تسجيل الدخول ==========
    public function login($email, $password): bool {
        // Check users table (with flag)
        $stmt = $this->conn->prepare("SELECT id, name, password, flag FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $id = $name = $storedPassword = null;
        $flag = false;
        $stmt->bind_result($id, $name, $storedPassword, $flag);

        if ($stmt->fetch()) {
            // First, check if user is suspended
            if ($flag) {
                echo "<script>alert('you can not log in as you suspend'); window.location.href='login.php';</script>";

                $stmt->close();
                return false;
            }

            // Then, check password
            if ($password === $storedPassword) {
                $this->id = $id;
                $this->name = $name;
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                $_SESSION['is_admin'] = false;
                $stmt->close();
                return true;
            }
        }
        $stmt->close();

        // Check admin table (no flag column here)
        $stmt = $this->conn->prepare("SELECT id, name, password FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $id = $name = $storedPassword = null;
        $stmt->bind_result($id, $name, $storedPassword);

        if ($stmt->fetch() && $password === $storedPassword) {
            $this->id = $id;
            $this->name = $name;
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['is_admin'] = true;
            $stmt->close();
            return true;
        }

        $stmt->close();
        return false;
    }

    // ========== 3. تسجيل الخروج ==========
    public function logout(): void {
        session_start();
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit();
    }

    // ========== 4. تحديث البروفايل ==========
    public function updateProfile($name, $email, $bio): bool {
        if ($email !== $this->email) {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $this->id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) return false;
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

    // ========== 5. تغيير كلمة السر ==========
    public function updatePassword($currentPassword, $newPassword): bool {
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) return false;
        $storedPassword = $row['password'];

        if ($currentPassword === $storedPassword) {
            $updateStmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->bind_param("si", $newPassword, $this->id);
            return $updateStmt->execute();
        }

        return false;
    }

    // ========== 6. علاقة متابعة ==========
    public function isFollowing($user_id, $follower_user_id): bool {
        $stmt = $this->conn->prepare("SELECT 1 FROM followers WHERE user_id = ? AND follower_user_id = ?");
        $stmt->bind_param("ii", $user_id, $follower_user_id);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    // ========== 7. وظائف لوحة الإدارة ==========
    public function getAll(): array {
        $sql = "SELECT * FROM users";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function search($query): array {
        $q = "%$query%";
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ? OR id = ?");
        $stmt->bind_param("ssi", $q, $q, $query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function suspend($id): bool {
        $stmt = $this->conn->prepare("UPDATE users SET flag = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function unsuspend($id): bool {
        $stmt = $this->conn->prepare("UPDATE users SET flag = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function deleteUser($id): bool {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

class Admin extends User {
    public function editAnyPost($postId, $newContent) {
        $stmt = $this->conn->prepare("UPDATE posts SET content = ? WHERE post_id = ?");
        $stmt->bind_param("si", $newContent, $postId);
        return $stmt->execute();
    }

}
?>