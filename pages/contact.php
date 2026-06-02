<?php
$pageTitle = 'اتصل بنا — EliteShop';
require_once '../includes/header.php';
require_once '../includes/db.php';
require_once '../includes/navbar.php';

$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'يرجى تعبئة جميع الحقول المطلوبة.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'البريد الإلكتروني غير صحيح.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('ssss', $name, $email, $subject, $message);
        $success = $stmt->execute();
        if (!$success) $error = 'حدث خطأ، حاول مجدداً.';
    }
}
?>

<main class="main-container" style="max-width: 1200px; margin: 0 auto; padding: 60px 20px; font-family: system-ui, -apple-system, sans-serif; direction: ltr; min-height: 80vh;">
    
    <!-- عرض رسائل الخطأ والنجاح فوق البطاقة بشكل متناسق -->
    <div style="max-width: 950px; margin: 0 auto;">
        <?php if ($success): ?>
            <div class="alert alert-success" style="background-color: #ecfdf5; color: #059669; padding: 12px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; margin-bottom: 20px; border: 1px solid #a7f3d0; text-align: right;">✅ تم إرسال رسالتك بنجاح! سنرد عليك قريباً.</div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error" style="background-color: #fef2f2; color: #dc2626; padding: 12px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; margin-bottom: 20px; border: 1px solid #fca5a5; text-align: right;">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>

    <div class="contact-combined-card" style="max-width: 950px; margin: 0 auto; background: #ffffff; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.06); display: flex; flex-wrap: wrap; overflow: hidden; border: 1px solid #f1f5f9;">
        
        <div class="contact-form-side" style="flex: 1.3; min-width: 320px; padding: 45px 40px 40px 40px; display: flex; flex-direction: column; direction:rtl">
            <h2 style="font-size: 24px; color: #0f172a; margin-top: 0; margin-bottom: 25px; font-weight: bold; text-align: right; display: flex; align-items: center; justify-content: flex-start; gap: 10px;">
                📩 أرسل لنا رسالة
            </h2>

            <form method="POST" style="display: flex; flex-direction: column; gap: 16px;">
                
                <!-- حقل الاسم كامل بدون ليبل خارجي، تماماً كما في الـ Placeholders بالصورة -->
                <div style="position: relative;">
                    <input type="text" name="name" required
                           placeholder="الاسم الكامل"
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                           style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; box-sizing: border-box; text-align: right; background-color: #fafafa;">
                </div>

                <!-- حقل البريد الإلكتروني -->
                <div>
                    <input type="email" name="email" required
                           placeholder="البريد الإلكتروني"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; box-sizing: border-box; text-align: right; background-color: #fafafa;">
                </div>

                <!-- قائمة خيار الموضوع المنسدلة -->
                <div>
                    <select name="subject" style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; background-color: #fafafa; cursor: pointer; box-sizing: border-box; text-align: right; color: #475569;">
                        <option value="استفسار عن منتج">استفسار عن منتج</option>
                        <option value="شكوى">شكوى</option>
                        <option value="اقتراح">اقتراح</option>
                        <option value="طلب دعم">طلب دعم</option>
                    </select>
                </div>

                <!-- نص الرسالة الكبير -->
                <div>
                    <textarea name="message" rows="5" required
                              placeholder="نص الرسالة..."
                              style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; box-sizing: border-box; resize: none; font-family: inherit; text-align: right; background-color: #fafafa; min-height: 120px;"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>

                <!-- زر الإرسال بيضاوي في زاوية اليسار السفلية كالصورة تماماً -->
                <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                    <button type="submit" style="background-color: #0f172a; color: white; border: none; padding: 12px 30px; border-radius: 25px; font-weight: bold; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(15,23,42,0.15);">
                        <span>إرسال الرسالة</span> 📨
                    </button>
                </div>
            </form>
        </div>

        <div class="contact-info-side" style="flex: 1; min-width: 280px; background: #0c2340; color: #ffffff; padding: 45px 35px; display: flex; flex-direction: column; justify-content: flex-start; text-align: right; border-top-left-radius: 0px; border-bottom-left-radius: 0px; direction:rtl">
            <h2 style="font-size: 22px; color: #ffffff; margin-top: 0; margin-bottom: 35px; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                📋 معلومات التواصل
            </h2>
            
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 25px;">
                <!-- العنوان -->
                <li style="display: flex; align-items: center; gap: 15px; font-size: 14px;">
                    <span style="font-size: 18px; color: rgba(255,255,255,0.7);">📍</span>
                    <span style="color: #e2e8f0;">غزة - شارع عمر المختار، فلسطين</span>
                </li>
                
                <!-- رقم الهاتف -->
                <li style="display: flex; align-items: center; gap: 15px; font-size: 14px;">
                    <span style="font-size: 18px; color: rgba(255,255,255,0.7);">📞</span>
                    <span style="color: #e2e8f0; direction: ltr; font-weight: 500;">+970 59 XXX XXXX</span>
                </li>
                
                <!-- البريد الإلكتروني -->
                <li style="display: flex; align-items: center; gap: 15px; font-size: 14px;">
                    <span style="font-size: 18px; color: rgba(255,255,255,0.7);">📧</span>
                    <span style="color: #e2e8f0; font-weight: 500;">support@eliteshop.com</span>
                </li>
                
                <!-- أوقات العمل -->
                <li style="display: flex; align-items: center; gap: 15px; font-size: 14px;">
                    <span style="font-size: 18px; color: rgba(255,255,255,0.7);">🕐</span>
                    <span style="color: #e2e8f0;">السبت - الخميس: 9ص - 9م</span>
                </li>
            </ul>
        </div>

    </div>
</main>

<?php require_once '../includes/footer.php'; ?>