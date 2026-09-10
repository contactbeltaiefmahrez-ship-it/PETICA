# Routes et API

Toutes les routes sont préfixées par APP_BASE (`/petica` en installation locale). Les POST/PATCH exigent le jeton CSRF, y compris connexion et QR public. Aucune mutation par GET.

| Route | Méthode | Rôle | Source | Langue | Auth | Résultat | Erreurs | Statut |
|---|---|---|---|---|---|---|---|---|
| /{fr / ar}/404.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/500.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/a-propos.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/accessoires.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/admin/client.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Détail testé dans parcours ou contrôle manuel restant |
| /{fr / ar}/admin/clients.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/admin/codes-promo.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/admin/commandes.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/admin/compagnon.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Détail testé dans parcours ou contrôle manuel restant |
| /{fr / ar}/admin/compagnons.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/admin/contenu.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/admin/index.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/admin/professionnels.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/admin/qr-identites.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/animal-perdu.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/blog/article.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/blog/conseils-fugue.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/blog/histoire-noisette.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/blog/identite-ethique.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/blog/index.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/comment-ca-marche.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/compte-type.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/conditions.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/confidentialite.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/connexion.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/contact.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/crm/client.html | GET ; POST formulaires si présents | Admin | CRM | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Détail testé dans parcours ou contrôle manuel restant |
| /{fr / ar}/crm/clients.html | GET ; POST formulaires si présents | Admin | CRM | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/crm/index.html | GET ; POST formulaires si présents | Admin | CRM | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/crm/interactions.html | GET ; POST formulaires si présents | Admin | CRM | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/crm/prospects.html | GET ; POST formulaires si présents | Admin | CRM | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/crm/suivis.html | GET ; POST formulaires si présents | Admin | CRM | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-client/ajouter-compagnon.html | GET ; POST formulaires si présents | Client / droits dossier | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Détail testé dans parcours ou contrôle manuel restant |
| /{fr / ar}/espace-client/commandes.html | GET ; POST formulaires si présents | Client / droits dossier | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-client/compagnon.html | GET ; POST formulaires si présents | Client / droits dossier | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Détail testé dans parcours ou contrôle manuel restant |
| /{fr / ar}/espace-client/index.html | GET ; POST formulaires si présents | Client / droits dossier | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-client/mes-compagnons.html | GET ; POST formulaires si présents | Client / droits dossier | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-client/notifications.html | GET ; POST formulaires si présents | Client / droits dossier | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-client/profil.html | GET ; POST formulaires si présents | Client / droits dossier | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-pro/ajouter-compagnon.html | GET ; POST formulaires si présents | Professionnel | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Détail testé dans parcours ou contrôle manuel restant |
| /{fr / ar}/espace-pro/client.html | GET ; POST formulaires si présents | Professionnel | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Détail testé dans parcours ou contrôle manuel restant |
| /{fr / ar}/espace-pro/clients.html | GET ; POST formulaires si présents | Professionnel | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-pro/commandes.html | GET ; POST formulaires si présents | Professionnel | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-pro/compagnon.html | GET ; POST formulaires si présents | Professionnel | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Détail testé dans parcours ou contrôle manuel restant |
| /{fr / ar}/espace-pro/index.html | GET ; POST formulaires si présents | Professionnel | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-pro/profil.html | GET ; POST formulaires si présents | Professionnel | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/faq.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/futur.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/id/dog-000000001.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/index.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/inscription/index.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/inscription/pro.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/mot-de-passe-oublie.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/mouvement.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/offline.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/produits/carte-identite.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/produits/qr-tag.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/q/chat-present.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/q/chien-perdu.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/q/chien-present.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/tarifs.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/inscription/client.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-client/compagnons.html | GET ; POST formulaires si présents | Client / droits dossier | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-client/recuperation.html | GET ; POST formulaires si présents | Client / droits dossier | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-client/scan.html | GET ; POST formulaires si présents | Client / droits dossier | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Détail testé dans parcours ou contrôle manuel restant |
| /{fr / ar}/espace-client/relations.html | GET ; POST formulaires si présents | Client / droits dossier | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/espace-client/commande.html | GET ; POST formulaires si présents | Client / droits dossier | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Détail testé dans parcours ou contrôle manuel restant |
| /{fr / ar}/espace-pro/commande.html | GET ; POST formulaires si présents | Professionnel | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Détail testé dans parcours ou contrôle manuel restant |
| /{fr / ar}/admin/commande.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Détail testé dans parcours ou contrôle manuel restant |
| /{fr / ar}/admin/parametres.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/admin/health.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/admin/messages.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/admin/scans.html | GET ; POST formulaires si présents | Admin | Dossiers / commandes / paramètres SQL | FR + AR | Oui | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/world.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/billion-movement.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/auth/reset-password.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/auth/mot-de-passe-oublie.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |
| /{fr / ar}/auth/connexion.html | GET ; POST formulaires si présents | Public / compte selon étape | Contenu / catalogue / connaissances / SQL selon page | FR + AR | Selon action | Page, redirection ou état vide réel | 403 / 404 / 410 / 422 / 500 / 503 selon contexte | Rendu HTTP exécuté |

## Contrats API

Les familles ci-dessous utilisent le répartiteur commun. Erreurs usuelles : 400 JSON/query, 401 session, 403 droits/CSRF, 404 absent, 409 version/devis/idempotence, 410 capacité expirée, 413 volume, 422 saisie, 429 fréquence, 503 configuration/service. Réponses localisées par `lang=fr|ar`.

