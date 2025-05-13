<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../../useclass.php';


class HomeController {
    public $db;
    private $post;
    private $comment;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->post = new Post($db);
        $this->comment = new Comment($db);
        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
            $this->user = new Admin($db);
        } else {
            $this->user = new User($db);
        }
    }

    private function checkUserSession() {
        if (!isset($_SESSION['user_id'])) {
            return;
        }
        if (!empty($_SESSION['is_admin'])) {
            return;
        }
        $stmt = $this->db->prepare('SELECT id FROM users WHERE id = ?');
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            session_unset();
            session_destroy();
            header('Location: index.php');
            exit();
        }
        $stmt->close();
    }

    public function index() {
        $this->checkUserSession();
        $sortBy = $_GET['sort'] ?? 'date';
        $order = $_GET['order'] ?? 'desc';
        $tag = $_GET['tag'] ?? '';
        $search = $_GET['search'] ?? '';
        $posts = [];
        $notifications = [];
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $resultNotif = $stmt->get_result();
            while ($notif = $resultNotif->fetch_assoc()) {
                $notifications[] = $notif;
            }
        }
        $result = $this->post->getAll($sortBy, $order, $tag, $search);
        while ($row = $result->fetch_assoc()) {
            $comments = $this->comment->getByPost($row['post_id']);
            $row['comments'] = $comments->fetch_all(MYSQLI_ASSOC);
            $row['is_following'] = false;
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $row['user_id']) {
                $userModel = new User($this->db);
                $row['is_following'] = $userModel->isFollowing($row['user_id'], $_SESSION['user_id']);
            }
            $posts[] = $row;
        }
        require __DIR__ . '/../views/layouts/main.php';
    }

    public function createPost() {
        $this->checkUserSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $this->post->user_id = $_SESSION['user_id'];
            $this->post->post_tag = $_POST['post_tag'];
            $this->post->content = $_POST['content'];
            $this->post->create();
        }
        header('Location: index.php');
        exit();
    }

    public function createComment() {
        $this->checkUserSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $this->comment->user_id = $_SESSION['user_id'];
            $this->comment->post_id = $_POST['post_id'];
            $this->comment->content = $_POST['content'];
            $this->comment->create();
        }
        header('Location: index.php');
        exit();
    }

    public function votePost() {
        $this->checkUserSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $this->post->post_id = $_POST['post_id'];
            $this->post->vote($_SESSION['user_id'], $_POST['vote_type']);
        }
        header('Location: index.php');
        exit();
    }

    public function editPost() {
        $this->checkUserSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $post_id = $_POST['post_id'];
            $new_content = $_POST['content'];
            $user_id = $_SESSION['user_id'];
            $user_points = $_SESSION['user_points'] ?? 0;
            // Rely on session to detect admin
            $is_admin = !empty($_SESSION['is_admin']); 

            if ($is_admin) {
                // Let admin edit any post
                $_SESSION['edit_reason'] = 'Edit allowed: You are an admin.';
                $this->post->edit($post_id, $new_content);
                header('Location: index.php');
                exit();
            }

            // Otherwise, check ownership or points
            $result = $this->db->query("SELECT user_id FROM posts WHERE post_id = " . intval($post_id));
            $row = $result->fetch_assoc();
            $is_owner = $row && $row['user_id'] == $user_id;

            if ($is_owner) {
                $_SESSION['edit_reason'] = 'Edit allowed: You are the owner of the post.';
                $this->post->edit($post_id, $new_content);
                header('Location: index.php');
                exit();
            } else if ($user_points >= 500) {
                $_SESSION['edit_reason'] = 'Edit allowed: You have 500+ points.';
                $this->post->edit($post_id, $new_content);
                header('Location: index.php');
                exit();
            } else {
                echo "<script>alert('You are not allowed to edit this post.'); window.location.href='index.php';</script>";
                exit();
            }
        }
        header('Location: index.php');
        exit();
    }

    public function editComment() {
        $this->checkUserSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $comment_id = $_POST['comment_id'];
            $new_content = $_POST['content'];
            $user_id = $_SESSION['user_id'];
            $user_points = $_SESSION['user_points'] ?? 0;
            $is_admin = ($user_id == 1 || ($_SESSION['is_admin'] ?? false));
            // جلب صاحب التعليق
            $result = $this->db->query("SELECT user_id FROM comments WHERE comment_id = " . intval($comment_id));
            $row = $result->fetch_assoc();
            $is_owner = $row && $row['user_id'] == $user_id;
            if ($is_owner || $user_points >= 500 || $is_admin) {
                $this->comment->edit($comment_id, $new_content);
            }
        }
        header('Location: index.php');
        exit();
    }

    public function voteComment() {
        $this->checkUserSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $comment_id = $_POST['comment_id'];
            $vote_type = $_POST['vote_type'];
            $this->comment->vote($comment_id, $_SESSION['user_id'], $vote_type);
        }
        header('Location: index.php');
        exit();
    }

    public function reportPost() {
        $this->checkUserSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $post_id = $_POST['post_id'];
            $reason = $_POST['reason'];
            $this->post->report($post_id, $_SESSION['user_id'], $reason);
        }
        header('Location: index.php');
        exit();
    }

    public function reportComment() {
        $this->checkUserSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $comment_id = $_POST['comment_id'];
            $reason = $_POST['reason'];
            $this->comment->report($comment_id, $_SESSION['user_id'], $reason);
        }
        header('Location: index.php');
        exit();
    }

    public function followUser() {
        $this->checkUserSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $user_id = $_POST['user_id']; // صاحب البوست
            $follower_user_id = $_SESSION['user_id']; // من يتابع
            if ($user_id != $follower_user_id) {
                $stmt = $this->db->prepare("SELECT 1 FROM followers WHERE user_id = ? AND follower_user_id = ?");
                $stmt->bind_param("ii", $user_id, $follower_user_id);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows == 0) {
                    $insert = $this->db->prepare("INSERT INTO followers (user_id, follower_user_id) VALUES (?, ?)");
                    $insert->bind_param("ii", $user_id, $follower_user_id);
                    $insert->execute();
                }
                $stmt->close();
            }
        }
        header('Location: index.php');
        exit();
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: /real_real_last/website/register_and_login/login.php');
        exit();
    }
}