# Design system — Bibliotheque Numerique Scolaire (BNS)

> Version 2 (session 4 — refonte AdminLTE + charte Burundi). La version 1 (palette
> teal/ambre, composants Blade Tailwind maison) est archivee dans l'historique git et
> dans `context.md` (sessions 1-3) ; ce document decrit desormais le systeme courant.

## Perimetre : deux systemes visuels distincts, assumes

Ce produit a **deux surfaces visuelles volontairement differentes**, chacune avec son
propre framework CSS — elles ne doivent jamais être mélangées sur une même page (Bootstrap
et Tailwind appliquent tous les deux des resets globaux qui entrent en conflit) :

| Surface | Framework | Pages |
|---|---|---|
| **Back-office authentifié** (admin, enseignant, élève) | AdminLTE 3 (Bootstrap 4) | `layouts/adminlte.blade.php` et tout ce qui en hérite |
| **Site public** (vitrine + connexion/inscription) | Tailwind CSS (maison) | `landing.blade.php`, `layouts/guest.blade.php` (login/register) |

La charte de couleurs Burundi est **partagée** par les deux surfaces (mêmes valeurs
hexadécimales), seule l'implémentation CSS diffère (variables Bootstrap-like surchargées
en `.css` pour AdminLTE, variables `:root` + tokens Tailwind pour le site public).

## Couleurs — charte Burundi

Choix imposé par le cahier des charges (bleu ciel / vert / rouge / blanc). Chaque teinte
"vive" a été vérifiée au ratio de contraste WCAG (luminance relative) plutôt que reprise
telle quelle : **le bleu ciel vif `#1CA9E6` échoue au AA avec du texte blanc (~2.7:1,
sous le seuil de 3:1 même pour du grand texte)** — même problème que la palette teal
d'origine avec l'ambre. Solution retenue : une teinte "accent" vive pour la décoration et
une teinte "action" plus profonde, dérivée de la même famille de couleur, pour tout texte
ou icône porteur de sens sur fond clair, et pour tout texte blanc sur fond coloré.

| Rôle | Hex | Contraste vérifié | Usage |
|---|---|---|---|
| Bleu — accent (vif) | `#1CA9E6` | 2.7:1 sur blanc (décoratif uniquement) | Dégradés (stop clair), icônes accompagnées d'un libellé texte, bordures, fonds de badge clair avec texte foncé |
| Bleu — action (profond, AA) | `#0E7BAA` | 4.7:1 sur blanc / avec texte blanc | Boutons primaires, navbar, sidebar (état actif), liens de texte, `.bg-primary`/`.btn-primary` AdminLTE |
| Bleu — profond (surfaces sombres) | `#073C56` | > 10:1 avec texte blanc | Fond du hero public, dégradés de section sombre |
| Vert — accent (vif) | `#1EB53A` | 2.7:1 sur blanc (décoratif uniquement) | Puces de statut, icônes accompagnées d'un libellé, fonds de badge clair |
| Vert — action (profond, AA) | `#178A2D` | 4.5:1 avec texte blanc | Boutons de succès, alertes/toasts succès, `.btn-success`/`.bg-success` AdminLTE |
| Rouge — danger | `#CE1126` | 5.6:1 avec texte blanc (déjà conforme tel quel) | Boutons/alertes/toasts destructeurs, `.btn-danger`/`.bg-danger` AdminLTE |
| Blanc | `#FFFFFF` | — | Fonds de carte, texte sur fond profond |
| Surface (gris très clair) | `#F4F7FA` | — | Fond de page, sections alternées |
| Texte | `#2B2B2B` | 14.1:1 sur blanc | Texte principal |
| Texte atténué | `#475569` | conforme AA (repris tel quel de la palette v1, déjà validé) | Texte secondaire, libellés |

Nuances dérivées (hover/active), calculées par assombrissement HSL (~7.5 %/10 %/12.5 %,
convention Bootstrap) plutôt qu'estimées à l'œil :

```
--bns-blue-500: #1CA9E6;   /* accent vif */
--bns-blue-600: #1692C8;   /* hover du bleu action */
--bns-blue-700: #0E7BAA;   /* action AA */
--bns-blue-800: #158ABC;   /* active */
--bns-blue-900: #073C56;   /* surfaces sombres */
--bns-green-500: #1EB53A;
--bns-green-600: #19942F;
--bns-green-700: #178A2D;  /* action AA */
--bns-red-500: #CE1126;
--bns-red-600: #AB0E1F;    /* hover */
--bns-red-700: #9F0D1D;    /* active */
```

### Implémentation AdminLTE (back-office)

`resources/css/adminlte-skin.css`, chargé après `adminlte.min.css` (via `@push('css')`
dans `layouts/adminlte.blade.php`, compilé par Vite). Surcharge par sélecteur — AdminLTE 3
compile Bootstrap 4 en CSS statique (pas de variables custom-property réellement utilisées
par les classes), donc chaque classe `-primary`/`-success` utilisée dans l'app est
redéfinie explicitement (`.bg-primary`, `.btn-primary` + états hover/focus/active,
`.text-primary`, `.border-primary`, `.navbar-primary`, `.card-primary`,
`.card-outline.card-primary`, `.badge-primary`, `.alert-primary`,
`.sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active`,
`.bg-gradient-primary`, équivalents `-success`/`-danger`). Voir le fichier pour la liste
complète.

### Implémentation Tailwind (site public)

`resources/css/app.css` — mêmes valeurs hex, exposées en variables CSS `:root` et
mappées dans `tailwind.config.js` (`theme.extend.colors.bns.*`), plus les teintes
Tailwind natives `sky-*` utilisées directement pour les dégradés/décors (remplace les
anciennes classes `teal-*`).

