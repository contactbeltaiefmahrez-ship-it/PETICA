# Architecture et contrats

Apache réécrit les URL `.html` FR/AR vers `page.php`, qui contrôle route, session et rôle avant rendu. Les formulaires utilisent POST puis redirection 303 ; JavaScript ajoute progression réelle des téléversements, QR, NERO, géolocalisation et rafraîchissements. `router.php` émule ces routes pour le serveur PHP de développement.

L’API retourne `{success,data}` ou `{success:false,error:{code,message,validation_errors}}`. Les clés historiques du résultat existent aussi au premier niveau. Les anciens fichiers API délèguent au même répartiteur ; le chemin demandé prime sur les paramètres de capture de réécriture.

| Module | Responsabilité |
|---|---|
| `app/core.php` | PDO préparé, transactions, sessions, CSRF, idempotence, limites, audit |
| `app/accounts.php` | Comptes, accès, réinitialisation, consentements, professionnels, visites |
| `app/identity.php` | Dossiers, permissions, édition versionnée, profils, médias, Mouvement |
| `includes/serial.php` | Allocation transactionnelle des séries |
| `includes/qr.php` | Deux jetons permanents, URL et filtre public |
| `app/recovery.php` | Mode Perdu, consultations, capacités temporaires, chiffrement, rétention |
| `app/public_qr.php` | Page autonome de contact et consentement |
| `app/commerce.php` | Millimes, devis, commandes, snapshots, production |
| `app/operations.php` | Catalogue, promotions, versions de contenu, CRM |
| `app/nero.php` | Connaissances PETICA, contenus publiés, catalogue |
| `app/jobs.php` | Rétention et transports configurés |
| `app/wizard.php` | Sept étapes d’inscription |
| `views/` | Rendu FR/AR et espaces par rôle |
| `bin/console.php` | CLI uniquement : migration, clé, administrateur, santé et tâches |

## Invariants

- Identité, propriétaire primaire, deux jetons et événement du Mouvement sont créés dans une transaction.
- Une clé d’idempotence est liée à l’utilisateur, au type d’action et au contenu. Une clé réutilisée avec d’autres données échoue en 409.
- L’UPSERT du compteur InnoDB s’exécute dans la transaction : aucun numéro ne dépend du nombre de lignes ou du navigateur.
- Les prix se calculent en millimes entiers ; les colonnes monétaires sont `DECIMAL(12,3)`. Répartition des remises selon les plus grands restes, départage stable par ligne.
- Cumul approuvé : remise secondaire, remise globale professionnelle ou multi, promotion autorisée à se cumuler, livraison. La remise pro remplace la multi lorsqu’elle est demandée. Sans approbation, les combinaisons sont bloquées.
- Devis verrouillé et `orders.quote_id` unique : une seule commande par devis. Les quotas promo sont recontrôlés à la confirmation.
- Les snapshots conservent lignes, versions, données de préparation, médias, éventuel titulaire secondaire, livraison et URL. L’approbation BAT fige un second snapshot. Remplacer une photo crée un nouveau fichier.
- Chaque lecture privée vérifie les droits. Suspension ou invalidation de session s’applique à la requête suivante.
- Consultation QR et position sont deux faits distincts. Un scan ne résout jamais le Mode Perdu.
- NERO ne modifie aucun dossier, ne commande rien et n’envoie pas de message externe.

## Migrations

| Migration | Effet |
|---|---|
| 001 | Colonnes et tables Phase 3, compteurs avancés, frontière historique du Mouvement, catalogue initial |
| 002 | Précision monétaire et contacts clients historiques |
| 003 | Import unique des notes, interactions et suivis CRM, tables originales conservées |
| 004 | Identifiant unique du devis associé à une commande |

Le Mouvement actif utilise `registration_events` plus la base historique de 100. Les anciennes tables de référence ne pilotent pas ce chiffre.

## Ressources

Les 21 visuels approuvés sont dérivés en WebP responsives ; les maîtres restent hors accès HTTP. Le visuel 02 est recadré sur le chat, ses cartes illustratives intégrées retirées ; les maquettes officielles sont affichées séparément. NERO conserve son SVG officiel et le visuel 21 dans son contexte prévu.

Fraunces, Plus Jakarta Sans et IBM Plex Sans Arabic sont servis localement en WOFF, licences OFL jointes. QRCode.js provient de `davidshimjs/qrcodejs`, blob GitHub `993e88f396640f881b69f98db7a4d17401ef83ca`, licence MIT jointe. Les QR sont calculés à partir des URL serveur, jamais générés par IA. Le décodeur indépendant de recette est jsQR (`cozmo/jsQR`, blob `99ea9df26907009e5553233ffe03c529c1521739`), absent du runtime de production.
