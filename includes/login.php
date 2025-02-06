<?php

// Inclure tous les scripts nécessaires
require __DIR__ . "/discord.php";
require __DIR__ . "/functions.php";
require "../config.php";

// Initialisation des valeurs requises
init($client_id, $secret_id, $scopes, $redirect_url);

// Récupérer les informations de l'utilisateur avec des tentatives répétées
$user = retry_get_user();

if ($user === null) {
    die("Erreur: Impossible de récupérer les informations de l'utilisateur après plusieurs tentatives.");
    retry_get_user();
}

// Stocker les informations de l'utilisateur dans la session
$_SESSION['user'] = [
    'id' => $user->id,
    'username' => $user->username,
    'discriminator' => $user->discriminator,
    'avatar' => $user->avatar,
    'email' => $user->email
];

// Connexion à la base de données
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Vérifier si l'utilisateur existe déjà
$stmt = $mysqli->prepare("SELECT admin FROM users WHERE discord_id = ?");
$stmt->bind_param("s", $user->id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Utilisateur existant, mettre à jour ses informations
    $stmt->bind_result($admin_level);
    $stmt->fetch();
    $stmt->close();
    $stmt = $mysqli->prepare("UPDATE users SET username = ?, email = ?, avatar = ? WHERE discord_id = ?");
    $stmt->bind_param("ssss", $user->username, $user->email, $user->avatar, $user->id);
    $stmt->execute();
    $stmt->close();
} else {
    // Nouvel utilisateur, insérer ses informations
    $stmt->close();
    $stmt = $mysqli->prepare("INSERT INTO users (discord_id, username, email, avatar, admin) VALUES (?, ?, ?, ?, ?)");
    $admin_level = 0; // Par défaut, admin à 0. Vous pouvez ajuster cela en fonction de vos besoins.
    $stmt->bind_param("ssssd", $user->id, $user->username, $user->email, $user->avatar, $admin_level);
    $stmt->execute();
    $stmt->close();
}

$mysqli->close();

// Rediriger l'utilisateur vers la page d'accueil
header("Location: ../index.php");
exit();

?>
