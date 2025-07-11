<?php
require('routeros_api.class.php'); // Bibliothèque MikroTik API

$API = new RouterosAPI();
$type = $_GET['type'] ?? '5h';

// Profils valides
$validProfiles = ['5h', '24h', '3j', '7j', '30j'];

if (!in_array($type, $validProfiles)) {
    die("Profil invalide.");
}

// Connexion au routeur MikroTik
if ($API->connect('192.168.88.1', 'admin', 'ton_mdp')) {

    // Générer login/mdp
    $username = 'user' . rand(1000, 9999);
    $password = rand(100000, 999999);

    // Associer à un profil selon type
    $profileMap = [
        '5h' => '5H',
        '24h' => '24H',
        '3j' => '3J',
        '7j' => '7J',
        '30j' => '30J'
    ];

    $profileName = $profileMap[$type];

    // Créer l'utilisateur sur MikroTik
    $API->comm("/ip/hotspot/user/add", [
        "name" => $username,
        "password" => $password,
        "profile" => $profileName,
        "limit-uptime" => "" // facultatif, si déjà défini dans le profil
    ]);

    $API->disconnect();

    // Redirection vers ticket
    header("Location: ticket.html?user={$username}&pass={$password}");
    exit;

} else {
    echo "Connexion au MikroTik échouée.";
}
