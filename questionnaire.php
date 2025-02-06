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

// Récupérer les informations de l'utilisateur
$discord_id = $_SESSION['user']['id'];
$stmt = $mysqli->prepare("SELECT qcm_failures, qcm_passed FROM users WHERE discord_id = ?");
$stmt->bind_param("s", $discord_id);
$stmt->execute();
$stmt->bind_result($qcm_failures, $qcm_passed);
$stmt->fetch();
$stmt->close();

// Vérifier si l'utilisateur a déjà réussi le QCM
if ($qcm_passed) {
    header("Location: background.php");
    exit();
}

// Vérifier le nombre de tentatives échouées
if ($qcm_failures >= 3) {
    die("Vous avez échoué au questionnaire plus de 3 fois. Vous ne pouvez plus passer le questionnaire.");
}

// Initialiser les questions et réponses
$questions = [
    // Questions originales
    ["question" => "Quel est l'objectif principal d'un serveur roleplay (RP) sur FiveM ?", "choices" => ["Gagner des courses", "Incarner un personnage et vivre des interactions réalistes", "Compléter des missions de Rockstar"], "answer" => 1],
    ["question" => "Quelle est l'importance des règles de RP sur un serveur FiveM ?", "choices" => ["Elles ne sont pas importantes, c'est juste un jeu", "Elles sont essentielles pour garantir une expérience immersive et respectueuse", "Elles sont optionnelles et peuvent être ignorées"], "answer" => 1],
    ["question" => "Que doit-on faire si on est témoin d'un comportement de triche ?", "choices" => ["Ignorer le comportement", "Participer à la triche", "Signaler le comportement aux administrateurs du serveur"], "answer" => 2],
    ["question" => "Quelle est la manière correcte d'interagir avec d'autres joueurs en RP ?", "choices" => ["Ignorer leur histoire et agir comme on veut", "Suivre son propre script indépendamment des autres", "Réagir de manière réaliste et en respectant le rôle de chacun"], "answer" => 2],
    ["question" => "Qu'est-ce que le 'Meta-gaming' ?", "choices" => ["Utiliser des informations obtenues hors jeu dans le jeu", "Jouer en équipe", "Utiliser des stratégies complexes en jeu"], "answer" => 0],
    ["question" => "Vous êtes en plein braquage et un autre joueur entre par hasard dans la banque. Que faites-vous ?", "choices" => ["Vous lui tirez dessus sans explication", "Vous l'ignorez et continuez votre braquage", "Vous interagissez avec lui de manière réaliste, en le menaçant ou en le prenant en otage selon le contexte RP"], "answer" => 2],
    ["question" => "Vous êtes un officier de police et vous arrêtez un joueur pour excès de vitesse. Comment gérez-vous la situation ?", "choices" => ["Vous lui infligez immédiatement une amende maximale sans explication", "Vous engagez une conversation, demandez des explications, et décidez de la suite en fonction de son comportement", "Vous ignorez son infraction et continuez votre route"], "answer" => 1],
    ["question" => "Votre personnage vient de se faire voler son véhicule. Quelle est votre réaction ?", "choices" => ["Vous poursuivez immédiatement le voleur sans tenir compte des règles de circulation", "Vous contactez la police en RP et suivez leurs instructions", "Vous vous déconnectez du serveur par frustration"], "answer" => 1],
    ["question" => "Vous êtes médecin dans un hôpital et un joueur arrive avec des blessures graves après un accident de voiture. Que faites-vous ?", "choices" => ["Vous ignorez le joueur et continuez vos activités", "Vous commencez un traitement médical en demandant des détails sur l'accident pour assurer une immersion réaliste", "Vous le soignez rapidement sans interaction et passez à autre chose"], "answer" => 1],
    ["question" => "Un joueur vous insulte et vous provoque sans raison valable. Comment réagissez-vous ?", "choices" => ["Vous répondez par la violence physique", "Vous essayez de désamorcer la situation en restant dans le cadre du RP", "Vous signalez son comportement aux administrateurs du serveur"], "answer" => 1],
    ["question" => "Vous avez assisté à un vol à main armée et vous êtes le seul témoin. Que faites-vous ?", "choices" => ["Vous ignorez ce que vous avez vu et continuez votre route", "Vous contactez la police en RP et leur fournissez des informations détaillées", "Vous poursuivez les voleurs par vous-même"], "answer" => 1],
    ["question" => "Vous êtes en pleine discussion RP avec un autre joueur et un administrateur vous contacte en OOC (Out of Character) pour une question. Comment réagissez-vous ?", "choices" => ["Vous ignorez l'administrateur et continuez votre discussion", "Vous terminez rapidement votre interaction RP et répondez à l'administrateur", "Vous répondez immédiatement à l'administrateur en OOC, puis reprenez votre RP"], "answer" => 2],
    ["question" => "Vous êtes un officier de police en patrouille et vous voyez un joueur faire un échange de drogue dans une ruelle sombre. Comment réagissez-vous ?", "choices" => ["Vous sortez votre arme et tirez sans sommation", "Vous observez discrètement la scène pour collecter des preuves et intervenez ensuite en suivant les procédures de police", "Vous ignorez la situation et continuez votre patrouille"], "answer" => 1],
    ["question" => "Votre personnage est un avocat et un joueur vous engage pour le défendre dans un procès pour meurtre. Quelle est votre approche ?", "choices" => ["Vous acceptez l'affaire sans poser de questions et assurez de gagner à tout prix", "Vous examinez les preuves, discutez des circonstances avec votre client, et préparez une défense réaliste en respectant les règles de la profession d'avocat", "Vous dénoncez immédiatement votre client à la police"], "answer" => 1],
    ["question" => "En tant que chef d'une organisation criminelle, vous découvrez qu'un de vos membres collabore avec la police. Quelle est votre réaction ?", "choices" => ["Vous organisez une rencontre pour discuter et confirmer ses allégations, puis décidez des actions en fonction des preuves obtenues", "Vous le tuez immédiatement pour trahison", "Vous ignorez les informations et continuez vos activités"], "answer" => 0],
    ["question" => "Vous êtes propriétaire d'un club et un joueur se comporte de manière agressive envers les autres clients. Comment gérez-vous la situation ?", "choices" => ["Vous l'expulsez immédiatement sans explication", "Vous lui parlez calmement, essayez de désamorcer la situation et, si nécessaire, demandez à la sécurité de l'expulser en respectant les procédures RP", "Vous appelez la police directement sans essayer de résoudre le problème vous-même"], "answer" => 1],
    ["question" => "Votre personnage est médecin et un joueur arrive avec des blessures compatibles avec une fusillade récente. Quelle est votre procédure ?", "choices" => ["Vous soignez ses blessures sans poser de questions", "Vous stabilisez le patient, interrogez sur les circonstances de ses blessures tout en restant professionnel, et signalez l'incident aux autorités compétentes si nécessaire", "Vous refusez de traiter le patient en raison de la nature suspecte de ses blessures"], "answer" => 1],
    ["question" => "Vous êtes un journaliste et vous recevez des informations sensibles sur une corruption au sein de la police. Comment procédez-vous ?", "choices" => ["Vous publiez immédiatement l'information sans vérifier les sources", "Vous enquêtez discrètement, vérifiez vos sources, et présentez les preuves de manière objective dans votre reportage", "Vous ignorez l'information pour éviter des ennuis"], "answer" => 1],
    ["question" => "Votre personnage est un pompier et un joueur vous appelle pour un incendie suspect dans un bâtiment. Quelle est votre réaction ?", "choices" => ["Vous refusez d'intervenir car le bâtiment est connu pour des activités illégales", "Vous intervenez immédiatement pour éteindre le feu, assurez la sécurité des personnes présentes, et rapportez vos observations aux autorités pour enquête", "Vous attendez les renforts sans intervenir"], "answer" => 1],
    ["question" => "Vous jouez un personnage criminel et planifiez un grand coup avec votre équipe. Un nouveau membre semble hésitant et nerveux. Que faites-vous ?", "choices" => ["Vous l'excluez immédiatement du plan", "Vous discutez avec lui pour comprendre ses inquiétudes, le rassurez et réévaluez ses compétences avant de prendre une décision finale", "Vous continuez le plan sans prendre en compte son comportement"], "answer" => 1],
    
    // Questions de règlement
    ["question" => "Quel est le nom du serveur pour lequel ce règlement est fait ?", "choices" => ["Alta RP", "RolePlay City", "FiveM World"], "answer" => 0],
    ["question" => "Quel est le nom requis sur Discord pour les joueurs du serveur ?", "choices" => ["Pseudo", "Nom Prénom RP", "Nom de famille uniquement"], "answer" => 1],
    ["question" => "Que doivent faire les joueurs qui ont des suggestions de nouvelles règles ?", "choices" => ["Les ignorer", "Les proposer en ticket ou en suggestion", "Les poster dans le chat général"], "answer" => 1],
    ["question" => "Combien de tentatives échouées sont autorisées pour passer le questionnaire RP ?", "choices" => ["2", "3", "5"], "answer" => 1],
    ["question" => "Que faire si un joueur est témoin de comportement inapproprié en jeu ?", "choices" => ["Ignorer", "Répondre par la violence", "Contacter un staff"], "answer" => 2],
    ["question" => "Quel type de photos de profil sont interdites sur le Discord ?", "choices" => ["Paysages", "Photos de profil pornographiques/racistes/politiques/idéologiques", "Photos de jeux vidéo"], "answer" => 1],
    ["question" => "Quelle est la conséquence d'un bannissement permanent sur le Discord ?", "choices" => ["Rien", "Un bannissement temporaire en jeu", "Un bannissement permanent en jeu"], "answer" => 2],
    ["question" => "Que doit faire un joueur s'il rencontre un problème lors d'une scène RP ?", "choices" => ["Quitter immédiatement le jeu", "Jouer la scène correctement jusqu'à la fin, puis contacter un staff", "Ignorer le problème"], "answer" => 1],
    ["question" => "Est-il permis d'utiliser des radios extérieures au jeu pour les scènes RP ?", "choices" => ["Oui", "Non", "Seulement si le staff l'autorise"], "answer" => 1],
    ["question" => "Que doit faire un joueur qui découvre un bug en jeu ?", "choices" => ["L'ignorer", "Le signaler au staff", "L'exploiter pour un avantage"], "answer" => 1],
    ["question" => "Est-il permis d'utiliser un modificateur de voix en jeu ?", "choices" => ["Oui, toujours", "Non, jamais", "Seulement avec autorisation de la modération"], "answer" => 2],
    ["question" => "Qu'est-ce que le 'Freekill' ?", "choices" => ["Le fait de tuer quelqu'un sans raison", "Le fait de tuer en légitime défense", "Le fait de tuer avec une arme illégale"], "answer" => 0],
    ["question" => "Que signifie 'Fear RP' ?", "choices" => ["Agir comme si on n'avait pas peur de la mort", "Agir de manière réaliste comme on le ferait dans la vraie vie, en ayant peur de la mort", "Ne pas respecter les règles du serveur"], "answer" => 1],
    ["question" => "Que signifie 'No RP' ?", "choices" => ["Refuser de jouer une scène dans son intégralité", "Faire du RP de manière incorrecte", "Utiliser des cheats en jeu"], "answer" => 0],
    ["question" => "Quelle est la règle concernant les insultes en RP ?", "choices" => ["Interdites", "Tolérées jusqu'à une certaine limite", "Autorisé sans restriction"], "answer" => 1],
    ["question" => "Que signifie 'Power Gaming' ?", "choices" => ["Les actions irréalisables dans la vraie vie", "Les actions réalistes en jeu", "L'utilisation de mods"], "answer" => 0],
    ["question" => "Que doit faire un joueur s'il est en plein RP et qu'un administrateur le contacte en OOC ?", "choices" => ["L'ignorer", "Terminer rapidement son interaction RP et répondre à l'administrateur", "Ne pas répondre et continuer son RP"], "answer" => 1],
    ["question" => "Que doit faire un joueur après avoir été blessé par balle en RP ?", "choices" => ["Continuer ses activités normalement", "Aller à l'hôpital et ne plus participer à des activités illégales pendant 20 minutes", "Quitter le jeu"], "answer" => 1],
];

