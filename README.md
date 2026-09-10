# PETICA — Phase 3 · PHP / XAMPP

Livraison du 10 septembre 2026. PHP, Apache et MySQL/MariaDB ; JavaScript/CSS natifs ; français et arabe. Aucun SaaS, Composer, Node ou npm n’est nécessaire pour l’installation.

Les parcours sont reliés à la base : compte, inscription en sept étapes, identité permanente, deux QR, Mode Perdu, consultations et positions consenties, espace client, relations professionnelles, commandes, production, administration et CRM. Les anciens écrans de démonstration ne servent plus de données réelles.

**Recette exécutée :** PHP 8.3.6 et MariaDB 10.11.7 sous Linux, tests HTTP, concurrence et migration historique. **NON VÉRIFIÉ :** XAMPP Windows/Apache, Safari/Firefox/Edge, mobiles physiques, transport email et clé Google Maps. Chrome desktop a été inspecté partiellement ; le contrôle automatique a ensuite bloqué une interaction pour limite d’utilisation. Lire `docs/RECETTE.md` avant mise en service publique.

## Installation neuve

1. Installer XAMPP avec PHP 8.1 ou supérieur et MySQL/MariaDB. Copier `petica` dans `C:\xampp\htdocs\petica`.
2. Vérifier dans `C:\xampp\php\php.ini` : extensions `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`, `gd`, et `exif` pour l’orientation JPEG. Régler `upload_max_filesize=10M`, `post_max_size=24M`, `memory_limit=256M`. Redémarrer Apache après modification.
3. Activer `mod_rewrite` et autoriser `AllowOverride All` pour ce répertoire. Conserver tous les `.htaccess`.
4. Démarrer Apache et MySQL dans XAMPP. Créer une base vide `petica`, encodage `utf8mb4`, dans phpMyAdmin ou le client SQL. Ne pas installer de données de démonstration.
5. Copier `config/example.php` vers `config/local.php`. Renseigner les accès SQL. En XAMPP local, l’utilisateur est souvent `root`, mot de passe vide ; utiliser un compte dédié pour le public. Ce fichier est exclu de Git et de la livraison.
6. Définir `APP_URL=http://localhost/petica`. `PUBLIC_URL` est le domaine imprimé sur les nouveaux QR, défaut fourni : `https://petica.pet`. Vérifier ce domaine et ses routes avant toute gravure.
7. Générer une clé :

```bat
cd C:\xampp\htdocs\petica
C:\xampp\php\php.exe bin\console.php key
```

Copier la valeur dans `APP_KEY` du fichier local. Conserver cette clé avec la sauvegarde sécurisée : elle déchiffre les positions. Ne pas la publier ou la remplacer sans reprise des données chiffrées.

8. Installer le schéma et créer le premier administrateur :

```bat
C:\xampp\php\php.exe bin\console.php migrate
C:\xampp\php\php.exe bin\console.php create-admin votre-adresse@example.tn
C:\xampp\php\php.exe bin\console.php health
```

Le mot de passe administrateur est lu sur l’entrée standard, minimum 12 caractères. Aucun compte ou mot de passe de production n’est livré. `migrate` installe le schéma absent puis applique les migrations une fois ; il ne crée pas la base SQL elle-même.

9. Ouvrir `http://localhost/petica/fr/index.html` ou `/ar/index.html`. L’administrateur utilise la connexion normale.
10. Vérifier les droits d’écriture sur `storage/uploads` et le répertoire de sessions PHP. Garder les journaux hors accès public.

## Mise à jour

Ne pas réimporter `schema.sql` sur une base métier. Sauvegarder code, SQL, médias et configuration avec sa clé, puis tester `php bin/console.php migrate` sur une copie de base. Vérifier les anciens QR avant bascule. Voir `docs/SAUVEGARDE-RETOUR.md`.

Les migrations préservent les identifiants, séries, jetons et anciennes tables CRM, avancent les compteurs et importent l’historique sans duplication. Les anciens contacts publics demandent désormais un consentement explicite ; leurs valeurs privées ne sont pas supprimées. Les relations professionnelles demandent un accord client.

