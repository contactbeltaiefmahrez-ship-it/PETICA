# Sécurité et limites

Ces contrôles ciblés ne remplacent pas un audit externe. Les tests n’ont utilisé que des données synthétiques.

| Risque | Emplacement | Mesure | Test | Résultat |
|---|---|---|---|---|
| Accès inter-comptes | Dossiers, commandes, événements | Contrôle de propriétaire/relation à chaque requête | Client étranger et anonyme | 403/401 observés |
| Élévation professionnelle | Relations et médical | Vérification + consentement + rôle/périmètre | En attente, groomer médical, révocation | Refus observés |
| Position publique | QR, scans | Capacité courte ; lecture réservée au primaire | Autre client et administrateur | 403 observés |
| CSRF | Tous POST/PATCH | Jeton de session et en-tête/champ vérifié | Jeton incorrect | 403 observé |
| Injection SQL | Paramètres API | PDO préparé, noms SQL issus du code | Email avec payload SQL | 401, pas de connexion |
| XSS | Nom et pages | Échappement HTML ; textContent NERO ; CSP | Nom avec script dans QR | Balises échappées |
| Fichier exécutable | Upload | MIME, extension, décodage GD, réencodage JPG, noms aléatoires | Fichier PHP prétendu JPEG | 422 observé |
| Double identité | Création et séries | Transaction + clé d’idempotence + compteur InnoDB | Réessai et huit processus concurrents | Aucun doublon observé |
| Double commande | Devis et ordre | Verrou devis + quote_id unique | Nouvelle clé sur même devis | Même commande observée |
| Prix modifié / quota | Commerce | Recalcul, verrou produit/promo, snapshots | Prix changé et quota consommé | 409/422, ancien prix conservé |
| Rejeu de position | QR | Capacité hachée, expiry, consentement et validation | Capacité invalide ; latitude hors plage | 403/422 observés |
| Conservation excessive | Position | Expiration à la lecture et purge du cipher | Date expirée puis jobs | Position inaccessible puis cipher effacé |
| Session suspendue | Compte | Version d’auth et état revérifiés | Suspension après connexion | 401 observé |
| Secrets web | Config, SQL, référence | Règles Apache et routeur ; local.php exclu | Accès direct aux fichiers | 403 via routeur PHP ; Apache restant |
| Abus de scan | Création événements | Rate limit, nonce de session, déduplication rapide | Deux signalements même nonce | Événement unique observé |
| Traçabilité historique | Migrations | Aucune suppression de tables ; import unique | Migration deux fois et fixtures antérieures | Sept assertions réussies |

## Configuration de déploiement

Activer HTTPS sur le domaine réel : nécessaire aux cookies Secure et à la géolocalisation hors localhost. Conserver les règles Apache, les extensions et un répertoire de sessions fonctionnel. Protéger sauvegardes et APP_KEY ; appliquer des accès SQL dédiés. Aucune clé, configuration locale, base SQL métier, session ou photo de recette n’est incluse dans la livraison.

Les données de livraison et médicales restent privées en base ; les positions seules bénéficient ici d’un chiffrement applicatif AES-GCM. Aucune prétention de chiffrement global de la base ou de certification n’est faite. Les clés navigateur Google doivent être restreintes.

Les textes juridiques, la politique opérationnelle de rétention des autres dossiers, la réception email, les tests de navigateur et la revue de sécurité de l’hébergement restent à valider avant ouverture publique.
