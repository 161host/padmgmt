<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title><?php echo $config['product_name']; ?> - Zugriff beantragen</title>
<link href="tailwind.css" rel="stylesheet">

<script>
function toggleFields() {
    const type = document.querySelector('input[name="type"]:checked')?.value;

    document.getElementById('groupInfo').classList.toggle('hidden', type !== 'group');
    document.getElementById('accountInfo').classList.toggle('hidden', type !== 'account');
}
</script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<form action="submit.php" method="POST"
class="bg-white w-full max-w-xl p-8 rounded-2xl shadow-lg space-y-6">

<h1 class="text-2xl font-bold text-gray-800">Zugriff beantragen</h1>

<!-- Auswahl -->
<div class="space-y-3">
<label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
<input type="radio" name="type" value="group" onclick="toggleFields()" required>
<span>Gruppe verwalten</span>
</label>

<label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
<input type="radio" name="type" value="account" onclick="toggleFields()" required>
<span>Account erstellen</span>
</label>
</div>

<!-- Gruppe -->
<div id="groupInfo" class="hidden space-y-3">
<input name="group_name" placeholder="Name der Gruppe"
class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">

<textarea name="group_description" placeholder="Beschreibung der Gruppe"
class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>

<input name="group_contact_name" placeholder="Dein Name"
class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">

<input name="group_contact_email" type="email" placeholder="Deine E-Mail Adresse"
class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">
</div>

<!-- Account -->
<div id="accountInfo" class="hidden space-y-3">
<input name="name" placeholder="Name"
class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">

<input name="email" type="email" placeholder="E-Mail"
class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500">

<textarea name="reason" placeholder="Begründung"
class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
</div>

<!-- Zustimmung -->
<div class="text-sm bg-gray-50 p-4 rounded-lg border">
<p class="mb-3">
Ich akzeptiere, dass dies ein Service ist, der jederzeit eingestellt werden kann.
<br/>
Ich habe keinen Anspruch auf diesen Service.
<br />
<b>Ich hasse Nazis!</b>
</p>

<div class="flex gap-4">
<label><input type="radio" name="accept" value="yes" required> Ja</label>
<label><input type="radio" name="accept" value="no"> Nein</label>
</div>
</div>

<button class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg font-semibold">
Anfrage absenden
</button>

<?php if (isset($config['legal'])): ?>
    <a href="<?= $config['legal'] ?>" target="_blank" rel="noopener noreferrer">Legal</a>
<?php endif; ?>
</form>
</body>
</html>
