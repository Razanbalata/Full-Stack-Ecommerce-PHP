<?php
// auth/register.php — إنشاء حساب (مطابق للهوية البصرية والكارت الموحد)
session_start();
require_once __DIR__ . '/../includes/db.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['full_name']  ?? '');
    $email = trim($_POST['email']      ?? '');
    $pass  = trim($_POST['password']   ?? '');
    $pass2 = trim($_POST['password2']  ?? '');
    $phone = trim($_POST['phone']      ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($pass)) {
        $error = 'يرجى تعبئة جميع الحقول المطلوبة.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'البريد الإلكتروني غير صحيح.';
    } elseif (strlen($pass) < 6) {
        $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.';
    } elseif ($pass !== $pass2) {
        $error = 'كلمتا المرور غير متطابقتين.';
    } else {
        // تحقق من عدم تكرار البريد الإلكتروني
        $chk = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $chk->bind_param('s', $email);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $error = 'هذا البريد الإلكتروني مسجل مسبقاً.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                "INSERT INTO users (full_name, email, password_hash, phone) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param('ssss', $name, $email, $hash, $phone);
            if ($stmt->execute()) {
                $_SESSION['user_id']   = $conn->insert_id;
                $_SESSION['full_name'] = $name;
                header('Location: /php-ecommerce-project/index.php');
                exit;
            } else {
                $error = 'حدث خطأ، حاول مجدداً.';
            }
        }
    }
}

$pageTitle = 'إنشاء حساب — EliteShop';
require_once '../includes/header.php';
//require_once '../includes/navbar.php';
?>

<main class="auth-container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px; font-family: system-ui, -apple-system, sans-serif; direction: rtl; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    
    <div class="auth-master-card" style="display: flex; flex-wrap: wrap; width: 100%; max-width: 950px; background: #ffffff; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.06); border: 1px solid #f1f5f9; overflow: hidden;">

        <div class="auth-form-wrap" style="flex: 1.3; min-width: 320px; padding: 40px 45px;">
            <h2 style="font-size: 24px; color: #0f172a; margin-top: 0; margin-bottom: 8px; font-weight: bold;">إنشاء حساب جديد ✨</h2>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 25px;">انضم إلينا اليوم واستمتع بتجربة تسوق فريدة ومخصصة.</p>

            <?php if ($error): ?>
                <div class="alert alert-error" style="background-color: #fef2f2; color: #dc2626; padding: 12px 20px; border-radius: 10px; font-size: 14px; margin-bottom: 20px; border: 1px solid #fca5a5;">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form" style="display: flex; flex-direction: column; gap: 15px;">
                
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <label style="font-size: 13px; color: #475569; font-weight: bold;">الاسم الكامل *</label>
                    <input type="text" name="full_name" required
                           placeholder="مثال: أحمد محمد"
                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                           style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; transition: border-color 0.2s; box-sizing: border-box; text-align: right;"
                           onfocus="this.style.borderColor='#0f172a'">
                </div>

                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <label style="font-size: 13px; color: #475569; font-weight: bold;">البريد الإلكتروني *</label>
                    <input type="email" name="email" required
                           placeholder="name@example.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; transition: border-color 0.2s; box-sizing: border-box; text-align: right; direction: ltr;"
                           onfocus="this.style.borderColor='#0f172a'">
                </div>

                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <label style="font-size: 13px; color: #475569; font-weight: bold;">رقم الهاتف</label>
                    <input type="tel" name="phone"
                           placeholder="059XXXXXXXX"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                           style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; transition: border-color 0.2s; box-sizing: border-box; text-align: right; direction: ltr;"
                           onfocus="this.style.borderColor='#0f172a'">
                </div>

                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <label style="font-size: 13px; color: #475569; font-weight: bold;">كلمة المرور * <span style="font-size: 11px; color: #94a3b8; font-weight: normal;">(6 أحرف على الأقل)</span></label>
                    <input type="password" name="password" required
                           placeholder="••••••••"
                           style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; transition: border-color 0.2s; box-sizing: border-box; text-align: right; direction: ltr;"
                           onfocus="this.style.borderColor='#0f172a'">
                </div>

                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <label style="font-size: 13px; color: #475569; font-weight: bold;">تأكيد كلمة المرور *</label>
                    <input type="password" name="password2" required
                           placeholder="••••••••"
                           style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; transition: border-color 0.2s; box-sizing: border-box; text-align: right; direction: ltr;"
                           onfocus="this.style.borderColor='#0f172a'">
                </div>

                <button type="submit" class="btn-hero" style="background-color: #0f172a; color: white; border: none; padding: 13px; border-radius: 25px; font-weight: bold; font-size: 15px; cursor: pointer; transition: background-color 0.2s; margin-top: 10px; width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px;">
                    إنشاء الحساب ◀
                </button>
            </form>

            <p style="text-align: center; margin-top: 20px; font-size: 14px; color: #64748b;">
                لديك حساب بالفعل؟ <a href="login.php" style="color: #f59e0b; text-decoration: none; font-weight: bold; transition: color 0.2s;" onmouseover="this.style.color='#d97706'" onmouseout="this.style.color='#f59e0b'">سجّل دخولك الآن</a>
            </p>
        </div>

        <div class="auth-info-panel" style="flex: 0.8; min-width: 280px; background-color: #0c2340; color: #ffffff; padding: 45px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; border-radius: 0;">
            <div style="font-size: 45px; margin-bottom: 15px;">🚀</div>
            <h3 style="font-size: 24px; color: #ffffff; margin: 0 0 10px 0; font-weight: bold;">EliteShop</h3>
            <p style="color: #cbd5e1; font-size: 14px; line-height: 1.6; max-width: 220px; margin: 0;">سجّل معنا لتتمكن من تتبع طلباتك، وإدارة سلة مشترياتك والحصول على أفضل العروض الحصرية.</p>
        </div>

    </div>
</main>

<?php require_once '../includes/footer.php'; ?>