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
    <title>ALTA RP WHITELIST</title>
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

        .content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section {
            margin: 40px 0;
            opacity: 0; /* Start hidden */
            transform: translateY(20px); /* Start shifted down */
            transition: opacity 0.6s ease-out, transform 0.6s ease-out; /* Smooth transition */
        }

        .section.visible {
            opacity: 1; /* Fully visible */
            transform: translateY(0); /* Return to original position */
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

        .staff-level {
            margin: 40px 0;
        }

        .staff-level h3 {
            font-size: 1.8em;
            text-align: center;
            color: #ffcc00; /* Different color for subheadings */
        }

        .staff {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .staff .member {
            background-color: #1c1c1e;
            border-radius: 10px;
            margin: 15px;
            width: 250px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: 0.3s;
            cursor: pointer;
        }

        .staff .member:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            transform: scale(1.05);
        }

        .staff .member img {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .staff .container {
            padding: 15px;
        }

        .staff h4 {
            margin: 10px 0;
            color: white;
        }

        .staff p {
            margin: 10px 0;
            color: #aaa;
        }

        .images {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
        }

        .images img {
            width: 300px;
            height: 200px;
            object-fit: cover;
            margin: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .images img:hover {
            transform: scale(1.05);
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            max-height: 90vh; /* Limit height to prevent overflow */
            object-fit: contain; /* Ensure the image fits within the modal */
        }

        .modal-content, #caption {
            animation-name: zoom;
            animation-duration: 0.6s;
        }

        @keyframes zoom {
            from {transform: scale(0)} 
            to {transform: scale(1)}
        }

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        #caption {
            margin: auto;
            display: block;
            text-align: center;
            color: #ccc;
            padding: 10px 0;
            height: 150px;
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
    </style>
</head>
<body>
    <?php session_start(); ?>
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
            <li><a href="#discord">Discord</a></li>
            <li><a href="douane.php">Douane</a></li>
            <?php if(isset($_SESSION['user'])): ?>
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
            <h2>Notre Mentalité</h2>
            <p>
                Chez ALTA RP, nous croyons en une expérience de jeu immersive et respectueuse. Notre priorité est de créer un environnement où les joueurs peuvent s'immerger pleinement dans leurs rôles, tout en respectant les règles et en maintenant un comportement positif et respectueux envers les autres membres de la communauté.
            </p>
        </section>
        <section class="section">
            <h2>Notre Équipe</h2>
            <p>
                Notre équipe est composée de passionnés de RP qui sont dévoués à offrir la meilleure expérience possible à tous les joueurs. Nous sommes là pour vous aider, répondre à vos questions et assurer que tout se déroule sans accroc sur le serveur. Nous valorisons l'entraide, le respect et l'engagement dans tout ce que nous faisons.
            </p>
        </section>
        <section class="section">
            <h2>Équipe du Staff</h2>
            <div class="staff-level">
                <h3>Fondateur</h3>
                <div class="staff">
                    <div class="member" onclick="openModal(this, 'Voleskal', 'Fondateur', 'Description du fondateur.')">
                        <img src="IMG/voles.jpg" alt="Staff Member 1">
                        <div class="container">
                            <h4><b>Voleskal</b></h4>
                            <p>Fondateur</p>
                        </div>
                    </div>
                    <!-- Add more founders as needed -->
                </div>
            </div>
            <div class="staff-level">
                <h3>Développeurs</h3>
                <div class="staff">
                    <div class="member" onclick="openModal(this, 'Rémi', 'Développeur', 'Description du développeur Rémi.')">
                        <img src="IMG/remi.png" alt="Staff Member 3">
                        <div class="container">
                            <h4><b>Rémi</b></h4>
                            <p>Développeur</p>
                        </div>
                    </div>
                    <div class="member" onclick="openModal(this, 'VHK', 'Développeur', 'Description du développeur VHK.')">
                        <img src="IMG/vhk.jpg" alt="Staff Member 2">
                        <div class="container">
                            <h4><b>VHK</b></h4>
                            <p>Développeur</p>
                        </div>
                    </div>
                    <!-- Add more developers as needed -->
                </div>
            </div>
            <!-- <div class="staff-level">
                <h3>Responsables</h3>
                <div class="staff">
                    <div class="member" onclick="openModal(this, 'Elias', 'Responsable Illégal', 'Description du responsable illégal Elias.')">
                        <img src="IMG/elias.png" alt="Staff Member 3">
                        <div class="container">
                            <h4><b>Elias</b></h4>
                            <p>Administrateur</p>
                        </div>
                    </div>
                    <div class="member" onclick="openModal(this, 'Elias', 'Responsable Légal', 'Description du responsable légal Manny.')">
                        <img src="IMG/manny.JPG" alt="Staff Member 4">
                        <div class="container">
                            <h4><b>Manny</b></h4>
                            <p>Administrateur</p>
                        </div>
                    </div>
                    < Add more responsables as needed -->
                </div>
            </div> 
            <!-- Add more staff levels as needed -->
        </section>
        <section class="section">
            <h2>Galerie</h2>
            <div class="images">
                <img src="IMG/image1.png" alt="Image 1" onclick="openImageModal(this)">
                <img src="IMG/image2.png" alt="Image 2" onclick="openImageModal(this)">
                <img src="IMG/image3.png" alt="Image 3" onclick="openImageModal(this)">
                <img src="IMG/image4.png" alt="Image 4" onclick="openImageModal(this)">
                <img src="IMG/image5.png" alt="Image 5" onclick="openImageModal(this)">
                <img src="IMG/image6.png" alt="Image 6" onclick="openImageModal(this)">
                <!-- Add more images as needed -->
            </div>
        </section>
    </div>

    <!-- The Modal for Images -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeImageModal()">&times;</span>
        <img class="modal-content" id="img01">
        <div id="imageCaption"></div>
    </div>

    <!-- The Modal for Staff -->
    <div id="staffModal" class="modal">
        <span class="close" onclick="closeStaffModal()">&times;</span>
        <img class="modal-content" id="staffImg">
        <div id="staffCaption"></div>
        <div id="staffDescription" style="color: white; text-align: center; margin-top: 10px;"></div>
    </div>

    <script>
        function toggleMenu() {
            var navbar = document.querySelector('.navbar');
            navbar.classList.toggle('show');
        }

        function openImageModal(element) {
            var modal = document.getElementById("imageModal");
            var modalImg = document.getElementById("img01");
            var captionText = document.getElementById("imageCaption");
            modal.style.display = "block";
            modalImg.src = element.src;
            captionText.innerHTML = element.alt;
        }
        
        function closeImageModal() {
            var modal = document.getElementById("imageModal");
            modal.style.display = "none";
        }
        
        function openModal(element, name, role, description) {
            var modal = document.getElementById("staffModal");
            var modalImg = document.getElementById("staffImg");
            var captionText = document.getElementById("staffCaption");
            var descText = document.getElementById("staffDescription");
            modal.style.display = "block";
            modalImg.src = element.getElementsByTagName('img')[0].src;
            captionText.innerHTML = name + " - " + role;
            descText.innerHTML = description;
        }
        
        function closeStaffModal() {
            var modal = document.getElementById("staffModal");
            modal.style.display = "none";
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            var staffMembers = document.querySelectorAll('.staff .member');
        
            staffMembers.forEach(function(member) {
                member.addEventListener('mouseenter', function() {
                    member.style.transform = 'scale(1.05)';
                    member.style.transition = 'transform 0.3s';
                });
        
                member.addEventListener('mouseleave', function() {
                    member.style.transform = 'scale(1)';
                });
            });
        
            // Scroll animation
            var sections = document.querySelectorAll('.section');
        
            function checkVisibility() {
                sections.forEach(function(section) {
                    var rect = section.getBoundingClientRect();
                    if (rect.top < window.innerHeight && rect.bottom >= 0) {
                        section.classList.add('visible');
                    } else {
                        section.classList.remove('visible');
                    }
                });
            }
        
            window.addEventListener('scroll', checkVisibility);
            checkVisibility(); // Initial check
        });
    </script>
</body>
</html>
