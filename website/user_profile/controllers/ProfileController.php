<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Use the local config
require_once __DIR__ . '/../config/database.php';

class ProfileController {
    public $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /real_real_last/website/register_and_login/login.php');
            exit();
        }
        $userData = $this->getUserById($_SESSION['user_id']);
        $badgeHistory = $this->getBadgeHistory($_SESSION['user_id']);
        require __DIR__ . '/../views/userprofile.php';
    }

    public function update() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /real_real_last/website/register_and_login/login.php');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $stmt = $this->db->prepare('UPDATE users SET name=?, email=? WHERE id=?');
            $stmt->bind_param('ssi', $name, $email, $_SESSION['user_id']);
            $success = $stmt->execute();
            $_SESSION['profile_message'] = $success ? 'Profile updated!' : 'Update failed!';
        }
        header('Location: index.php');
        exit();
    }

    public function changePassword() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /real_real_last/website/register_and_login/login.php');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current = $_POST['current_password'];
            $new = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];
            $stmt = $this->db->prepare('SELECT password FROM users WHERE id=?');
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $stmt->bind_result($hash);
            $stmt->fetch();
            $stmt->close();
            if ($new !== $confirm) {
                $_SESSION['password_message'] = 'Passwords do not match!';
            } elseif ($current !== $hash) {
                $_SESSION['password_message'] = 'Current password incorrect!';
            } else {
                $stmt = $this->db->prepare('UPDATE users SET password=? WHERE id=?');
                $stmt->bind_param('si', $new, $_SESSION['user_id']);
                $stmt->execute();
                $_SESSION['password_message'] = $stmt->affected_rows > 0 ? 'Password updated!' : 'Update failed!';
            }
        }
        header('Location: index.php');
        exit();
    }

    private function getUserById($id) {
        $stmt = $this->db->prepare('SELECT id, name, email, points, number_of_followers, reports_cnt, flag FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    private function getBadgeHistory($id) {
        $stmt = $this->db->prepare('SELECT b.badge_name, b.badge_description, ub.created_at FROM user_badges ub JOIN badges b ON ub.badge_id = b.badge_id WHERE ub.user_id = ? ORDER BY ub.created_at DESC');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
