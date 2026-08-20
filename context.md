# Contexte projet — Bibliothèque Numérique Scolaire (BNS)

> Mémoire persistante du projet. À maintenir à jour à chaque étape (section 13 du plan).
> Dernière mise à jour : 2026-08-20 — Étape 1/13 en cours (chore: init).

## 1. Résumé produit

Bibliothèque numérique **interne** à un établissement scolaire, déployée sur un serveur local,
accessible uniquement au sein de l'établissement (pas de dépendance Internet en production).

- **Élèves** : créent leur compte, consultent les manuels de leur niveau + les ressources "communes".
- **Enseignants** : téléversent/gèrent des manuels pour les élèves de leur(s) niveau(x).
- **Admin** : tous les privilèges (utilisateurs, catalogue, config système, audit).
- Module de statistiques complet (Chart.js) + exports CSV/PDF.

Échéance cible : 1er décembre 2026.

## 2. Décisions d'architecture (et pourquoi)

| Décision | Choix retenu | Raison |
|---|---|---|
| Framework | Laravel 11 (PHP 8.2) | Imposé par le cahier des charges |
| CSS/UI | Tailwind CSS | Le scaffold Laravel 11 par défaut inclut déjà Vite + Tailwind ; choix validé via ui-ux-pro-max-skill (design system à produire avant les écrans) |
| Auth | Sessions Laravel (pas Sanctum/API tokens) | App 100% intranet, pas d'API publique externe — cookies de session suffisent et simplifient le CSRF |
| Hash mot de passe | Argon2id | Imposé (section 9) |
| Fichiers manuels (PDF/EPUB) | `storage/app/manuels` + `storage/app/couvertures`, **jamais** dans `public/` | Exigence "hors racine web" (section 9). Le volume Docker `public_data` monté sur `nginx` ne contient QUE `public/` — `storage/app` n'est même pas accessible au niveau filesystem par nginx, défense en profondeur en plus du contrôle RBAC applicatif |
| Service de fichiers | Contrôleur Laravel qui stream le fichier après vérification RBAC (`FileService`) | Permet le contrôle d'accès par niveau/rôle sur des PDF/EPUB, impossible avec un lien statique public |
| Cache/session | `SESSION_DRIVER=database`, `CACHE_STORE=redis`, `QUEUE_CONNECTION=sync` | Redis dispo comme service Docker (optionnel dans l'esprit mais inclus) pour le cache des stats ; pas de vrai besoin de queue asynchrone (uploads traités en synchrone, volumétrie faible sur un intranet d'établissement) |
| Export PDF | `barryvdh/laravel-dompdf` | Simple, pas de dépendance binaire externe (contrairement à wkhtmltopdf), fonctionne bien en conteneur |
| Lecteur documents | PDF.js (CDN-free, assets vendorisés) + EPUB.js | Imposé (section 2) |
| Conteneurisation | 4 services : `app` (PHP-FPM 8.2 + Node pour build Vite), `nginx`, `db` (MariaDB 10.11), `redis` | Conforme section 2. Volumes nommés `public_data`/`storage_data` partagés entre `app` et `nginx` (pas de bind-mount du code source, image auto-suffisante) |

## 3. État d'avancement (section 13)

- [x] **Étape 1 — chore: init** : dépôt git initialisé dans `burundi_biblio/`, scaffold Laravel 11 (Tailwind+Vite inclus), Docker (Dockerfile multi-rôle PHP+Node, nginx, docker-compose avec app/nginx/db/redis), `context.md`. *(commit en cours)*
- [ ] Étape 2 — feat: migrations + modèles + seeders
- [ ] Étape 3 — feat: auth + rôles + RoleMiddleware (RBAC)
- [ ] Étape 4 — design: design system + composants UI (ui-ux-pro-max-skill)
- [ ] Étape 5 — feat: catalogue filtré + recherche AJAX
- [ ] Étape 6 — feat: lecteur PDF/EPUB + suivi consultation + favoris
- [ ] Étape 7 — feat: espace enseignant (upload/gestion)
- [ ] Étape 8 — feat: back-office admin (users, config, catalogue, audit)
- [ ] Étape 9 — feat: module statistiques (StatsService + Chart.js + exports)
- [ ] Étape 10 — feat: durcissement sécurité
- [ ] Étape 11 — test: tests unitaires + fonctionnels
- [ ] Étape 12 — feat: scripts sauvegarde/restauration
- [ ] Étape 13 — docs: README + guides + infrastructure + design-system + context.md final

## 4. Contraintes d'environnement observées

- Le shell d'exécution local dispose de **git 2.47, PHP 8.2.12 (XAMPP), Composer 2.8.11, Node 20.19** mais **pas de Docker/docker-compose**.
  → Les fichiers Docker sont écrits avec soin mais **non testés localement** ; `docker compose up -d --build` est à valider par l'utilisateur.
- Aucun remote git n'est configuré sur ce dépôt. Les commits se font en local à chaque étape ; le push nécessitera qu'un remote soit ajouté (à la demande de l'utilisateur).
- Le workspace contient un projet **sans rapport**, `app_fie/` (appli PHP procédurale "FIE Burundi"), à la racine de `C:\claude_workspace`. Non touché, aucun partage de code avec `burundi_biblio/`.
- Un hook `GateGuard` exige un bloc de justification avant chaque édition de fichier / commande shell sensible dans cette session — ralentit l'exécution mais n'empêche rien ; laissé actif à la demande de l'utilisateur.

## 5. Conventions retenues (ambiguïtés du cahier des charges tranchées ici)

- `validation_auto` (paramètre système, table `parametres`) : si `false` (défaut), un compte élève créé est `actif=false` jusqu'à validation admin. Si `true`, `actif=true` immédiatement à l'inscription.
- Niveaux (`niveaux`) : structure libre (ex. "6ème", "5ème", ... ou "1ère année", ...) avec un champ `ordre` pour le tri — pas de valeurs figées en dur, gérées entièrement via le back-office admin (CRUD niveaux).
- Un enseignant peut être rattaché à **plusieurs niveaux** : relation `user_niveau` (pivot), en plus du `niveau_id` simple sur `users` qui sert pour les élèves (un seul niveau/classe). Voir migrations étape 2.
- Droit "commun" pour un enseignant : accordé par l'admin via un flag sur l'utilisateur enseignant (`peut_publier_commun`), pas un droit systématique.
- Uploads : formats acceptés = PDF (`application/pdf`) et EPUB (`application/epub+zip`), validation par détection MIME réelle (`finfo`), pas seulement l'extension.

## 6. Identifiants de démo (à publier dans README à l'étape 13)

À définir à l'étape 2 (seeders) et documenté au fur et à mesure ici.
