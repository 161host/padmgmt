<?php

$config = require 'config.php'; // enthält $config['data_file']
$dataFile = $config['data_file'];

// Lade vorhandene Requests
if (!file_exists($dataFile)) {
    echo "Datei $dataFile existiert nicht.\n";
    exit;
}

$data = json_decode(file_get_contents($dataFile), true);
if (!is_array($data)) {
    echo "Fehler beim Laden der JSON-Daten.\n";
    exit;
}

$now = time();
$sevenDays = 7 * 24 * 60 * 60; // Sekunden in 7 Tagen

$removed = 0;

foreach ($data as $token => $entry) {
    if (isset($entry['verified'], $entry['timestamp']) && $entry['verified'] === false) {
        if ($entry['timestamp'] < ($now - $sevenDays)) {
            unset($data[$token]);
            $removed++;
        }
    }
}

// Speichere die gefilterten Daten zurück
file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

echo "Aufräumen abgeschlossen. $removed veraltete Einträge gelöscht.\n";
