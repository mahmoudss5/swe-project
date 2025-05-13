<?php
class Post {
    private $db;
    public $user_id;
    public $post_tag;
    public $content;
    public $post_id;
    public $up_vote;
    public $down_vote;
    public $created_at;
    public $updated_at;
    public function __construct($db) {
        $this->db = $db;
    }

    public function create() {
        $stmt = $this->db->prepare("INSERT INTO posts (user_id, post_tag, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $this->user_id, $this->post_tag, $this->content);
        return $stmt->execute();
    }

    public function getAll($sortBy = 'date', $order = 'desc', $tag = '', $search = '') {
        $orderBy = "p.created_at DESC";
        if ($sortBy == 'user') {
            $orderBy = "u.name " . ($order === 'asc' ? 'ASC' : 'DESC');
        } elseif ($sortBy == 'date') {
            $orderBy = "p.created_at " . ($order === 'asc' ? 'ASC' : 'DESC');
        } elseif ($sortBy == 'tag') {
            $orderBy = "p.post_tag ASC";
        }
        $where = [];
        $params = [];
        $types = '';
        if ($tag !== '') {
            $where[] = "FIND_IN_SET(?, p.post_tag)";
            $params[] = $tag;
            $types .= 's';
        }
        if ($search !== '') {
            $where[] = "p.content LIKE ?";
            $params[] = "%$search%";
            $types .= 's';
        }
        $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';
        $sql = "SELECT p.*, u.name as user_name FROM posts p JOIN users u ON p.user_id = u.id $whereSql ORDER BY $orderBy";
        $stmt = $this->db->prepare($sql);
        if (count($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    public function vote($user_id, $vote_type) {
        // تحقق إذا كان هناك تصويت سابق
        $stmt = $this->db->prepare("SELECT vote_type FROM post_votes WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $this->post_id, $user_id);
        $stmt->execute();
        $stmt->bind_result($old_vote);
        if ($stmt->fetch()) {
            $stmt->close();
            if ($old_vote === $vote_type) {
                // نفس التصويت، لا تفعل شيئًا
                return false;
            } else {
                // حدث التصويت
                $update = $this->db->prepare("UPDATE post_votes SET vote_type = ? WHERE post_id = ? AND user_id = ?");
                $update->bind_param("sii", $vote_type, $this->post_id, $user_id);
                return $update->execute();
            }
        } else {
            $stmt->close();
            // أضف تصويت جديد
            $insert = $this->db->prepare("INSERT INTO post_votes (post_id, user_id, vote_type) VALUES (?, ?, ?)");
            $insert->bind_param("iis", $this->post_id, $user_id, $vote_type);
            return $insert->execute();
        }
    }

    public function edit($post_id, $new_content) {
        $stmt = $this->db->prepare("UPDATE posts SET content = ? WHERE post_id = ?");
        $stmt->bind_param("si", $new_content, $post_id);
        return $stmt->execute();
    }

    public function report($post_id, $user_id, $reason) {
        $stmt = $this->db->prepare("INSERT INTO post_reports (post_id, user_id, report_reason) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $reason);
        return $stmt->execute();
    }
} 