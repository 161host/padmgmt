<?php
require 'mailer.php';
$config = require 'config.php';

$dataFile = $config['data_file'];
$data = json_decode(file_get_contents($dataFile), true);

$token = $_GET['token'] ?? '';

if (!isset($data[$token])) {
    die("Der Token ist entweder abgelaufen oder ung&uuml;ltig!");
}

$data[$token]['verified'] = true;
file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

$adminToken = hash_hmac('sha256', $token, $config['admin_secret']);
$adminLink = $config['base_url'] . "/admin.php?token=$token&admintoken=$adminToken";

$message = "Neue Anfrage:\n"
    . $data[$token]['name'] . "\n"
    . "Bearbeiten: $adminLink";

file_get_contents("https://api.telegram.org/bot{$config['telegram_bot_token']}/sendMessage?" . http_build_query([
    'chat_id' => $config['telegram_chat_id'],
    'text' => $message
]));
echo "<title>" . $config['product_name'] . " - VERIFY</title>";
echo "E-Mail best&auml;tigt!";
?>
<script>
setTimeout(() => window.close(), 10000);
</script>
