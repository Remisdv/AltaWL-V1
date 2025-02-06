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
$stmt = $mysqli->prepare("SELECT admin, username FROM users WHERE discord_id = ?");
$stmt->bind_param("s", $discord_id);
$stmt->execute();
$stmt->bind_result($admin_level, $reviewer_username);
$stmt->fetch();
$stmt->close();

if ($admin_level < 1) {
    $mysqli->close();
    header("Location: index.php");
    exit();
}

// Vérifiez si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $background_id = $_POST['background_id'];
    $note_lexique = $_POST['note_lexique'];
    $note_background = $_POST['note_background'];
    $note_ressenti = $_POST['note_ressenti'];
    $commentaire = $_POST['commentaire'];
    $status = $_POST['status']; // accepted / seconde_chance / rejected

    // Mettre à jour les notes et le commentaire
    $stmt = $mysqli->prepare("UPDATE backgrounds SET note_lexique = ?, note_background = ?, note_ressenti = ?, commentaire = ?, status = ?, reviewer_id = ?, review_date = ? WHERE id = ?");
    $review_date = date("Y-m-d H:i:s");
    $final_status = $status == 'seconde_chance' ? 'accepted' : $status; // Garde "accepted" si "seconde_chance" est sélectionné
    $stmt->bind_param("iiissisi", $note_lexique, $note_background, $note_ressenti, $commentaire, $final_status, $reviewer_username, $review_date, $background_id);
    $stmt->execute();
    $stmt->close();

    // Récupérer les informations de l'utilisateur
    $stmt = $mysqli->prepare("SELECT discord_id, username FROM backgrounds WHERE id = ?");
    $stmt->bind_param("i", $background_id);
    $stmt->execute();
    $stmt->bind_result($user_discord_id, $username);
    $stmt->fetch();
    $stmt->close();

    // Si discord_id est null, chercher dans la table users
    if (empty($user_discord_id)) {
        $stmt = $mysqli->prepare("SELECT discord_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_discord_id);
        $stmt->fetch();
        $stmt->close();
    }

    if (empty($user_discord_id)) {
        die("Erreur : Impossible de récupérer le Discord ID de l'utilisateur.");
    }

    // Ajouter ou retirer le rôle sur Discord
    if ($status == 'accepted') {
        $role_id = '1193630112109842603'; // Role pour WL
        $message = "Félicitations <@{$user_discord_id}>, vous avez été accepté avec les notes suivantes : Lexique RP: $note_lexique, Background: $note_background, Ressenti: $note_ressenti. \nAccepté par: $reviewer_username";
        removeDiscordRole($user_discord_id, '1020034077191045121');
    } elseif ($status == 'seconde_chance') {
        $role_id = '1222280302739718175'; // Role pour seconde chance
        $message = "<@{$user_discord_id}>, vous avez été mis en seconde chance avec les notes suivantes : Lexique RP: $note_lexique, Background: $note_background, Ressenti: $note_ressenti. \nAccepté par: $reviewer_username";
        // Ne pas retirer le rôle 1020034077191045121
    } else { // rejected
        $role_id = '1227302321256534147'; // Role pour rejeté
        $message = "<@{$user_discord_id}>, vous avez été rejeté avec les notes suivantes : Lexique RP: $note_lexique, Background: $note_background, Ressenti: $note_ressenti. \nRejeté par: $reviewer_username";
        removeDiscordRole($user_discord_id, '1020034077191045121');
    }

    $response_role = addDiscordRole($user_discord_id, $role_id);
    $message_response_channel = sendDiscordMessageChannel('1225162219218796634', $message);
    $message_response_private = sendDiscordMessagePrivate($user_discord_id, $message);
    
    // Redirection
    header("Location: panel_douane.php");
    exit();
}

// Fonction pour ajouter un rôle sur Discord
function addDiscordRole($discord_id, $role_id) {
    $bot_token = ''; 
    $guild_id = '';
    $url = "";

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

// Fonction pour retirer un rôle sur Discord
function removeDiscordRole($discord_id, $role_id) {
    $bot_token = ''; // Remplacez par votre token de bot
    $guild_id = '1017437733054066828'; // Remplacez par l'ID de votre serveur
    $url = "https://discord.com/api/v8/guilds/$guild_id/members/$discord_id/roles/$role_id";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bot $bot_token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notation</title>
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
            max-width: 800px;
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }

        .form-group textarea {
            resize: vertical;
        }

        .button {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            border-radius: 8px;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        .button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .button-reject {
            background-color: #f44336;
        }

        .button-reject:hover {
            background-color: #e41e1e;
        }

        .button-back {
            background-color: #008CBA;
        }

        .button-back:hover {
            background-color: #007bb5;
        }
    </style>
</head>
<body>
    <div class="content">
        <section class="section">
            <h2>Notation</h2>
            <?php
            // Récupérer le background pour l'afficher
            $background_id = $_GET['background_id'];
            $stmt = $mysqli->prepare("SELECT background FROM backgrounds WHERE id = ?");
            $stmt->bind_param("i", $background_id);
            $stmt->execute();
            $stmt->bind_result($background);
            $stmt->fetch();
            $stmt->close();
            ?>
            <p><strong>Background:</strong> <?php echo nl2br(htmlspecialchars($background)); ?></p>
            <form method="POST" action="notation.php">
                <input type="hidden" name="background_id" value="<?php echo htmlspecialchars($background_id); ?>">
                <div class="form-group">
                    <label for="note_lexique">Note Lexique RP (/10):</label>
                    <input type="number" name="note_lexique" id="note_lexique" min="0" max="10" required>
                </div>
                <div class="form-group">
                    <label for="note_background">Note Background (/10):</label>
                    <input type="number" name="note_background" id="note_background" min="0" max="10" required>
                </div>
                <div class="form-group">
                    <label for="note_ressenti">Note Ressenti (/10):</label>
                    <input type="number" name="note_ressenti" id="note_ressenti" min="0" max="10" required>
                </div>
                <div class="form-group">
                    <label for="commentaire">Commentaire:</label>
                    <textarea name="commentaire" id="commentaire" rows="4" required></textarea>
                </div>
                <div>
                    <button type="submit" name="status" value="accepted" class="button">Accepter</button>
                    <button type="submit" name="status" value="seconde_chance" class="button button-reject">Seconde Chance</button>
                    <button type="submit" name="status" value="rejected" class="button button-reject">Rejeter</button>
                    <a href="panel_douane.php" class="button button-back">Retour</a>
                </div>
            </form>
        </section>
    </div>
</body>
</html>
