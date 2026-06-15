<?php
// auth/login.php — تسجيل الدخول (مطابق للهوية البصرية والكارت الموحد)
session_start();
require_once __DIR__ . '/../includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /php-ecommerce-project/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']    ?? '');
    $pass  = trim($_POST['password'] ?? '');

    if (empty($email) || empty($pass)) {
        $error = 'يرجى إدخال البريد وكلمة المرور.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($pass, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];

            $redirect = $_GET['redirect'] ?? '/php-ecommerce-project/index.php';
            header("Location: $redirect");
            exit;
        } else {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
        }
    }
}

$pageTitle = 'تسجيل الدخول — EliteShop';
require_once '../includes/header.php';
//require_once '../includes/navbar.php';
?>

<main class="auth-container" style="max-width: 1200px; margin: 0 auto; padding: 60px 20px; font-family: system-ui, -apple-system, sans-serif; direction: rtl; min-height: 75vh; display: flex; align-items: center; justify-content: center;">
    
    <div class="auth-master-card" style="display: flex; flex-wrap: wrap; width: 100%; max-width: 900px; background: #ffffff; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.06); border: 1px solid #f1f5f9; overflow: hidden;">

        <div class="auth-form-wrap" style="flex: 1.2; min-width: 320px; padding: 45px;">
            <h2 style="font-size: 26px; color: #0f172a; margin-top: 0; margin-bottom: 10px; font-weight: bold;">مرحباً بك مجدداً 👋</h2>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 30px;">الرجاء إدخال بياناتك للوصول إلى حسابك.</p>

            <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'login_required'): ?>
                <div class="alert alert-error" style="background-color: #fef2f2; color: #dc2626; padding: 12px 20px; border-radius: 10px; font-size: 14px; margin-bottom: 20px; border: 1px solid #fca5a5;">⚠️ يجب تسجيل الدخول أولاً للوصول لهذه الصفحة.</div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error" style="background-color: #fef2f2; color: #dc2626; padding: 12px 20px; border-radius: 10px; font-size: 14px; margin-bottom: 20px; border: 1px solid #fca5a5;">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form" style="display: flex; flex-direction: column; gap: 20px;">
                
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #475569; font-weight: bold;">البريد الإلكتروني</label>
                    <input type="email" name="email" required
                           placeholder="example@domain.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           style="width: 100%; padding: 14px 18px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; transition: border-color 0.2s; box-sizing: border-box; text-align: right; direction: ltr;"
                           onfocus="this.style.borderColor='#0f172a'">
                </div>

                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 13px; color: #475569; font-weight: bold;">كلمة المرور</label>
                    <input type="password" name="password" required
                           placeholder="••••••••"
                           style="width: 100%; padding: 14px 18px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; transition: border-color 0.2s; box-sizing: border-box; text-align: right; direction: ltr;"
                           onfocus="this.style.borderColor='#0f172a'">
                </div>

                <button type="submit" class="btn-hero" style="background-color: #0f172a; color: white; border: none; padding: 14px; border-radius: 25px; font-weight: bold; font-size: 15px; cursor: pointer; transition: background-color 0.2s; margin-top: 10px; width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px;">
                    تسجيل الدخول ◀
                </button>
            </form>

            <p style="text-align: center; margin-top: 25px; font-size: 14px; color: #64748b;">
                ليس لديك حساب؟ <a href="register.php" style="color: #f59e0b; text-decoration: none; font-weight: bold; transition: color 0.2s;" onmouseover="this.style.color='#d97706'" onmouseout="this.style.color='#f59e0b'">سجّل حساباً جديداً</a>
            </p>
        </div>

        <div class="auth-info-panel" style="flex: 0.8; min-width: 280px; background-color: #0c2340; color: #ffffff; padding: 45px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; border-radius: 0;">
            <div style="font-size: 45px; margin-bottom: 15px;">🛍️</div>
            <h3 style="font-size: 24px; color: #ffffff; margin: 0 0 10px 0; font-weight: bold;">EliteShop</h3>
            <p style="color: #cbd5e1; font-size: 14px; line-height: 1.6; max-width: 220px; margin: 0;">أحدث التقنيات والأجهزة الذكية بأفضل الأسعار التنافسية.</p>
        </div>

    </div>
</main>

<?php require_once '../includes/footer.php'; ?>