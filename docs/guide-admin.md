# Guide administrateur

## Utilisateurs

**Utilisateurs** dans le menu latéral : liste complète, filtrable par
rôle/statut/recherche. Actions par ligne :

- **Modifier** : changer rôle, niveau(x), classe, droit "commun" (pour un enseignant), réinitialiser via le formulaire.
- **Valider** : active un compte élève en attente d'inscription.
- **Désactiver** : bloque immédiatement l'accès, même si une session est déjà ouverte.
- **Réinitialiser MDP** : génère un nouveau mot de passe aléatoire, affiché une seule fois — à communiquer à l'utilisateur par un canal sécurisé (il n'est jamais stocké en clair ni ré-affichable).
- **Supprimer** : impossible sur le dernier compte administrateur (protection contre le verrouillage total du système).

**Importer un CSV** : colonnes `nom,prenom,identifiant,niveau,classe` —
crée des comptes élèves en lot (mot de passe aléatoire, à réinitialiser
individuellement si besoin).

## Catalogue

**Catalogue** dans le menu : vue globale de tous les manuels, tous
enseignants confondus, avec possibilité de modifier ou supprimer
n'importe quelle ressource (contrairement à l'espace enseignant, limité
à ses propres manuels).

## Niveaux / Matières

CRUD simple. Un niveau ou une matière utilisé par des élèves/manuels ne
peut pas être supprimé (message d'erreur explicite) — retirez d'abord
les dépendances.

## Configuration

**Configuration** : nom de l'établissement, taille maximale des
fichiers, formats autorisés, politique de mot de passe (longueur
minimale), durée de conservation des consultations (minimisation des
données d'élèves mineurs), et **validation automatique des
inscriptions** (si activée, un élève qui s'inscrit est actif
immédiatement sans validation manuelle).

## Statistiques

Vue globale (tous niveaux) avec filtres niveau/granularité temporelle,
et export CSV/PDF.

## Journaux d'audit

**Journaux d'audit** : trace de toutes les actions sensibles
(connexions, uploads, modifications, suppressions, changements de
configuration) avec utilisateur, date et adresse IP. Filtrable par
action.

## Sauvegarde

Voir `docs/sauvegarde.md` pour la procédure complète de
sauvegarde/restauration (planifiée automatiquement chaque nuit).
