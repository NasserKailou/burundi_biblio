# Sauvegarde et restauration

## Ce qui est sauvegardé

Chaque sauvegarde (`scripts/sauvegarde.sh`) produit un dossier horodaté
dans `storage/app/sauvegardes/AAAAMMJJ_HHMMSS/` contenant :

- `base_de_donnees.sql` — export complet MySQL/MariaDB (`mysqldump --single-transaction --routines --triggers`).
- `fichiers.tar.gz` — archive de `storage/app/manuels` et `storage/app/couvertures` (les fichiers PDF/EPUB et couvertures téléversés).

Le code applicatif n'est **pas** inclus (il est dans le dépôt Git) ; le
fichier `.env` n'est **pas** inclus non plus (contient des secrets — à
sauvegarder séparément, hors du dépôt et hors de cette archive, par
l'administrateur système).

## Planification automatique

Une sauvegarde quotidienne (02h00) et une purge hebdomadaire de
l'historique de lecture ancien (`bns:purger-consultations`, section 9 —
minimisation des données d'élèves mineurs) sont enregistrées dans
`routes/console.php` via le planificateur Laravel. Le conteneur
`scheduler` (service dédié dans `docker-compose.yml`) exécute
`php artisan schedule:run` toutes les minutes en boucle — il n'y a pas de
cron système dans l'image Docker.

Rétention : les dossiers de sauvegarde de plus de 30 jours sont purgés
automatiquement (variable `BNS_BACKUP_RETENTION_DAYS`, configurable dans
`.env`).

## Sauvegarde manuelle

```bash
docker compose exec app sh scripts/sauvegarde.sh
```

## Restauration

**⚠️ Opération destructive** : écrase la base de données et les fichiers
manuels/couvertures actuels. À exécuter en connaissance de cause, jamais
sur un environnement de production sans confirmation explicite d'un
administrateur.

1. Lister les sauvegardes disponibles :
   ```bash
   docker compose exec app ls storage/app/sauvegardes
   ```
2. Lancer la restauration en indiquant l'horodatage voulu :
   ```bash
   docker compose exec app sh scripts/restauration.sh 20260820_020000
   ```
   Le script demande une confirmation explicite (`oui`) avant d'écraser
   quoi que ce soit.
3. Vérifier l'application (connexion, catalogue, quelques manuels) avant
   de la remettre en service auprès des utilisateurs.

## Copie hors site

Ces scripts sauvegardent **localement** (dans le volume Docker
`storage_data`). Sur un déploiement réel, une copie régulière du dossier
`storage/app/sauvegardes` vers un support externe (disque de sauvegarde
dédié, cf. `docs/infrastructure.md`) est nécessaire pour survivre à une
panne du serveur lui-même — ces scripts ne s'en chargent pas.

## Limite de vérification

Ces scripts n'ont pas pu être testés en conditions réelles dans cette
session (ni `mysqldump` ni `mysql` ne sont disponibles dans
l'environnement de développement utilisé pour construire ce projet, et
Docker n'y est pas installé — voir `context.md`). La syntaxe shell a été
validée (`sh -n`) et la logique relue avec soin, mais une vérification
par l'administrateur lors du premier déploiement réel (`docker compose
exec app sh scripts/sauvegarde.sh` puis inspection du dossier produit)
est recommandée avant de faire confiance à la sauvegarde planifiée.
