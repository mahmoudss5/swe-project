  <?php
  ?>
  <!DOCTYPE html>
  <html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Knowledge Exchange Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #f8f9fa;
      }
      .login-container {
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
      .lang-switch {
        position: absolute;
        top: 20px;
        right: 30px;
      }
      
    </style>
  </head>


  <body>
  <div class="lang-switch">
    <button class="btn btn-sm btn-outline-secondary" onclick="toggleLang()">عربي / EN</button>
  </div>
  <div class="login-container" id="loginForm" lang="en" dir="ltr">
    <h3 class="text-center mb-4" id="title">Knowledge Exchange Login</h3>
    <form action="loginF.php"  method="post">
      <div class="mb-3">
        <label for="email" class="form-label" id="emailLabel">Email address</label>
        <input type="email" class="form-control" id="email"  name="email"   placeholder="you@example.com" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label" id="passwordLabel">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary" id="loginBtn">Login</button>
      </div>
      <div class="text-center mt-3">
        <small id="signupText">Don't have an account? <a href="register.php">Sign up</a></small>
      </div>
    </form>
  </div>



  <script>
    let isArabic = false;

    function toggleLang() {
      isArabic = !isArabic;
      const form = document.getElementById('loginForm');
      form.lang = isArabic ? 'ar' : 'en';
      form.dir = isArabic ? 'rtl' : 'ltr';

      document.getElementById('title').textContent = isArabic ? 'تسجيل الدخول إلى منصة تبادل المعرفة' : 'Knowledge Exchange Login';
      document.getElementById('emailLabel').textContent = isArabic ? 'البريد الإلكتروني' : 'Email address';
      document.getElementById('passwordLabel').textContent = isArabic ? 'كلمة المرور' : 'Password';
      document.getElementById('loginBtn').textContent = isArabic ? 'تسجيل الدخول' : 'Login';
      document.getElementById('signupText').innerHTML = isArabic ? 'ليس لديك حساب؟ <a href="/register_and_login/register.php">إنشاء حساب</a>' : "Don't have an account? <a href='/register_and_login/register.php'>Sign up</a>";
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
  </html>
