<html>
<head>
<title>Anfrage - <?= $config['product_name'] ?></title>
</head>
<body>
<?php
require 'mailer.php';
$config = require 'config.php';

$dataFile = $config['data_file'];
$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

$type = $_POST['type'] ?? '';
$accept = $_POST['accept'] ?? '';

if (!$type || $accept !== 'yes') {
    $name = urlencode(trim($_POST['name'] ?? ''));
    echo "<script>window.location.href='/allehassennazis.php?name=$name';</script>";
    exit;
}

$token = bin2hex(random_bytes(16));

if ($type === 'account') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $reason = trim($_POST['reason']);

    if (!$name || !$email || !$reason) {
        die("Bitte alle Felder ausf&uuml;llen.");
    }

    $data[$token] = [
        'type' => 'account',
        'name' => $name,
        'email' => $email,
        'reason' => $reason,
        'verified' => false,
        'timestamp' => time()
    ];

} elseif ($type === 'group') {

    $groupName = trim($_POST['group_name']);
    $groupDescription = trim($_POST['group_description']);
    $contactName = trim($_POST['group_contact_name']);
    $contactEmail = trim($_POST['group_contact_email']);

    if (!$groupName || !$groupDescription || !$contactName || !$contactEmail) {
        die("Bitte alle Felder ausf&uuml;llen.");
    }

    $data[$token] = [
        'type' => 'group',
        'group_name' => $groupName,
        'group_description' => $groupDescription,
        'name' => $contactName,
        'email' => $contactEmail,
        'verified' => false,
        'timestamp' => time()
    ];

    $email = $contactEmail;
} else {
    die("Ung&uuml;ltiger Typ");
}

file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

$link = $config['base_url'] . "/verify.php?token=$token";

$body = "
<h2>Best&auml;tigung deiner Anfrage</h2>
<b>Du erh&auml;ltst diese E-Mail, weil jemand (vermutlich du) diese Adresse verwendet hat.</b>
<br><br>
<p>Bitte best&auml;tige deine Anfrage durch Klick auf diesen Link:</p>
<a href='$link'>$link</a>
<br><br>
<i>Wenn du das nicht warst, kannst du diese E-Mail ignorieren.</i>
<br>
<b>Nicht best&auml;tigte Anfragen werden nach 7 Tagen gel&ouml;scht.</b>
";

sendMail($email, $config['product_name'] . " - Anfrage bestaetigen", $body);
echo "<title>" . $config['product_name'] . " - Anfrage eingegangen</title>";
echo "Bitte pr&uuml;fe deine E-Mail zur Best&auml;tigung.";
?>
<script>
setTimeout(() => window.close(), 10000);
</script>
</body>
</html>
