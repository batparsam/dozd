<?php

$headers = ['Content-Type: application/json'];
$bot_token = getenv("BOT_TOKEN");
$channel = getenv("CHANNEL_ID");

date_default_timezone_set("Asia/Tehran");
$time = date("H:i");
$date = date("Y/m/d");

$url = "https://t.me/s/iProxy_up";

// خواندن سورس HTML
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT => "Mozilla/5.0"
]);

$html = curl_exec($curl);
curl_close($curl);

// تبدیل کدهای HTML مثل &#x3A; به :
$decoded_html = html_entity_decode($html);

// گرفتن تمام پیام‌های متنی داخل DIV مربوط به پیام‌ها
preg_match_all('/<div class="tgme_widget_message_text js-message_text" dir="auto">(.*?)<\/div>/s', $decoded_html, $matches);

if (!empty($matches[1])) {
    $latest = strip_tags(end($matches[1])); // آخرین پیام
    $msg  = "📡 پیام جدید از @iProxy_up:\n";
    $msg .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $msg .= $latest . "\n";
    $msg .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $msg .= "📅 {$date}   ⏰ {$time}\n📡 @VPNByBaT";
} else {
    $msg = "❌ هیچ پیام متنی پیدا نشد.";
}

// ارسال پیام
$data = [
    'chat_id' => $channel,
    'text' => $msg,
    'parse_mode' => 'HTML',
    'disable_web_page_preview' => true
];

$ch = curl_init("https://api.telegram.org/bot{$bot_token}/sendMessage");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "📤 Response: $response\n";