# Contexte projet — Bibliothèque Numérique Scolaire (BNS)

> Mémoire persistante du projet. À maintenir à jour à chaque étape (section 13 du plan).
> Dernière mise à jour : 2026-08-22 (session 4, en cours) — Refonte professionnelle AdminLTE 3 + charte Burundi. Voir section "Session 4" en fin de fichier.

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

- [x] **Étape 1 — chore: init** : dépôt git initialisé dans `burundi_biblio/`, scaffold Laravel 11 (Tailwind+Vite inclus), Docker (Dockerfile multi-rôle PHP+Node, nginx, docker-compose avec app/nginx/db/redis), `context.md`.
- [x] **Étape 2 — feat: migrations + modèles + seeders** : 11 migrations métier (roles, niveaux, matieres, users, user_niveau, manuels, manuel_niveau, consultations, favoris, logs_audit, parametres) + password_reset_tokens supprimée (reset MDP admin-only, pas de flow email) ; 9 modèles Eloquent avec relations + scopes RBAC (`Manuel::scopeVisiblePour($user)` filtre côté requête) ; config/hashing.php (Argon2id) ; 7 seeders (Role/Niveau/Matiere/Parametre/User/Manuel/Consultation) + script `database/seeders/support/generate_demo_assets.php` qui génère les fixtures de démo (10 PDF via Dompdf, 4 EPUB via ZipArchive, 10 couvertures JPG via GD) commitées dans `database/seeders/assets/`. **Validé en local** : migrations + seeders exécutés avec succès sur SQLite éphémère (`/tmp/bns_test.sqlite`, jamais commité) — 16 users, 11 manuels (10 publiés + 1 brouillon), 68 consultations, 20 favoris, scope RBAC vérifié (eleve1 en 6ème voit 5 manuels = 2 spécifiques + 3 communs).
- [x] **Étape 3 — feat: auth + rôles + RoleMiddleware (RBAC)** : `AuthController` (login par `identifiant` avec `Auth::attempt`, throttling `RateLimiter` 5 tentatives/min en plus du `throttle:10,1` sur les routes, inscription élève avec compte `actif=false` par défaut sauf `validation_auto`, logout, régénération de session) ; `RoleMiddleware` (`role:admin|enseignant|eleve`, aborts 403) et `EnsureUserIsActive` (deconnecte immediatement un compte desactive meme si sa session est encore ouverte, appliquee globalement au groupe `web`) ; `AuditService` (log best-effort dans `logs_audit` pour connexion/deconnexion/inscription, sans jamais faire echouer l'action principale) ; dashboards fonctionnels par role (eleve/enseignant/admin) avec layout Tailwind de base. **Validé en local dans un navigateur réel** (php artisan serve + vite dev, SQLite éphémère) : login/logout eleve+enseignant+admin OK, 403 sur route admin pour un eleve, compte inactif rejeté avec message, inscription cree bien un compte inactif. Incident de debug notable : deux processus `php artisan serve` etaient restes lies au meme port sur Windows (l'un avec `SESSION_DRIVER=array`) et se partageaient le trafic de facon non deterministe, causant des `419 Page Expired` intermittents - resolu en tuant les processus par PID Windows (`taskkill`) plutot que `pkill` (qui ne matche pas les PID natifs Windows depuis Git Bash).
- [x] **Étape 4 — design: design system + composants UI (ui-ux-pro-max-skill)** : `docs/design-system.md` produit via des recherches ciblees du skill `ui-ux-pro-max` (domaines `product`/`color`/`typography`/`ux`/`chart` + stack `laravel`) — les 2 premieres requetes `--design-system` auto-agregees etaient hors-sujet (Claymorphism enfantin, puis dashboard NOC sombre), rejetees et documentees comme telles au lieu d'etre appliquees telles quelles (protocole de repli du skill). Palette teal/ambre (base "LMS", corrigee pour le contraste AA) + typographie Lexend/Source Sans 3 ("Corporate Trust", orientee accessibilite) appliquees via variables CSS (`resources/css/app.css`) + tokens Tailwind (`tailwind.config.js`, classes `bg-bns-*`/`text-bns-*`). 8 composants Blade anonymes crees (`x-button`, `x-input`, `x-select`, `x-card`, `x-alert`, `x-badge`, `x-stat-card`, `x-sidebar-link`) + `layouts/admin.blade.php` (shell sidebar pour le back-office, etendu au fil des etapes suivantes) ; `x-book-cover` differe a l'etape 5 (couplé aux données catalogue). Toutes les vues des etapes 1-3 refactorisees pour utiliser ces composants/tokens. `resources/js/bns-colors.js` : palette exposee en JS pour Chart.js (etape 9). **Verifie en navigateur reel** (build de prod `npm run build`, `getComputedStyle` sur body/h1/bouton) : polices et couleurs correctement appliquees, flux de connexion admin fonctionnel avec le nouveau layout sidebar. Incident : `public/hot` (fichier laisse par un `npm run dev` precedent) forçait Vite a charger les assets depuis un serveur de dev arrete → page sans CSS ; resolu en supprimant ce fichier (non commite, deja dans .gitignore).
- [x] **Étape 5 — feat: catalogue filtré + recherche AJAX** : `CatalogueController` (index, show, couverture — filtrage RBAC via `Manuel::scopeVisiblePour`, jamais uniquement cote vue) ; `Api\ManuelController` (JSON, `GET /api/manuels?matiere=&q=` avec pagination, `GET /api/manuels/{id}`) ; `FileService::streamCouverture()` (diffuse les JPEG depuis le disque prive `couvertures`, jamais expose directement par nginx) ; recherche/filtre en Fetch API vanilla (`resources/js/catalogue.js`, debounce 300ms, `AbortController` pour annuler les requetes obsoletes) ; composant `x-book-cover`. Le bouton "Lire" et les favoris sont volontairement absents de la fiche manuel a ce stade — ajoutes a l'etape 6 avec le lecteur, pour eviter tout lien mort entre deux commits. **Verifie en navigateur reel** : grille correctement filtree par niveau+commun (5 manuels pour eleve1 en 6eme, coherent avec l'etape 2), recherche AJAX fonctionnelle, fiche manuel avec streaming de couverture (200, image/jpeg), 403 confirme sur un manuel hors niveau (id 3, 5eme) et sur le manuel brouillon (id 11).
- [x] **Étape 6 — feat: lecteur PDF/EPUB + suivi consultation + favoris** : `pdfjs-dist@6.2.108` et `epubjs@0.4.2` vendorisés via npm/Vite (aucune dependance CDN, requis par la contrainte "aucune dependance Internet" en production). `ReaderController` (RBAC via `visiblePour`, streaming du fichier via `FileService::streamManuel`) ; API `POST/PATCH /api/consultations` et `POST /api/favoris` + `DELETE /api/favoris/{manuel}` (ownership verifiee - 403 si la consultation n'appartient pas a l'utilisateur). Toolbar lecteur accessible (clavier fleches, focus visible, aria-*) avec pages/zoom/plein ecran/signets/favoris. Signets implementes en `localStorage` (pas de table dediee dans le schema impose par la section 4 - convention documentee). Dashboard eleve enrichi de "Reprendre la lecture" et "Mes favoris".
  **6 bugs reels trouves et corriges en testant dans un navigateur reel** (et non de simples suppositions) :
  1. `pdfjs-dist` v6 a retire le raccourci `getDocument(urlString)` → `getDocument({url})` requis.
  2. `Storage::disk()->response()` (StreamedResponse) ignore les en-tetes `Range`, renvoie systematiquement 200 avec le fichier entier → remplace par `response()->file()` (BinaryFileResponse) qui gere correctement les requetes HTTP Range (206), necessaire pour PDF.js.
  3. `epubjs` 0.4.x : `ePub(url)` est redevenu une factory ASYNCHRONE (retourne une Promise) quand on lui passe une chaine - notre code ne l'attendait pas (`this.book.renderTo is not a function`).
  4. `epubjs` determine le type d'ouverture (archive vs repertoire eclate) via l'**extension** du chemin de l'URL : sans extension, il tente de parcourir l'URL comme un repertoire et echoue en 404 → route `reader.fichier` modifiee pour inclure un segment `{nom}` decoratif se terminant par `.pdf`/`.epub`.
  5. Le systeme d'enregistrement des ViewManagers d'epubjs (`ePub.register.manager('default', ...)`, lookup par chaine) ne survit pas au tree-shaking de Rollup (`this.ViewManager is not a constructor`) → contourne en import direct de `DefaultViewManager` et passage explicite via l'option `manager`.
  6. `epubjs` 0.4.x : `book.generateLocations()` resout vers un simple tableau de CFI (pas un objet `Locations` riche avec `locationFromCfi`/`cfiFromLocation` comme en 0.3.x) → conversion CFI/entier reimplementee manuellement via `ePub.CFI.compare`.
  **Durcissement supplementaire** : `overrides.xmldom` dans `package.json` (force `@xmldom/xmldom@^0.9.0`, la dependance XML d'epubjs etait vulnerable - CVE critique, `npm audit` etait passe de 0 a 3 vulnerabilites hautes lors de l'installation initiale) ; Node 22 installe dans `docker/Dockerfile` via NodeSource (`pdfjs-dist@6.2.108` exige `>=22.13`) ; garde-fous de timeout (15s rendu PDF, 8s generation des locations EPUB) pour echouer proprement plutot que de bloquer indefiniment si le rendu est retarde (onglet en arriere-plan, gros fichier).
  **Limite de verification honnete** : le rendu PIXEL final (canvas PDF.js, iframe EPUB.js) n'a pas pu etre confirme visuellement dans cette session - le panneau navigateur de l'outil de test s'est revele etre en etat `document.hidden === true` (confirme via `javascript_tool`, et le message d'erreur explicite de l'outil de capture d'ecran : "the Browser pane is not displayed, so the page is not compositing frames"). Le rendu canvas/iframe depend du pipeline de compositing actif du navigateur, qui est legitimement suspendu quand `document.hidden` est vrai (comportement standard de la Page Visibility API) - ni le contenu PDF.js ni EPUB.js ne peuvent donc terminer leur rendu dans cet environnement de test precis, independamment de la validite du code. **Tout le reste a ete verifie avec succes en conditions reelles** : navigation/RBAC (403 sur manuel hors niveau), les 4 endpoints API (consultation create/update avec verification d'ownership 403, favori add/remove) avec persistance verifiee en base, dashboard "Reprendre la lecture"/"Mes favoris" peuplés de vraies données, bouton favori sur la fiche manuel. **Recommandation** : l'utilisateur devrait verifier une fois manuellement l'affichage pixel du lecteur dans un navigateur reel (`docker compose up` puis ouvrir `/lecteur/{id}` sur un manuel PDF et EPUB) avant mise en production - c'est la seule zone du produit non confirmee visuellement a ce stade.
- [x] **Étape 7 — feat: espace enseignant (upload/gestion)** : `Teacher\ManuelController` (index/create/store/edit/update/destroy), CRUD strictement limite a `uploader_id = user.id` (403 sinon) — convention tranchee : "CRUD sur ses manuels" (section 5) = ses propres uploads uniquement ; "et ceux de son niveau" = droit de consultation deja couvert par `visiblePour()`, pas un droit d'edition sur le contenu d'un collegue. `FileService` etendu : `detecterType()` (MIME reel via finfo, rejette un fichier dont le contenu ne correspond pas a PDF/EPUB meme si l'extension trompe), `storerManuel`/`storerCouverture` (noms UUID, jamais le nom original), `supprimerFichierManuel`/`supprimerCouverture` (granulaires, pour ne pas supprimer la couverture lors d'un remplacement du seul fichier et vice-versa). Niveaux cibles limites a `idsNiveauxGeres()` (`Rule::in`), option "commun" verifiee cote serveur (`peut_publier_commun`) meme si le champ est manipule cote client. Taille max lue depuis `Parametre::get('taille_max_fichier_mo')`. `docker/php/uploads.ini` (upload_max_filesize 150M / post_max_size 155M) ajoute au Dockerfile. Actions sensibles journalisees via `AuditService` (upload/modification/suppression).
  **Verifie en navigateur reel** (upload multipart via `FormData`/`fetch`, formulaire natif pour la modification) : creation avec fichier PDF + couverture PNG valides → 100% fonctionnel, apparait dans la liste avec le bon comptage de consultations ; rejet 422 d'un fichier au contenu invalide malgre une extension `.pdf` trompeuse ; rejet 422 d'un niveau non autorise ; modification (conservation des fichiers si non re-televerses) ; suppression (fichiers effaces du disque, verifie) ; 403 confirme quand enseignant1 tente d'editer un manuel d'enseignant2 ; 4 entrees `logs_audit` correctement enregistrees avec le bon `user_id`.
- [x] **Étape 8 — feat: back-office admin (users, config, catalogue, audit)** : `Admin\{UserController,NiveauController,MatiereController,ManuelController,ConfigurationController,AuditController}`. Utilisateurs : CRUD complet, import CSV (`nom,prenom,identifiant,niveau,classe`), validation/desactivation, reset MDP (genere et affiche une fois, jamais stocke en clair), protection du dernier admin (422 si suppression). Niveaux/Matieres : CRUD avec suppression bloquee si utilises (422). Catalogue admin : CRUD sur TOUS les manuels (pas de restriction d'ownership, contrairement a l'espace enseignant), reutilise `FileService`. Configuration : formulaire sur `parametres` (nom etablissement, taille max, formats, politique MDP, `validation_auto`). Audit : liste paginee filtrable de `logs_audit`. Toutes les actions journalisees.
  **Verifie en navigateur reel, integration bout en bout** : validation d'inscription (eleve4/12), reset MDP (mot de passe genere retourne correctement), creation/suppression utilisateur, protection dernier admin (422 confirme), CRUD niveaux avec blocage si utilise (422 confirme), admin editant un manuel d'un AUTRE enseignant (contrairement a la restriction d'ownership de l'espace enseignant — confirme fonctionnel), **test d'integration cross-phase** : bascule de `validation_auto` a `true` via la config admin → nouvelle inscription immediatement active et connectee (verifie le lien reel entre l'etape 8 et l'`AuthController` de l'etape 3, pas seulement des vues isolees), page audit affichant fidelement la trace complete de toutes ces actions de test.
