# Somasteel - Espace Employé & Gestion des Achats

Ce projet est une application Laravel conçue pour gérer l'espace employé de Somasteel, incluant la gestion des demandes de congé, les absences, un annuaire, et un module complet de gestion des processus d'achat.

## Prérequis

*   PHP >= 8.2 (basé sur vos logs d'erreur récents)
*   Composer (Gestionnaire de dépendances PHP)
*   MySQL (ou autre base de données compatible avec Laravel)
*   Node.js & NPM (pour la compilation des assets frontend)
*   Un serveur web (Apache, Nginx) si vous ne prévoyez pas d'utiliser `php artisan serve` en production.
*   Un gestionnaire de file d'attente configuré (par exemple, `database` ou `redis`) pour le traitement asynchrone des notifications.

## Étapes d'Installation et Configuration

1.  **Cloner le dépôt**:
    Si vous partez d'un dépôt Git :
    ```bash
    git clone https://github.com/San-AMRANI/somasteel_espace_employe.git
    cd somasteel_espace_employe
    ```
    Si vous avez les fichiers localement, naviguez simplement vers le répertoire du projet.

2.  **Installer les dépendances PHP**:
    Assurez-vous que Composer est installé, puis exécutez :
    ```bash
    composer install
    ```

3.  **Configurer le fichier d'environnement**:
    Copiez `.env.example` vers `.env` :
    ```bash
    cp .env.example .env
    ```
    Ouvrez le fichier `.env` et mettez à jour les configurations suivantes :

    *   **Application Name & URL**:
        ```dotenv
        APP_NAME="SomaSteel Espace Employé"
        APP_ENV=local
        APP_KEY= # Sera généré à l'étape suivante
        APP_DEBUG=true
        APP_URL=http://localhost:8000 # Ou votre URL de développement/production
        ```

    *   **Base de Données**:
        Configurez vos identifiants de base de données. Exemple pour MySQL :
        ```dotenv
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=somasteel_espace_employe # Ou le nom de votre base de données
        DB_USERNAME=root # Votre utilisateur MySQL
        DB_PASSWORD= # Votre mot de passe MySQL
        ```
        Créez la base de données si elle n'existe pas.

    *   **Configuration Email (Crucial pour les Notifications)**:
        Pour que les notifications par email fonctionnent, configurez votre service d'envoi. Exemple avec Gmail (pour le développement, assurez-vous d'autoriser les applications moins sécurisées ou d'utiliser un mot de passe d'application si l'authentification à 2 facteurs est activée) :
        ```dotenv
        MAIL_MAILER=smtp
        MAIL_HOST=smtp.gmail.com
        MAIL_PORT=587
        MAIL_USERNAME=votreadresse@gmail.com
        MAIL_PASSWORD='votre_mot_de_passe_application_gmail' # Ex: abcd efgh ijkl mnop
        MAIL_ENCRYPTION=tls
        MAIL_FROM_ADDRESS="votreadresse@gmail.com"
        MAIL_FROM_NAME="${APP_NAME}"
        ```
        **Note pour Gmail :** L'utilisation de mots de passe d'application est fortement recommandée. Consultez la documentation de Google pour les configurer.

    *   **Configuration de la File d'Attente (Queue) pour les Notifications Asynchrones**:
        Les notifications par email sont configurées pour être envoyées via une file d'attente pour améliorer les performances.
        ```dotenv
        QUEUE_CONNECTION=database # Recommandé pour commencer, ou 'redis' si vous avez Redis
        ```
        Si vous utilisez `database`, une table pour les tâches en file d'attente sera créée par les migrations.

4.  **Générer la clé d'application**:
    ```bash
    php artisan key:generate
    ```

5.  **Lien Symbolique pour le Stockage Public**:
    Crucial pour que les fichiers uploadés (scans de factures, preuves de paiement, photos de profil) soient accessibles via le web.
    ```bash
    php artisan storage:link
    ```
    *   Si vous obtenez une erreur indiquant que le lien existe déjà, vous devrez peut-être supprimer manuellement le dossier `public/storage` (s'il n'est pas déjà un lien symbolique) avant de relancer la commande.
    *   Sur Windows, exécutez votre terminal en tant qu'administrateur.

6.  **Configurer la Base de Données (Migrations & Seeders)**:
    Exécutez les migrations pour créer la structure de la base de données :
    ```bash
    php artisan migrate
    ```
    Si vous utilisez `QUEUE_CONNECTION=database`, cela inclut la création de la table `jobs`.

    Ensuite, remplissez les tables avec les données initiales et les utilisateurs de test :
    ```bash
    php artisan db:seed --class=ServicesTableSeeder
    php artisan db:seed --class=UsersTableSeeder # Si vous l'utilisez toujours
    php artisan db:seed --class=TestUserSeeder   # Pour les utilisateurs de test avec différents rôles
    # Vous pouvez aussi lancer tous les seeders définis dans DatabaseSeeder.php avec :
    # php artisan db:seed
    ```
    **Note :** Assurez-vous que `TestUserSeeder` contient des utilisateurs pour tous les rôles nécessaires (`directeur`, `purchase`, `magasinier`, `comptable`, `ouvrier`, `rh`, `administrateur`).

7.  **Installer les dépendances frontend**:
    ```bash
    npm install
    ```

8.  **Compiler les assets frontend**:
    Pour le développement :
    ```bash
    npm run dev
    ```
    Pour la production :
    ```bash
    npm run build
    ```

9.  **Démarrer le Worker de File d'Attente**:
    Pour traiter les notifications par email (et autres tâches en file d'attente), vous devez lancer le worker. Laissez cette commande tourner dans un terminal séparé pendant le développement :
    ```bash
    php artisan queue:work
    ```
    En production, vous utiliserez un gestionnaire de processus comme Supervisor pour maintenir le worker actif.

10. **Démarrer le serveur de développement**:
    ```bash
    php artisan serve
    ```
    Visitez `http://localhost:8000` (ou l'URL définie dans `APP_URL`) pour voir l'application.

## Dépannage Courant

*   **Erreurs de classes non trouvées / Erreurs de routes / Changements non pris en compte**:
    ```bash
    php artisan optimize:clear
    # Ou individuellement :
    # php artisan config:clear
    # php artisan cache:clear
    # php artisan route:clear
    # php artisan view:clear
    composer dump-autoload
    ```

*   **Erreur "Storage link already exists"**:
    Supprimez le dossier `public/storage` (s'il n'est pas un lien symbolique) puis relancez `php artisan storage:link`.

*   **Emails non envoyés**:
    1.  Vérifiez votre configuration `.env` pour `MAIL_...`.
    2.  Assurez-vous que le worker de file d'attente (`php artisan queue:work`) est en cours d'exécution.
    3.  Vérifiez les logs de Laravel (`storage/logs/laravel.log`) et les logs de la file d'attente (si configurés) pour des erreurs.
    4.  Utilisez un service comme Mailtrap.io pendant le développement pour intercepter et déboguer les emails.

*   **Fichiers uploadés non accessibles (404 ou erreur d'autorisation)**:
    Assurez-vous que `php artisan storage:link` a été exécuté avec succès et que les URL dans vos vues utilisent `Storage::url('chemin/vers/fichier.ext')`.

## Rôles Utilisateur Importants (Types)

Pour tester les différentes fonctionnalités, utilisez les comptes créés par `TestUserSeeder` (ou créez les vôtres) avec les types suivants :

*   `administrateur` : Accès complet.
*   `rh` : Gestion du personnel, annuaire.
*   `directeur` : Validation des demandes d'achat, supervision.
*   `purchase` : Service Achat - gestion des RFQ, création des Bons de Commande.
*   `magasinier` : Réception des produits.
*   `comptable` : Gestion des factures fournisseurs, enregistrement des paiements.
*   `responsable` : Validation des absences.
*   `ouvrier` : Utilisateur de base, peut soumettre des demandes d'achat et de congé.

**Exemple de login (si `TestUserSeeder` est utilisé) :**
*   Email: `[role]@somasteel.com` (ex: `directeur@somasteel.com`, `purchase@somasteel.com`)
*   Mot de passe: `password` (ou celui que vous avez défini dans le seeder)

---
*Développé par [Hamza El Barrak]*
