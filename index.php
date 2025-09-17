<?php
if (!file_exists('includes/config.php')) {
    header("Location: wp-admin/install.php");
    exit();
}

session_start();
require_once 'includes/auth.php';
$auth = new Auth();

if ($_POST) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($auth->login($username, $password)) {
        $role = $_SESSION['role'];
        if ($role === 'admin') header("Location: admin/dashboard.php");
        elseif ($role === 'teacher') header("Location: teacher/dashboard.php");
        elseif ($role === 'student') header("Location: student/dashboard.php");
        exit();
    } else {
        $error = "نام کاربری یا رمز عبور اشتباه است.";
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به دفتر کلاسی هوشمند</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex align-items-center justify-content-center vh-100 px-3">
        <div class="col-md-8 col-lg-4">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">📚 دفتر کلاسی هوشمند</h2>
                        <p class="text-muted">ورود به سامانه</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger border-0 rounded-3 mb-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-medium">نام کاربری</label>
                            <input type="text" name="username" class="form-control form-control-lg rounded-3" placeholder="admin / teacher / student" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium">رمز عبور</label>
                            <input type="password" name="password" class="form-control form-control-lg rounded-3" placeholder="1234" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold">
                            ورود به سامانه
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="#" class="text-decoration-none text-primary small" data-bs-toggle="modal" data-bs-target="#forgotModal">
                            فراموشی رمز عبور؟
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('forgotForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('✅ درخواست شما ثبت شد. معلم یا مدیر به زودی رمز جدیدی برای شما تنظیم خواهد کرد.');
            bootstrap.Modal.getInstance(document.getElementById('forgotModal')).hide();
        });
    </script>
</body>
</html>
