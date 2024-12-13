// PROFILE DROPDOWN
document.addEventListener("DOMContentLoaded", function () {
    const profile = document.querySelector('nav .profile');
    const imgProfile = profile ? profile.querySelector('.img') : null;
    const dropdownProfile = profile ? profile.querySelector('.profile-link') : null;

    if (imgProfile && dropdownProfile) {
        imgProfile.addEventListener('click', function (e) {
            // Impede o clique no ícone de fechar o menu ao clicar dentro do menu
            e.stopPropagation(); 
            dropdownProfile.classList.toggle('show');
        });
    }

    // MENU
    const allMenu = document.querySelectorAll('main .content-data .head .menu');

    allMenu.forEach(item => {
        const icon = item.querySelector('.icon');
        const menuLink = item.querySelector('.menu-link');

        if (icon && menuLink) {
            icon.addEventListener('click', function (e) {
                e.stopPropagation(); // Impede o clique no ícone de fechar o menu
                menuLink.classList.toggle('show');
            });
        }
    });

    // Fechar todos os menus se o clique for fora de qualquer menu
    window.addEventListener('click', function (e) {
        if (dropdownProfile && e.target !== imgProfile && !dropdownProfile.contains(e.target)) {
            dropdownProfile.classList.remove('show');
        }

        allMenu.forEach(item => {
            const icon = item.querySelector('.icon');
            const menuLink = item.querySelector('.menu-link');

            if (icon && menuLink && e.target !== icon && !menuLink.contains(e.target)) {
                menuLink.classList.remove('show');
            }
        });
    });
});