## Typographie

Inchangé depuis la v1 : **Lexend** (titres, boutons, nav) + **Source Sans 3** (corps de
texte), auto-hébergées via `@fontsource` (zéro dépendance Internet). Le back-office
AdminLTE réutilise la même paire — `google_fonts.allowed` est mis à `false` dans
`config/adminlte.php` (qui pointerait sinon vers Google Fonts en CDN) et
`adminlte-skin.css` réimporte les mêmes polices locales sur `body`.

```css
@import '@fontsource/lexend/500.css';
@import '@fontsource/lexend/600.css';
@import '@fontsource/lexend/700.css';
@import '@fontsource/source-sans-3/400.css';
@import '@fontsource/source-sans-3/500.css';
@import '@fontsource/source-sans-3/600.css';
```

## AdminLTE — conventions retenues

- **Package** : `jeroennoten/laravel-adminlte` (v3.16, AdminLTE 3.2 vendorisé) plutôt que
  des assets AdminLTE copiés à la main — publie les fichiers en local dans
  `public/vendor/adminlte/...` (`php artisan adminlte:install`), zéro CDN, cohérent avec
  la contrainte "aucune dépendance Internet" déjà appliquée aux polices/PDF.js/EPUB.js.
  Plugins publiés localement : DataTables, Select2, Sweetalert2, Toastr, Chart.js,
  Pace (`php artisan adminlte:plugins install --plugin=...`).
- **Layout unique** `resources/views/layouts/adminlte.blade.php` (`@extends('adminlte::page')`)
  pour les 3 rôles authentifiés — pas boxé (`layout_boxed` par défaut = pleine largeur),
  sidebar rétractable (`sidebar_collapse` = true).
- **Menu par rôle** : un seul tableau `config('adminlte.menu')`, filtré à l'affichage via
  des *Gates* Laravel (`'can' => 'is-admin'|'is-enseignant'|'is-eleve'`, définies dans
  `AppServiceProvider::boot()`) plutôt que 3 vues de sidebar séparées — évite la
  duplication et reste compatible avec `php artisan config:cache` (le filtrage se fait au
  rendu, pas à la lecture du fichier de config).
- **Composants** : classes Bootstrap/AdminLTE natives directement dans les vues
  (`card`, `small-box`, `info-box`, `table`, `badge`, `btn btn-*`) plutôt que les anciens
  composants Blade `x-*` (v1) qui généraient du HTML/Tailwind — les vues des étapes 4 à 6
  ont été migrées une à une, mêmes routes/contrôleurs/champs de formulaire, changement
  visuel uniquement.
- **Icônes** : Font Awesome Free, fourni avec AdminLTE (`public/vendor/fontawesome-free`),
  remplace le registre SVG maison (`x-icon`) dans le back-office ; le site public
  Tailwind conserve `x-icon` (déjà fonctionnel, pas de raison de le remplacer là où
  Tailwind reste en place).

## Animations & convivialité

- Transitions AdminLTE natives (sidebar collapse, `data-widget="treeview"`) conservées
  telles quelles — déjà fluides et déjà testées par des millions d'installations, pas de
  raison de les réécrire.
- Apparition des cartes/sections : classe utilitaire `.bns-reveal` (fade + translateY
  léger, `animation-delay` en cascade via `nth-child`), ajoutée dans `adminlte-skin.css`,
  respecte `prefers-reduced-motion` (comme `bns-fade-in-up` sur le site public).
- **Toasts** : plugin Toastr (déjà vendorisé), configuré aux couleurs Burundi
  (`toastr.options` dans `resources/js/adminlte-app.js`) — succès en vert profond, erreur
  en rouge, positionné en haut à droite, `closeButton: true` (accessibilité — pas de
  disparition forcée avant lecture).
- **Confirmations destructrices** (suppression) : Sweetalert2 (déjà vendorisé) remplace
  `confirm()` natif, cohérent avec le retrait de `onsubmit="confirm(...)"` fait à l'étape
  10 pour la CSP.
- **Chart.js** : palette Burundi exposée dans `resources/js/bns-colors.js` (déjà
  existant, valeurs mises à jour).

## Accessibilité

- Contraste AA vérifié par calcul (voir tableau couleurs ci-dessus), pas par estimation
  visuelle.
- `prefers-reduced-motion` respecté sur toutes les animations ajoutées (héritage direct
  de la règle posée en v1, section 8 du cahier des charges).
- Navigation clavier : AdminLTE gère nativement le focus sur la sidebar/navbar/modales
  Bootstrap ; vérifié à l'étape 9 (session 4) sur les écrans reconvertis.
- La couleur n'est jamais le seul vecteur d'information (règle héritée v1) : badges de
  statut toujours accompagnés d'un libellé texte, graphiques avec légende + tableau de
  repli accessible.

## Espace public (Tailwind, inchangé dans sa structure — recoloré)

`landing.blade.php` : même structure qu'en v1 (header sticky, hero, fonctionnalités,
"comment ça marche", CTA, footer), palette `teal-*` remplacée par `sky-*`/`bns-blue-*`,
ajout d'une section "chiffres publics" (nombre de manuels publiés, niveaux couverts —
aucune donnée personnelle) et d'une section "À propos". Bouton principal explicite
« Se connecter à la bibliothèque ».

## Responsive

Inchangé : 768px (tablette — cible principale avec le desktop), 1024px, 1440px. AdminLTE
gère nativement le repli de la sidebar en mode tablette/mobile (`sidebar-mini`, `lg`).
