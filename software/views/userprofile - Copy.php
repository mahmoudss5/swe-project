<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title id="pageTitle">User Profile | Knowledge Exchange</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  
  <style>
    body {
      background-color: #f8f9fa;
    }
    .profile-card {
      background: #fff;
      border-radius: 10px;
      padding: 30px;
      margin-top: 30px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .badge-history li {
      margin-bottom: 6px;
    }
    .btn-orange {
      background-color: #ff6f00;
      color: #fff;
    }
    .btn-orange:hover {
      background-color: #e65c00;
    }
  </style>
</head>



<body>
<div class="container">
  <!-- Language Selector -->
  <div class="d-flex justify-content-end">
    <select id="languageSelect" class="form-select form-select-sm w-auto">
      <option value="en">English</option>
      <option value="ar">العربية</option>
    </select>
  </div>

  <div class="profile-card">
    <div class="row">
      <div class="col-md-4 text-center mb-3">
        <h4 id="username">John Doe</h4>

        <p class="text-muted"><span id="reputationLabel">Reputation points</span>: <strong id="reputation">






        <strong><?php echo $userPoints['points']; ?></strong>





        </strong></p>

        <p class="text-muted"><span id="followersLabel">Followers</span>: <strong id="followers">320</strong></p>
        <p class="text-muted"><span id="flagsLabel">Flags</span>: <strong id="flags">5</strong></p>

        <!-- Current Badge Moved Here -->
        <h6 class="mt-3" id="currentBadgeTitle">Current Badge</h6>
        <div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
          <span class="badge bg-success">Expert</span>
        </div>

        <button class="btn btn-sm btn-outline-danger" onclick="reportError()">Report an Error</button>
      </div>

      <div class="col-md-8">
        <h5 id="updateInfoTitle">Update Profile Info</h5>

      
      <form method="POST" action="update_profile.php">
  <div class="row mb-3">
    <div class="col">
      <label for="name" class="form-label">Full Name</label>
      <input type="text" class="form-control" id="name" name="name" value="John Doe">
    </div>
    <div class="col">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" value="john@example.com">
    </div>
  </div>
  <div class="mb-3">
    <label for="bio" class="form-label">Bio</label>
    <textarea class="form-control" id="bio" name="bio" rows="3">Passionate about sharing knowledge!</textarea>
  </div>
  <button type="submit" class="btn btn-orange mb-3" id="saveChangesBtn">Save Changes</button>
</form>


        <h5 id="changePasswordTitle">Change Password</h5>
        <form>
          <div class="mb-3">
            <label for="currentPassword" class="form-label" id="currentPasswordLabel">Current Password</label>
            <input type="password" class="form-control" id="currentPassword">
          </div>
          <div class="mb-3">
            <label for="newPassword" class="form-label" id="newPasswordLabel">New Password</label>
            <input type="password" class="form-control" id="newPassword">
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label" id="confirmPasswordLabel">Confirm New Password</label>
            <input type="password" class="form-control" id="confirmPassword">
          </div>
          <button type="submit" class="btn btn-orange" id="updatePasswordBtn">Update Password</button>
        </form>

        <hr class="my-4">

        <h6 id="badgeHistoryTitle">Badge History</h6>
        <ul class="list-unstyled badge-history">
<ul>






<?php foreach ($userBadges as $badge): ?>
  <li>🏅 Earned <b><?php echo $badge['badge_name']; ?></b> at: <?php echo $badge['created_at']; ?></li>
<?php endforeach; ?>
</ul>







        </ul>
      </div>
    </div>
  </div>
</div>




<script>
  function reportError() {
    alert("Thank you! Our team will review your report shortly.");
  }

  const translations = {
    en: {
      profileTitle: "User Profile",
      updateInfo: "Update Profile Info",
      usernameLabel: "Full Name",
      bioLabel: "Bio",
      saveChanges: "Save Changes",
      changePassword: "Change Password",
      currentPassword: "Current Password",
      newPassword: "New Password",
      confirmPassword: "Confirm New Password",
      updatePasswordBtn: "Update Password",
      reputation: "Reputation points",
      followers: "Followers",
      flags: "Flags",
      currentBadgeTitle: "Current Badge",
      badgeHistoryTitle: "Badge History"
    },
    ar: {
      profileTitle: "الملف الشخصي",
      updateInfo: "تحديث معلومات الملف",
      usernameLabel: "الاسم الكامل",
      bioLabel: "نبذة",
      saveChanges: "حفظ التغييرات",
      changePassword: "تغيير كلمة المرور",
      currentPassword: "كلمة المرور الحالية",
      newPassword: "كلمة المرور الجديدة",
      confirmPassword: "تأكيد كلمة المرور",
      updatePasswordBtn: "تحديث كلمة المرور",
      reputation: "النقاط",
      followers: "المتابعين",
      flags: "بلاغات",
      currentBadgeTitle: "الشارة الحالية",
      badgeHistoryTitle: "سجل الشارات"
    }
  };

  const langSelect = document.getElementById("languageSelect");

  langSelect.addEventListener("change", function () {
    const lang = this.value;
    const t = translations[lang];
    document.getElementById("pageTitle").innerText = t.profileTitle + " | Knowledge Exchange";
    document.getElementById("updateInfoTitle").innerText = t.updateInfo;
    document.getElementById("usernameLabel").innerText = t.usernameLabel;
    document.getElementById("bioLabel").innerText = t.bioLabel;
    document.getElementById("saveChangesBtn").innerText = t.saveChanges;
    document.getElementById("changePasswordTitle").innerText = t.changePassword;
    document.getElementById("currentPasswordLabel").innerText = t.currentPassword;
    document.getElementById("newPasswordLabel").innerText = t.newPassword;
    document.getElementById("confirmPasswordLabel").innerText = t.confirmPassword;
    document.getElementById("updatePasswordBtn").innerText = t.updatePasswordBtn;
    document.getElementById("reputationLabel").innerText = t.reputation;
    document.getElementById("followersLabel").innerText = t.followers;
    document.getElementById("flagsLabel").innerText = t.flags;
    document.getElementById("currentBadgeTitle").innerText = t.currentBadgeTitle;
    document.getElementById("badgeHistoryTitle").innerText = t.badgeHistoryTitle;

    document.documentElement.setAttribute("lang", lang);
    document.documentElement.setAttribute("dir", lang === "ar" ? "rtl" : "ltr");
  });
</script>
</body>
</html>
