<?php
session_start();
require "config.php";

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$admin_level = 0;
if (isset($_SESSION['user'])) {
    $discord_id = $_SESSION['user']['id'];
    $stmt = $mysqli->prepare("SELECT admin FROM users WHERE discord_id = ?");
    $stmt->bind_param("s", $discord_id);
    $stmt->execute();
    $stmt->bind_result($admin_level);
    $stmt->fetch();
    $stmt->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-5P18LT16SQ"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-5P18LT16SQ');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Rejoignez ALTA RP, un serveur GTARP Whitelist de qualité. Inscrivez-vous pour vivre des aventures RP uniques sur notre serveur GTA.">
    <meta name="keywords" content="serveur GTARP, serveur GTA RP, Whitelist, ALTA RP, serveur RP GTA, GTA role play, xbox, playstation, gameplay, gaming, multijoueur, xbox one, consoles, jeux vidéo, console, fps, jeu vidéo, switch, rédemption, gamer, assassin, psx, rpg, battle royale, red dead redemption, opus, duty, jeu xbox, nintendo switch, manette, multiplayer, graphismes, arcade, jouez, dlc, black ops, epic, fantasy, wii, monde ouvert, gamers, développeurs, resident evil, jeux vidéos, deluxe, odyssey, far cry, dawn, mode multijoueur, rockstar games, pokémon, steam, final fantasy, mods, jeu xbox one, grand theft, zombies, ps3, tous les jeux, mod, codes de triche, réalité virtuelle, apex, jeux pc, nouveau jeu, blizzard, spawn, warfare, ram, résident, dragon ball, quêtes, bug, infinité, gang, xbox live, meilleurs jeux, annonce du jeu, sortie du jeu, sorti du jeu, expérience de jeu, open world, jeux xbox one, mode de jeu, contenu supplémentaire, gta online, shooter, certains jeux, patch, video, aperçu du jeu, multijoueurs, kart, avatar, manettes, meilleurs jeux pc, vidéoludique, modes de jeu, bon jeu, mondes ouverts, jeux xbox, meilleur jeu, panel, claque graphique, jouable, jaquette, enix, jeux playstation, référence du jeu, simulator, bugs, achat jeu, jeu d action, débloquer, factions, mario kart, adversaires, mini jeux, game pass, jeux vidéo pc, jump force, elder, virtuel, studios, contenu additionnel, elder scrolls, jeu sur pc, xbox game pass, hunter, hardcore, 3ds, sims, vente de jeux vidéo, carte graphique, casques, los santos, forza horizon, hardware, cars, date de sortie, jouer avec des amis, pnj, coéquipiers, jedi, jeux les plus vendus, zone de jeu, concernant le jeu, gameloft, super mario, jeux les plus attendus, exclusives, nouveau jeu vidéo, mode solo, terminer les missions, finir le jeu, esport, jeu de rôle, faction, voir tous les jeux, terminer le jeu, soldes jeux, jeux ps, jeux sur pc, sorties de jeux, jeux gratuits, playstation vr, halo, pegi, concernant les jeux, sortie de jeux, série de jeux vidéo, jeu ps, payer pour jouer, console de jeu, jeux ps3, video games, jeu playstation, détaille le contenu, missions secondaires, square enix, devenir un jeu, mode online, pvp, rainbow six, série de jeux, game design, contenu téléchargeable, jeu pc, infinite warfare, western, bande annonce, oculus, yoshi, collector, kingdom, epic games, portables, nouvelles armes, ssd, type de jeu, smash, banner saga, cosmétiques, wii u, développeur, tomb raider, le site officiel, microsoft xbox, scripts, smash bros, premium, days gone, jouez gratuitement, vente de jeu, jeux à venir, coop, java, slim, nba, twitch, config, legend, super smash, donjons, console xbox, terminal, cpu, game awards, jeu online, jeux à prix, jeu sur console, hdr, engine, jeux plus, rend le jeu, nouveau jeux, consoles de jeux, jeux sortis, arène, metal gear, bataille royale, ray, cube world, campagne solo, jouer en coopération, connectés, tous les jeux pc, processeur, addictif, utilisent le jeu, magasin de jeux vidéo, pack xbox, tester le jeu, jouer à tous les jeux, pack xbox one">
    <meta name="robots" content="index, follow">
    <title>ALTA RP WHITELIST - Serveur GTARP de Qualité</title>
    <link rel="icon" type="image/png" href="IMG/alta1.png">
    <link rel="stylesheet" href="CSS/index.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgba(3,9,19,255);
        }

        nav {
            background-color: rgba(3,9,19,255);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
        }

        .logo {
            height: 50px;
            margin: 10px 20px;
        }

        .navbar {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: space-around;
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

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .hamburger div {
            width: 25px;
            height: 3px;
            background-color: white;
            margin: 4px;
            transition: 0.4s;
        }

        .navbar {
            display: none;
        }

        .navbar.show {
            display: flex;
            flex-direction: column;
            width: 100%;
            text-align: center;
        }

        .user-menu {
            position: relative;
            display: inline-block;
        }

        .user-menu-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .user-menu-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .user-menu-content a:hover {
            background-color: #f1f1f1;
        }

        .user-menu:hover .user-menu-content {
            display: block;
        }

        .banner {
            padding: 10px 0;
            font-size: 25px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            position: relative;
        }

        .banner h1 {
            font-size: 40px;
            margin-top: 20px;
        }

        .banner img {
            width: 80%;
            height: auto;
        }

        .banner p {
            font-size: 20px;
            margin-top: 20px;
            padding: 0 10px;
        }

        .banner .button-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        .button-discord, .button-douane {
            text-align: center;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #7289da;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            text-decoration: none;
            display: inline-block;
            width: 80%;
            max-width: 200px;
        }

        .button-discord:hover, .button-douane:hover {
            background-color: #5b6eae;
            transform: scale(1.1);
        }

        @media (min-width: 600px) {
            .navbar {
                display: flex;
                flex-direction: row;
                justify-content: space-around;
                max-width: 1000px;
                margin: auto;
            }

            .hamburger {
                display: none;
            }

            .banner h1 {
                font-size: 50px;
            }

            .banner p {
                font-size: 25px;
            }

            .banner .button-container {
                flex-direction: row;
                gap: 20px;
            }

            .button-discord, .button-douane {
                font-size: 20px;
                width: 200px;
            }
        }

        @media (min-width: 768px) {
            .logo {
                height: 60px;
            }

            .banner h1 {
                font-size: 60px;
            }

            .banner p {
                font-size: 30px;
            }

            .banner img {
                width: 40%;
            }
        }

        @media (max-width: 600px) {
            .hamburger {
                display: flex;
            }

            .navbar {
                display: none;
            }

            .navbar.show {
                display: flex;
            }
        }

        /* Texte caché pour SEO */
        .seo-text {
            display: none;
        }
    </style>
</head>
<body>
    <nav>
        <img src="IMG/alta1.png" alt="Logo ALTA" class="logo">
        <div class="hamburger" onclick="toggleMenu()">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <ul class="navbar">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="nous.php">À propos d'ALTA</a></li>
            <li><a href="https://docs.google.com/document/d/1fidOubvW5WF3yXgau2iOYBIwCYTUQaXvrGPf7-8Ryn4/edit">Réglement</a></li>
            <li><a href="https://top-serveurs.net/gta/alta-rp">Vote</a></li>
            <li><a href="douane.php">Douane</a></li>
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($admin_level > 0): ?>
                    <li><a href="panel_douane.php">Panel Douane</a></li>
                <?php endif; ?>
                <?php if ($admin_level > 1): ?>
                    <li><a href="admin_management.php">Panel Admin</a></li>
                <?php endif; ?>
                <li class="user-menu">
                    <a href="#"><?php echo htmlspecialchars($_SESSION['user']['username']); ?> &#x25BC;</a>
                    <div class="user-menu-content">
                        <a href="includes/logout.php">Déconnexion</a>
                    </div>
                </li>
            <?php else: ?>
                <li><a href="includes/login.php">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="banner">
        <img src="IMG/alta2.png" alt="IMG/alta2.png">
        <h1>Bienvenue à la maison</h1>
        <p>Rejoins-nous dès maintenant sur Discord et passe la douane pour être sur la whitelist du serveur !</p>
        <div class="button-container">
            <a href="#" class="button-discord">Discord</a>
            <a href="douane.php" class="button-douane">Douane</a>
        </div>
    </div>

    <div class="seo-text">
        Serveur GTARP ALTA RP, un serveur Whitelist de qualité pour les passionnés de GTA Role Play. Inscrivez-vous dès maintenant pour rejoindre notre communauté active et immersive. ALTARP est le meilleur choix pour ceux qui cherchent une expérience RP authentique et bien encadrée. Serveur GTARP francophone, serveur GTA RP, serveur Whitelist, rejoignez ALTA RP aujourd'hui. Mots-clés supplémentaires : xbox, playstation, gameplay, gaming, multijoueur, xbox one, consoles, jeux vidéo, console, fps, jeu vidéo, switch, rédemption, gamer, assassin, psx, rpg, battle royale, red dead redemption, opus, duty, jeu xbox, nintendo switch, manette, multiplayer, graphismes, arcade, jouez, dlc, black ops, epic, fantasy, wii, monde ouvert, gamers, développeurs, resident evil, jeux vidéos, deluxe, odyssey, far cry, dawn, mode multijoueur, rockstar games, pokémon, steam, final fantasy, mods, jeu xbox one, grand theft, zombies, ps3, tous les jeux, mod, codes de triche, réalité virtuelle, apex, jeux pc, nouveau jeu, blizzard, spawn, warfare, ram, résident, dragon ball, quêtes, bug, infinité, gang, xbox live, meilleurs jeux, annonce du jeu, sortie du jeu, sorti du jeu, expérience de jeu, open world, jeux xbox one, mode de jeu, contenu supplémentaire, gta online, shooter, certains jeux, patch, video, aperçu du jeu, multijoueurs, kart, avatar, manettes, meilleurs jeux pc, vidéoludique, modes de jeu, bon jeu, mondes ouverts, jeux xbox, meilleur jeu, panel, claque graphique, jouable, jaquette, enix, jeux playstation, référence du jeu, simulator, bugs, achat jeu, jeu d action, débloquer, factions, mario kart, adversaires, mini jeux, game pass, jeux vidéo pc, jump force, elder, virtuel, studios, contenu additionnel, elder scrolls, jeu sur pc, xbox game pass, hunter, hardcore, 3ds, sims, vente de jeux vidéo, carte graphique, casques, los santos, forza horizon, hardware, cars, date de sortie, jouer avec des amis, pnj, coéquipiers, jedi, jeux les plus vendus, zone de jeu, concernant le jeu, gameloft, super mario, jeux les plus attendus, exclusives, nouveau jeu vidéo, mode solo, terminer les missions, finir le jeu, esport, jeu de rôle, faction, voir tous les jeux, terminer le jeu, soldes jeux, jeux ps, jeux sur pc, sorties de jeux, jeux gratuits, playstation vr, halo, pegi, concernant les jeux, sortie de jeux, série de jeux vidéo, jeu ps, payer pour jouer, console de jeu, jeux ps3, video games, jeu playstation, détaille le contenu, missions secondaires, square enix, devenir un jeu, mode online, pvp, rainbow six, série de jeux, game design, contenu téléchargeable, jeu pc, infinite warfare, western, bande annonce, oculus, yoshi, collector, kingdom, epic games, portables, nouvelles armes, ssd, type de jeu, smash, banner saga, cosmétiques, wii u, développeur, tomb raider, le site officiel, microsoft xbox, scripts, smash bros, premium, days gone, jouez gratuitement, vente de jeu, jeux à venir, coop, java, slim, nba, twitch, config, legend, super smash, donjons, console xbox, terminal, cpu, game awards, jeu online, jeux à prix, jeu sur console, hdr, engine, jeux plus, rend le jeu, nouveau jeux, consoles de jeux, jeux sortis, arène, metal gear, bataille royale, ray, cube world, campagne solo, jouer en coopération, connectés, tous les jeux pc, processeur, addictif, utilisent le jeu, magasin de jeux vidéo, pack xbox, tester le jeu, jouer à tous les jeux, pack xbox one.
    </div>

    <script>
        function toggleMenu() {
            var navbar = document.querySelector('.navbar');
            navbar.classList.toggle('show');
        }
        
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
