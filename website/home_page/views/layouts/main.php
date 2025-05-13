<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['path' => '/']);
    session_start();
}
if (isset($_POST['lang'])) {
    $_SESSION['lang'] = $_POST['lang'];
}

$lang = $_SESSION['lang'] ?? 'en';
$texts = [
    'en' => [
        'brandName' => 'Knowledge Exchange',
        'home' => 'Home',
        'questions' => 'Questions',
        'tags' => 'Tags',
        'topContributors' => 'Top Contributors',
        'popularTags' => 'Popular Tags',
        'noPosts' => 'No posts found.',
        'postedBy' => 'Posted by',
        'upvote' => '▲ Upvote',
        'downvote' => '▼ Downvote',
        'comments' => 'Comments',
        'noComments' => 'No comments yet.',
        'addComment' => 'Add a comment...',
        'commentBtn' => 'Comment',
        'postBtn' => 'Post',
        'searchPlaceholder' => 'Search posts...'
    ],
    'ar' => [
        'brandName' => 'تبادل المعرفة',
        'home' => 'الرئيسية',
        'questions' => 'الأسئلة',
        'tags' => 'الوسوم',
        'topContributors' => 'المساهمون الأفضل',
        'popularTags' => 'الوسوم الشائعة',
        'noPosts' => 'لا توجد منشورات.',
        'postedBy' => 'نشر بواسطة',
        'upvote' => '▲ تصويت إيجابي',
        'downvote' => '▼ تصويت سلبي',
        'comments' => 'التعليقات',
        'noComments' => 'لا توجد تعليقات بعد.',
        'addComment' => 'أضف تعليق...',
        'commentBtn' => 'تعليق',
        'postBtn' => 'نشر',
        'searchPlaceholder' => 'ابحث في المنشورات...'
    ]
];
$t = $texts[$lang];
$dir = $lang === 'ar' ? 'rtl' : 'ltr';

if (isset($this) && isset($this->db)) {
    $res = $this->db->query('SELECT COUNT(*) as c FROM users');
    $row = $res->fetch_assoc();
} else {
    echo '<div style="color:red; font-weight:bold;">DB connection not set in controller!</div>';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $t['brandName']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #ff6f00;
            --primary-hover: #e65c00;
            --secondary-color: #ffd180;
        }
        body { background-color: #f8f9fa; }
        .navbar { background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn-orange { background-color: var(--primary-color); color: white; }
        .btn-orange:hover { background-color: var(--primary-hover); color: white; }
        .search-bar { background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .post-card, .notification-card, .leaderboard-card { background-color: white; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s; margin-bottom: 20px; padding: 15px; }
        .post-card:hover { transform: translateY(-2px); }
        .tag { background-color: var(--secondary-color); border-radius: 5px; padding: 2px 8px; margin-right: 5px; font-size: 12px; display: inline-block; margin-bottom: 5px; }
        .action-buttons { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 10px; }
        .filter-section { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/"><?php echo $t['brandName']; ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="#">home</a></li>
                <li class="nav-item"><a class="nav-link" href="#"
                                        onclick="checkUserRole(<?php echo isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'true' : 'false' ?>)">dashboard</a>
                </li>
            </ul>
            <form method="POST" class="me-3">
                <select name="lang" onchange="this.form.submit()" class="form-select form-select-sm">
                    <option value="en" <?php if($lang=='en') echo 'selected'; ?>>English</option>
                    <option value="ar" <?php if($lang=='ar') echo 'selected'; ?>>العربية</option>
                </select>
            </form>
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-secondary dropdown-toggle position-relative" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <?php if (!empty($notifications)): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo count($notifications); ?>
                                    </span>
                            <?php endif; ?>
                            Notifications
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown" style="min-width:320px; max-height:350px; overflow-y:auto;">
                            <?php if (empty($notifications)): ?>
                                <li><span class="dropdown-item text-muted">No notifications</span></li>
                            <?php else: ?>
                                <?php foreach ($notifications as $notif): ?>
                                    <li>
                                            <span class="dropdown-item small">
                                                <span class="fw-bold"><?php echo htmlspecialchars($notif['notification_type']); ?>:</span>
                                                <?php echo htmlspecialchars($notif['notification_message']); ?>
                                                <br>
                                                <span class="text-muted" style="font-size:0.85em;">
                                                    <?php echo date('M j, Y H:i', strtotime($notif['created_at'])); ?>
                                                </span>
                                            </span>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <span class="fw-bold me-3"><i class="bi bi-person"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
                    <!-- Logout form with POST method -->
                    <form action="../register_and_login/login.php" method="post" style="display:inline;">
                        <button type="submit" class="btn btn-danger">Log out</button>
                    </form>


                <?php else: ?>
                    <a href="../../../register_and_login/login.php" class="btn btn-outline-primary me-2">Log in</a>
                    <a href="../../../register_and_login/register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Search Bar -->
            <div class="search-bar mb-4">
                <form method="GET" action="index.php">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="search" placeholder="<?php echo $t['searchPlaceholder']; ?>" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button class="btn btn-orange"><i class="bi bi-search"></i></button>
                    </div>
                    <div class="filter-section">
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="date" <?php if(($_GET['sort'] ?? 'date')=='date') echo 'selected'; ?>><?php echo $lang=='ar' ? 'الأحدث' : 'Latest'; ?></option>
                            <option value="user" <?php if(($_GET['sort'] ?? '')=='user') echo 'selected'; ?>><?php echo $lang=='ar' ? 'حسب المستخدم' : 'By User'; ?></option>
                            <option value="tag" <?php if(($_GET['sort'] ?? '')=='tag') echo 'selected'; ?>><?php echo $lang=='ar' ? 'حسب الوسم' : 'By Tag'; ?></option>
                        </select>
                        <select name="order" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="asc" <?php if(($_GET['order'] ?? 'desc')=='asc') echo 'selected'; ?>>A-Z / <?php echo $lang=='ar' ? 'تصاعدي' : 'Ascending'; ?></option>
                            <option value="desc" <?php if(($_GET['order'] ?? 'desc')=='desc') echo 'selected'; ?>>Z-A / <?php echo $lang=='ar' ? 'تنازلي' : 'Descending'; ?></option>
                        </select>
                        <input type="text" name="tag" class="form-control form-control-sm" placeholder="<?php echo $lang=='ar' ? 'وسم' : 'Tag'; ?>" value="<?php echo htmlspecialchars($_GET['tag'] ?? ''); ?>" style="max-width:120px;">
                    </div>
                </form>
            </div>
            <!-- Posts -->
            <?php require __DIR__ . '/../home/components/posts.php'; ?>
        </div>
        <!-- Sidebar -->
        <div class="col-lg-4">
            <?php require __DIR__ . '/../home/components/sidebar.php'; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function checkUserRole(isAdmin) {
        if (isAdmin) {
            window.location.href = "../admin_dashboard/public/index.php";
        } else {
            window.location.href = "../user_profile/index.php";
        }
    }
</script>
</body>
</html>