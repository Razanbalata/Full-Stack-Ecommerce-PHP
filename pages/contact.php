<?php
// استدعاء الهيدر الموحد
include '../includes/header.php';
?>

<main style="max-width: 1100px; margin: 60px auto; padding: 0 20px; min-height: 550px; direction: ltr;">
    
    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; flex-direction: row; gap: 0; overflow: hidden; border: 1px solid #e2e8f0; flex-wrap: wrap;">
        
        <div style="direction:rtl; flex: 1.5; min-width: 320px; padding: 40px; text-align: right;">
            <h3 style="font-size: 22px; color: #1e293b; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; font-weight: bold;">
                📩 أرسل لنا رسالة
            </h3>
            
            <form id="contactForm" onsubmit="handleContactSubmit(event)" style="display: flex; flex-direction: column; gap: 20px;">
                <div>
                    <input type="text" id="senderName" placeholder="الاسم الكامل" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; background-color: #f8fafc; outline: none; font-size: 14px; box-sizing: border-box; text-align: right;">
                </div>
                
                <div>
                    <input type="email" id="senderEmail" placeholder="البريد الإلكتروني" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; background-color: #f8fafc; outline: none; font-size: 14px; box-sizing: border-box; text-align: right;">
                </div>
                
                <div>
                    <select id="subject" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; background-color: #f8fafc; outline: none; font-size: 14px; color: #475569; box-sizing: border-box; text-align: right;">
                        <option value="استفسار عن منتج">استفسار عن منتج</option>
                        <option value="شكوى">تقديم شكوى أو ملاحظة</option>
                        <option value="اقتراح">اقتراح لتطوير المتجر</option>
                    </select>
                </div>
                
                <div>
                    <textarea id="message" rows="5" placeholder="نص الرسالة..." required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; background-color: #f8fafc; outline: none; font-size: 14px; resize: vertical; box-sizing: border-box; font-family: inherit; text-align: right;"></textarea>
                </div>
                
                <div style="text-align: right;">
                    <button type="submit" style="background-color: #0f2c4a; color: white; border: none; padding: 12px 35px; border-radius: 20px; font-weight: bold; font-size: 14px; cursor: pointer; transition: 0.3s;">
                        🚀 إرسال الرسالة
                    </button>
                </div>
            </form>
        </div>

        <div style="direction:rtl; flex: 0.7; min-width: 150px; background-color: #0f2c4a; color: white; padding: 40px; display: flex; flex-direction: column; justify-content: start; text-align: right;">
            <h3 style="font-size: 22px; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; font-weight: bold;">
                📋 معلومات التواصل
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 25px; font-size: 15px;">
                <p style="display: flex; align-items: center; gap: 12px; margin: 0;">
                    📍 غزة - شارع عمر المختار، فلسطين
                </p>
                <p style="display: flex; align-items: center; gap: 12px; margin: 0;">
                    📞 +970 5X XXX XXXX
                </p>
                <p style="display: flex; align-items: center; gap: 12px; margin: 0;">
                    ✉️ support@eliteshop.com
                </p>
                <p style="display: flex; align-items: center; gap: 12px; margin: 0;">
                    🕒 السبت - الخميس: 9ص - 9م
                </p>
            </div>
        </div>

    </div>
</main>

<script>
function handleContactSubmit(event) {
    event.preventDefault(); 
    
    let name = document.getElementById('senderName').value;
    let email = document.getElementById('senderEmail').value;
    
    if(name.trim() === "" || email.trim() === "") {
        alert("يرجى ملء كافة الحقول الإلزامية أولاً!");
        return;
    }

    alert(`شكراً لتواصلكِ معنا يا ${name}. تم استلام رسالتكِ بنجاح وسنقوم بالرد عليكِ قريباً!`);
    document.getElementById('contactForm').reset();
}
</script>

<?php
// استدعاء الفوتر الموحد
include '../includes/footer.php';
?>