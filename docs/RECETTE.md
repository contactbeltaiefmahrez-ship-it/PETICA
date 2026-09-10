# PETICA — Rapport de recette Phase 3

Date : 10 septembre 2026. Branche : `implementation/phase-3`.

## Résultats exécutés

- **426 contrôles HTTP réussis, 0 échec**, avec PHP 8.3.6 et MariaDB 10.11.7 sous Linux. Les contrôles comptent des assertions de réponse, de parcours et de rendu, pas autant de scénarios indépendants.
- **95 fichiers PHP valides syntaxiquement**. Vérification de syntaxe des trois scripts JavaScript actifs également effectuée.
- **7 contrôles de migration historique réussis** : série, deux jetons, ancien CRM conservé, import unique, historique non recompté, compteur avancé, état conservé. Deux exécutions successives de migrate.
- **3 destinations QR relues exactement par un décodeur indépendant** : identité, médaille et forme historique. Ce test porte sur l’encodage algorithmique, pas sur une gravure physique ou l’export canvas dans chaque navigateur.
- **Chrome desktop, contrôle partiel** : accueil FR/AR et ouverture du dialogue NERO. La saisie suivante a été refusée par le contrôle automatique avec le motif « limite d’utilisation atteinte ». Aucun contournement effectué. Les derniers ajustements CSS, notamment le logo et la galerie, n’ont pas été recapturés.

Les scripts utilisent une base isolée `petica_test` et une base historique synthétique `petica_legacy_test`. Aucune base de production, position réelle ou messagerie externe n’a été utilisée. Les coordonnées de recette sont explicitement fictives et restent hors de la livraison des données.

71 images actives ont été vérifiées comme lisibles et les empreintes des 21 maîtres fournis ont été comparées avec les références. Deux variantes WebP vides ont été régénérées puis vérifiées.

## Matrice des fonctionnalités

« Vérifié HTTP » signifie vérification du contrat serveur et du rendu reçu, pas validation complète visuelle, matérielle ou multi-navigateur. Les interfaces externes non configurées restent indisponibles honnêtement.

