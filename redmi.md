# Projet MVC Gestion

## Présentation
Ce projet est une application PHP simple organisée en MVC (Modèle-Vue-Contrôleur) pour la gestion des utilisateurs et des patients.

## Structure du projet
- `controlle/`
  - `patientcontroller.php` : traitement des actions CRUD pour les patients.
  - `usercontrole.php` : traitement de l'authentification, de la création d'utilisateur et des actions utilisateur.
- `DAO/`
  - `patientdao.php` : accès aux données des patients.
  - `userdao.php` : accès aux données des utilisateurs.
- `model/`
  - `patient.php` : classe `patient`.
  - `user.php` : classe `user`.
- `view/`
  - `dashbord.php` : tableau de bord principal et routeur de pages.
  - `home_view.aphp` : vue d'accueil du dashboard.
  - `login.php` : formulaire de connexion.
  - `index.php` : formulaire d'inscription.
- `patients.php` : vue de gestion des patients incluse dans le dashboard.

## Installation
1. Placer le projet dans le répertoire du serveur web (ex: `c:\xampp\htdocs\STAGE`).
2. Vérifier la connexion à la base de données dans `DAO/userdao.php` et `DAO/patientdao.php`.
3. Créer une base de données MySQL nommée `mvc_gestion`.
4. Créer les tables `users` et `patients` avec les champs appropriés.

## Utilisation
- Ouvrir `view/login.php` pour se connecter.
- Après connexion, l'utilisateur est redirigé vers `view/dashbord.php`.
- Depuis le dashboard, accéder à la gestion des patients via `?page=patients`.
- Le formulaire d'inscription est accessible via `view/index.php`.

## Notes
- La session stocke l'objet `user` et nécessite que `model/user.php` soit chargé avant `session_start()` dans les scripts qui utilisent `$_SESSION['user']`.
- L'application utilise PDO pour communiquer avec MySQL.
- Les vues `home_view.php` et `patients.php` sont incluses par `view/dashbord.php` et doivent être traitées comme des fragments HTML.
