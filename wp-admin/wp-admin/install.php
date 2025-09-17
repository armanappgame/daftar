<?php
session_start();

if (file_exists('../includes/config.php')) {
    header("Location: ../index.php");
    exit();
}

$error = '';
$success = false;

if ($_POST) {
    $host = trim($_POST['db_host'] ?? 'localhost');
    $name = trim($_POST['db_name'] ?? '');
    $user = trim($_POST['db_user'] ?? '');
    $pass = $_POST['db_pass'] ?? '';

    if (empty($name) || empty($user)) {
        $error = "نام دیتابیس و نام کاربری اجباری است.";
    } else {
        try {
            $pdo = new PDO("mysql:host=$host", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_persian_ci");

            $pdo = new PDO("mysql:host=$host;dbname=$name", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            $sql = file_get_contents('../sql/schema.sql');
            $pdo->exec($sql);

            $hashed = password_hash('1234', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT IGNORE INTO users (national_id, password, first_name, last_name, role, is_active) VALUES 
                ('admin', ?, 'مدیر', 'سیستم', 'admin', 1),
                ('teacher', ?, 'معلم', 'نمونه', 'teacher', 1),
                ('student', ?, 'دانش‌آموز', 'نمونه', 'student', 1)");
            $stmt->execute([$hashed, $hashed, $hashed]);

            $configContent = "<?php\n";
            $configContent .= "define('DB_HOST', " . var_export($host, true) . ");\n";
            $configContent .= "define('DB_NAME', " . var_export($name, true) . ");\n";
            $configContent .= "define('DB_USER', " . var_export($user, true) . ");\n";
            $configContent .= "define('DB_PASS', " . var_export($pass, true) . ");\n";
            $configContent .= "define('APP_NAME', 'دفتر کلاسی هوشمند');\n";
            $configContent .= "define('APP_URL', 'http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "');\n";
            $configContent .= "define('UPLOAD_DIR', __DIR__ . '/../uploads');\n";

            file_put_contents('../includes/config.php', $configContent);

            $success = true;

        } catch (Exception $e) {
            $error = "خطا در نصب: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نصب دفتر کلاسی هوشمند — مرحله ۱</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); min-height: 100vh; }
        .install-container { max-width: 600px; }
        .install-card { border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); border: none; }
        .step-indicator { display: flex; justify-content: space-between; margin-bottom: 2rem; }
        .step-dot { width: 30px; height: 30px; border-radius: 50%; background: #e9ecef; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .step-dot.active { background: #0d6efd; color: white; }
        .step-label { margin-top: 0.5rem; font-size: 0.9rem; color: #6c757d; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="container install-container">
        <div class="text-center mb-4">
            <h2 class="text-white fw-bold">🚀 دفتر کلاسی هوشمند</h2>
            <p class="text-white-50">نصب خودکار — مرحله ۱ از ۱</p>
        </div>

        <div class="step-indicator">
            <div class="text-center">
                <div class="step-dot active">۱</div>
                <div class="step-label">تنظیمات دیتابیس</div>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="card install-card text-center p-5">
                <div class="display-1 mb-3">🎉</div>
                <h3 class="mb-3">نصب با موفقیت انجام شد!</h3>
                <p class="text-muted mb-4">همه جداول و کاربران تست ایجاد شدند.</p>
                <a href="../index.php" class="btn btn-success btn-lg px-5">ورود به سامانه</a>
            </div>
        <?php else: ?>
            <div class="card install-card p-4">
                <h4 class="mb-4">تنظیمات دیتابیس</h4>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">هست دیتابیس</label>
                        <input type="text" name="db_host" class="form-control form-control-lg" value="localhost" required>
                        <div class="form-text">معمولاً localhost</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نام دیتابیس</label>
                        <input type="text" name="db_name" class="form-control form-control-lg" required>
                        <div class="form-text">دیتابیس باید وجود داشته باشد یا قابل ایجاد باشد</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نام کاربری دیتابیس</label>
                        <input type="text" name="db_user" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">رمز عبور دیتابیس</label>
                        <input type="password" name="db_pass" class="form-control form-control-lg">
                        <div class="form-text">اگر رمز ندارد، خالی بگذارید</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold">
                        ادامه نصب ✅
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
