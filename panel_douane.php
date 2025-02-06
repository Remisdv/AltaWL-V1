<?php
session_start();
require "config.php";

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: includes/login.php");
    exit();
}

// Connexion à la base de données
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupérer les informations de l'utilisateur
$discord_id = $_SESSION['user']['id'];
$stmt = $mysqli->prepare("SELECT admin FROM users WHERE discord_id = ?");
$stmt->bind_param("s", $discord_id);
$stmt->execute();
$stmt->bind_result($admin_level);
$stmt->fetch();
$stmt->close();

if ($admin_level < 1) {
    $mysqli->close();
    header("Location: index.php");
    exit();
}

// Traitez les actions pour les admins de niveau 2
if ($admin_level == 2 && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $background_id = $_POST['background_id'];
    $reviewer_id = $_SESSION['user']['username'];
    $review_date = date("Y-m-d H:i:s");

    if ($action == 'accept') {
        // Mettre à jour l'état du background à accepté
        $stmt = $mysqli->prepare("UPDATE backgrounds SET status = 'accepted', reviewer_id = ?, review_date = ? WHERE id = ?");
        $stmt->bind_param("ssi", $reviewer_id, $review_date, $background_id);
        $stmt->execute();
        $stmt->close();

        // Récupérer l'utilisateur à partir de background_id
        $stmt = $mysqli->prepare("SELECT username FROM backgrounds WHERE id = ?");
        $stmt->bind_param("i", $background_id);
        $stmt->execute();
        $stmt->bind_result($username);
        $stmt->fetch();
        $stmt->close();

        // Récupérer les informations de l'utilisateur à partir de username
        $stmt = $mysqli->prepare("SELECT discord_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_discord_id);
        $stmt->fetch();
        $stmt->close();

        // Ajouter le rôle sur Discord
        $response = addDiscordRole($user_discord_id, '1020034077191045121'); // Remplacez 'ROLE_ID_HERE' par l'ID du rôle sur Discord

        // Notifier l'utilisateur sur Discord
        $message_response_channel = sendDiscordMessageChannel('1194240057419243560', "Félicitations <@{$user_discord_id}>, votre background a été accepté !");
        $message_response_private = sendDiscordMessagePrivate($user_discord_id, "Félicitations, votre background a été accepté !");
        
        // Afficher la réponse pour le débogage
        var_dump($response, $message_response_channel, $message_response_private);
    } elseif ($action == 'reject') {
        // Mettre à jour l'état du background à rejeté
        $stmt = $mysqli->prepare("UPDATE backgrounds SET status = 'rejected', reviewer_id = ?, review_date = ? WHERE id = ?");
        $stmt->bind_param("ssi", $reviewer_id, $review_date, $background_id);
        $stmt->execute();
        $stmt->close();

        // Récupérer l'utilisateur à partir de background_id
        $stmt = $mysqli->prepare("SELECT username FROM backgrounds WHERE id = ?");
        $stmt->bind_param("i", $background_id);
        $stmt->execute();
        $stmt->bind_result($username);
        $stmt->fetch();
        $stmt->close();

        // Récupérer les informations de l'utilisateur à partir de username
        $stmt = $mysqli->prepare("SELECT discord_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_discord_id);
        $stmt->fetch();
        $stmt->close();

        // Notifier l'utilisateur sur Discord
        $message_response_channel = sendDiscordMessageChannel('1194240057419243560', "<@{$user_discord_id}>, votre background a été rejeté.");
        $message_response_private = sendDiscordMessagePrivate($user_discord_id, "Votre background a été rejeté.");
        
        // Afficher la réponse pour le débogage
        var_dump($message_response_channel, $message_response_private);
    }

    $mysqli->close();
    header("Location: panel_douane.php");
    exit();
}

// Fonction pour ajouter un rôle sur Discord
function addDiscordRole($discord_id, $role_id) {
    $bot_token = ''; // Remplacez par votre token de bot
    $guild_id = '1017437733054066828'; // Remplacez par l'ID de votre serveur
    $url = "https://discord.com/api/v8/guilds/$guild_id/members/$discord_id/roles/$role_id";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bot $bot_token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'response' => json_decode($response, true),
        'http_code' => $http_code
    ];
}

// Fonction pour envoyer un message dans un canal Discord
function sendDiscordMessageChannel($channel_id, $message) {
    $bot_token = ''; // Remplacez par votre token de bot
    $url = "https://discord.com/api/v8/channels/$channel_id/messages";

    $data = [
        'content' => $message,
        'tts' => false
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bot $bot_token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'response' => json_decode($response, true),
        'http_code' => $http_code
    ];
}

// Fonction pour envoyer un message privé sur Discord
function sendDiscordMessagePrivate($discord_id, $message) {
    $bot_token = ''; // Remplacez par votre token de bot
    $url = "https://discord.com/api/v8/users/@me/channels";

    // Créer un DM channel avec l'utilisateur
    $data = [
        'recipient_id' => $discord_id
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bot $bot_token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (isset($response['id'])) {
        $dm_channel_id = $response['id'];
        $url = "https://discord.com/api/v8/channels/$dm_channel_id/messages";
        $data = [
            'content' => $message,
            'tts' => false
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bot $bot_token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'response' => json_decode($response, true),
            'http_code' => $http_code
        ];
    }

    return [
        'response' => $response,
        'http_code' => 400
    ];
}

// Gestion des actions de tri et de recherche
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'pending';

$query = "SELECT * FROM backgrounds WHERE (username LIKE ? OR background LIKE ?) AND status = ? ORDER BY created_at $order";
$stmt = $mysqli->prepare($query);
$search_param = "%" . $search . "%";
$stmt->bind_param("sss", $search_param, $search_param, $status);
$stmt->execute();
$result = $stmt->get_result();

$backgrounds = [];
while ($row = $result->fetch_assoc()) {
    $backgrounds[] = $row;
}

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Douane</title>
    <link rel="icon" type="image/png" href="IMG/alta1.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: white;
            background-color: rgba(3,9,19,255);
        }

        .content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section {
            margin: 40px 0;
        }

        .section h2 {
            font-size: 2em;
            border-bottom: 2px solid #1c1c1e;
            padding-bottom: 10px;
        }

        .section p {
            font-size: 1.2em;
            line-height: 1.6em;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .background-card {
            background-color: #1c1c1e;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-all;
        }

        .background-card h3 {
            margin-top: 0;
        }

        .background-card p {
            margin-bottom: 10px;
        }

        .background-card button {
            margin-right: 10px;
        }

        .navbar {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: space-around;
            max-width: 1000px;
            margin: auto;
            flex-grow: 1;
        }

        .navbar li {
            position: relative;
        }

        .navbar a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .navbar a::after {
            content: '';
            display: block;
            width: 0;
            height: 2px;
            background: #fff;
            transition: width 0.3s;
            margin: auto;
        }

        .navbar a:hover::after {
            width: 100%;
        }

        .background-card a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            transition: background-color 0.3s;
            margin-top: 10px;
            border: 1px solid white;
            border-radius: 5px;
        }

        .background-card a:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <ul class="navbar">
        <li><a href="index.php">Accueil</a></li>
    </ul>
    <div class="content">
        <section class="section">
            <h2>Panel Douane</h2>
            <div class="search-bar">
                <form method="GET" action="panel_douane.php">
                    <input type="text" name="search" placeholder="Rechercher..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">Rechercher</button>
                    <label for="order">Trier par date:</label>
                    <select name="order" id="order" onchange="this.form.submit()">
                        <option value="ASC" <?php echo $order == 'ASC' ? 'selected' : ''; ?>>Croissant</option>
                        <option value="DESC" <?php echo $order == 'DESC' ? 'selected' : ''; ?>>Décroissant</option>
                    </select>
                    <label for="status">Statut:</label>
                    <select name="status" id="status" onchange="this.form.submit()">
                        <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>En attente</option>
                        <option value="accepted" <?php echo $status == 'accepted' ? 'selected' : ''; ?>>Accepté</option>
                        <option value="rejected" <?php echo $status == 'rejected' ? 'selected' : ''; ?>>Rejeté</option>
                    </select>
                </form>
            </div>
            <?php foreach ($backgrounds as $background): ?>
                <div class="background-card">
                    <h3><?php echo htmlspecialchars($background['username']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($background['background'])); ?></p>
                    <p><strong>Date de soumission :</strong> <?php echo $background['created_at']; ?></p>
                    <p><strong>Status :</strong> <?php echo $background['status']; ?></p>
                    <?php if ($admin_level == 2 && $background['status'] == 'pending'): ?>
                        <form method="POST" action="panel_douane.php" style="display:inline;">
                            <input type="hidden" name="action" value="accept">
                            <input type="hidden" name="background_id" value="<?php echo $background['id']; ?>">
                            <button type="submit">Accepter</button>
                        </form>
                        <form method="POST" action="panel_douane.php" style="display:inline;">
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="background_id" value="<?php echo $background['id']; ?>">
                            <button type="submit">Rejeter</button>
                        </form>
                    <?php elseif ($background['status'] == 'accepted'): ?>
                        <a href="notation.php?background_id=<?php echo $background['id']; ?>">Noter ce background</a>
                        <p><strong>Revu par :</strong> <?php echo htmlspecialchars($background['reviewer_id']); ?></p>
                        <p><strong>Date de revue :</strong> <?php echo $background['review_date']; ?></p>
                    <?php elseif ($background['status'] != 'pending'): ?>
                        <p><strong>Revu par :</strong> <?php echo htmlspecialchars($background['reviewer_id']); ?></p>
                        <p><strong>Date de revue :</strong> <?php echo $background['review_date']; ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbarLinks = document.querySelectorAll('.navbar a');

            navbarLinks.forEach(link => {
                link.addEventListener('mouseover', () => {
                    link.style.transform = 'scale(1.1)';
                });

                link.addEventListener('mouseout', () => {
                    link.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>
