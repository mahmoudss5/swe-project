<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = "127.0.0.1";
$username = "newuser";
$password = "password";
$database = "project";

// Connect to database
$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_badge'])) {
        $name = mysqli_real_escape_string($conn, $_POST['badge_name']);
        $description = mysqli_real_escape_string($conn, $_POST['badge_description']);
        $privilege = mysqli_real_escape_string($conn, $_POST['privileges']);
        $points = (int)$_POST['points'];

        $sql = "INSERT INTO badges (badge_name, badge_description, privilege, points) 
                VALUES ('$name', '$description', '$privilege', $points)";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('تم إضافة الشارة بنجاح!');</script>";
        } else {
            echo "<script>alert('خطأ في إضافة الشارة: " . mysqli_error($conn) . "');</script>";
        }
    }
    
    if (isset($_POST['edit_badge'])) {
        $id = (int)$_POST['badge_id'];
        $name = mysqli_real_escape_string($conn, $_POST['badge_name']);
        $description = mysqli_real_escape_string($conn, $_POST['badge_description']);
        $privilege = mysqli_real_escape_string($conn, $_POST['privileges']);
        $points = (int)$_POST['points'];

        $sql = "UPDATE badges SET 
                badge_name = '$name',
                badge_description = '$description',
                privilege = '$privilege',
                points = $points
                WHERE badge_id = $id";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('تم تحديث الشارة بنجاح!');</script>";
        } else {
            echo "<script>alert('خطأ في تحديث الشارة: " . mysqli_error($conn) . "');</script>";
        }
    }
    
    if (isset($_POST['delete_badge'])) {
        $id = (int)$_POST['badge_id'];
        $sql = "DELETE FROM badges WHERE badge_id = $id";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('تم حذف الشارة بنجاح!');</script>";
        } else {
            echo "<script>alert('خطأ في حذف الشارة: " . mysqli_error($conn) . "');</script>";
        }
    }
}

