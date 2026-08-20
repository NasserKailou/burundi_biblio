#!/bin/sh
# Sauvegarde de la base de donnees (mysqldump) et des fichiers manuels/
# couvertures (storage/app/manuels + storage/app/couvertures) dans une
# archive horodatee. Concu pour tourner DANS le conteneur "app" (variables
# DB_* et chemins storage/ deja disponibles).
#
# Usage : sh scripts/sauvegarde.sh
# Planifie automatiquement en production via le service "scheduler" du
# docker-compose (cf. routes/console.php) - tache quotidienne a 02h00.

set -eu

RACINE="$(cd "$(dirname "$0")/.." && pwd)"
DOSSIER_SAUVEGARDES="${BNS_BACKUP_DIR:-$RACINE/storage/app/sauvegardes}"
HORODATAGE="$(date +%Y%m%d_%H%M%S)"
DOSSIER_COURANT="$DOSSIER_SAUVEGARDES/$HORODATAGE"
CONSERVATION_JOURS="${BNS_BACKUP_RETENTION_DAYS:-30}"

mkdir -p "$DOSSIER_COURANT"

echo "[sauvegarde] Export de la base de donnees..."
MYSQL_PWD="${DB_PASSWORD:-}" mysqldump \
    --host="${DB_HOST:-db}" \
    --port="${DB_PORT:-3306}" \
    --user="${DB_USERNAME:-bns_user}" \
    --single-transaction \
    --routines \
    --triggers \
    "${DB_DATABASE:-bns}" > "$DOSSIER_COURANT/base_de_donnees.sql"

echo "[sauvegarde] Archivage des fichiers manuels/couvertures..."
tar -czf "$DOSSIER_COURANT/fichiers.tar.gz" \
    -C "$RACINE/storage/app" \
    manuels couvertures

echo "[sauvegarde] Sauvegarde terminee : $DOSSIER_COURANT"

echo "[sauvegarde] Purge des sauvegardes de plus de ${CONSERVATION_JOURS} jours..."
find "$DOSSIER_SAUVEGARDES" -mindepth 1 -maxdepth 1 -type d -mtime "+${CONSERVATION_JOURS}" -exec rm -rf {} \;

echo "[sauvegarde] OK."