| Fonction | Implémenté | Connecté | Interactif | Testé / vérifié | Notes |
|---|---|---|---|---|---|
| Authentification | Oui | Oui | Formulaires | HTTP | Accès, CSRF, suspension ; email réel non configuré |
| Inscription | Oui | Oui | 7 étapes | HTTP | POST réels, multipart, résumé, confirmation, idempotence |
| Compagnon | Oui | Oui | Dossier | HTTP | Création, édition, portrait, droits croisés |
| Séries | Oui | Oui | Serveur | HTTP + concurrence | DOG/CAT/HRS neuf chiffres ; huit créations simultanées |
| QR identité | Oui | Oui | Lien et export | Routes + décodeur | Jeton permanent ; impression physique non vérifiée |
| QR Tag | Oui | Oui | Lien et export | Routes + décodeur | URL opaque et format historique id/série |
| Consultation QR | Oui | Oui | Page et signalement | HTTP | Événement persistant, doublon rapide et capacité |
| Position | Oui | Oui | Consentement navigateur | API + chiffrement | Coordonnées de test synthétiques, pas de position physique capturée |
| Google Maps | Oui | Configuration requise | Chargement explicite | NON VÉRIFIÉ | Clé et map ID non fournis ; lien de secours implémenté |
| Notifications | Oui | Base interne | Liste, lecture | HTTP | Email/push/SMS externes non vérifiés ; aucun faux envoi |
| Mode Perdu | Oui | Oui | Transition explicite | HTTP | Scan ne résout pas ; propriétaire confirme le retour |
| Prix | Oui | Oui | Configurateur | HTTP | Millimes, multi, secondaire, pro, promo/quota, devis périmé |
| Commandes | Oui | Oui | Devis et confirmation | HTTP | Une commande par devis même avec nouvelle clé de reprise |
| Production | Oui | Oui | Étapes et BAT | HTTP | Toutes transitions, snapshots et historique ; test imprimeur restant |
| Titulaire secondaire | Oui | Oui | Nom et confirmation | HTTP | Carte seule −10 %, médaille supplémentaire refusée |
| Espace client | Oui | Oui | Dossiers et cartes | HTTP | Accès et formulaires réels ; galerie finale non revue visuellement |
| Espace pro | Oui | Oui | Profil, recherche, visites | HTTP | Approbation, accord, révocation, médical vétérinaire, commande |
| Administration | Oui | Oui | Gestion et production | HTTP | Données, états, catalogue, versions de contenu ; pas de compte livré |
| CRM | Oui | Oui | Contacts, journal, suivis | HTTP + migration | Prospect sans utilisateur ; import historique conservé |
| NERO | Oui | Oui | Dialogue et FAQ | API ; UI partielle | Dialogue ouvert dans Chrome ; soumission UI bloquée par limite automatique |
| Français | Oui | Oui | Parcours localisés | HTTP + Chrome partiel | Pages rendues ; relecture linguistique complète non effectuée |
| Arabe | Oui | Oui | Parcours localisés | HTTP + Chrome partiel | Pages rendues, dir=rtl ; revue linguistique native restante |
| RTL | Oui | Oui | CSS logique | Chrome partiel | Placement du texte accueil corrigé ; capture finale non refaite |
| Mobile | Oui | Oui | CSS responsive | NON VÉRIFIÉ | 320/375/390/430 et tablette : pas de recette navigateur finale |
| Desktop | Oui | Oui | Interface | Chrome partiel | Accueil FR/AR observé ; pas de recette visuelle exhaustive |
| SEO | Oui | Oui | Métadonnées et XML | HTTP + code | Sitemap, robots, canonical, langues ; pas d’indexation externe vérifiée |
| Accessibilité | Oui | Oui | Contrôles natifs | Revue de code partielle | Labels, focus, régions live, reduced-motion ; pas d’audit WCAG complet |
| Sécurité | Oui | Oui | Contrôles serveur | HTTP ciblé | Matrice séparée ; pas de pentest indépendant exhaustif |
| Performance | Oui | Oui | Variantes et chargements | Analyse des fichiers | WebP, polices locales WOFF, pagination ; pas de score Lighthouse annoncé |
| Mouvement | Oui | Oui | Compteur réel | HTTP + migration | Base 100 ; un événement éligible par identité, historique non recompté |

## Scénarios critiques

Le journal machine `docs/evidence/integration.json` détaille les assertions : inscription en sept étapes avec vraie photo multipart, réessai sans doublon, conservation des deux QR après édition, consultation et refus de position, coordonnées consenties chiffrées, droits propriétaire exclusifs, Mode Perdu et retour explicite, carte secondaire sans médaille, prix 1/2/3 compagnons et promotions, vérification professionnelle et révocation, interdiction inter-comptes, production jusqu’à completed. Huit processus PHP concurrents ont créé huit séries distinctes.

Les lectures de pages couvrent les routes dans les deux langues. Certaines pages de contenu non publié répondent intentionnellement 404 ; les anciennes démonstrations QR répondent 410 ; les espaces non autorisés peuvent répondre 403. La page d’erreur 500 est censée répondre 500. Ces statuts attendus ne prouvent pas que toutes les fonctionnalités d’une page ont été exercées.

## NON VÉRIFIÉ — raisons et procédure exacte

