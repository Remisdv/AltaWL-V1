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
