<?php
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — MVC Gestion</title>
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

<div class="card shadow-lg p-4" style="width: 100%; max-width: 400px;">

    <!-- Titre -->
    <div class="text-center mb-4">
        <h1 class="h4 fw-bold">MVC Gestion</h1>
        <p class="text-muted small">Connectez-vous à votre espace</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <!-- Formulaire (Changé en POST pour la sécurité) -->
    <form id="form-login" method="POST" action="../controlle/usercontrole.php?action=login" novalidate>

        <!-- Champ Email -->
        <div class="mb-3">
            <label for="email" class="form-label small fw-medium">Adresse email</label>
            <input type="email" class="form-control" id="email" name="email"
                   placeholder="admin@example.com"
                   value="<?= htmlspecialchars($_GET['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   required>
        </div>

        <!-- Champ Mot de passe -->
        <div class="mb-4">
            <label for="password" class="form-label small fw-medium">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" 
                   placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-2">
            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
        </button>
    </form>

        <!-- BOUTON 2 : Créer un compte (Secondaire, stylisé en bouton Bootstrap) -->
        <a href="index.php" class="btn btn-outline-secondary w-100 py-2 fw-semibold">
            <i class="bi bi-person-plus me-2"></i>Créer un compte
        </a>

</div>
<script>
// Aucun traitement JavaScript côté création d'utilisateur n'est actuellement utilisé.
</script>
</body>
</html>