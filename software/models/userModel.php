<?php
class UserModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getUserPoints($userId) {
        $sql = "SELECT points FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUserBadges($userId) {
        $sql = "SELECT b.badge_name, DATE(ub.created_at) AS created_at
                FROM user_badges ub
                JOIN badges b ON ub.badge_id = b.badge_id
                WHERE ub.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
