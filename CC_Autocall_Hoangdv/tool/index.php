<?php
date_default_timezone_set("Asia/Ho_Chi_Minh");
$currentTime = date('H:i d-m-Y ');
require 'vendor/autoload.php'; // Đường dẫn đến autoload.php của thư viện

use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

 $command = 'xvfb-run --server-args="-screen 0, 1920x1080x24" wkhtmltoimage --load-error-handling ignore http://103.112.209.154/monitor/ /media/imgCC.png';

// Khởi tạo đối tượng Telegram API
$telegram = new Api('6585137930:AAEm1XLVeqtVgaZ6sZLSnXEaSVnnPeymgOk'); //6585137930:AAEm1XLVeqtVgaZ6sZLSnXEaSVnnPeymgOk //6270605477:AAExLD7871ALJiRHOqIG8pH40FOhmtDstNQ

// Chat ID của người nhận (thay YOUR_CHAT_ID bằng chat ID thực tế)
$chatId = '1357759489'; // -1002125329148 //1357759489 //-1001943556791

// Đường dẫn đến file output.png
$filePath = '/media/imgCC.png';
$inputFile = new InputFile($filePath);
$caption = 'Kính gửi Ban Lãnh Đạo!' . "\n" .
    'Kỹ thuật viên: Đinh Việt Hoàng' . "\n" .
    'Kính gửi - Báo Cáo Cuộc Gọi Hệ Thống AutoCall' . "\n" .
    'Thời gian: ' . $currentTime;

try {
    // Thực hiện lệnh và nhận kết quả
    $output = shell_exec($command);
	sleep(20);
    // Kiểm tra xem lệnh có chạy thành công hay không
    if ($output !== null) {
        // Gửi file qua Telegram
        $telegram->sendPhoto([
            'chat_id' => $chatId,
            'photo' => $inputFile,
            'caption' => $caption,
        ]);

        echo 'File đã được gửi thành công!';
    } else {
        echo 'Lệnh chạy không thành công.';
    }
} catch (\Telegram\Bot\Exceptions\TelegramSDKException $e) {
    echo 'Có lỗi xảy ra: ' . $e->getMessage();
}
