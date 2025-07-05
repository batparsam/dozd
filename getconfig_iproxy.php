<?php

$headers = ['Content-Type: application/json'];
$bot_token = getenv("BOT_TOKEN");
$channel = getenv("CHANNEL_ID");

date_default_timezone_set("Asia/Tehran");
$time = date("H:i");
$date = date("Y/m/d");

$url = "https://t.me/s/iProxy_up";

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

// برای نمایش درست حروف و علامت‌ها
$decoded_html = html_entity_decode($html);

// استخراج پیام‌های داخل <div class="tgme_widget_message_text">...</div>
preg_match_all('/<div class="tgme_widget_message_text js-message_text" dir="auto">(.*?)<\/div>/s', $decoded_html, $matches);

$found_message = null;

foreach (array_reverse($matches[1]) as $textBlock) {
    $plain = strip_tags($textBlock); // حذف تگ‌های HTML
    if (mb_stripos($plain, "کانفیگ") !== false) {
        $found_message = $plain;
        break;
    }
}

if (!$found_message) {
    $msg = "❌ هیچ پیامی با کلمه «کانفیگ» پیدا نشد.";
} else {
    $msg  = "📡 جدیدترین پیام دارای «کانفیگ» از iProxy:\n";
    $msg .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $msg .= $found_message . "\n";
    $msg .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    $msg .= "⏰ {$time}   📅 {$date}\n";
    $msg .= "📢 @iProxy_up → 📬 @VPNByBaT";
}

// ارسال به تلگرام
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

echo "📩 RESPONSE: $response\n";