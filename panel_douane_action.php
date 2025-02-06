<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: includes/login.php");
    exit();
}

// Connexion à la base de données
require "config.php";
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Vérifiez si l'utilisateur est admin
$discord_id = $_SESSION['user']['id'];
$stmt = $mysqli->prepare("SELECT admin FROM users WHERE discord_id = ?");
$stmt->bind_param("s", $discord_id);
$stmt->execute();
$stmt->bind_result($admin_level);
$stmt->fetch();
$stmt->close();

if ($admin_level <= 0) {
    die("Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.");
}

// Traiter les actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $background_id = $_POST['background_id'];
    $reviewer_id = $_SESSION['user']['username'];
    $review_date = date("Y-m-d H:i:s");

    if ($action == 'accept') {
        // Mettre à jour l'état du background à accepté
        $stmt = $mysqli->prepare("UPDATE backgrounds SET status = 'accepted', reviewer_id = ?, review_date = ? WHERE id = ?");
    } elseif ($action == 'reject') {
        // Mettre à jour l'état du background à rejeté
        $stmt = $mysqli->prepare("UPDATE backgrounds SET status = 'rejected', reviewer_id = ?, review_date = ? WHERE id = ?");
    }

    if ($stmt) {
        $stmt->bind_param("ssi", $reviewer_id, $review_date, $background_id);
        $stmt->execute();
        $stmt->close();
    }
}

$mysqli->close();
header("Location: panel_douane.php");
exit();
?>
