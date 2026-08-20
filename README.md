# Bibliothèque Numérique Scolaire (BNS)

Bibliothèque numérique **interne** à un établissement scolaire : les
élèves consultent les manuels de leur niveau (+ les ressources
"communes"), les enseignants téléversent et gèrent leurs manuels, et
l'administrateur pilote l'ensemble (utilisateurs, catalogue,
configuration, statistiques, audit). Conçue pour tourner **sans aucune
dépendance Internet** sur le réseau local d'un établissement.

## Pile technique

Laravel 11 (PHP 8.2) · Blade + Tailwind CSS + Fetch API (AJAX) ·
MySQL/MariaDB · PDF.js + EPUB.js (vendorisés) · Chart.js · Docker +
docker-compose (app, nginx, db, redis, scheduler).

## Démarrage rapide

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

L'application est ensuite disponible sur **http://localhost:8080**.

## Comptes de démonstration

Mot de passe commun par rôle (démo uniquement — à changer en production
via **Réinitialiser MDP** dans le back-office admin) :

| Rôle | Identifiant(s) | Mot de passe |
|---|---|---|
| Admin | `admin` | `Admin@2026!` |
| Enseignant | `enseignant1`, `enseignant2`, `enseignant3` | `Enseignant@2026!` |
| Élève | `eleve1` à `eleve12` | `Eleve@2026!` |

`eleve4` et `eleve12` sont volontairement inactifs (démontrent le
workflow de validation d'inscription par un administrateur). Détail
complet des comptes de démo et des conventions retenues : `context.md`.

## Documentation

| Document | Contenu |
|---|---|
| [docs/guide-eleve.md](docs/guide-eleve.md) | Guide d'utilisation élève |
| [docs/guide-enseignant.md](docs/guide-enseignant.md) | Guide d'utilisation enseignant |
| [docs/guide-admin.md](docs/guide-admin.md) | Guide d'utilisation administrateur |
| [docs/design-system.md](docs/design-system.md) | Palette, typographie, composants UI |
| [docs/security.md](docs/security.md) | Mesures de sécurité mises en œuvre |
| [docs/sauvegarde.md](docs/sauvegarde.md) | Procédure de sauvegarde/restauration |
| [docs/infrastructure.md](docs/infrastructure.md) | Dimensionnement matériel/réseau et budget indicatif |
| [context.md](context.md) | Mémoire de projet : décisions, conventions, historique complet des 13 étapes de construction |

## Développement local (sans Docker)

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
# Ajuster DB_* dans .env pour pointer vers un MySQL/MariaDB local, ou
# utiliser sqlite (DB_CONNECTION=sqlite, DB_DATABASE=/chemin/vers/fichier.sqlite)
php artisan migrate --seed
php artisan serve
```

## Tests

```bash
php artisan test
```

29 tests (unitaires + fonctionnels + sécurité) — voir `context.md` étape
11 pour le détail de la couverture.

## Sauvegarde

Planifiée automatiquement (service `scheduler` du docker-compose).
Procédure manuelle et restauration : [docs/sauvegarde.md](docs/sauvegarde.md).

## Limites connues et zones non vérifiées visuellement

Ce projet a été construit et testé de bout en bout dans un environnement
sans accès à Docker ni à un navigateur graphique standard. Deux
conséquences documentées en détail dans `context.md` :

- Le rendu **pixel** du lecteur PDF/EPUB (canvas PDF.js, iframe EPUB.js) n'a pas pu être confirmé visuellement — toute la logique métier (RBAC, suivi de consultation, favoris) est vérifiée, mais une vérification visuelle manuelle est recommandée avant mise en production.
- Les scripts de sauvegarde/restauration n'ont pas pu être exécutés contre un vrai MySQL (syntaxe validée, logique relue, non exécutée en conditions réelles).

Convention de choix documentée pour toute ambiguïté du cahier des
charges : voir la section "Conventions retenues" de `context.md`.