- [x] **Étape 9 — feat: module statistiques (StatsService + Chart.js + exports)** : `StatsService` (overview, manuels/eleves/enseignants les plus actifs, consultations par periode, repartition matiere/niveau) — portee via `?array $niveauIds` (null = global/admin, tableau = restreint/enseignant). Regroupement temporel fait cote PHP (Carbon), pas en SQL brut (`strftime`/`DATE_FORMAT`), pour rester agnostique SQLite (dev/tests) vs MySQL (prod). `chart.js@4.5.1` vendorise (0 vuln.) ; composant `x-chart` avec **tableau de donnees accessible toujours present** (`sr-only`) en repli, conforme a la regle du design system "la couleur n'est jamais le seul vecteur d'information". `Admin\StatsController` (vue + export CSV/PDF via `barryvdh/laravel-dompdf`) et `Teacher\StatsController` (scope force a `idsNiveauxGeres()`, jamais global).
  **Bug trouve et corrige avant commit** : `export()` etait type `Illuminate\Http\Response` mais `streamDownload()` retourne un `StreamedResponse` (TypeError immediat) — corrige en `Symfony\Component\HttpFoundation\Response` (type commun aux deux retours possibles CSV/PDF). **Verifie en navigateur reel** : KPIs et tous les tableaux de donnees accessibles corrects (verifie chiffre par chiffre), export CSV et PDF fonctionnels (PDF valide, 4.4 Ko), scope enseignant confirme restreint a ses niveaux (4 manuels vs 11 en global, y compris un manuel d'un collegue partageant le meme niveau — corrige le libelle "Mes manuels" trompeur en "Manuels de mon niveau" suite a cette observation), 403 confirme sur `/admin/statistiques` pour un enseignant.
- [x] **Étape 10 — feat: durcissement sécurité** : audit (aucun `{!! !!}`, aucun SQL brut avec variable interpolée, CSRF jamais désactivé, `http_only`/`same_site=lax` par défaut) + corrections ciblées. **2 vrais problèmes trouvés et corrigés** : (1) `resources/css/app.css` chargeait les polices via le CDN Google Fonts — violation de la contrainte "aucune dépendance Internet" (section 1) — remplacé par `@fontsource/lexend`+`@fontsource/source-sans-3` auto-hébergées, vérifié en navigateur (0 requête externe) ; (2) CSP `script-src` gardait `'unsafe-inline'` à cause de 6 attributs `onsubmit="confirm(...)"` dans le back-office — remplacés par `data-confirm` + délégation d'événement (`resources/js/confirm-forms.js`), CSP durci, comportement revérifié fonctionnel. Ajouts : `php artisan bns:purger-consultations` (minimisation des données d'élèves mineurs, respecte `parametres.duree_conservation_consultations_jours`, teste avec succès : 59/72 lignes purgées avec une fenêtre de 10 jours, audit loggé), throttling sur le groupe de routes `/api` (120/min). `docs/security.md` : récapitulatif complet avec justification explicite de `SESSION_SECURE_COOKIE=false` (intranet sans TLS, pas un oubli).
- [x] **Étape 11 — test: tests unitaires + fonctionnels** : `phpunit.xml` bascule sur SQLite `:memory:` pour les tests (etait commente). Factories ajoutees (Matiere/Niveau/Manuel/Consultation) + `HasFactory` sur les modeles correspondants. 29 tests / 73 assertions, tous verts (`php artisan test`) : 2 fichiers Unit (StatsService : overview/tri/portee par niveau ; regles de permission `Manuel::scopeVisiblePour` pour les 3 roles) + 5 fichiers Feature (authentification : connexion, compte inactif rejete, inscription avec/sans validation_auto ; RBAC : 403 croise entre les 3 roles + redirection invite ; catalogue : filtrage API, 403 hors niveau, 403 brouillon, recherche AJAX ; upload enseignant : succes, rejet MIME reel invalide, niveau non autorise rejete, 403 sur le manuel d'un collegue ; gestion admin : validation inscription, protection dernier admin, reset MDP change le hash).
  **Bug trouve et corrige** : `bcrypt()` utilise dans les tests pour pre-hasher un mot de passe provoquait une `RuntimeException` ("Could not verify the hashed value's configuration") car le driver de hachage de l'app est Argon2id, pas bcrypt — corrige en `Hash::make()` (respecte le driver configure), qui est de toute façon la bonne pratique.
- [x] **Étape 12 — feat: scripts sauvegarde/restauration** : `scripts/sauvegarde.sh` (dump `mysqldump` + archive `tar.gz` de `storage/app/{manuels,couvertures}`, purge auto >30j) et `scripts/restauration.sh` (confirmation explicite requise). Nouveau service `scheduler` dans `docker-compose.yml` (boucle `php artisan schedule:run` toutes les 60s — pas de cron systeme dans l'image). Planifie dans `routes/console.php` : sauvegarde quotidienne 02h00, purge hebdomadaire des consultations (`bns:purger-consultations`, etape 10). `docs/sauvegarde.md` : procedure complete. **Verifie** : syntaxe shell valide (`sh -n`), `php artisan schedule:list` confirme les 2 taches enregistrees avec les bonnes expressions cron. **Non teste** : execution reelle de `mysqldump`/`mysql` (indisponibles dans cet environnement de dev sans Docker) — limite documentee explicitement dans docs/sauvegarde.md, verification recommandee a l'utilisateur lors du premier deploiement.
- [x] **Étape 13 — docs: README + guides + infrastructure + design-system + context.md final** : `README.md` reecrit entierement (demarrage Docker, comptes de demo, table des matieres vers `docs/`, limites connues assumees explicitement) ; `docs/guide-eleve.md`, `docs/guide-enseignant.md`, `docs/guide-admin.md` (un par role, couvrant chaque ecran construit) ; `docs/infrastructure.md` (section 10 : serveur, reseau, budget indicatif ~12 900 €, avec rappel explicite qu'un relevé de site reel est necessaire). `docs/design-system.md` (etape 4) et `docs/security.md` (etape 10) deja a jour, references depuis le README.

## Bilan final (13/13 etapes)

Projet complet : 3 roles fonctionnels avec RBAC filtre cote requete a chaque niveau, catalogue + lecteur PDF/EPUB (bugs reels trouves et corriges en test navigateur), espace enseignant, back-office admin complet, module statistiques avec exports, durcissement securite (2 failles reelles trouvees et corrigees : CDN Google Fonts + CSP unsafe-inline), 29 tests verts, scripts de sauvegarde planifies. 13 commits, un par etape du plan section 13.

**Deux limites honnetes, assumees et documentees** (jamais dissimulees) :
1. Rendu pixel du lecteur PDF.js/EPUB.js non confirme visuellement (panneau de test en `document.hidden=true` dans cet environnement — toute la logique sous-jacente est verifiee).
2. Scripts `mysqldump`/`mysql` non executes en conditions reelles (outils absents de l'environnement de developpement sans Docker).

Aucun TODO, aucune fonction vide, aucun placeholder dans le code livre.

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

Mot de passe commun par rôle (démo uniquement — à changer en production) :

| Rôle | Identifiant(s) | Mot de passe |
|---|---|---|
| Admin | `admin` | `Admin@2026!` |
| Enseignant | `enseignant1`, `enseignant2`, `enseignant3` | `Enseignant@2026!` |
| Élève | `eleve1` à `eleve12` | `Eleve@2026!` |

- `enseignant1` (niveau principal 6ème + niveaux additionnels 5ème/2nde, droit "commun" accordé)
- `enseignant2` (niveau principal 5ème + niveau additionnel 4ème, droit "commun" accordé)
- `enseignant3` (niveau principal 3ème + niveau additionnel Terminale, droit "commun" accordé)
- `eleve4` et `eleve12` sont volontairement `actif=false` pour démontrer le workflow de validation d'inscription admin.
- 10 manuels publiés (dont 3 "communs") + 1 manuel en `brouillon` (invisible aux élèves) pour démontrer le filtrage par statut.

## Session 2 — 2026-08-20 : correctif 500 au login + refonte design auth

**Signalé par l'utilisateur** : erreur 500 lors de la tentative de connexion après déploiement (config faite de son côté).

**Cause identifiée** : `AuthController::login()` appelle `RateLimiter::tooManyAttempts()` en tout premier (avant même `Auth::attempt()`), qui utilise le **cache par défaut** (`CACHE_STORE`, `.env.example`). Ce cache était configuré sur **Redis** par défaut. Si Redis n'est pas joignable dans l'environnement de déploiement de l'utilisateur, cette ligne lève une exception → 500, précisément à la soumission du formulaire de connexion (pas au chargement de la page), ce qui correspond exactement au symptôme rapporté.

**Correctif** : `CACHE_STORE` par défaut passé de `redis` à `database` dans `.env.example` (utilise la table `cache` déjà migrée, zéro dépendance de service supplémentaire). Redis reste disponible comme service Docker optionnel (cohérent avec la section 2 du cahier des charges qui le qualifie d'"optionnel") mais n'est plus un point de défaillance pour une fonctionnalité aussi centrale que la connexion.

**⚠️ Action requise côté utilisateur** : ce correctif modifie `.env.example`, pas le `.env` déjà généré sur son déploiement. Il doit soit régénérer son `.env` depuis le nouveau `.env.example`, soit changer manuellement `CACHE_STORE=redis` en `CACHE_STORE=database` dans son `.env` existant, puis relancer les conteneurs (`docker compose up -d --build`, ou simplement redémarrer `app` si les fichiers sont déjà à jour).

**Vérifié** : test de connexion complet (GET /login → extraction du token CSRF → POST /login avec `admin`/`Admin@2026!`) via `curl`, avec `CACHE_STORE=database` explicite et sans Redis démarré → `302` vers `/admin/dashboard` confirmé. Reste non vérifié : le comportement exact dans l'environnement Docker réel de l'utilisateur (cause probable mais pas prouvée à 100% sans accès à ses logs — si le problème persiste après ce correctif, demander `docker compose logs app` ou le contenu de `storage/logs/laravel.log`).

**Design pages de connexion/inscription** (demande explicite : "plus attractif, plus animatif, plus professionnel") : nouveau `layouts/guest.blade.php` — mise en page deux colonnes sur grand écran (panneau de marque avec dégradé animé + formes décoratives "blob" en CSS, formulaire dans la colonne droite), animation d'entrée en fondu (`bns-fade-in-up`), micro-interaction sur le bouton principal (léger soulèvement au survol). Toutes les animations respectent `prefers-reduced-motion` (déjà une exigence du design system, section 8). `login.blade.php` et `register.blade.php` réécrits pour utiliser ce nouveau layout, cohérence visuelle conservée avec le reste du design system (palette teal/ambre, Lexend/Source Sans 3). Vérifié : `npm run build` sans erreur, page `/login` retourne 200 avec le nouveau balisage présent.

**Nouvelle règle de workflow** (mémorisée) : mettre à jour ce fichier et pousser sur `origin` (https://github.com/NasserKailou/burundi_biblio.git, branche `master`) à la fin de chaque session sur ce projet, pas seulement sur demande explicite.

## Session 4 — 2026-08-22 : refonte professionnelle AdminLTE 3 + charte Burundi (en cours)

**Demande explicite de l'utilisateur** : remplacer le design Tailwind maison (sessions 1-3)
par **AdminLTE 3** sur tout le back-office, appliquer une charte graphique **Burundi**
(bleu ciel / blanc / vert / rouge) au lieu du teal/ambre, ajouter animations/toasts/
loaders, et livrer un espace grand public. Décision explicitement confirmée avec
l'utilisateur avant de commencer : AdminLTE (Bootstrap 4) et Tailwind ne cohabitent pas
sur une même page (resets globaux en conflit) — voir section 1 de `docs/design-system.md`
pour la répartition retenue (back-office = AdminLTE, site public/connexion = Tailwind,
inchangés dans leur framework, recolorés Burundi).

**Étape 1 (chore, terminée)** : audit — inventaire des layouts (`layouts/admin.blade.php`
= sidebar Tailwind utilisée uniquement par les vues `admin/*` ; `layouts/app.blade.php`
= navbar Tailwind utilisée par `teacher/*`, `dashboard/eleve`, `dashboard/enseignant`,
`catalogue/*`, `reader/show` ; `layouts/guest.blade.php` = login/register, autonome).
34 vues Blade recensées. Choix d'intégration : package `jeroennoten/laravel-adminlte`
(v3.16, AdminLTE 3.2.0 vendorisé) plutôt que des assets copiés à la main — publie tout en
local (`public/vendor/adminlte/...`, `public/vendor/{datatables,select2,sweetalert2,
toastr,chart.js,fontawesome-free,jquery,bootstrap,overlayScrollbars,...}`), zéro CDN,
cohérent avec la contrainte "aucune dépendance Internet" déjà appliquée aux polices/
PDF.js/EPUB.js (étape 10, session 1).

**Étape 2 (design, terminée)** : `docs/design-system.md` réécrit (v2). Palette Burundi
vérifiée par calcul de contraste WCAG (luminance relative), pas reprise telle quelle : le
bleu ciel vif imposé `#1CA9E6` échoue au AA avec du texte blanc (~2.7:1, sous le seuil
même pour du grand texte) — même piège que l'ambre en session 1. Palette à deux niveaux
retenue : `#1CA9E6` (accent vif, décoratif) + `#0E7BAA` (bleu "action", 4.7:1 avec blanc,
utilisé pour tout texte/bouton porteur de sens) + `#073C56` (bleu profond, surfaces
sombres) ; vert `#1EB53A` (accent) + `#178A2D` (action, 4.5:1 avec blanc) ; rouge
`#CE1126` conforme tel quel (5.6:1 avec blanc, pas de variante nécessaire). Appliqué en
CSS `:root` (`resources/css/app.css`, surface Tailwind) et en surcharge de classes
Bootstrap (`resources/css/adminlte-skin.css`, surface AdminLTE). Skill `ui-ux-pro-max`
invoqué (`--domain color --domain accessibility`) mais n'a renvoyé que la documentation
générique du skill (pas d'analyse ciblée exploitable) — la vérification de contraste a
donc été faite directement par calcul plutôt que reprise du skill, cohérent avec le
protocole de repli déjà documenté en session 1 (rejeter un résultat non pertinent et le
documenter plutôt que l'appliquer aveuglément).

**Étape 3 (feat, en cours)** : intégration AdminLTE.
- `composer require jeroennoten/laravel-adminlte`, `php artisan adminlte:install` +
  `adminlte:plugins install --plugin=datatables,select2,sweetalert2,toastr,chartJs`
  (Pace/progress-bar volontairement désactivé — plugin cosmétique non essentiel).
- `config/adminlte.php` : titre, logo (placeholder SVG `public/images/logo-etablissement.svg`
  — **à remplacer par le vrai logo de l'établissement**, emplacement réservé comme demandé),
  `google_fonts.allowed = false` (évite un CDN Google Fonts par défaut), `sidebar_collapse
  = true`, `use_route_url = true`. **Menu unique** (pas 3 sidebars séparées) filtré par
  rôle via des *Gates* Laravel (`'can' => 'is-admin'|'is-enseignant'|'is-eleve'`, définies
  dans `AppServiceProvider::boot()`) — reste compatible `config:cache` car le filtrage se
  fait au rendu. Items admin : Tableau de bord, Statistiques, Utilisateurs, Catalogue,
  Niveaux, Matières, Configuration, Journaux d'audit. **"Sauvegardes" volontairement
  omis** : aucune interface web n'existe pour déclencher `mysqldump` depuis le navigateur
  (`scripts/sauvegarde.sh` reste un outil serveur, section 12) — ajouter un lien mort ou
  bâtir une UI de déclenchement de sauvegarde dépasserait le périmètre "refonte visuelle"
  annoncé, décision documentée ici plutôt que pyivré silencieusement. Items enseignant :
  Tableau de bord, Mes ressources, Téléverser un manuel, Statistiques. Items élève :
  Accueil, Catalogue (« Reprendre la lecture »/« Mes favoris »/« Mon profil » restent des
  sections du tableau de bord élève, pas des routes dédiées — aucune de ces routes
  n'existe, cohérent avec "ne pas casser l'existant / ne pas ajouter de fonctionnalité").
- `app/Models/User.php` : accesseur `getNameAttribute()` (→ `nomComplet()`) et méthode
  `adminlte_desc()` (libellé de rôle) — le package attend `Auth::user()->name`, le schéma
  applicatif n'a que nom/prénom. Purement additif, RBAC/auth inchangés.
- `resources/views/layouts/adminlte.blade.php` (nouveau layout unique pour les 3 rôles) :
  `@extends('adminlte::page')`, lien de marque dynamique par rôle, flash messages
  (`session('status')`/`session('erreur')`) déclenchant un toast Toastr en plus de
  l'alerte Bootstrap classique, footer, sections `page-title`/`page-description`/
  `page-actions` pour uniformiser l'en-tête de contenu.
- `resources/css/adminlte-skin.css` + `resources/js/adminlte-app.js` (nouveaux, compilés
  via Vite comme le reste des assets) : surcharge complète des classes Bootstrap
  `-primary`/`-success`/`-danger` vers la charte Burundi (boutons, badges, alertes, navbar,
  sidebar actif, cartes), bandeau tricolore discret sous la navbar (signature visuelle
  demandée), animations d'apparition `.bns-reveal`/`.bns-reveal-list` (respecte
  `prefers-reduced-motion`), squelettes de chargement `.bns-skeleton`, config Toastr
  (couleurs Burundi), confirmation SweetAlert2 sur `[data-confirm]` (remplace `confirm()`
  natif, cohérent avec le retrait des `onsubmit` pour la CSP en session 1), init
  DataTables/Select2 sur les éléments marqués `data-datatable`/`data-select2`.
- `resources/js/bns-colors.js` : palette Chart.js mise à jour aux couleurs Burundi.
- **Première vue migrée à titre de preuve de bout en bout** : `dashboard/admin.blade.php`
  (`@extends('layouts.adminlte')`, KPIs en `small-box`, cartes de raccourci en `card`).
  **Vérifié en conditions réelles** (`php artisan serve` sur SQLite local + `curl`,
  identifiants de démo) : connexion admin → `/admin/dashboard` 200, sidebar affiche
  uniquement les items admin (aucune fuite des items enseignant/élève — RBAC de menu
  vérifié), CSS Burundi chargé, aucune erreur PHP dans la réponse ; connexion élève →
  `/admin/dashboard` toujours 403 (RBAC applicatif intact, indépendant du menu) ;
  `/dashboard` élève (page pas encore migrée) toujours 200 sur l'ancien layout Tailwind —
  confirme que la double surface (AdminLTE + Tailwind en parallèle pendant la migration
  progressive) ne casse rien.
- Reste à faire (étapes 4-10 du plan, voir liste de tâches) : migrer les ~24 vues
  restantes (admin/teacher/eleve/catalogue/reader) vers `layouts/adminlte.blade.php` avec
  markup AdminLTE natif (`card`, `table` + DataTables, `badge`, formulaires Bootstrap) ;
  recolorer le site public restant si besoin ; vérification responsive/accessibilité
  finale ; retirer `layouts/admin.blade.php` et `layouts/app.blade.php` une fois plus
  aucune vue ne les référence (actuellement encore utilisés par les vues non migrées, donc
  conservés pour l'instant).

**Incident local (session courante)** : `.env` local (non versionné, utilisé pour les
tests `php artisan serve` sans Docker) écrasé par erreur avec le contenu de `.env.example`
avant vérification de son contenu — corrigé en le reconfigurant pour SQLite local
(`DB_CONNECTION=sqlite`, `php artisan key:generate`) afin de pouvoir continuer les
vérifications en conditions réelles. Aucun impact sur le dépôt git (fichier ignoré) ni sur
un éventuel déploiement réel de l'utilisateur (`.env` de déploiement séparé, cf. session 2) — mais action prise sans vérification préalable, à éviter à l'avenir.

## Session 3 — 2026-08-20 : refonte visuelle globale + espace public

**Signalé par l'utilisateur** : le correctif 500 fonctionne, mais insatisfaction claire sur le design du back-office admin (exemples cités : `/admin/manuels/creer`, `/admin/niveaux`) malgré la demande initiale d'utiliser `ui-ux-pro-max-skill`. Trois demandes explicites : (1) revoir le template global de la plateforme, (2) prévoir un espace grand public sur `/` avant la page de connexion, (3) design général "très professionnel, très joli".

**Skill `ui-ux-pro-max`** : réinvoqué avec des requêtes ciblées (`--domain icons`, `--domain ux`, `--domain landing`) plutôt que le mode `--design-system` agrégé (déjà signalé peu fiable en session précédente). Résultats exploités : bibliothèque d'icônes recommandée (Phosphor — noms retenus : `house`, `users`, `book-open`, `tag`, `gear`, `shield`, `chart-bar`/`chart-pie`, `pencil-simple`, `trash`, `plus`, `magnifying-glass`, `sign-out`), implémentés en SVG outline maison (pas de dépendance npm supplémentaire, cohérent avec la contrainte "zéro dépendance Internet"). Guidance UX table/formulaire (bulk actions, hover vs tap, empty states) et pattern `feature-rich-showcase` (hero > grille de fonctionnalités > preuve/mécanique > CTA final) retenu pour la page publique.

**Nouveaux composants partagés** (`resources/views/components/`) : `icon.blade.php` (registre de ~30 icônes SVG outline), `page-header.blade.php` (titre + description + actions, uniformise l'en-tête de toutes les pages admin/enseignant), `empty-state.blade.php`, `form-section.blade.php` (regroupe les formulaires longs en sections titrées avec séparateurs). `button.blade.php` étendu pour accepter un prop `href` (rend un `<a>` stylé identique à un `<button>`, nécessaire pour les CTA de type lien). `stat-card.blade.php` et `sidebar-link.blade.php` étendus avec un prop `icon` optionnel (rétrocompatibles).

**Refonte du template global** : `layouts/admin.blade.php` — barre latérale reconstruite en carte (fond blanc, ombre, coins arrondis) avec icônes par lien et regroupement en 3 sections (Pilotage / Gestion / Système). `layouts/app.blade.php` — barre de navigation supérieure sticky avec avatar (initiales), rôle affiché, icônes sur les liens actifs, bouton de déconnexion iconifié.

**Pages retravaillées** (mêmes routes/contrôleurs/champs de formulaire, changement visuel uniquement) : tout le CRUD admin (`manuels`, `niveaux`, `matieres`, `utilisateurs` + import CSV, `audit`, `configuration`), le tableau de bord admin (cartes de raccourcis), les statistiques (admin + enseignant), le CRUD `teacher/manuels`, les tableaux de bord élève/enseignant, et la page catalogue élève (barre de filtre en carte, icône de recherche). Tables uniformisées : en-têtes plus contrastés, lignes avec hover, actions représentées par des boutons icône (modifier/supprimer/valider/désactiver/réinitialiser mot de passe), états vides gérés via `x-empty-state`. Formulaires longs découpés en sections (`x-form-section`), sélecteurs de niveaux transformés en "chips" cliquables (`has-[:checked]:` Tailwind, nécessite Tailwind ≥ 3.4, déjà en place).

**Nouvel espace public** : `resources/views/landing.blade.php`, page autonome (pas d'auth requise) — en-tête sticky avec logo + liens "Se connecter"/"Créer un compte", hero en dégradé teal avec formes décoratives animées (réutilise les classes `.bns-blob`/`.bns-fade-in-up` déjà définies pour les écrans d'auth), grille de 6 fonctionnalités, section "Comment ça marche" en 3 étapes, bandeau CTA final, pied de page. Route racine (`routes/web.php`) modifiée : `GET /` affiche désormais cette page pour un visiteur non connecté ; si l'utilisateur est déjà authentifié, redirection directe vers son tableau de bord (admin/enseignant/élève) au lieu de repasser par `/login`.

**Vérifié en conditions réelles** (`php artisan serve` + navigateur, connexions successives `admin`/`enseignant1`/`eleve1` avec les identifiants de démo) : page publique `/` (200, aucune erreur console), `/admin/manuels/creer`, `/admin/niveaux`, tableau de bord admin, `/admin/utilisateurs`, `/admin/matieres`, `/admin/audit`, `/admin/configuration`, `/admin/statistiques`, `/teacher/manuels`, `/teacher/manuels/creer`, tableau de bord enseignant, tableau de bord élève, `/catalogue` — toutes rendues sans erreur console ni erreur Blade. `npm run build` exécuté sans erreur avant les tests.
