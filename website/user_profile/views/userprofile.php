<?php
// Error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'en';
$userName = $_SESSION['user_name'] ?? $_SESSION['name'] ?? $_SESSION['username'] ?? 'User';
$texts = [
    'en' => [
        'brandName' => 'Knowledge Exchange',
        'home' => 'Home',
        'profile' => 'Profile',
        'logout' => 'Logout'
    ],
    'ar' => [
        'brandName' => 'تبادل المعرفة',
        'home' => 'الرئيسية',
        'profile' => 'الملف الشخصي',
        'logout' => 'تسجيل الخروج'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: #fff !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding-top: 0.7rem;
            padding-bottom: 0.7rem;
        }
        .navbar .navbar-brand {
            font-weight: bold;
            font-size: 1.4rem;
            letter-spacing: 0.5px;
        }
        .navbar .nav-link.active {
            font-weight: 600;
        }
        .navbar .btn-danger {
            margin-left: 0.5rem;
        }
        .navbar .me-3 {
            margin-right: 1.5rem !important;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white">
  <div class="container-fluid">
    <a class="navbar-brand" href="/real_real_last/website/home_page/index.php"><?php echo $texts[$lang]['brandName']; ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="/real_real_last/website/home_page/index.php"><?php echo $texts[$lang]['home']; ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="/real_real_last/website/user_profile/index.php"><?php echo $texts[$lang]['profile']; ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/real_real_last/website/admin_dashboard/index.php">dashboard</a>
        </li>
      </ul>
      <div class="d-flex align-items-center">
        <div class="dropdown me-3">
         
          <ul class="dropdown-menu" aria-labelledby="notificationsDropdown">
            <li><span class="dropdown-item-text">No new notifications</span></li>
          </ul>
        </div>
        <span class="me-3"><?php echo htmlspecialchars($userName); ?></span>
        <a href="?lang=<?php echo $lang === 'en' ? 'ar' : 'en'; ?>" class="btn btn-outline-secondary btn-sm me-2">
          <?php echo $lang === 'en' ? 'عربي' : 'English'; ?>
        </a>
        <a href="/real_real_last/website/home_page/index.php?action=logout" class="btn btn-danger btn-sm"><?php echo $texts[$lang]['logout']; ?></a>
      </div>
    </div>
  </div>
</nav>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <?php if (empty($userData) || !is_array($userData)): ?>
                    <div class="alert alert-danger">User data could not be loaded.</div>
                <?php else: ?>
                    <h2 class="text-center mb-3"><?php echo htmlspecialchars($userData['name']); ?></h2>
                    <p class="text-center">Reputation points: <b><?php echo $userData['points']; ?></b></p>
                    <p class="text-center">Followers: <b><?php echo $userData['number_of_followers']; ?></b></p>
                    <p class="text-center">Reports: <b><?php echo $userData['reports_cnt']; ?></b></p>
                    <p class="text-center">Flagged: <b><?php echo $userData['flag'] ? 'Yes' : 'No'; ?></b></p>
                    <div class="text-center mb-3">
                        <button class="btn btn-outline-danger btn-sm">Report an Error</button>
                    </div>
                    <hr>
                    <form method="post" action="index.php?action=update">
                        <h5>Update Profile Info</h5>
                        <div class="row mb-3">
                            <div class="col">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($userData['name']); ?>" required>
                            </div>
                            <div class="col">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning">Save Changes</button>
                    </form>
                    <hr>
                    <?php if (isset($_SESSION['password_message'])): ?>
                        <div class="alert alert-info text-center">
                            <?php echo $_SESSION['password_message']; unset($_SESSION['password_message']); ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="index.php?action=changePassword">
                        <h5>Change Password</h5>
                        <div class="mb-2">
                            <input type="password" name="current_password" class="form-control" placeholder="Current Password" required>
                        </div>
                        <div class="mb-2">
                            <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
                        </div>
                        <div class="mb-2">
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Update Password</button>
                    </form>
                    <hr>
                    <h5>Badge History</h5>
                    <ul>
                        <?php if (!empty($badgeHistory) && is_array($badgeHistory)): ?>
                            <?php foreach ($badgeHistory as $badge): ?>
                                <li>
                                    <b><?php echo htmlspecialchars($badge['badge_name']); ?></b>
                                    (<?php echo htmlspecialchars($badge['badge_description']); ?>)
                                    - Awarded: <?php echo htmlspecialchars($badge['created_at']); ?>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No badge history found.</li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html> 