# Système d'Authentification et de Récupération de Mot de Passe

Ce dossier contient tous les fichiers nécessaires pour le système d'authentification et de récupération de mot de passe de la plateforme de jeux.

## Fonctionnalités Implémentées

### 1. Système d'Authentification
- Inscription des utilisateurs avec validation des données
- Connexion des utilisateurs avec vérification des identifiants
- Protection des pages de jeux par vérification de session
- Affichage des informations de l'utilisateur connecté
- Déconnexion des utilisateurs

### 2. Récupération de Mot de Passe
- Ajout du champ date de naissance dans le formulaire d'inscription
- Lien "Mot de passe oublié" sur la page de connexion
- Processus de récupération de mot de passe en 3 étapes :
  - Étape 1 : Saisie de l'adresse email
  - Étape 2 : Vérification de la date de naissance
  - Étape 3 : Définition d'un nouveau mot de passe

## Fichiers Principaux

- `formulaire_de_creation.php` : Formulaire d'inscription des utilisateurs
- `formulaire_de_connexion.php` : Formulaire de connexion des utilisateurs
- `mot_de_passe_oublie.php` : Processus de récupération de mot de passe
- `deconnexion.php` : Script de déconnexion des utilisateurs
- `add_date_naissance_column.sql` : Script SQL pour ajouter la colonne date_naissance
- `update_database.php` : Script pour exécuter la modification de la base de données
- `update_game_paths.php` : Script pour mettre à jour les chemins des jeux dans la base de données
- `test_password_reset.php` : Script de test pour vérifier le système de récupération de mot de passe

## Fichiers Alternatifs

En cas de problème avec la base de données (colonne date_naissance manquante), des versions alternatives des fichiers sont disponibles :

- `formulaire_de_creation_alt.php` : Version alternative du formulaire d'inscription qui fonctionne même si la colonne date_naissance n'existe pas
- `mot_de_passe_oublie_alt.php` : Version alternative du processus de récupération de mot de passe qui fonctionne même si la colonne date_naissance n'existe pas
- `fix_database_issue.php` : Script pour résoudre les problèmes de base de données et utiliser les fichiers alternatifs si nécessaire

## Installation et Configuration

1. Assurez-vous que la base de données est correctement configurée dans `../config.php`
2. Exécutez le script `update_database.php` pour ajouter la colonne date_naissance à la table connexion_games
3. Exécutez le script `update_game_paths.php` pour mettre à jour les chemins des jeux dans la base de données
4. En cas de problème, exécutez le script `fix_database_issue.php` pour résoudre les problèmes de base de données

## Utilisation

### Inscription
1. Accédez à `formulaire_de_creation.php`
2. Remplissez le formulaire avec vos informations (nom, email, date de naissance, mot de passe)
3. Soumettez le formulaire pour créer votre compte

### Connexion
1. Accédez à `formulaire_de_connexion.php`
2. Entrez votre email et mot de passe
3. Cliquez sur "Se connecter" pour accéder à votre compte

### Récupération de Mot de Passe
1. Accédez à `formulaire_de_connexion.php`
2. Cliquez sur le lien "Mot de passe oublié"
3. Suivez les étapes du processus de récupération :
   - Entrez votre adresse email
   - Vérifiez votre identité en saisissant votre date de naissance (si disponible)
   - Définissez un nouveau mot de passe

### Test du Système
1. Accédez à `test_password_reset.php` pour vérifier que tout est correctement configuré
2. Suivez les instructions pour tester manuellement le processus de récupération de mot de passe

### Résolution des Problèmes
1. Si vous rencontrez des problèmes avec la base de données, accédez à `fix_database_issue.php`
2. Ce script tentera de résoudre les problèmes et utilisera les fichiers alternatifs si nécessaire

## Sécurité

- Les mots de passe sont hachés avec la fonction `password_hash()` de PHP
- Les données utilisateur sont échappées avec la fonction `escape_html()` pour éviter les attaques XSS
- Les requêtes SQL utilisent des requêtes préparées pour éviter les injections SQL
- Les sessions sont utilisées pour gérer l'authentification des utilisateurs
- La vérification de la date de naissance est utilisée comme méthode de vérification d'identité pour la récupération de mot de passe (si disponible)