document.addEventListener('DOMContentLoaded', function () {
    console.log('Document ready');

    function showNotification(message, type = 'success') {
        const notification = document.getElementById('notification');
        if (notification) {
            notification.textContent = message;
            notification.className = `alert alert-${type}`;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000);
        }
    }

    // Validation du formulaire d'inscription
    const registerForm = document.querySelector('form[action="register.php"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function (event) {
            const username = document.getElementById('nom_utilisateur').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('mot_de_passe').value;
            const role = document.getElementById('role').value;

            if (!username || !email || !password || !role) {
                showNotification('Veuillez remplir tous les champs.', 'danger');
                event.preventDefault();
            }
        });
    }

    // Validation du formulaire de connexion
    const loginForm = document.querySelector('form[action="login.php"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            const username = document.getElementById('nom_utilisateur').value;
            const password = document.getElementById('mot_de_passe').value;

            if (!username || !password) {
                showNotification('Veuillez remplir tous les champs.', 'danger');
                event.preventDefault();
            }
        });
    }
});
