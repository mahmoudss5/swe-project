<?php
class Comment {
    private $db;
    public $user_id;
    public $post_id;
    public $content;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create() {
        $stmt = $this->db->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $this->post_id, $this->user_id, $this->content);
        return $stmt->execute();
    }

    public function getByPost($post_id) {
        $stmt = $this->db->prepare("SELECT comments.*, users.name as user_name FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY created_at ASC");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function edit($comment_id, $new_content) {
        $stmt = $this->db->prepare("UPDATE comments SET content = ? WHERE comment_id = ?");
        $stmt->bind_param("si", $new_content, $comment_id);
        return $stmt->execute();
    }

    public function vote($comment_id, $user_id, $vote_type) {
        $stmt = $this->db->prepare("SELECT vote_type FROM comment_votes WHERE comment_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $comment_id, $user_id);
        $stmt->execute();
        $stmt->bind_result($old_vote);
        if ($stmt->fetch()) {
            $stmt->close();
            if ($old_vote === $vote_type) {
                return false;
            } else {
                $update = $this->db->prepare("UPDATE comment_votes SET vote_type = ? WHERE comment_id = ? AND user_id = ?");
                $update->bind_param("sii", $vote_type, $comment_id, $user_id);
                return $update->execute();
            }
        } else {
            $stmt->close();
            $insert = $this->db->prepare("INSERT INTO comment_votes (comment_id, user_id, vote_type) VALUES (?, ?, ?)");
            $insert->bind_param("iis", $comment_id, $user_id, $vote_type);
            return $insert->execute();
        }
    }

    public function report($comment_id, $user_id, $reason) {
        $stmt = $this->db->prepare("INSERT INTO comment_reports (comment_id, user_id, report_reason) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $comment_id, $user_id, $reason);
        return $stmt->execute();
    }
} 