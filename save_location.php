<?php
// التحقق من توكن الأمان
$valid_token = 'SECURE_TOKEN_12345';
$received_token = $_POST['token'] ?? '';

if ($received_token !== $valid_token) {
    header("HTTP/1.1 403 Forbidden");
    exit('غير مصرح بالوصول');
}

// معالجة البيانات الواردة
$data = json_decode($_POST['data'] ?? '', true);

if (!$data) {
    exit('بيانات غير صالحة');
}

// إعداد مسار ملف السجلات
$logDir = __DIR__ . '/logs/';
if (!is_dir($logDir)) {
    mkdir($logDir, 0700, true);
}

// إنشاء اسم ملف شهري
$logFile = $logDir . 'locations_' . date('Y-m') . '.txt';

// إضافة معلومات إضافية
$data['ip'] = $_SERVER['REMOTE_ADDR'];
$data['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$data['received_at'] = date('c');

// تنسيق البيانات للسجل
$logEntry = sprintf(
    "[%s] %s, %s (دقة: %s متر) - مصدر: %s - IP: %s\n",
    $data['timestamp'],
    $data['latitude'],
    $data['longitude'],
    $data['accuracy'],
    $data['source'],
    $data['ip']
);

// إذا كان هناك خطأ
if (isset($data['error'])) {
    $logEntry = sprintf(
        "[%s] خطأ: %s - IP: %s\n",
        $data['timestamp'],
        $data['error'],
        $data['ip']
    );
}

// إذا كانت هناك معلومات مدينة/دولة
if (isset($data['city']) && isset($data['country'])) {
    $logEntry = sprintf(
        "[%s] %s, %s (%s, %s) - دقة: %s متر - مصدر: %s - IP: %s\n",
        $data['timestamp'],
        $data['latitude'],
        $data['longitude'],
        $data['city'],
        $data['country'],
        $data['accuracy'],
        $data['source'],
        $data['ip']
    );
}

// حفظ البيانات في الملف
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

// إرسال رد ناجح
echo "OK";
?>