## Exploitation

- Inscription : sept étapes, brouillon de compte, portrait réel réencodé, réessai sans double identité. Séries `DOG`, `CAT`, `HRS`, tiret et neuf chiffres.
- Propriétaire : données, photos, contacts publiés sur consentement, Mode Perdu et historique QR. Le retour à l’état présent est explicite.
- Professionnel : vérification administrative, relation acceptée et périmètre métier. Le médical demande un vétérinaire autorisé. Les notes privées restent propres au professionnel.
- Commerce : calcul serveur en millimes. Catalogue initial : carte 49 TND, argent 49, or 59, livraison 8. Multi : 2 % par compagnon supplémentaire ; carte secondaire confirmée −10 %, sans médaille supplémentaire ; professionnel −10 %. Les cumuls sont bloqués sans approbation des règles. Les Combo historiques restent non commandables jusqu’à validation explicite.
- Commande : devis valable 15 minutes, recalcul verrouillé, une seule commande par devis. Tarifs et données de préparation figés.
- Production : draft → review → bat → approved → production → ready → shipped → completed. Le BAT est une feuille de préparation avec données et QR réels, pas une certification d’impression physique.
- CRM : prospects distincts des comptes, contacts, journal, échéances et conversion vers un compte existant. Journaliser un échange n’envoie pas de message externe.
- Contenu : aperçu, versions FR/AR, publication et restauration. Slugs : `conditions`, `confidentialite`, `blog/mon-article`. Réponses communes FAQ/NERO : `nero/identity`, `nero/lost`, `nero/location`, `nero/register`, `nero/secondary`, `nero/professional`, `nero/world`, `nero/movement`, `nero/notifications`, `nero/reset`, `nero/contact`. Les prix NERO viennent du catalogue.

## Positions et notifications

La médaille n’a pas de GPS. Une consultation signalée ne prouve pas qu’un animal a été vu. Le navigateur transmet une position ponctuelle après accord explicite ; un refus ou une panne n’empêche pas le contact.

Les coordonnées sont chiffrées AES-256-GCM et réservées au propriétaire principal, y compris face aux administrateurs et professionnels. Un lien Google Maps accompagne une position disponible. La carte intégrée nécessite `MAPS_KEY` et `MAPS_MAP_ID`, et ne se charge qu’après action du propriétaire. Restreindre la clé au domaine et aux API nécessaires. La clé navigateur est un identifiant public restreint.

Les notifications internes sont persistantes. Garder `EMAIL_ENABLED=false` tant que PHP `mail()` et `EMAIL_FROM` ne sont pas configurés et testés. Acceptation par le transport ne signifie pas réception. Push/SMS ont des interfaces non configurées, sans faux envoi. Le mot de passe oublié indique honnêtement l’indisponibilité de l’email.

Planifier toutes les cinq minutes, par exemple dans le Planificateur Windows :

```bat
C:\xampp\php\php.exe C:\xampp\htdocs\petica\bin\console.php jobs
```

Cette commande traite la file email activée et purge les données techniques et positions expirées. La lecture refuse déjà une position expirée même sans tâche planifiée. Conservation des coordonnées : 30 jours par défaut, configurable entre 1 et 90. Les événements subsistent sans leurs coordonnées.

## Documents

`docs/RECETTE.md`, `ROUTES.md`, `DATABASE.md`, `SECURITE.md`, `ARCHITECTURE.md`, `SAUVEGARDE-RETOUR.md` et `CHANGEMENTS.md` décrivent validation, contrats et exploitation. `docs/evidence/` contient les résultats machine ; `image-manifest.json` décrit les sources et variantes.

Les scripts de tests nécessitent une base jetable `_test` et utilisent uniquement des comptes fictifs `@petica-test.invalid`. Ils ne sont jamais installés par les migrations. Python et les outils de tests ne servent pas à exécuter PETICA. Les maîtres graphiques se trouvent dans `reference/masters`, refusé par Apache ; les pages chargent leurs variantes optimisées.
