#!/bin/sh
# Restauration d'une sauvegarde produite par scripts/sauvegarde.sh.
# ATTENTION : ecrase la base de donnees et les fichiers manuels/
# couvertures actuels. A executer DANS le conteneur "app".
#
# Usage : sh scripts/restauration.sh 20260820_020000
#         (le nom de dossier horodate affiche par sauvegarde.sh)

set -eu

if [ "$#" -ne 1 ]; then
    echo "Usage : sh scripts/restauration.sh <horodatage>" >&2
    echo "Exemple : sh scripts/restauration.sh 20260820_020000" >&2
    exit 1
fi

RACINE="$(cd "$(dirname "$0")/.." && pwd)"
DOSSIER_SAUVEGARDES="${BNS_BACKUP_DIR:-$RACINE/storage/app/sauvegardes}"
DOSSIER_COURANT="$DOSSIER_SAUVEGARDES/$1"

if [ ! -d "$DOSSIER_COURANT" ]; then
    echo "Sauvegarde introuvable : $DOSSIER_COURANT" >&2
    exit 1
fi

echo "Ceci va ECRASER la base de donnees et les fichiers manuels/couvertures actuels."
printf "Continuer ? (oui/non) "
read -r CONFIRMATION
if [ "$CONFIRMATION" != "oui" ]; then
    echo "Annule."
    exit 0
fi

echo "[restauration] Restauration de la base de donnees..."
MYSQL_PWD="${DB_PASSWORD:-}" mysql \
    --host="${DB_HOST:-db}" \
    --port="${DB_PORT:-3306}" \
    --user="${DB_USERNAME:-bns_user}" \
    "${DB_DATABASE:-bns}" < "$DOSSIER_COURANT/base_de_donnees.sql"

echo "[restauration] Restauration des fichiers manuels/couvertures..."
rm -rf "$RACINE/storage/app/manuels" "$RACINE/storage/app/couvertures"
tar -xzf "$DOSSIER_COURANT/fichiers.tar.gz" -C "$RACINE/storage/app"

echo "[restauration] Termine. Verifiez l'application avant de la remettre en service."
