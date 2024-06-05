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
document.addEventListener('DOMContentLoaded', function() {
    const discordButton = document.querySelector('.button-discord');
    const douaneButton = document.querySelector('.button-douane');
    
    discordButton.addEventListener('click', function() {
        window.location.href = 'https://discord.gg/altawl';
    });
    
    douaneButton.addEventListener('click', function() {
        window.location.href = '#douane';
    });

    // Ajout de l'animation au survol
    [discordButton, douaneButton].forEach(button => {
        button.addEventListener('mouseover', function() {
            button.style.transform = 'scale(1.1)';
        });

        button.addEventListener('mouseout', function() {
            button.style.transform = 'scale(1)';
        });
    });
});
