<?php
class User {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function getUserById($id) {
        $stmt = $this->db->prepare('SELECT id, name, email, points, number_of_followers, reports_cnt, flag FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function updateUser($id, $data) {
        $stmt = $this->db->prepare('UPDATE users SET name=?, email=? WHERE id=?');
        $stmt->bind_param('ssi', $data['name'], $data['email'], $id);
        return $stmt->execute();
    }
    public function changePassword($id, $current, $new, $confirm) {
        if ($new !== $confirm) return 'Passwords do not match!';
        $stmt = $this->db->prepare('SELECT password FROM users WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($hash);
        $stmt->fetch();
        $stmt->close();
        if (!password_verify($current, $hash)) return 'Current password incorrect!';
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('UPDATE users SET password=? WHERE id=?');
        $stmt->bind_param('si', $newHash, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0 ? 'Password updated!' : 'Update failed!';
    }
    public function getBadgeHistory($id) {
        $stmt = $this->db->prepare('SELECT b.badge_name, b.badge_description, ub.created_at FROM user_badges ub JOIN badges b ON ub.badge_id = b.badge_id WHERE ub.user_id = ? ORDER BY ub.created_at DESC');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
} 