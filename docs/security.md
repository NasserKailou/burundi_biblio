# Sécurité — Bibliothèque Numérique Scolaire (BNS)

Récapitulatif des mesures de sécurité mises en œuvre (section 9 du cahier
des charges), avec l'étape du projet où chacune a été introduite.

## Authentification et mots de passe

- **Argon2id** comme algorithme de hachage (`config/hashing.php`, étape 2) — jamais bcrypt.
- Politique de mot de passe configurable par l'admin (longueur minimale, `parametres.politique_mdp_longueur_min`), complexité imposée (majuscule + minuscule + chiffre) via `Illuminate\Validation\Rules\Password` (étapes 3 et 8).
- Anti brute-force : `RateLimiter` applicatif (5 tentatives/identifiant+IP/minute, avec compte à rebours) **et** `throttle:10,1` sur les routes de connexion/inscription (étape 3).
- Régénération de session à la connexion et à la déconnexion (`session()->regenerate()` / `invalidate()` + `regenerateToken()`), protection fixation de session.
- Un compte désactivé par l'admin est déconnecté immédiatement même si sa session était encore active (`EnsureUserIsActive`, étape 3).
- Réinitialisation de mot de passe **admin uniquement** (pas de flux email en intranet sans accès Internet) : mot de passe généré aléatoirement, affiché une seule fois via message flash, jamais journalisé en clair dans `logs_audit` (étape 8).

## Autorisation (RBAC)

- `RoleMiddleware` filtre l'accès aux **routes** ; le filtrage des **données** est systématiquement fait côté requête Eloquent (`Manuel::scopeVisiblePour($user)`, ownership sur les contrôleurs Teacher/Admin), jamais uniquement côté vue (étapes 3, 5-9).
- Vérifié en tests navigateur réels à chaque étape : 403 confirmé sur les tentatives d'accès hors périmètre (manuel hors niveau, ressource d'un collègue, route d'un autre rôle).
- Protection du dernier compte administrateur contre l'auto-suppression (étape 8).

## Injection et XSS

- Eloquent / query builder partout, aucune concaténation SQL brute avec entrée utilisateur (audité à l'étape 10 : `grep` sur `DB::raw`/`whereRaw` avec variable interpolée → aucune occurrence).
- Échappement Blade systématique (`{{ }}`) ; **aucune** occurrence de `{!! !!}` dans le projet (audité à l'étape 10).
- CSRF sur toutes les routes d'état (`web` middleware group par défaut, jamais désactivé) ; jeton exposé via balise `<meta name="csrf-token">` pour les appels Fetch API.

## En-têtes de sécurité et CSP

Définis dans `docker/nginx/conf.d/app.conf` (étape 1, durci à l'étape 10) :

- `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`.
- `Content-Security-Policy` : `script-src 'self'` **sans** `'unsafe-inline'` — les gestionnaires `onsubmit="confirm(...)"` initialement utilisés dans le back-office ont été remplacés par un attribut `data-confirm` + délégation d'événement (`resources/js/confirm-forms.js`), vérifié fonctionnel en navigateur (le message de confirmation s'affiche et bloque bien la soumission).
- `style-src` conserve `'unsafe-inline'` (classes utilitaires Tailwind générées dynamiquement) — risque XSS résiduel jugé faible comparé au script-src.
- Fichiers `.env`, `docker/`, `database/`, `storage/app/*` explicitement inaccessibles via nginx.

## Aucune dépendance Internet en production

- **Corrigé à l'étape 10** : les polices (Lexend, Source Sans 3) étaient initialement chargées via le CDN Google Fonts, ce qui viole la contrainte "aucune dépendance Internet" (section 1 — l'établissement n'a pas d'accès Internet). Remplacé par `@fontsource/lexend` et `@fontsource/source-sans-3` (polices auto-hébergées, servies comme n'importe quel autre asset Vite). Vérifié en navigateur : toutes les requêtes de polices pointent vers `/build/assets/...`, aucune requête vers `fonts.googleapis.com`/`fonts.gstatic.com`.
- PDF.js et EPUB.js sont vendorisés via npm/Vite depuis l'étape 6 (jamais de CDN).

## Uploads de fichiers

- Détection du type réel par contenu (`FileService::detecterType()`, `finfo`/`getMimeType()` sur le fichier réellement reçu) — un fichier renommé `.pdf` mais dont le contenu n'est pas un vrai PDF est rejeté (422), vérifié en test navigateur à l'étape 7.
- Noms de fichiers assainis : toujours un UUID généré côté serveur, jamais le nom fourni par le client (protection contre l'injection de chemin).
- Fichiers stockés hors racine web (`storage/app/manuels`, `storage/app/couvertures`) ; le volume Docker partagé avec nginx ne contient que `public/` — `storage/app` n'est même pas accessible au niveau filesystem par nginx (défense en profondeur, étape 1).
- Taille maximale configurable par l'admin (`parametres.taille_max_fichier_mo`), appliquée côté validation Laravel **et** côté `php.ini` (`docker/php/uploads.ini`, étape 7).

## Données d'élèves mineurs

- Minimisation : `email` nullable pour les élèves (l'identifiant/mot de passe suffit à l'authentification intranet).
- Durée de conservation configurable (`parametres.duree_conservation_consultations_jours`, défaut 730 jours) et **appliquée** par la commande `php artisan bns:purger-consultations` (étape 10, à planifier en tâche cron à l'étape 12) — purge uniquement l'historique de lecture détaillé, jamais les comptes ni le catalogue.
- Suppression de compte par l'admin : les `consultations` et `favoris` de l'utilisateur sont supprimés en cascade (contraintes `cascadeOnDelete()` définies dans les migrations), aucune donnée résiduelle orpheline.

## Journalisation (audit)

- `AuditService` journalise toutes les actions sensibles (connexion/déconnexion, upload/modification/suppression de manuel, gestion des utilisateurs, changements de configuration, purge de données) dans `logs_audit` — jamais de données sensibles (mots de passe, contenu de fichiers) dans le journal.
- Journalisation *best-effort* : une erreur d'écriture du journal ne bloque jamais l'action métier principale.
- Consultable et filtrable par l'admin (`/admin/audit`, étape 8).

## Limite connue (non un défaut de code)

`SESSION_SECURE_COOKIE=false` est **intentionnel** : l'application est déployée sur un intranet d'établissement sans TLS (section 1 — réseau local, pas de certificat HTTPS prévu). Un cookie "secure" ne serait jamais envoyé sur une connexion HTTP et casserait les sessions. `http_only` et `same_site=lax` restent actifs (défauts Laravel, non modifiés). Si l'établissement met en place HTTPS en interne, `SESSION_SECURE_COOKIE=true` doit être activé.
