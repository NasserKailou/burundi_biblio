# Infrastructure et budget indicatif

Déploiement local sur un intranet d'établissement, sans dépendance
Internet, couvrant un rayon d'environ 500 m (section 1 du cahier des
charges). **Un relevé de site réel (nombre de bâtiments, épaisseur des
murs, matériaux, effectifs simultanés) est nécessaire avant tout achat**
— les indications ci-dessous sont un point de départ dimensionné pour un
établissement de taille moyenne (quelques centaines d'élèves/enseignants
connectés simultanément), pas un devis définitif.

## Serveur applicatif

| Composant | Recommandation | Justification |
|---|---|---|
| CPU | Xeon/i7, 8 cœurs | Marge pour PHP-FPM + MariaDB + Redis simultanés en heure de pointe |
| RAM | 32 Go | MariaDB (cache InnoDB), Redis, plusieurs workers PHP-FPM |
| Stockage système | SSD 500 Go | OS + conteneurs Docker + logs |
| Stockage données | 2 To (idéalement RAID 1) | Manuels PDF/EPUB (les gros fichiers dominent le volume, pas la BDD) |
| Sauvegarde | Disque dédié séparé | Cible de `scripts/sauvegarde.sh` (cf. `docs/sauvegarde.md`) — jamais sur le même disque que les données live |
| Onduleur | 1500 VA | Coupure propre en cas de panne secteur, évite la corruption BDD |
| OS | Ubuntu Server 24.04 LTS | Support long terme, compatible Docker |

## Réseau

| Composant | Recommandation |
|---|---|
| Backbone | Câblage Cat6 entre bâtiments/étages |
| Switches | Gigabit PoE (alimentent les bornes Wi-Fi) |
| Bornes Wi-Fi | 8 à 15 bornes Wi-Fi 6 mesh selon la topologie du site — **à confirmer par un relevé de site** (matériaux des murs, nombre d'étages) |
| Routeur/pare-feu | Avec VLAN élèves / VLAN administration séparés |
| Baie de brassage | Centralise le câblage cuivre/fibre |

## Sécurité réseau

- VLAN élèves isolé du VLAN administration (le serveur BNS et les postes admin ne doivent pas être accessibles directement depuis le VLAN élèves au niveau réseau, en plus du RBAC applicatif).
- Aucune passerelle Internet requise pour le fonctionnement de BNS (l'app est conçue pour tourner 100% offline, cf. `docs/security.md`) — un accès Internet peut néanmoins exister sur le réseau pour d'autres usages, sans lien avec BNS.

## Budget indicatif (hors postes clients, hors développement)

| Poste | Estimation |
|---|---|
| Serveur (CPU/RAM/stockage/onduleur) | ~3 500 € |
| Switches Gigabit PoE + baie de brassage | ~2 000 € |
| Bornes Wi-Fi 6 mesh (8-15 unités) | ~4 000 € |
| Câblage Cat6 (fourniture + pose) | ~2 000 € |
| Routeur/pare-feu | ~800 € |
| Prestation d'installation/configuration | ~600 € |
| **Total indicatif** | **~12 900 €** |

Ce budget est **indicatif** et ne remplace pas un devis établi après
relevé de site par un intégrateur réseau. Il n'inclut pas les postes
clients (ordinateurs/tablettes des élèves et enseignants), déjà supposés
existants dans l'établissement.
