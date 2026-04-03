<?php
require __DIR__ . '/vendor/autoload.php';
$config = require 'config.php';

use Jumbojett\OpenIDConnectClient;

session_start();

if (!isset($_SESSION['user'])) {
    $oidc = new OpenIDConnectClient(
        $config['oidc_url'],
        $config['client_id'],
        $config['client_secret']
    );

    $oidc->setRedirectURL($config['redirect_uri']);
    $oidc->addScope(['openid', 'profile']);
    $oidc->authenticate();

    $user = $oidc->requestUserInfo('preferred_username')
        ?? $oidc->requestUserInfo('name');

    $_SESSION['user'] = $user;
}

$user = $_SESSION['user'];

$invite_link = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['expires'];
    $type = $_POST['invite_type'] ?? 'single';

    $dt = DateTime::createFromFormat('d.m.Y', $date);

    if (!$dt) {
        $error = "Ungültiges Datum.";
    } else {
        $now = new DateTime();
        $max = clone $now;
        if ($type === 'single') {
            $max->modify('+7 days');
        } else {
            $max->modify('+2 days');
        }

        if ($dt < $now || $dt > $max) {
            $error = "Datum muss innerhalb der nächsten " . ($type === 'single' ? "7" : "2") . " Tage liegen.";
        } else {
            $dt->setTime(0, 0, 0);
            $expires_iso = $dt->format('Y-m-d') . 'T00:00:00.000Z';

            $payload = [
                'name' => $user,
                'expires' => $expires_iso,
                'fixed_data' => ['group' => 'example'],
                'single_use' => $type === 'single',
                'flow' => $config['flow_id']
            ];

            $ch = curl_init($config['api_url']);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'accept: application/json',
                    'content-type: application/json',
                    'Authorization: Bearer ' . $config['api_key']
                ],
                CURLOPT_POSTFIELDS => json_encode($payload)
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $error = "API Fehler: " . curl_error($ch);
            } else {
                $data = json_decode($response, true);

                if (isset($data['pk'])) {
                    $invite_link = $config['invite_url'] . $data['pk'];
                } else {
                    $error = "Unerwartete API-Antwort.";
                }
            }

            curl_close($ch);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link href="/tailwind.css" rel="stylesheet">
    <script src="/qrcode.min.js"></script>
    <title><?= htmlspecialchars($config['product_name']) ?> MGMT</title>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-6 rounded-2xl shadow w-96">
    <h1 class="text-xl font-bold mb-4">Invite Link Generator</h1>

    <p class="mb-4">Eingeloggt als: <b><?= htmlspecialchars($user) ?></b></p>

    <form method="POST" onsubmit="return validateDate()">

        <label class="block mb-2">Art der Einladung</label>
        <select name="invite_type" id="inviteType" class="w-full border p-2 rounded mb-4" onchange="updateInfo()">
            <option value="single" <?= ($_POST['invite_type'] ?? '') === 'single' ? 'selected' : '' ?>>Einfache Einladung</option>
            <option value="multi" <?= ($_POST['invite_type'] ?? '') === 'multi' ? 'selected' : '' ?>>Mehrfach Einladung</option>
        </select>

        <div id="multiInfo" class="mb-4 font-bold text-red-600 hidden">
            Bei Mehrfach-Einladung maximal 2 Tage gültig
        </div>

        <label class="block mb-2">Gültigkeit (max. 7 Tage)</label>
        <input id="dateInput" type="text" name="expires" placeholder="DD.MM.YYYY" required
               class="w-full border p-2 rounded mb-4" value="<?= htmlspecialchars($_POST['expires'] ?? '') ?>">

        <button class="bg-blue-500 text-white px-4 py-2 rounded w-full flex items-center justify-center gap-2">

            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
            Generieren
        </button>
    </form>

    <?php if ($error): ?>
        <div class="mt-4 p-2 bg-red-100 text-red-700 rounded">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($invite_link): ?>
        <div class="mt-4 p-4 bg-green-100 rounded">

            <p class="mb-2 break-all text-sm">
                <?= htmlspecialchars($invite_link) ?>
            </p>

            <div class="flex gap-2 mb-4">

                <button onclick="copyLink()" class="bg-gray-700 text-white px-3 py-2 rounded w-full flex items-center justify-center gap-1">
                    📋 Copy
                </button>

                <button onclick="shareLink()" class="bg-blue-500 text-white px-3 py-2 rounded w-full flex items-center justify-center gap-1">
                    📤 Share
                </button>

            </div>

            <div id="qrcode" class="flex justify-center"></div>

        </div>
    <?php endif; ?>
</div>

<div id="toast" class="fixed bottom-5 right-5 bg-black text-white px-4 py-2 rounded opacity-0 transition">
    Copied!
</div>

<script>
const link = "<?= $invite_link ?>";

if (link) {
    new QRCode(document.getElementById("qrcode"), {
        text: link,
        width: 200,
        height: 200
    });
}

function copyLink() {
    navigator.clipboard.writeText(link);
    showToast("Link kopiert!");
}

function shareLink() {
    if (navigator.share) {
        navigator.share({ url: link });
    } else {
        showToast("Sharing nicht unterstützt");
    }
}

function showToast(msg) {
    const t = document.getElementById("toast");
    t.innerText = msg;
    t.classList.remove("opacity-0");
    setTimeout(() => t.classList.add("opacity-0"), 2000);
}

function validateDate() {
    const input = document.getElementById("dateInput").value;
    const parts = input.split(".");
    if (parts.length !== 3) return false;

    const d = new Date(parts[2], parts[1]-1, parts[0]);
    const now = new Date();
    const type = document.getElementById("inviteType").value;
    const max = new Date();
    max.setDate(now.getDate() + (type === 'single' ? 7 : 2));

    if (d < now || d > max) {
        showToast(type === 'single' ? "Max 7 Tage erlaubt" : "Max 2 Tage erlaubt");
        return false;
    }

    return true;
}

function updateInfo() {
    const type = document.getElementById("inviteType").value;
    document.getElementById("multiInfo").style.display = type === 'multi' ? 'block' : 'none';
}

updateInfo();
</script>

</body>
</html>