| Périmètre | Vérification restante |
|---|---|
| XAMPP / Apache Windows | Installer selon README ; exécuter migrate puis health ; tester /fr/index.html, /api/auth/me, /id/SERIE, /q/JETON et la paire historique. Vérifier 403 sur /config/local.php et /database/schema.sql. |
| 320, 375, 390, 430 px, tablette et desktop | Pour FR puis AR : accueil, tarifs, sept étapes, dossier, scan, liste de notifications, commande et CRM. Vérifier absence de débordement horizontal, cibles tactiles, lisibilité et CTA/NERO non bloquants. |
| iOS Safari / Android Chrome | Avec HTTPS réel, scanner un QR imprimé ; refuser puis autoriser la localisation ; vérifier appel sans autorisation, précision et horodatage dans le seul compte propriétaire. Vérifier également timeout et navigateur sans géolocalisation. |
| Firefox / Edge / Safari desktop | Tester connexion, multipart, NERO, export QR, clavier, dialog, retour arrière et rechargement des formulaires. Ne pas assimiler Chrome à ces navigateurs. |
| Google Maps | Configurer MAPS_KEY + MAPS_MAP_ID, enregistrer une position consentie, ouvrir son événement propriétaire et cliquer Afficher la carte. Vérifier marqueur, cercle de précision et lien de secours lorsque la clé est invalide. |
| Email / réinitialisation | Configurer PHP mail et EMAIL_FROM, activer EMAIL_ENABLED, déclencher un événement de recette puis exécuter jobs. Vérifier acceptation dans la file ET réception dans une boîte de test. Demander un lien de reset, l’utiliser une fois puis vérifier son refus et l’invalidation des anciennes sessions. |
| Impression / gravure | Comparer les deux destinations au dossier, exporter les QR, vérifier zone blanche, scanner chaque épreuve réelle à plusieurs tailles. Valider les maquettes physiques du BAT avant production ; aucune garantie d’imprimeur n’est simulée. |
| Accessibilité / motion | Naviguer uniquement au clavier ; vérifier focus visible et restitution lecteur d’écran. Activer Réduire les animations, tester zoom 200 %, contraste, RTL et lecteur d’écran mobile. |
| Performance | Mesurer Lighthouse/DevTools sur hébergement réel, réseau mobile et cache vide, en FR/AR. Vérifier LCP, CLS et INP ; aucun score chiffré n’a été inventé. |
| Publication éditoriale et commerciale | Publier les textes FR/AR validés de conditions/confidentialité. Vérifier les taux Combo et cumuls en administration avant activation ; ne pas transformer les valeurs historiques en promesse commerciale. |

## Limites connues

- Pas d’environnement XAMPP Windows ni d’exécution Apache de la livraison dans cette recette. Les tests HTTP utilisent le routeur PHP de développement.
- Email via PHP mail prêt à configurer ; push et SMS sans fournisseur implémenté/configuré. Aucun délai d’alerte externe garanti.
- Les mots de passe oubliés nécessitent un transport email opérationnel. Un test serveur prouve l’erreur honnête quand il est absent, pas une réception de message.
- Le BAT est une préparation de production, avec images, données et QR ; la découpe, la gravure et le rendu d’imprimeur demandent une validation physique.
- Les dossiers historiques sans snapshot de commande demandent une préparation manuelle documentée avant approbation. Le système n’invente pas leurs données au moment de l’achat.
- Les pièces vétérinaires historiques restent dans leurs tables ; aucun nouvel envoi de pièce jointe vétérinaire n’est proposé. Les portraits et photos de patte sont pris en charge.
- Les fiches CRM présentent au plus 100 activités et 50 commandes récentes ; les listes/API paginées et l’administration permettent de poursuivre la consultation. Les notes professionnelles propres affichées dans le dossier suivent la pagination serveur.
- Les nouveaux textes légaux et les taux Combo doivent être publiés/validés par PETICA ; aucune conclusion juridique n’est fournie ici.
- Pas de score Lighthouse, de certification WCAG, de pentest externe ni de preuve d’indexation moteur.

## Reproduire

Les tests HTTP demandent Python avec requests et Pillow, une base **jetable** vide `petica_test` et PHP disponible. Définir `PETICA_DB_*`, `PETICA_APP_URL` et `PETICA_PHP` pour l’environnement local puis exécuter `python tests/integration.py`. Le serveur de test utilise le port 8088. Les scripts fixture refusent une base qui ne finit pas par `_test`. Ne jamais diriger ces tests vers la base d’exploitation.

Pour la migration historique : créer une base vide `petica_legacy_test`, définir `PETICA_DB_NAME=petica_legacy_test`, puis exécuter successivement `php tests/legacy.php seed`, `php bin/console.php migrate` deux fois et `php tests/legacy.php verify`.

Les résultats machine livrés sont ceux de cette exécution, sans secrets ni logs bruts. Les outils binaires PHP/MariaDB isolés et le matériel de simulation du navigateur ne sont pas des dépendances de production livrées.
