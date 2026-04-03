<?php
require 'mailer.php';
$config = require 'config.php';

$dataFile = $config['data_file'];
$data = json_decode(file_get_contents($dataFile), true);

$token = $_GET['token'] ?? '';
$adminToken = $_GET['admintoken'] ?? '';

if (!isset($data[$token])) {
    die("Kein Zugriff: ung&uuml;ltiger Token");
}

$expectedToken = hash_hmac('sha256', $token, $config['admin_secret']);
if (!hash_equals($expectedToken, $adminToken)) {
    die("Kein Zugriff: ung&uuml;ltiger Admin-Token");
}

$request = $data[$token];
$type = $request['type'] ?? 'account';

$email = $request['email'];
$name = ($type === 'group') ? $request['group_name'] : $request['name'];


function saveData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function removeRequest($token, &$data, $file) {
    unset($data[$token]);
    saveData($file, $data);
}

function logRejection($file, $name, $email, $reason) {
    $line = date("Y-m-d H:i:s") . ";$name;$email;\"" . str_replace('"', '""', $reason) . "\"\n";
    file_put_contents($file, $line, FILE_APPEND);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['accept'])) {

        $expires = new DateTime('+49 hours');
        $expires_iso = $expires->format('Y-m-d\TH:i:s.000\Z');

        function slugify($text) {
            $text = strtolower($text);
            $text = preg_replace('/[^a-z0-9]+/', '-', $text);
            $text = trim($text, '-');
            return $text ?: 'user';
        }

        $slugName = slugify($name);

        $flowId = ($type === 'group') ? $config['group_flow_id'] : $config['flow_id'];

        $payload = [
            'name' => $slugName,
            'expires' => $expires_iso,
            'single_use' => true,
            'flow' => $flowId
        ];

        $ch = curl_init($config['api_url']);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'Authorization: Bearer ' . $config['api_key'],
                'content-type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($payload)
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            die("API Fehler: " . curl_error($ch));
        }

        $res = json_decode($response, true);
        curl_close($ch);

        if (!isset($res['pk'])) {
            echo "<pre>";
            print_r($res);
            echo "</pre>";
            die("Fehler bei Invite Erstellung");
        }
        $invite = $config['invite_url'] . $config['flow_name'] . "/?itoken=" . $res['pk'];
        $invitegroup = $config['invite_url'] . $config['group_flow_name'] . "/?itoken=" . $res['pk'];
        if ($type === 'group') {

    $body = "
    <h2>Dein Zugang</h2>
    <b>Dein Zugang wurde freigeschaltet.</b>
    <p>Klicke auf den folgenden Link:</p>
    <a href='$invitegroup'>$invitegroup</a>
    <br><br>
    <b>Nach dem du deinen Account angelegt hast kannst du &uuml;ber dieses Portal selbstst&auml;ndig Einladungen anlegen:</b><br>
    <a href='{$config['mgmt_url']}'>{$config['mgmt_url']}</a>
    <br><br>
    <b>Der Link ist 48 Stunden g&uuml;ltig.</b>
    ";

} else {

    $body = "
    <h2>Dein Zugang</h2>
    <b>Dein Zugang wurde freigeschaltet.</b>
    <p>Klicke auf den folgenden Link:</p>
    <a href='$invite'>$invite</a>
    <br><br>
    <b>Der Link ist 48 Stunden g&uuml;ltig.</b>
    ";

}

        sendMail($email, "Dein \"" . $config['product_name'] . "\" Zugang ist da!", $body);

        removeRequest($token, $data, $dataFile);

        echo "<title>" . $config['product_name'] . " - ADMIN</title>";
        echo "Einladung gesendet!";
        exit;
    }

    if (isset($_POST['reject'])) {

        $reason = ($type === 'group')
            ? $request['group_description']
            : $request['reason'];

        $body = '
        <h2>Deine Anfrage</h2>
        <p>Leider k&ouml;nnen wir dir aktuell keinen Zugang anbieten.</p>
        <p>Du kannst dich ggf. erneut bewerben.</p>
        <p>Bei Fragen kannst du dich gerne an <a href="mailto:support@161host.net">support@161host.net</a> wenden.</p>
        ';

        sendMail($email, $config['product_name'] . " - Anfrage abgelehnt", $body);

        logRejection($config['reject_file'], $name, $email, $reason);
        removeRequest($token, $data, $dataFile);

        echo "<title>" . $config['product_name'] . " - ADMIN</title>";
        echo "Anfrage abgelehnt!";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title><?= $config['product_name'] ?> - ADMIN</title>
<link href="tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-lg">

<h2 class="text-xl font-bold mb-4">Anfrage bearbeiten</h2>

<div class="mb-4 text-sm text-gray-600">

<?php if ($type === 'group'): ?>
<div class="p-3 mb-3 bg-yellow-100 border-l-4 border-yellow-500 font-bold">
GRUPPENANFRAGE
</div>

<b>Gruppenname:</b> <?= htmlspecialchars($request['group_name']) ?><br>
<b>Beschreibung:</b><br>
<?= nl2br(htmlspecialchars($request['group_description'])) ?><br><br>

<b>Ansprechpartner:</b> <?= htmlspecialchars($request['name']) ?><br>
<b>Email:</b> <?= htmlspecialchars($request['email']) ?>

<?php else: ?>

<b>Name:</b> <?= htmlspecialchars($request['name']) ?><br>
<b>Email:</b> <?= htmlspecialchars($request['email']) ?><br>
<b>Begr&uuml;ndung:</b><br>
<?= nl2br(htmlspecialchars($request['reason'])) ?>

<?php endif; ?>

</div>

<form method="POST" class="space-y-4">
<button name="accept" class="w-full bg-green-600 hover:bg-green-700 text-white p-3 rounded-lg">
Akzeptieren
</button>
<button name="reject" class="w-full bg-red-600 hover:bg-red-700 text-white p-3 rounded-lg">
Ablehnen
</button>
</form>

</div>
</body>
</html>
