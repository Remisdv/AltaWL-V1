<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: includes/login.php");
    exit();
}

require "config.php";
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$discord_id = $_SESSION['user']['id'];
$stmt = $mysqli->prepare("SELECT qcm_passed FROM users WHERE discord_id = ?");
$stmt->bind_param("s", $discord_id);
$stmt->execute();
$stmt->bind_result($qcm_passed);
$stmt->fetch();
$stmt->close();

if (!$qcm_passed) {
    die("Vous devez réussir le questionnaire pour accéder à cette page.");
}

$stmt = $mysqli->prepare("SELECT COUNT(*) FROM backgrounds WHERE discord_id = ?");
$stmt->bind_param("s", $discord_id);
$stmt->execute();
$stmt->bind_result($background_count);
$stmt->fetch();
$stmt->close();

if ($background_count > 0) {
    die("Vous avez déjà soumis un background.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $background = trim($_POST['background']);
    
    if (strlen($background) < 800) {
        $error_message = "Votre background doit contenir au moins 800 caractères.";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO backgrounds (discord_id, username, email, avatar, background) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $_SESSION['user']['id'], $_SESSION['user']['username'], $_SESSION['user']['email'], $_SESSION['user']['avatar'], $background);

        if ($stmt->execute()) {
            // Envoyer un message privé à l'utilisateur
            $username = $_SESSION['user']['username'];
            $message = "Bonjour $username,\n\nVotre background a bien été envoyé et sera traité sous maximum 48 heures.";
            $message_response_private = sendDiscordMessagePrivate($discord_id, $message);

            header("Location: index.php");
            exit();
        } else {
            $error_message = "Une erreur est survenue lors de la soumission de votre background. Veuillez réessayer.";
        }

        $stmt->close();
    }
}

$mysqli->close();

// Fonction pour envoyer un message privé sur Discord
function sendDiscordMessagePrivate($discord_id, $message) {
    $bot_token = ''; 
    $url = "";

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
    <title>Soumettre Background</title>
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

        .textarea {
            width: 100%;
            height: 200px;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .button {
            background-color: #4CAF50;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 12px;
        }

        .button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="content">
        <section class="section">
            <h2>Soumettre Background</h2>
            <p>Félicitation tu as réussi le QCM !</p>
            <p>Veuillez écrire votre background. Il doit contenir au moins 800 caractères.</p>
            <?php if (isset($error_message)): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if (isset($success_message)): ?>
                <p class="success"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <form method="POST" action="background.php">
                <textarea name="background" class="textarea" placeholder="écrivez votre background ici..."></textarea>
                <div class="button-container">
                    <button type="submit" class="button">Soumettre</button>
                </div>
            </form>
        </section>
    </div>
</body>
</html>
