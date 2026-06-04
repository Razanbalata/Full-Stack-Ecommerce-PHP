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

<!-- التنسيقات العصرية لتوزيع الكتل المترابطة الجديد -->
<style>
    .info-grid-top {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 35px;
        direction: rtl;
    }
    .info-quick-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.01);
        transition: transform 0.3s ease, border-color 0.3s ease;
    }
    .info-quick-card:hover {
        transform: translateY(-4px);
        border-color: #0f172a;
    }
    .info-quick-icon {
        width: 48px;
        height: 48px;
        background: #f8fafc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin: 0 auto 15px auto;
        color: #0f172a;
        border: 1px solid #f1f5f9;
    }
    .form-full-block {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 45px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        direction: rtl;
    }
    .modern-stacked-input {
        width: 100%;
        padding: 14px 18px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
        text-align: right;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }
    .modern-stacked-input:focus {
        border-color: #0f172a;
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.06);
    }
    .btn-submit-stacked {
        background-color: #0f172a;
        color: #ffffff;
        border: none;
        padding: 14px 40px;
        border-radius: 12px;
        font-weight: bold;
        font-size: 15px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s;
    }
    .btn-submit-stacked:hover {
        background-color: #1e293b;
    }
</style>

<main class="main-container" style="max-width: 1100px; margin: 0 auto; padding: 50px 20px; font-family: system-ui, -apple-system, sans-serif; direction: ltr; min-height: 80vh;">
    
    <!-- عرض الرسائل التنبيهية فوق المحتوى -->
    <div style="max-width: 1100px; margin: 0 auto;">
        <?php if ($success): ?>
            <div class="alert alert-success" style="background-color: #ecfdf5; color: #059669; padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; margin-bottom: 25px; border: 1px solid #a7f3d0; text-align: right;">✅ تم إرسال رسالتك بنجاح! سنرد عليك قريباً.</div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error" style="background-color: #fef2f2; color: #dc2626; padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; margin-bottom: 25px; border: 1px solid #fca5a5; text-align: right;">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>

    <!-- التقسيمة الجديدة (1): شبكة بطاقات المعلومات السريعة في الأعلى أفقياً -->
    <div class="info-grid-top">
        <!-- بطاقة العنوان -->
        <div class="info-quick-card">
            <div class="info-quick-icon">📍</div>
            <h4 style="margin: 0 0 6px 0; font-size: 13px; color: #64748b; font-weight: bold;">المقر الرئيسي</h4>
            <p style="margin: 0; color: #1e293b; font-size: 14px; font-weight: 500;">غزة - شارع عمر المختار، فلسطين</p>
        </div>

        <!-- بطاقة الهاتف -->
        <div class="info-quick-card">
            <div class="info-quick-icon">📞</div>
            <h4 style="margin: 0 0 6px 0; font-size: 13px; color: #64748b; font-weight: bold;">اتصال مباشر</h4>
            <p style="margin: 0; color: #1e293b; font-size: 14px; font-weight: 600; direction: ltr;">+970 59 XXX XXXX</p>
        </div>

        <!-- بطاقة البريد الإلكتروني -->
        <div class="info-quick-card">
            <div class="info-quick-icon">📧</div>
            <h4 style="margin: 0 0 6px 0; font-size: 13px; color: #64748b; font-weight: bold;">المراسلة الفورية</h4>
            <p style="margin: 0; color: #1e293b; font-size: 14px; font-weight: 600;">support@eliteshop.com</p>
        </div>

        <!-- بطاقة ساعات العمل -->
        <div class="info-quick-card">
            <div class="info-quick-icon">🕐</div>
            <h4 style="margin: 0 0 6px 0; font-size: 13px; color: #64748b; font-weight: bold;">ساعات الدوام</h4>
            <p style="margin: 0; color: #1e293b; font-size: 14px; font-weight: 500;">السبت - الخميس: 9ص - 9م</p>
        </div>
    </div>

    <!-- التقسيمة الجديدة (2): كتلة نموذج الإرسال العريضة بالكامل بالأسفل -->
    <div class="form-full-block">
        <div style="margin-bottom: 30px; text-align: right;">
            <h2 style="font-size: 24px; color: #0f172a; margin: 0 0 6px 0; font-weight: 800;">📩 أرسل لنا استفسارك</h2>
            <p style="font-size: 14px; color: #64748b; margin: 0;">إذا كان لديك أي سؤال أو تحتاج إلى مساعدة تخص طلباتك، لا تتردد بمراسلتنا.</p>
        </div>

        <form method="POST">
            <!-- حقول الاسم والبريد الإلكتروني بجانب بعضهما في الشاشات الكبيرة -->
            <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px;">
                <div style="flex: 1; min-width: 250px;">
                    <input type="text" name="name" required
                           placeholder="الاسم الكامل"
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                           class="modern-stacked-input">
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <input type="email" name="email" required
                           placeholder="البريد الإلكتروني"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           class="modern-stacked-input">
                </div>
            </div>

            <!-- حقل اختيار الموضوع -->
            <div style="margin-bottom: 20px;">
                <select name="subject" class="modern-stacked-input" style="cursor: pointer; color: #475569;">
                    <option value="استفسار عن منتج">استفسار عن منتج</option>
                    <option value="شكوى">تقديم شكوى</option>
                    <option value="اقتراح">اقتراح جديد</option>
                    <option value="طلب دعم">طلب دعم فني</option>
                </select>
            </div>

            <!-- نص الرسالة -->
            <div style="margin-bottom: 25px;">
                <textarea name="message" rows="6" required
                          placeholder="اكتب تفاصيل رسالتك هنا..."
                          class="modern-stacked-input" style="resize: none; font-family: inherit; min-height: 140px; line-height: 1.6;"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>

            <!-- أزرار التحكم والإرسال متناسقة لليمين -->
            <div style="text-align: right;">
                <button type="submit" class="btn-submit-stacked">
                    <span>إرسال الرسالة الآن</span> 📨
                </button>
            </div>
        </form>
    </div>

</main>

<?php require_once '../includes/footer.php'; ?>