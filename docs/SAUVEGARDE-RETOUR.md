# Sauvegarde et retour arrière

`PETICA-baseline-phase3.zip` conserve les 287 fichiers de la copie source extraite avant modification. Chaque fichier a été comparé par empreinte avec cette copie. Ce ZIP n’est pas une sauvegarde d’une base en exploitation : aucune base externe n’a été fournie ou consultée. Un ancien visuel `world-market.jpg` de la copie reçue était tronqué ; il a été retiré des ressources actives au profit des nouveaux visuels approuvés.

## Avant mise à jour

1. Suspendre les écritures le temps d’une copie cohérente.
2. Exporter la base avec phpMyAdmin ou `mysqldump --single-transaction --routines --triggers -u UTILISATEUR -p petica > petica-avant.sql`. Saisir le mot de passe à l’invite.
3. Copier code, `storage/uploads`, pièces privées, `config/local.php` et `APP_KEY` hors de la racine web, dans un emplacement protégé.
4. Relever quelques séries et leurs deux jetons, les nombres de dossiers, commandes et événements, dans un journal privé.
5. Sur une copie isolée, exécuter deux fois `php bin/console.php migrate`. La seconde exécution ne doit rien réappliquer.

## Bascule

Conserver les URL déjà imprimées. Installer le nouveau code avec ses `.htaccess`, configurer la base et lancer `health`. Vérifier connexion, QR identité, QR médaille, paire historique id/série, édition d’un dossier et commande de recette. Réouvrir ensuite les écritures.

## Retour arrière

Les DDL MySQL ne sont pas annulables par un simple `ROLLBACK`. Aucun downgrade destructif automatique n’est fourni.

- Avant réouverture des écritures : arrêter Apache, restaurer ensemble code précédent, SQL, médias et configuration avec sa clé, puis vérifier comptes et QR.
- Après de nouvelles écritures : une restauration brute les perdrait. Sauvegarder d’abord la base courante et les nouveaux médias, relever les nouvelles identités, jetons et commandes, puis préparer une reprise contrôlée. Ne jamais remettre les compteurs en arrière ni réutiliser une série.
- Conserver les anciennes tables CRM. Les migrations copient leur historique avec une référence d’origine unique.

Le développement utilise la branche `implementation/phase-3`. Le bundle Git livré conserve le commit de référence et les modifications. Aucun dépôt GitHub externe n’a été créé ni modifié.
