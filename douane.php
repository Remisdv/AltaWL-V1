<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: includes/login.php");
    exit();
}
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
    <title>Douane</title>
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
            justify-content: space-between; /* Ajouté pour espacer les éléments */
            padding: 10px 20px; /* Ajout de padding */
        }
        
        .logo {
            height: 50px; /* Ajustez la hauteur selon vos besoins */
            margin: 10px 20px; /* Ajoutez un peu d'espace autour du logo */
        }
        
        .navbar {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: space-around;
            flex-grow: 1; /* Permet à la liste de prendre tout l'espace disponible */
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

        /* Hide menu by default */
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

        /* Media Queries for responsiveness */
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

            .section h2 {
                font-size: 2.5em;
            }

            .section p {
                font-size: 1.5em;
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
            <li><a href="https://docs.google.com/document/d/1fidOubvW5WF3yXgau2iOYBIwCYTUQaXvrGPf7-8Ryn4/edit">Règlement</a></li>
            <li><a href="https://top-serveurs.net/gta/alta-rp">Vote</a></li>
            <li><a href="douane.php">Douane</a></li>
            <?php if (isset($_SESSION['user'])): ?>
                <li class="user-menu">
                    <a href="#"><?php echo htmlspecialchars($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?> &#x25BC;</a>
                    <div class="user-menu-content">
                        <a href="includes/logout.php">Déconnexion</a>
                    </div>
                </li>
            <?php else: ?>
                <li><a href="includes/login.php">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="content">
        <section class="section">
            <h2>Douane</h2>
            <p>Bienvenue à la section Douane. Ici, vous passerez votre Whitelist. Les étapes sont les suivantes :</p>
            <ol>
                <li>Un questionnaire RP</li>
                <li>Un background</li>
                <li>La WL vocale (si le background est accepté)</li>
            </ol>
            <div class="button-container">
                <a href="questionnaire.php" class="button">Commencer le questionnaire</a>
            </div>
        </section>
    </div>
    <script>
        function toggleMenu() {
            var navbar = document.querySelector('.navbar');
            navbar.classList.toggle('show');
        }
    </script>
</body>
</html>
