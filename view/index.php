<?php
$error = $_GET['error'] ?? '';
$nameValue = $_GET['name'] ?? '';
$emailValue = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GESTION </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-10">
     <div id="create-user-form" class="container py-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card p-3">
                    <h5 class="card-title">Créer un utilisateur</h5>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                    <form method="POST" action="../controlle/usercontrole.php?action=create" novalidate>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($nameValue, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($emailValue, ENT_QUOTES, 'UTF-8'); ?>">
                            <div id="email-feedback" class="form-text text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Rôle</label>
                            <select id="role" name="role" class="form-select">
                                <option value="user" selected>Utilisateur</option>
                                <option value="admin">Administrateur</option>
                                <option value="test">testeur</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Créer</button>
                    </form>
                </div>
            </div>
        </div>
     </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
            crossorigin="anonymous"></script>
    <script>
        const emailInput = document.getElementById('email');
        const emailFeedback = document.getElementById('email-feedback');
        const userForm = document.querySelector('#create-user-form form');
        let emailExists = false;

        async function checkEmailAvailability() {
            const email = emailInput.value.trim();
            if (!email) {
                emailFeedback.textContent = '';
                emailExists = false;
                return;
            }

            try {
                const response = await fetch(`../controlle/usercontrole.php?action=check_email&email=${encodeURIComponent(email)}`);
                const data = await response.json();
                if (data.success && data.exists) {
                    emailFeedback.textContent = 'Cet email est déjà utilisé.';
                    emailExists = true;
                } else if (data.success) {
                    emailFeedback.textContent = 'Adresse email disponible.';
                    emailFeedback.classList.remove('text-danger');
                    emailFeedback.classList.add('text-success');
                    emailExists = false;
                } else {
                    emailFeedback.textContent = 'Veuillez saisir une adresse email valide.';
                    emailExists = false;
                }
            } catch (error) {
                emailFeedback.textContent = "Impossible de vérifier l'email pour le moment.";
                emailExists = false;
            }
        }

        emailInput.addEventListener('blur', checkEmailAvailability);
        userForm.addEventListener('submit', async function (event) {
            await checkEmailAvailability();
            if (emailExists) {
                event.preventDefault();
                emailFeedback.textContent = 'Vous ne pouvez pas enregistrer avec cet email.';
                emailFeedback.classList.remove('text-success');
                emailFeedback.classList.add('text-danger');
            }
        });
    </script>
</body>
</html>