| Route API | Méthode | Autorisation | Données / résultat |
|---|---|---|---|
| /auth/me | GET | Public | Utilisateur courant, profil et CSRF |
| /auth/login ; /auth/logout | POST | Public / connecté | Session et révocation locale |
| /auth/register ; /auth/register-professional | POST | Public | Compte ; pro en attente |
| /auth/forgot-password ; /auth/reset-password | POST | Public | Lien à usage unique si email configuré |
| /profile | GET, POST, PATCH | Connecté | Profil propriétaire |
| /registration/draft | GET, POST, PATCH | Connecté | Brouillon de compte |
| /uploads | POST multipart | Connecté | Média JPEG normalisé |
| /products ; /movement | GET | Public | Catalogue et compteur SQL |
| /nero/ask ; /contact | POST | Public | Réponse PETICA / demande persistée |
| /orders/preview | POST | Public ou connecté | Devis serveur |
| /companions | GET, POST | Client | Liste propre / création |
| /companions/{id} | GET, POST, PATCH | Droits dossier | Lecture ou édition versionnée |
| /companions/{id}/status | POST | Primaire ou admin | État audité |
| /companions/{id}/owners | GET, POST | Droits dossier / primaire écriture | Noms secondaires |
| /companions/{id}/owners/{invite}/confirm | POST | Primaire ou admin | Confirmation composite id + invite |
| /companions/{id}/vet | GET, POST | Droits médicaux ; vétérinaire en écriture | Historique paginé / acte |
| /companions/{id}/scan-events ; /scan-events/{event} | GET | Primaire exclusivement | Événements privés / position consentie |
| /qr/scans | POST | Session publique + nonce | Événement de consultation |
| /qr/scans/{event}/location | POST, PATCH | Capacité temporaire + CSRF | État géolocalisation et chiffrement |
| /notifications ; /notifications/{id}/read | GET ; POST | Utilisateur destinataire | Liste / lecture |
| /relationships ; /relationships/{id} | GET ; POST | Client destinataire | Liste / accord ou révocation |
| /orders ; /orders/{id} | GET ; GET | Propriétaire, émetteur, admin | Commandes autorisées |
| /orders | POST | Propriétaire ou pro lié | Confirmation unique du devis |
| /orders/{id}/status | POST | Admin | Transition production |
| /professionals/me | GET, POST, PATCH | Professionnel | Profil et nouvelle vérification |
| /professionals | GET | Admin | Liste des profils |
| /professionals/{id}/verification | POST | Admin | Décision de vérification |
| /professionals/clients | GET, POST | Pro vérifié | Relations et demande au client |
| /professionals/clients/{user_id} | GET | Pro vérifié + relation acceptée | Coordonnées minimales et compagnons |
| /professionals/clients/{user_id}/companions | POST | Pro vérifié + relation acceptée | Création pour bénéficiaire |
| /professionals/search-serial/{serial} | GET | Pro vérifié + droit dossier | Dossier autorisé |
| /professionals/recent-companions | GET | Pro vérifié | Dernière session déterministe |
| /professionals/companions/{id}/session-notes | GET, POST | Pro vérifié + relation | Notes propres et pagination |
| /admin/overview ; /admin/users ; /admin/companions ; /admin/orders | GET | Admin | Données et statistiques SQL |
| /admin/users/{id} | GET, POST, PATCH | Admin | Détail et profil client |
| /admin/users/{id}/status | POST | Admin | Suspension et invalidation |
| /admin/products ; /admin/products/{id} | GET ; POST/PATCH | Admin | Catalogue et prix versionnés |
| /admin/promo ; /admin/promo/{id}/active | GET, POST ; POST | Admin | Codes et activation |
| /admin/settings | GET, POST, PATCH | Admin | Règles commerciales versionnées |
| /admin/content ; /admin/content/{id}/restore | GET, POST ; POST | Admin | Versions FR/AR et republication |
| /admin/contact-requests ; /admin/health | GET | Admin | Demandes / diagnostic sans secrets |
| /crm/contacts ; /crm/customers ; /crm/leads | GET, POST | Admin | Contacts ou prospects |
| /crm/contacts/{id} ; /crm/customers/{id} | GET | Admin | Fiche contact CRM |
| /crm/contacts/{id}/convert | POST | Admin | Lien vers compte existant |
| /crm/contacts/{id}/activity | GET, POST | Admin | Journal paginé |
| /crm/customers/{id}/{notes / interactions / followups} | GET, POST | Admin | Compatibilité journal CRM |
| /crm/followups ; /crm/interactions ; /crm/segments | GET | Admin | Listes et agrégats |
| /crm/followups/{id}/status ; /crm/customers/{id}/status | POST | Admin | Suivi ou statut contact |

## QR et compatibilité

| Route | Contrôle | Résultat |
|---|---|---|
| /id/{jeton_identité} | Jeton identity exact | Profil public filtré |
| /id/{série} | Série ou alias prouvé | Même profil public |
| /q/{jeton_médaille} | Jeton tag exact | Récupération et signalement |
| /user/PETICAQR/index.php?id={id}&petica={série} | Paire id/série concordante ou alias prouvé | Récupération historique |
| /api/.../*.php | Adaptateurs physiques conservés | Même répartiteur, droits serveur et CSRF |
| /{fr / ar}/q/*.html ; /{fr / ar}/id/dog-000000001.html | Anciennes démonstrations | 410, pas de faux dossier |

Les anciennes routes CRM sont conservées comme alias de la nouvelle identité de contact CRM. Les intégrations tierces utilisant auparavant un id utilisateur dans `/crm/customers/{id}` doivent résoudre le contact via son `user_id` avant l’appel : cette différence est à migrer explicitement. Les anciennes tables et leur historique restent conservés.