// Vérifier les réponses soumises
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $age = isset($_POST['age']) ? intval($_POST['age']) : null;

    if ($age < 16) {
        die("Vous devez avoir au moins 16 ans pour passer ce questionnaire.");
    }

    $score = 0;
    foreach ($questions as $index => $question) {
        if (isset($_POST["question_$index"]) && $_POST["question_$index"] == $question['answer']) {
            $score++;
        }
    }

    // Si le score est supérieur à 35, mettre à jour qcm_passed et rediriger vers background.php
    if ($score > 33) {
        $stmt = $mysqli->prepare("UPDATE users SET qcm_passed = 1 WHERE discord_id = ?");
        $stmt->bind_param("s", $discord_id);
        $stmt->execute();
        $stmt->close();
        header("Location: background.php");
        exit();
    } else {
        // Augmenter le nombre de tentatives échouées
        $qcm_failures++;
        $stmt = $mysqli->prepare("UPDATE users SET qcm_failures = ? WHERE discord_id = ?");
        $stmt->bind_param("is", $qcm_failures, $discord_id);
        $stmt->execute();
        $stmt->close();

        $message = "Vous avez obtenu $score bonnes réponses sur 40. Vous devez obtenir plus de 33 bonnes réponses pour passer à l'étape suivante.";
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionnaire RP</title>
    <link rel="icon" type="image/png" href="IMG/alta1.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: white;
            background-color: rgba(3,9,19,255);
        }

        nav {
            background-color: rgba(3,9,19,255);
            display: flex;
            align-items: center;
        }

        .logo {
            height: 60px;
            margin: 20px 40px;
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

        .question {
            margin: 20px 0;
        }

        .choices {
            list-style-type: none;
            padding: 0;
        }

        .choices li {
            margin: 10px 0;
        }

        .button-container {
            margin-top: 20px;
            text-align: center;
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
    </style>
</head>
<body>
    <nav>
        <img src="IMG/alta1.png" alt="Logo ALTA" class="logo">
        <ul class="navbar">
            <li><a href="index.php">Accueil</a></li>
        </ul>
    </nav>
    <div class="content">
        <section class="section">
            <h2>Questionnaire RP</h2>
            <p>Répondez aux questions suivantes pour vérifier vos connaissances en RP sur Fivem.</p>
            <?php if (isset($message)): ?>
                <p style="color: red;"><?php echo $message; ?></p>
            <?php endif; ?>
            <form method="POST" action="questionnaire.php">
                <div class="question">
                    <p>Quel âge avez-vous ?</p>
                    <input type="number" name="age" required>
                </div>
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question">
                        <p><?php echo ($index + 1) . ". " . $question['question']; ?></p>
                        <ul class="choices">
                            <?php foreach ($question['choices'] as $choice_index => $choice): ?>
                                <li>
                                    <label>
                                        <input type="radio" name="question_<?php echo $index; ?>" value="<?php echo $choice_index; ?>" required>
                                        <?php echo $choice; ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
                <div class="button-container">
                    <button type="submit" class="button">Soumettre</button>
                </div>
            </form>
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