// Get all badges
$badges = [];
$result = mysqli_query($conn, "SELECT * FROM badges ORDER BY points DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $badges[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Knowledge Exchange Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #eef2f5;
        }
        .dashboard {
            padding: 30px;
        }
        .section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .section h4 {
            margin-bottom: 20px;
        }
        .btn-group-sm button {
            margin-right: 5px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .right-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
<div class="container dashboard">
    <div class="top-bar">
        <h2 id="dashboardTitle">Admin Dashboard</h2>
        <div class="right-buttons">
            <button class="btn btn-outline-secondary" onclick="toggleLanguage()">🌐 English / العربية</button>
            <button class="btn btn-outline-danger" onclick="logout()">🚪 Logout</button>
        </div>
    </div>

    <div class="section">
        <h4 id="searchUsersTitle">Search Users</h4>
        <div class="input-group">
            <input type="text" class="form-control" id="searchInput" placeholder="Enter name, email, or user ID">
            <button class="btn btn-primary" onclick="searchUser()" id="searchButton">Search</button>
        </div>
    </div>

    <div class="section">
        <h4 id="userActionsTitle">User Actions</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th id="nameHeader">Name</th>
                    <th id="emailHeader">Email</th>
                    <th id="actionsHeader">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>101</td>
                    <td>Ali Hassan</td>
                    <td>ali@example.com</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-warning" id="suspendBtn">Suspend</button>
                            <button class="btn btn-outline-secondary" id="flagBtn">Flag</button>
                            <button class="btn btn-outline-danger" id="deleteBtn">Delete</button>
                            <button class="btn btn-outline-dark" id="revokeBtn">Revoke Badge</button>
                            <button class="btn btn-outline-success" onclick="openGrantModal()" id="grantBtn">Grant Badge</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 id="badgesTitle">Badges</h4>
            <button class="btn btn-primary btn-sm" onclick="openAddBadgeModal()" id="addBadgeBtn">Add Badge</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th id="badgeNameHeader">Name</th>
                    <th id="badgeDescHeader">Description</th>
                    <th id="badgePointsHeader">Required Points</th>
                    <th id="badgePrivilegesHeader">Privileges</th>
                    <th id="actionsHeader">Actions</th>
                </tr>
            </thead>
            <tbody id="badgeTable">
                <?php foreach ($badges as $badge): ?>
                <tr>
                    <td><?php echo htmlspecialchars($badge['badge_name']); ?></td>
                    <td><?php echo htmlspecialchars($badge['badge_description']); ?></td>
                    <td><?php echo htmlspecialchars($badge['points']); ?></td>
                    <td><?php echo htmlspecialchars($badge['privilege']); ?></td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-warning" onclick="editBadge(<?php echo htmlspecialchars(json_encode($badge)); ?>)">Edit</button>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="badge_id" value="<?php echo $badge['badge_id']; ?>">
                                <button type="submit" name="delete_badge" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this badge?')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Badge Modal -->
<div class="modal fade" id="addBadgeModal" tabindex="-1" aria-labelledby="addBadgeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBadgeTitle">Add New Badge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="badge_name" class="form-label">Badge Name</label>
                        <input type="text" class="form-control" id="badge_name" name="badge_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="badge_description" class="form-label">Description</label>
                        <textarea class="form-control" id="badge_description" name="badge_description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="privileges" class="form-label">Privileges</label>
                        <textarea class="form-control" id="privileges" name="privileges" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="points" class="form-label">Required Points</label>
                        <input type="number" class="form-control" id="points" name="points" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_badge" class="btn btn-primary">Add Badge</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Badge Modal -->
<div class="modal fade" id="editBadgeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBadgeTitle">Edit Badge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="editBadgeForm">
                    <input type="hidden" name="badge_id" id="edit_badge_id">
                    <div class="mb-3">
                        <label for="edit_badge_name" class="form-label">Badge Name</label>
                        <input type="text" class="form-control" id="edit_badge_name" name="badge_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_badge_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_badge_description" name="badge_description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_privileges" class="form-label">Privileges</label>
                        <textarea class="form-control" id="edit_privileges" name="privileges" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_points" class="form-label">Required Points</label>
                        <input type="number" class="form-control" id="edit_points" name="points" min="0" required>
                    </div>
                    <button type="submit" name="edit_badge" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Grant Badge -->
<div class="modal fade" id="grantBadgeModal" tabindex="-1" aria-labelledby="grantBadgeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="grantBadgeTitle">Grant Badge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="badgeSelect" id="selectBadgeLabel">Select a badge:</label>
                <select id="badgeSelect" class="form-select">
                    <?php foreach ($badges as $badge): ?>
                    <option value="<?php echo htmlspecialchars($badge['badge_name']); ?>"><?php echo htmlspecialchars($badge['badge_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="grantBtnModal">Grant</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelBtnModal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let isArabic = false;

function toggleLanguage() {
    isArabic = !isArabic;
    document.getElementById('dashboardTitle').textContent = isArabic ? 'لوحة تحكم المدير' : 'Admin Dashboard';
    document.getElementById('searchUsersTitle').textContent = isArabic ? 'البحث عن المستخدمين' : 'Search Users';
    document.getElementById('userActionsTitle').textContent = isArabic ? 'إجراءات المستخدم' : 'User Actions';
    document.getElementById('badgesTitle').textContent = isArabic ? 'الشارات' : 'Badges';
    document.getElementById('searchButton').textContent = isArabic ? 'بحث' : 'Search';
    document.getElementById('suspendBtn').textContent = isArabic ? 'تعليق' : 'Suspend';
    document.getElementById('flagBtn').textContent = isArabic ? 'إبلاغ' : 'Flag';
    document.getElementById('deleteBtn').textContent = isArabic ? 'حذف' : 'Delete';
    document.getElementById('revokeBtn').textContent = isArabic ? 'سحب الشارة' : 'Revoke Badge';
    document.getElementById('grantBtn').textContent = isArabic ? 'منح شارة' : 'Grant Badge';
    document.getElementById('addBadgeBtn').textContent = isArabic ? 'إضافة شارة' : 'Add Badge';
    document.getElementById('badgeNameHeader').textContent = isArabic ? 'الاسم' : 'Name';
    document.getElementById('badgeDescHeader').textContent = isArabic ? 'الوصف' : 'Description';
    document.getElementById('grantBadgeTitle').textContent = isArabic ? 'منح شارة' : 'Grant Badge';
    document.getElementById('selectBadgeLabel').textContent = isArabic ? 'اختر شارة:' : 'Select a badge:';
    document.getElementById('grantBtnModal').textContent = isArabic ? 'منح' : 'Grant';
    document.getElementById('cancelBtnModal').textContent = isArabic ? 'إلغاء' : 'Cancel';
    document.getElementById('nameHeader').textContent = isArabic ? 'الاسم' : 'Name';
    document.getElementById('emailHeader').textContent = isArabic ? 'البريد الإلكتروني' : 'Email';
    document.getElementById('actionsHeader').textContent = isArabic ? 'الإجراءات' : 'Actions';
    document.getElementById('badgePointsHeader').textContent = isArabic ? 'النقاط المطلوبة' : 'Required Points';
    document.getElementById('badgePrivilegesHeader').textContent = isArabic ? 'الامتيازات' : 'Privileges';
    document.getElementById('addBadgeTitle').textContent = isArabic ? 'إضافة شارة جديدة' : 'Add New Badge';
}

function openAddBadgeModal() {
    const modal = new bootstrap.Modal(document.getElementById('addBadgeModal'));
    modal.show();
}

function editBadge(badge) {
    document.getElementById('edit_badge_id').value = badge.badge_id;
    document.getElementById('edit_badge_name').value = badge.badge_name;
    document.getElementById('edit_badge_description').value = badge.badge_description;
    document.getElementById('edit_privileges').value = badge.privilege;
    document.getElementById('edit_points').value = badge.points;
    
    new bootstrap.Modal(document.getElementById('editBadgeModal')).show();
}

function logout() {
    window.location.href = '/login';
}

function searchUser() {
    const query = document.getElementById('searchInput').value;
    alert((isArabic ? 'البحث عن: ' : 'Search for: ') + query);
}

function openGrantModal() {
    const modal = new bootstrap.Modal(document.getElementById('grantBadgeModal'));
    modal.show();
}
</script>
</body>
</html>
<?php
mysqli_close($conn);
?>
