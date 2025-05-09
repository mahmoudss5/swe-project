<?php
include_once "register.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register | Knowledge Exchange Platform</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .register-container {
      max-width: 400px;
      margin: auto;
      margin-top: 100px;
      padding: 30px;
      border-radius: 8px;
      background: #ffffff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .form-control:focus {
      box-shadow: none;
      border-color: #ff6f00;
    }
    .btn-primary {
      background-color: #ff6f00;
      border: none;
    }
    .btn-primary:hover {
      background-color: #e65c00;
    }
    .lang-toggle {
      position: absolute;
      top: 20px;
      right: 20px;
    }
  </style>
</head>
<body>

<button class="btn btn-outline-secondary btn-sm lang-toggle" onclick="toggleLanguage()">عربي</button>

<div class="register-container">
  <h3 class="text-center mb-4" id="form-title">Register an Account</h3>
  <form Action='registerF.php' method="post">
    <div class="mb-3">
      <label for="name" class="form-label" id="name-label">Full Name</label>
      <input type="text" class="form-control" id="name" name="fullname"   placeholder="Your Name" required>
    </div>
    <div class="mb-3">
      <label for="email" class="form-label" id="email-label">Email address</label>
      <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label" id="password-label">Password</label>
      <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
    </div>
    <div class="mb-3">
      <label for="dataofbirth" class="form-label" id="dob-label">Date of Birth</label>
      <input type="date" class="form-control" id="dob" name="dob" placeholder="2000/1/1" required>
    </div>
    <div class="d-grid">
      <button type="submit" class="btn btn-primary" id="register-button">Register</button>
    </div>
    <div class="text-center mt-3">
      <small id="login-link-text">Already have an account? <a href="#">Login</a></small>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  let isArabic = false;
  function toggleLanguage() {
    isArabic = !isArabic;
    document.documentElement.lang = isArabic ? 'ar' : 'en';
    document.querySelector('.lang-toggle').innerText = isArabic ? 'English' : 'عربي';

    document.getElementById('form-title').innerText = isArabic ? 'تسجيل حساب' : 'Register an Account';
    document.getElementById('name-label').innerText = isArabic ? 'الاسم الكامل' : 'Full Name';
    document.getElementById('dob-label').innerText = isArabic ? 'تاريخ الميلاد' : 'Date of Birth';
    document.getElementById('email-label').innerText = isArabic ? 'البريد الإلكتروني' : 'Email address';
    document.getElementById('password-label').innerText = isArabic ? 'كلمة المرور' : 'Password';
    document.getElementById('register-button').innerText = isArabic ? 'تسجيل' : 'Register';
    document.getElementById('login-link-text').innerHTML = isArabic 
      ? 'لديك حساب؟ <a href="#">تسجيل الدخول</a>'
      : 'Already have an account? <a href="#">Login</a>';
  }
</script>
</body>
</html>