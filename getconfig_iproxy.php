<?php

$headers = array('Content-Type: application/json');
$bot_token = getenv("BOT_TOKEN");
$channel = getenv("CHANNEL_ID");

date_default_timezone_set("Asia/Tehran");
$time = date("H:i");
$date = date("Y/m/d");

// فقط از چنل خاص گرفته میشه
$sources = [
    "https://t.me/s/iProxy_up"
];

$configs = [];

foreach ($sources as $url) {
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

    preg_match_all('#(vmess|vless|trojan|ss)://[^\s"\'<>]+#', $html, $matches);
    if (!empty($matches[0])) {
        $configs = array_merge($configs, $matches[0]);
    }
}

if (empty($configs)) {
    exit("❌ هیچ کانفیگی پیدا نشد.");
}

shuffle($configs);
$link = $configs[0];

$type = "🔰 کانفیگ عمومی";
$desc = "مناسب برای استفاده عمومی در اکثر اپ‌ها";

if (strpos($link, "vmess://") === 0) {
    $type = "🚀 کانفیگ VMESS";
    $desc = "• رمزگذاری سریع و پایدار برای دور زدن فیلترینگ معمولی\n• مناسب برای کاربرانی که پایداری بالا می‌خوان";
} elseif (strpos($link, "vless://") === 0) {
    $type = "⚡️ کانفیگ VLESS";
    $desc = "• بدون رمزگذاری داخلی → سرعت بالا\n• قابل استفاده در V2RayNG، Shadowrocket و NapsternetV";
} elseif (strpos($link, "trojan://") === 0) {
    $type = "🛡 کانفیگ TROJAN";
    $desc = "• رمزگذاری کامل با TLS\n• مخصوص عبور از فیلتر هوشمند (DPI)";
} elseif (strpos($link, "ss://") === 0) {
    $type = "💨 کانفیگ SHADOWSOCKS";
    $desc = "• سبک‌ترین پروتکل برای اینترنت ضعیف\n• مناسب برای گیم، پیام‌رسان، و استفاده روزمره";
}

$msg = "{$type}\n";
$msg .= "━━━━━━━━━━━━━━━━━━━━━━\n";
$msg .= "{$desc}\n";
$msg .= "━━━━━━━━━━━━━━━━━━━━━━\n";
$msg .= "⏰ زمان: {$time}   📅 تاریخ: {$date}\n";
$msg .= "━━━━━━━━━━━━━━━━━━━━━━\n";
$msg .= "🔑 کانفیگ:\n{$link}\n";
$msg .= "━━━━━━━━━━━━━━━━━━━━━━\n";
$msg .= "💬 \"دسترسی یعنی زندگی. بدون محدودیت، بدون مرز.\"\n";
$msg .= "📡 کانال ما: {$channel}";

$data = array(
    'chat_id' => $channel,
    'text' => $msg,
    'parse_mode' => 'Markdown',
    'disable_web_page_preview' => true
);

$ch = curl_init("https://api.telegram.org/bot{$bot_token}/sendMessage");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);

echo "✅ Config sent as plain text!";
?>
